/*
 * ============================================================
 * Smart Pakcoy Hidroponik — ESP32 WROOM-32 Firmware v3.0.0
 * ============================================================
 * Target  : ESP32 WROOM-32 (38 pin)
 * Perubahan dari v2.1.1:
 *   - Menambahkan peristaltik PESTISIDA (RELAY_PERI_PESTISIDA = IO25)
 *   - Endpoint pestisida: POST /api/v1/pestisida
 *   - Polling pestisida setiap INTERVAL_PESTISIDA (10 detik)
 *   - Pompa pestisida dikontrol by-duration (ml/detik)
 *   - Semua operasi non-blocking menggunakan millis()
 * ============================================================
 */

#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <OneWire.h>
#include <DallasTemperature.h>
#include <PZEM004Tv30.h>

// ============================================================
//  STRUCT
// ============================================================

struct DataListrik {
    float voltage;
    float current;
    float power;
    float energy;
    float frequency;
    float powerFactor;
    bool  valid;
};

// Deklarasi fungsi
DataListrik bacaListrik();

// ============================================================
//  KONFIGURASI JARINGAN & SERVER
// ============================================================

const char* WIFI_SSID     = "RIDHO";
const char* WIFI_PASSWORD = "Password123";
const char* SERVER_URL    = "http://10.207.249.121:8000";
const char* API_KEY       = "hidra-core-secret-key-2026";
const char* DEVICE_ID     = "ESP32-SENSOR-001";
const char* PUMP_CIRC_ID  = "ESP32-PUMP-SIRKULASI";
const char* PUMP_PERI_ID  = "ESP32-PUMP-PERISTALTIK";

// ============================================================
//  PIN MAPPING — ESP32 WROOM-32 (38 PIN)
// ============================================================
//
//  Sensor & Aktuator:
//    IO4  → OneWire (DS18B20 suhu)
//    IO34 ← ADC pH sensor         (input only, no pullup)
//    IO35 ← ADC TDS sensor         (input only, no pullup)
//    IO13 → Ultrasonic TRIG
//    IO12 ← Ultrasonic ECHO
//
//  Relay (active LOW):
//    IO26 → Relay sirkulasi
//    IO27 → Relay peristaltik NUTRISI
//    IO25 → Relay peristaltik PESTISIDA  ← BARU
//
//  Indikator:
//    IO2  → LED bawaan (WiFi connected)
//
//  PZEM-004T via Serial2:
//    IO16 ← PZEM RX
//    IO17 → PZEM TX
//
// ============================================================

#define ONE_WIRE_BUS         4
#define PH_PIN              34
#define TDS_PIN             35
#define TRIG_PIN            13
#define ECHO_PIN            12
#define RELAY_CIRC          26
#define RELAY_PERI_NUTRISI  27   // peristaltik nutrisi (kode lama: RELAY_PERI)
#define RELAY_PERI_PESTISIDA 25  // peristaltik pestisida ← BARU
#define LED_PIN              2
#define PZEM_RX_PIN         16
#define PZEM_TX_PIN         17

// ============================================================
//  KALIBRASI PESTISIDA
// ============================================================

float mlPerdetik = 1.0;   // Sesuaikan dengan kalibrasi pompa pestisida Anda

// ============================================================
//  BATAS OPTIMAL (akan di-update dari server)
// ============================================================

float phMin   = 5.5,  phMax   = 6.5;
float suhuMin = 22.0, suhuMax = 30.0;
float ppmMin  = 500.0, ppmMax = 1200.0;
float jarakNyala = 30.0, jarakMati = 10.0;

// ============================================================
//  STATUS RUNTIME
// ============================================================

bool circOn       = false;
bool nutrisiOn    = false;  // (dulu: peristalticOn)
bool pestisidaOn  = false;  // ← BARU

unsigned long lastSendTime        = 0;
unsigned long lastConfigTime      = 0;
unsigned long lastHeartbeatTime   = 0;
unsigned long lastCmdPollTime     = 0;
unsigned long nutrisiStart        = 0;
unsigned long lastNutrisiCmdSent  = 0;
unsigned long lastPestisidaTime   = 0;  // ← BARU

// ============================================================
//  INTERVAL & DURASI
// ============================================================

const unsigned long SEND_INTERVAL         = 60000UL;   // 60 detik
const unsigned long CONFIG_INTERVAL       = 300000UL;  // 5 menit
const unsigned long HEARTBEAT_INTERVAL    = 30000UL;   // 30 detik
const unsigned long CMD_POLL_INTERVAL     = 5000UL;    // 5 detik
const unsigned long NUTRISI_AUTO_DURATION = 60000UL;   // 60 detik
const unsigned long NUTRISI_CMD_COOLDOWN  = 65000UL;   // cooldown agar tidak spam
const unsigned long INTERVAL_PESTISIDA    = 10000UL;   // 10 detik ← BARU

