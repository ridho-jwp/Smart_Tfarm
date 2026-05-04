/*
 * ============================================================
 * Smart Pakcoy Hidroponik — ESP32 Firmware v2.1.1 (FIXED)
 * ============================================================
 */

#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <OneWire.h>
#include <DallasTemperature.h>
#include <PZEM004Tv30.h>

// ============================================================
//  STRUCT & PROTOTYPE (Perbaikan Error 'does not name a type')
// ============================================================

struct DataListrik {
    float voltage;      // Volt
    float current;      // Ampere
    float power;        // Watt
    float energy;       // kWh
    float frequency;    // Hz
    float powerFactor;  // PF (0.0 - 1.0)
    bool  valid;        // true jika pembacaan berhasil
};

// Deklarasi fungsi agar dikenal oleh kompiler
DataListrik bacaListrik(); 

// ============================================================
//  KONFIGURASI — UBAH SESUAI KEBUTUHAN
// ============================================================

const char* WIFI_SSID     = "RIDHO";
const char* WIFI_PASSWORD = "Password123";
const char* SERVER_URL    = "http://10.207.249.121:8000";
const char* API_KEY       = "hidra-core-secret-key-2026";
const char* DEVICE_ID     = "ESP32-SENSOR-001";
const char* PUMP_CIRC_ID  = "ESP32-PUMP-SIRKULASI";
const char* PUMP_PERI_ID  = "ESP32-PUMP-PERISTALTIK";

// ============================================================
//  PIN MAPPING
// ============================================================

#define ONE_WIRE_BUS    4
#define PH_PIN         34
#define TDS_PIN        35
#define TRIG_PIN       13
#define ECHO_PIN       12
#define RELAY_CIRC     26
#define RELAY_PERI     27
#define LED_PIN         2
#define PZEM_RX_PIN    16
#define PZEM_TX_PIN    17

// ============================================================
//  BATAS & STATUS RUNTIME
// ============================================================

float phMin = 5.5; float phMax = 6.5;
float suhuMin = 22.0; float suhuMax = 30.0;
float ppmMin = 500.0; float ppmMax = 1200.0;
float jarakNyala = 30.0; float jarakMati = 10.0;

bool circOn = false;
bool peristalticOn = false;
unsigned long lastSendTime = 0, lastConfigTime = 0, lastHeartbeatTime = 0, lastCmdPollTime = 0, peristalticStart = 0;
const unsigned long SEND_INTERVAL = 60000UL, CONFIG_INTERVAL = 300000UL, HEARTBEAT_INTERVAL = 30000UL, CMD_POLL_INTERVAL = 5000UL, PERI_AUTO_DURATION = 60000UL;
unsigned long lastPeriCommandSent = 0;
const unsigned long PERI_CMD_COOLDOWN = 65000UL;

// ============================================================
//  INISIALISASI LIBRARY
// ============================================================

OneWire oneWire(ONE_WIRE_BUS);
DallasTemperature tempSensor(&oneWire);
PZEM004Tv30 pzem(Serial2, PZEM_RX_PIN, PZEM_TX_PIN);

// ============================================================
//  SETUP & LOOP
// ============================================================

void setup() {
    Serial.begin(115200);
    pinMode(TRIG_PIN, OUTPUT); pinMode(ECHO_PIN, INPUT);
    pinMode(RELAY_CIRC, OUTPUT); pinMode(RELAY_PERI, OUTPUT); pinMode(LED_PIN, OUTPUT);
    digitalWrite(RELAY_CIRC, HIGH); digitalWrite(RELAY_PERI, HIGH);

    analogReadResolution(12);
    analogSetAttenuation(ADC_11db);

    tempSensor.begin();
    Serial2.begin(9600, SERIAL_8N1, PZEM_RX_PIN, PZEM_TX_PIN);
    
    connectWiFi();
    fetchConfigFromServer();
    Serial.println("[OK] Setup selesai.");
}