// ============================================================
//  INISIALISASI LIBRARY
// ============================================================

OneWire oneWire(ONE_WIRE_BUS);
DallasTemperature tempSensor(&oneWire);
PZEM004Tv30 pzem(Serial2, PZEM_RX_PIN, PZEM_TX_PIN);

// ============================================================
//  SETUP
// ============================================================

void setup() {
    Serial.begin(115200);

    // Pin mode
    pinMode(TRIG_PIN,             OUTPUT);
    pinMode(ECHO_PIN,             INPUT);
    pinMode(RELAY_CIRC,           OUTPUT);
    pinMode(RELAY_PERI_NUTRISI,   OUTPUT);
    pinMode(RELAY_PERI_PESTISIDA, OUTPUT);  // ← BARU
    pinMode(LED_PIN,              OUTPUT);

    // Relay active LOW — mulai dalam kondisi MATI
    digitalWrite(RELAY_CIRC,           HIGH);
    digitalWrite(RELAY_PERI_NUTRISI,   HIGH);
    digitalWrite(RELAY_PERI_PESTISIDA, HIGH);  // ← BARU

    // ADC setup
    analogReadResolution(12);
    analogSetAttenuation(ADC_11db);

    // Sensor init
    tempSensor.begin();
    Serial2.begin(9600, SERIAL_8N1, PZEM_RX_PIN, PZEM_TX_PIN);

    // Koneksi WiFi & config awal
    connectWiFi();
    fetchConfigFromServer();

    // Paksa cek pestisida langsung saat booting
    lastPestisidaTime = millis() - INTERVAL_PESTISIDA;

    Serial.println("[OK] Setup selesai. Board: ESP32 WROOM-32 (38 pin)");
}

// ============================================================
//  LOOP UTAMA (non-blocking, semua pakai millis)
// ============================================================

void loop() {
    if (WiFi.status() != WL_CONNECTED) connectWiFi();

    unsigned long now = millis();

    // Kirim data sensor setiap 60 detik
    if (now - lastSendTime >= SEND_INTERVAL) {
        lastSendTime = now;
        sendSensorData();
    }

    // Update konfigurasi dari server setiap 5 menit
    if (now - lastConfigTime >= CONFIG_INTERVAL) {
        lastConfigTime = now;
        fetchConfigFromServer();
    }

    // Heartbeat setiap 30 detik
    if (now - lastHeartbeatTime >= HEARTBEAT_INTERVAL) {
        lastHeartbeatTime = now;
        sendHeartbeat();
    }

    // Poll command sirkulasi setiap 5 detik
    if (now - lastCmdPollTime >= CMD_POLL_INTERVAL) {
        lastCmdPollTime = now;
        pollCirculationCommand();
    }

    // Auto-matikan nutrisi setelah durasi selesai
    if (nutrisiOn && (now - nutrisiStart >= NUTRISI_AUTO_DURATION)) {
        matikanNutrisi();
    }

    // Cek antrean dosis pestisida setiap 10 detik
    if (now - lastPestisidaTime >= INTERVAL_PESTISIDA) {
        lastPestisidaTime = now;
        cekAntreanPestisida();
    }

    delay(100);
}

// ============================================================
//  FUNGSI SENSOR
// ============================================================

DataListrik bacaListrik() {
    DataListrik data;
    data.valid = false;
    float v  = pzem.voltage();
    float c  = pzem.current();
    float p  = pzem.power();
    float e  = pzem.energy();
    float f  = pzem.frequency();
    float pf = pzem.pf();

    if (isnan(v) || isnan(c) || isnan(p)) {
        Serial.println("[PZEM] Gagal baca! Periksa Power AC & Wiring.");
        return data;
    }
    data.voltage     = v;
    data.current     = c;
    data.power       = p;
    data.energy      = isnan(e)  ? 0 : e;
    data.frequency   = isnan(f)  ? 0 : f;
    data.powerFactor = isnan(pf) ? 0 : pf;
    data.valid       = true;
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
    float slope   = 3.0 / (2.23 - 2.51);
    return constrain(7.0 + slope * (voltage - 2.51), 0.0, 14.0);
}