void loop() {
    if (WiFi.status() != WL_CONNECTED) connectWiFi();
    unsigned long now = millis();

    if (now - lastSendTime >= SEND_INTERVAL) { lastSendTime = now; sendSensorData(); }
    if (now - lastConfigTime >= CONFIG_INTERVAL) { lastConfigTime = now; fetchConfigFromServer(); }
    if (now - lastHeartbeatTime >= HEARTBEAT_INTERVAL) { lastHeartbeatTime = now; sendHeartbeat(); }
    if (now - lastCmdPollTime >= CMD_POLL_INTERVAL) { lastCmdPollTime = now; pollCirculationCommand(); }
    if (peristalticOn && (now - peristalticStart >= PERI_AUTO_DURATION)) matikanPeristaltic();
    delay(200);
}

// ============================================================
//  FUNGSI SENSOR & KONTROL (Logika Utama)
// ============================================================

DataListrik bacaListrik() {
    DataListrik data;
    data.valid = false;
    float v = pzem.voltage(); float c = pzem.current(); float p = pzem.power();
    float e = pzem.energy(); float f = pzem.frequency(); float pf = pzem.pf();

    if (isnan(v) || isnan(c) || isnan(p)) {
        Serial.println("[PZEM] Gagal baca! Periksa Power AC & Wiring.");
        return data;
    }
    data.voltage = v; data.current = c; data.power = p;
    data.energy = isnan(e) ? 0 : e; data.frequency = isnan(f) ? 0 : f;
    data.powerFactor = isnan(pf) ? 0 : pf;
    data.valid = true;
    return data;
}

float bacaSuhu() {
    tempSensor.requestTemperatures();
    float suhu = tempSensor.getTempCByIndex(0);
    return (suhu == DEVICE_DISCONNECTED_C) ? -127.0 : suhu;
}

float bacaPH() {
    long sum = 0;
    for (int i = 0; i < 30; i++) { sum += analogRead(PH_PIN); delay(5); }
    float voltage = (sum / 30.0) * 3.3 / 4095.0;
    float slope = 3.0 / (2.23 - 2.51); // Berdasarkan PH_VOLTAGE_AT_4 & 7
    return constrain(7.0 + slope * (voltage - 2.51), 0.0, 14.0);
}

float bacaTDS(float suhu) {
    if (suhu < -50) suhu = 25.0;
    long sum = 0;
    for (int i = 0; i < 30; i++) { sum += analogRead(TDS_PIN); delay(5); }
    float voltage = (sum / 30.0) * 3.3 / 4095.0;
    float compVol = voltage / (1.0 + 0.02 * (suhu - 25.0));
    return (133.42*pow(compVol,3) - 255.86*pow(compVol,2) + 857.39*compVol) * 0.5;
}

float bacaJarak() {
    digitalWrite(TRIG_PIN, LOW); delayMicroseconds(2);
    digitalWrite(TRIG_PIN, HIGH); delayMicroseconds(10);
    digitalWrite(TRIG_PIN, LOW);
    long durasi = pulseIn(ECHO_PIN, HIGH, 30000);
    return (durasi == 0) ? -1.0 : (durasi * 0.0343) / 2.0;
}

void nyalakanPeristaltic() { digitalWrite(RELAY_PERI, LOW); peristalticOn = true; peristalticStart = millis(); lastPeriCommandSent = millis(); }
void matikanPeristaltic() { digitalWrite(RELAY_PERI, HIGH); peristalticOn = false; }
void nyalakanSirkulasi() { digitalWrite(RELAY_CIRC, LOW); circOn = true; }
void matikanSirkulasi() { digitalWrite(RELAY_CIRC, HIGH); circOn = false; }

// ============================================================
//  FUNGSI KOMUNIKASI (HTTP POST/GET)
// ============================================================

void sendSensorData() {
    float s = bacaSuhu(); float ph = bacaPH(); float tds = bacaTDS(s); float jr = bacaJarak();
    DataListrik list = bacaListrik();

    if (tds > 0 && tds < ppmMin && !peristalticOn && (millis() - lastPeriCommandSent >= PERI_CMD_COOLDOWN)) nyalakanPeristaltic();

    if (WiFi.status() != WL_CONNECTED) return;
    HTTPClient http;
    http.begin(String(SERVER_URL) + "/api/v1/sensor-data");
    http.addHeader("Content-Type", "application/json");
    http.addHeader("X-API-Key", API_KEY);

    DynamicJsonDocument doc(1024);
    doc["device_id"] = DEVICE_ID;
    if (s != -127.0) doc["suhu"] = roundf(s * 100) / 100.0;
    doc["ph"] = roundf(ph * 100) / 100.0;
    doc["ppm"] = roundf(tds * 100) / 100.0;
    if (jr > 0) doc["water_level"] = roundf(jr * 100) / 100.0;

    if (list.valid) {
        doc["voltage"] = list.voltage; doc["current"] = list.current;
        doc["power"] = list.power; doc["energy"] = list.energy;
    }
    doc["pump_circ_on"] = circOn; doc["pump_peri_on"] = peristalticOn;

    String payload; serializeJson(doc, payload);
    int httpCode = http.POST(payload);
    Serial.printf("[HTTP] Data Sent, Code: %d\n", httpCode);
    http.end();
}

void connectWiFi() {
    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
    int t = 0; while (WiFi.status() != WL_CONNECTED && t < 20) { delay(500); t++; }
    if (WiFi.status() == WL_CONNECTED) digitalWrite(LED_PIN, HIGH);
}

void pollCirculationCommand() {
    HTTPClient http;
    http.begin(String(SERVER_URL) + "/api/v1/command/" + PUMP_CIRC_ID);
    http.addHeader("X-API-Key", API_KEY);
    if (http.GET() == 200) {
        StaticJsonDocument<256> doc; deserializeJson(doc, http.getString());
        if (doc["success"]) {
            const char* cmd = doc["command"] | "";
            if (strcmp(cmd, "circulation_on") == 0) nyalakanSirkulasi();
            else if (strcmp(cmd, "circulation_off") == 0) matikanSirkulasi();
        }
    }
    http.end();
}

void fetchConfigFromServer() {
    HTTPClient http;
    http.begin(String(SERVER_URL) + "/api/v1/configs");
    http.addHeader("X-API-Key", API_KEY);
    if (http.GET() == 200) {
        StaticJsonDocument<1024> doc; deserializeJson(doc, http.getString());
        JsonObject c = doc["configs"];
        if (c.containsKey("ph"))            { phMin = c["ph"]["min_optimal"];            phMax = c["ph"]["max_optimal"]; }
        if (c.containsKey("ppm"))           { ppmMin = c["ppm"]["min_optimal"];          ppmMax = c["ppm"]["max_optimal"]; }
        if (c.containsKey("suhu"))          { suhuMin = c["suhu"]["min_optimal"];        suhuMax = c["suhu"]["max_optimal"]; }
        if (c.containsKey("ketinggian_air")){ jarakMati = c["ketinggian_air"]["min_optimal"]; jarakNyala = c["ketinggian_air"]["max_optimal"]; }
        Serial.printf("[Config] pH:%.1f-%.1f | PPM:%.0f-%.0f | Suhu:%.1f-%.1f | Jarak:%.1f-%.1f\n",
            phMin, phMax, ppmMin, ppmMax, suhuMin, suhuMax, jarakMati, jarakNyala);
    }
    http.end();
}

void sendHeartbeat() {
    const char* ids[] = {DEVICE_ID, PUMP_CIRC_ID, PUMP_PERI_ID};
    for (int i=0; i<3; i++) {
        HTTPClient http;
        http.begin(String(SERVER_URL) + "/api/v1/heartbeat");
        http.addHeader("Content-Type", "application/json");
        http.addHeader("X-API-Key", API_KEY);
        http.POST("{\"device_id\":\"" + String(ids[i]) + "\"}");
        http.end();
    }
}