float bacaTDS(float suhu) {
    if (suhu < -50) suhu = 25.0;
    long sum = 0;
    for (int i = 0; i < 30; i++) { sum += analogRead(TDS_PIN); delay(5); }
    float voltage  = (sum / 30.0) * 3.3 / 4095.0;
    float compVol  = voltage / (1.0 + 0.02 * (suhu - 25.0));
    return (133.42 * pow(compVol, 3) - 255.86 * pow(compVol, 2) + 857.39 * compVol) * 0.5;
}

float bacaJarak() {
    digitalWrite(TRIG_PIN, LOW);  delayMicroseconds(2);
    digitalWrite(TRIG_PIN, HIGH); delayMicroseconds(10);
    digitalWrite(TRIG_PIN, LOW);
    long durasi = pulseIn(ECHO_PIN, HIGH, 30000);
    return (durasi == 0) ? -1.0 : (durasi * 0.0343) / 2.0;
}

// ============================================================
//  KONTROL RELAY — NUTRISI
// ============================================================

void nyalakanNutrisi() {
    digitalWrite(RELAY_PERI_NUTRISI, LOW);
    nutrisiOn           = true;
    nutrisiStart        = millis();
    lastNutrisiCmdSent  = millis();
    Serial.println("[NUTRISI] Pompa peristaltik NUTRISI ON");
}

void matikanNutrisi() {
    digitalWrite(RELAY_PERI_NUTRISI, HIGH);
    nutrisiOn = false;
    Serial.println("[NUTRISI] Pompa peristaltik NUTRISI OFF");
}

// ============================================================
//  KONTROL RELAY — PESTISIDA (blocking-safe karena delay pendek)
// ============================================================

void jalankanPompaPestisida(float dosisML) {
    unsigned long durasiMs = (unsigned long)((dosisML / mlPerdetik) * 1000);
    Serial.printf("[PESTISIDA] Eksekusi: %.2f mL | Durasi: %lu ms\n", dosisML, durasiMs);

    pestisidaOn = true;
    digitalWrite(RELAY_PERI_PESTISIDA, HIGH);  // aktif HIGH (sesuaikan jika relay Anda active LOW)
    delay(durasiMs);
    digitalWrite(RELAY_PERI_PESTISIDA, LOW);
    pestisidaOn = false;

    Serial.println("[PESTISIDA] Dosis selesai disalurkan.");
}

// ============================================================
//  KONTROL RELAY — SIRKULASI
// ============================================================

void nyalakanSirkulasi() { digitalWrite(RELAY_CIRC, LOW);  circOn = true; }
void matikanSirkulasi()  { digitalWrite(RELAY_CIRC, HIGH); circOn = false; }

// ============================================================
//  KIRIM DATA SENSOR
// ============================================================

void sendSensorData() {
    float s    = bacaSuhu();
    float ph   = bacaPH();
    float tds  = bacaTDS(s);
    float jr   = bacaJarak();
    DataListrik list = bacaListrik();

    // Auto nyalakan nutrisi jika PPM terlalu rendah
    if (tds > 0 && tds < ppmMin && !nutrisiOn &&
        (millis() - lastNutrisiCmdSent >= NUTRISI_CMD_COOLDOWN)) {
        nyalakanNutrisi();
    }

    if (WiFi.status() != WL_CONNECTED) return;

    HTTPClient http;
    http.begin(String(SERVER_URL) + "/api/v1/sensor-data");
    http.addHeader("Content-Type", "application/json");
    http.addHeader("X-API-Key", API_KEY);

    DynamicJsonDocument doc(1024);
    doc["device_id"]     = DEVICE_ID;
    if (s != -127.0) doc["suhu"] = roundf(s * 100) / 100.0;
    doc["ph"]            = roundf(ph  * 100) / 100.0;
    doc["ppm"]           = roundf(tds * 100) / 100.0;
    if (jr > 0) doc["water_level"] = roundf(jr * 100) / 100.0;

    if (list.valid) {
        doc["voltage"]   = list.voltage;
        doc["current"]   = list.current;
        doc["power"]     = list.power;
        doc["energy"]    = list.energy;
    }
    doc["pump_circ_on"]      = circOn;
    doc["pump_peri_on"]      = nutrisiOn;
    doc["pump_pestisida_on"] = pestisidaOn;  // ← BARU

    String payload;
    serializeJson(doc, payload);
    int httpCode = http.POST(payload);
    Serial.printf("[HTTP] Data Sent, Code: %d\n", httpCode);
    http.end();
}

// ============================================================
//  CEK ANTREAN DOSIS PESTISIDA  ← BARU
// ============================================================

void cekAntreanPestisida() {
    if (WiFi.status() != WL_CONNECTED) return;
    if (pestisidaOn) return;  // hindari overlap

    Serial.println("[PESTISIDA] Cek antrean dosis...");

    HTTPClient http;
    http.setTimeout(10000);
    http.begin(String(SERVER_URL) + "/api/v1/pestisida");
    http.addHeader("X-API-Key", API_KEY);
    http.addHeader("Content-Type", "application/json");

    int httpCode = http.POST("");

    if (httpCode == 200) {
        String response = http.getString();
        Serial.println("[PESTISIDA] Response: " + response);

        StaticJsonDocument<256> doc;
        DeserializationError err = deserializeJson(doc, response);

        if (!err) {
            float dosisML = doc["dosis"] | 0.0;
            if (dosisML > 0) {
                jalankanPompaPestisida(dosisML);
            } else {
                Serial.println("[PESTISIDA] Tidak ada antrean dosis (0 mL).");
            }
        } else {
            Serial.print("[PESTISIDA] JSON Error: ");
            Serial.println(err.f_str());
        }
    } else {
        Serial.printf("[PESTISIDA] Gagal! Code: %d\n", httpCode);
    }
    http.end();
}

// ============================================================
//  KONEKSI WIFI
// ============================================================

void connectWiFi() {
    Serial.print("[WiFi] Menghubungkan");
    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
    int t = 0;
    while (WiFi.status() != WL_CONNECTED && t < 20) {
        delay(500);
        Serial.print(".");
        t++;
    }
    if (WiFi.status() == WL_CONNECTED) {
        digitalWrite(LED_PIN, HIGH);
        Serial.println("\n[WiFi] Terhubung! IP: " + WiFi.localIP().toString());
    } else {
        Serial.println("\n[WiFi] Gagal terhubung.");
    }
}

// ============================================================
//  POLL COMMAND SIRKULASI
// ============================================================

void pollCirculationCommand() {
    if (WiFi.status() != WL_CONNECTED) return;

    HTTPClient http;
    http.begin(String(SERVER_URL) + "/api/v1/command/" + PUMP_CIRC_ID);
    http.addHeader("X-API-Key", API_KEY);

    if (http.GET() == 200) {
        StaticJsonDocument<256> doc;
        deserializeJson(doc, http.getString());
        if (doc["success"]) {
            const char* cmd = doc["command"] | "";
            if      (strcmp(cmd, "circulation_on")  == 0) nyalakanSirkulasi();
            else if (strcmp(cmd, "circulation_off") == 0) matikanSirkulasi();
        }
    }
    http.end();
}

// ============================================================
//  AMBIL KONFIGURASI DARI SERVER
// ============================================================

void fetchConfigFromServer() {
    if (WiFi.status() != WL_CONNECTED) return;

    HTTPClient http;
    http.begin(String(SERVER_URL) + "/api/v1/configs");
    http.addHeader("X-API-Key", API_KEY);

    if (http.GET() == 200) {
        StaticJsonDocument<1024> doc;
        deserializeJson(doc, http.getString());
        JsonObject c = doc["configs"];

        if (c.containsKey("ph"))             { phMin   = c["ph"]["min_optimal"];            phMax   = c["ph"]["max_optimal"]; }
        if (c.containsKey("ppm"))            { ppmMin  = c["ppm"]["min_optimal"];           ppmMax  = c["ppm"]["max_optimal"]; }
        if (c.containsKey("suhu"))           { suhuMin = c["suhu"]["min_optimal"];          suhuMax = c["suhu"]["max_optimal"]; }
        if (c.containsKey("ketinggian_air")) { jarakMati = c["ketinggian_air"]["min_optimal"]; jarakNyala = c["ketinggian_air"]["max_optimal"]; }

        Serial.printf("[Config] pH:%.1f-%.1f | PPM:%.0f-%.0f | Suhu:%.1f-%.1f | Jarak:%.1f-%.1f\n",
            phMin, phMax, ppmMin, ppmMax, suhuMin, suhuMax, jarakMati, jarakNyala);
    }
    http.end();
}

// ============================================================
//  HEARTBEAT (3 device ID)
// ============================================================

void sendHeartbeat() {
    if (WiFi.status() != WL_CONNECTED) return;

    const char* ids[] = { DEVICE_ID, PUMP_CIRC_ID, PUMP_PERI_ID };
    for (int i = 0; i < 3; i++) {
        HTTPClient http;
        http.begin(String(SERVER_URL) + "/api/v1/heartbeat");
        http.addHeader("Content-Type", "application/json");
        http.addHeader("X-API-Key", API_KEY);
        http.POST("{\"device_id\":\"" + String(ids[i]) + "\"}");
        http.end();
    }
}