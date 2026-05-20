// // CODE PROJECT GABUNGAN
// /*
//  * ============================================================
//  * Smart Pakcoy Hidroponik — ESP32 WROOM-32 Firmware v3.1.0
//  * ============================================================
//  * Perubahan dari v3.0.0:
//  *   - Integrasi polling status pompa hama (pump_status)
//  *   - RELAY_PUMP_HAMA aktif jika pump_status == true
//  *   - Non-blocking menggunakan millis() (tanpa delay blocking)
//  *   - URL_PUMP_STATUS: GET /api/v1/pump-status
//  *   - Pompa menyala 10 detik, cooldown 5 detik setelahnya
//  * ============================================================
//  */

// #include <WiFi.h>
// #include <HTTPClient.h>
// #include <ArduinoJson.h>
// #include <OneWire.h>
// #include <DallasTemperature.h>
// #include <PZEM004Tv30.h>

// // ============================================================
// //  STRUCT
// // ============================================================

// struct DataListrik {
//     float voltage;
//     float current;
//     float power;
//     float energy;
//     float frequency;
//     float powerFactor;
//     bool  valid;
// };

// DataListrik bacaListrik();

// // ============================================================
// //  KONFIGURASI JARINGAN & SERVER
// // ============================================================

// const char* WIFI_SSID     = "YAN PW NYA APA";
// const char* WIFI_PASSWORD = "password";
// const char* SERVER_URL    = "http://10.217.227.138:8000";
// const char* API_KEY       = "hidra-core-secret-key-2026";
// const char* DEVICE_ID     = "ESP32-SENSOR-001";
// const char* PUMP_CIRC_ID  = "ESP32-PUMP-SIRKULASI";
// const char* PUMP_PERI_ID  = "ESP32-PUMP-PERISTALTIK";

// // ============================================================
// //  PIN MAPPING
// // ============================================================

// #define ONE_WIRE_BUS          5
// #define PH_PIN               34
// #define TDS_PIN              35
// #define TRIG_PIN             13
// #define ECHO_PIN             12
// #define RELAY_CIRC           27
// #define RELAY_PUMP_HAMA      26   // pompa pestisida/hama (active HIGH)
// #define RELAY_PERI_NUTRISI   23
// #define RELAY_PERI_PESTISIDA 22 //22 aslinya
// #define LED_PIN               2
// #define PZEM_RX_PIN          16
// #define PZEM_TX_PIN          17

// // ============================================================
// //  KALIBRASI
// // ============================================================

// float mlPerdetik = 1.0;

// // ============================================================
// //  BATAS OPTIMAL (dari server)
// // ============================================================

// float phMin   = 5.5,  phMax   = 6.5;
// float suhuMin = 22.0, suhuMax = 30.0;
// float ppmMin  = 500.0, ppmMax = 1200.0;
// float jarakNyala = 30.0, jarakMati = 10.0;

// // ============================================================
// //  STATUS RUNTIME
// // ============================================================

// bool circOn       = false;
// bool nutrisiOn    = false;
// bool pestisidaOn  = false;

// // --- Status pompa hama (non-blocking) ---
// bool hamaOn             = false;  // relay sedang aktif
// bool hamaCooldownActive = false;  // sedang dalam jeda setelah semprot
// unsigned long hamaStart        = 0;  // waktu relay dinyalakan
// unsigned long hamaCooldownStart = 0; // waktu cooldown mulai

// const unsigned long HAMA_ON_DURATION  = 10000UL; // 10 detik menyala
// const unsigned long HAMA_COOLDOWN     = 5000UL;  // 5 detik cooldown

// // ============================================================
// //  TIMER MILLIS
// // ============================================================

// unsigned long lastSendTime        = 0;
// unsigned long lastConfigTime      = 0;
// unsigned long lastHeartbeatTime   = 0;
// unsigned long lastCmdPollTime     = 0;
// unsigned long nutrisiStart        = 0;
// unsigned long lastNutrisiCmdSent  = 0;
// unsigned long lastPestisidaTime   = 0;
// unsigned long lastHamaPollTime    = 0;  // ← BARU: timer poll hama

// // ============================================================
// //  INTERVAL
// // ============================================================

// const unsigned long SEND_INTERVAL         = 60000UL;
// const unsigned long CONFIG_INTERVAL       = 300000UL;
// const unsigned long HEARTBEAT_INTERVAL    = 30000UL;
// const unsigned long CMD_POLL_INTERVAL     = 5000UL;
// const unsigned long NUTRISI_AUTO_DURATION = 60000UL;
// const unsigned long NUTRISI_CMD_COOLDOWN  = 65000UL;
// const unsigned long INTERVAL_PESTISIDA    = 10000UL;
// const unsigned long INTERVAL_HAMA_POLL   = 5000UL;  // ← BARU: poll hama setiap 5 detik

// // ============================================================
// //  LIBRARY
// // ============================================================

// OneWire oneWire(ONE_WIRE_BUS);
// DallasTemperature tempSensor(&oneWire);
// PZEM004Tv30 pzem(Serial2, PZEM_RX_PIN, PZEM_TX_PIN);

// // ============================================================
// //  SETUP
// // ============================================================

// void setup() {
//     Serial.begin(115200);

//     pinMode(TRIG_PIN,             OUTPUT);
//     pinMode(ECHO_PIN,             INPUT);
//     pinMode(RELAY_CIRC,           OUTPUT);
//     pinMode(RELAY_PUMP_HAMA,      OUTPUT);
//     pinMode(RELAY_PERI_NUTRISI,   OUTPUT);
//     pinMode(RELAY_PERI_PESTISIDA, OUTPUT);
//     pinMode(LED_PIN,              OUTPUT);

//     // Relay active LOW — mulai MATI
//     // RELAY_PUMP_HAMA dikontrol active HIGH, mulai LOW (mati)
//     digitalWrite(RELAY_CIRC,           HIGH);
//     digitalWrite(RELAY_PUMP_HAMA,      LOW);   // mati saat boot
//     digitalWrite(RELAY_PERI_NUTRISI,   HIGH);
//     digitalWrite(RELAY_PERI_PESTISIDA, LOW);

//     analogReadResolution(12);
//     analogSetAttenuation(ADC_11db);

//     tempSensor.begin();
//     Serial2.begin(9600, SERIAL_8N1, PZEM_RX_PIN, PZEM_TX_PIN);

//     connectWiFi();
//     fetchConfigFromServer();

//     lastPestisidaTime = millis() - INTERVAL_PESTISIDA;
//     lastHamaPollTime  = millis() - INTERVAL_HAMA_POLL;

//     Serial.println("[OK] Setup selesai. Firmware v3.1.0");
// }

// // ============================================================
// //  LOOP UTAMA
// // ============================================================

// void loop() {
//     if (WiFi.status() != WL_CONNECTED) connectWiFi();

//     unsigned long now = millis();

//     // Kirim data sensor setiap 60 detik
//     if (now - lastSendTime >= SEND_INTERVAL) {
//         lastSendTime = now;
//         sendSensorData();
//     }

//     // Update konfigurasi setiap 5 menit
//     if (now - lastConfigTime >= CONFIG_INTERVAL) {
//         lastConfigTime = now;
//         fetchConfigFromServer();
//     }

//     // Heartbeat setiap 30 detik
//     if (now - lastHeartbeatTime >= HEARTBEAT_INTERVAL) {
//         lastHeartbeatTime = now;
//         sendHeartbeat();
//     }

//     // Poll command sirkulasi setiap 5 detik
//     if (now - lastCmdPollTime >= CMD_POLL_INTERVAL) {
//         lastCmdPollTime = now;
//         pollCirculationCommand();
//     }

//     // Auto-matikan nutrisi setelah durasi selesai
//     if (nutrisiOn && (now - nutrisiStart >= NUTRISI_AUTO_DURATION)) {
//         matikanNutrisi();
//     }

//     // Cek antrean dosis pestisida setiap 10 detik
//     if (now - lastPestisidaTime >= INTERVAL_PESTISIDA) {
//         lastPestisidaTime = now;
//         cekAntreanPestisida();
//     }

//     // ── POMPA HAMA ──────────────────────────────────────────
//     // 1) Matikan relay setelah 10 detik menyala
//     if (hamaOn && (now - hamaStart >= HAMA_ON_DURATION)) {
//         matikanPompaHama();
//     }

//     // 2) Hitung mundur cooldown setelah mati
//     if (hamaCooldownActive && (now - hamaCooldownStart >= HAMA_COOLDOWN)) {
//         hamaCooldownActive = false;
//         Serial.println("[HAMA] Cooldown selesai, siap polling lagi.");
//     }

//     // 3) Poll status hama setiap 5 detik (jika tidak sedang aktif/cooldown)
//     if (!hamaOn && !hamaCooldownActive &&
//         (now - lastHamaPollTime >= INTERVAL_HAMA_POLL)) {
//         lastHamaPollTime = now;
//         pollHamaStatus();
//     }
//     // ─────────────────────────────────────────────────────────

//     delay(100);
// }

// // ============================================================
// //  KONTROL POMPA HAMA
// // ============================================================

// void nyalakanPompaHama() {
//     digitalWrite(RELAY_PUMP_HAMA, HIGH);
//     hamaOn    = true;
//     hamaStart = millis();
//     Serial.println("[HAMA] Pompa HAMA ON (10 detik)");
// }

// void matikanPompaHama() {
//     digitalWrite(RELAY_PUMP_HAMA, LOW);
//     hamaOn             = false;
//     hamaCooldownActive = true;
//     hamaCooldownStart  = millis();
//     Serial.println("[HAMA] Pompa HAMA OFF — cooldown 5 detik.");
// }

// // ============================================================
// //  POLL STATUS HAMA DARI SERVER  ← BARU
// // ============================================================

// DataListrik bacaListrik() {
//     DataListrik data;
//     data.valid = false;
//     float v  = pzem.voltage();
//     float c  = pzem.current();
//     float p  = pzem.power();
//     float e  = pzem.energy();
//     float f  = pzem.frequency();
//     float pf = pzem.pf();
//     if (isnan(v) || isnan(c) || isnan(p)) {
//         Serial.println("[PZEM] Gagal baca! Periksa Power AC & Wiring.");
//         return data;
//     }
//     data.voltage     = v;
//     data.current     = c;
//     data.power       = p;
//     data.energy      = isnan(e)  ? 0 : e;
//     data.frequency   = isnan(f)  ? 0 : f;
//     data.powerFactor = isnan(pf) ? 0 : pf;
//     data.valid       = true;
//     return data;
// }

// float bacaSuhu() {
//     tempSensor.requestTemperatures();
//     float suhu = tempSensor.getTempCByIndex(0);
//     return (suhu == DEVICE_DISCONNECTED_C) ? -127.0 : suhu;
// }

// float bacaPH() {
//     long sum = 0;
//     for (int i = 0; i < 30; i++) { sum += analogRead(PH_PIN); delay(5); }
//     float voltage = (sum / 30.0) * 3.3 / 4095.0;
//     float slope   = 3.0 / (2.23 - 2.51);
//     return constrain(7.0 + slope * (voltage - 2.51), 0.0, 14.0);
// }

// float bacaTDS(float suhu) {
//     if (suhu < -50) suhu = 25.0;
//     long sum = 0;
//     for (int i = 0; i < 30; i++) { sum += analogRead(TDS_PIN); delay(5); }
//     float voltage  = (sum / 30.0) * 3.3 / 4095.0;
//     float compVol  = voltage / (1.0 + 0.02 * (suhu - 25.0));
//     return (133.42 * pow(compVol, 3) - 255.86 * pow(compVol, 2) + 857.39 * compVol) * 0.5;
// }

// float bacaJarak() {
//     digitalWrite(TRIG_PIN, LOW);  delayMicroseconds(2);
//     digitalWrite(TRIG_PIN, HIGH); delayMicroseconds(10);
//     digitalWrite(TRIG_PIN, LOW);
//     long durasi = pulseIn(ECHO_PIN, HIGH, 30000);
//     return (durasi == 0) ? -1.0 : (durasi * 0.0343) / 2.0;
// }

// // ============================================================
// //  KONTROL RELAY — NUTRISI
// // ============================================================

// void nyalakanNutrisi() {
//     digitalWrite(RELAY_PERI_NUTRISI, LOW);
//     nutrisiOn          = true;
//     nutrisiStart       = millis();
//     lastNutrisiCmdSent = millis();
//     Serial.println("[NUTRISI] Pompa peristaltik NUTRISI ON");
// }

// void matikanNutrisi() {
//     digitalWrite(RELAY_PERI_NUTRISI, HIGH);
//     nutrisiOn = false;
//     Serial.println("[NUTRISI] Pompa peristaltik NUTRISI OFF");
// }

// // ============================================================
// //  KONTROL RELAY — PESTISIDA
// // ============================================================

// void jalankanPompaPestisida(float dosisML) {
//     unsigned long durasiMs = (unsigned long)((dosisML / mlPerdetik) * 1000);
//     Serial.printf("[PESTISIDA] Eksekusi: %.2f mL | Durasi: %lu ms\n", dosisML, durasiMs);
//     pestisidaOn = true;
//     digitalWrite(RELAY_PERI_PESTISIDA, HIGH);
//     delay(durasiMs);
//     digitalWrite(RELAY_PERI_PESTISIDA, LOW);
//     pestisidaOn = false;
//     Serial.println("[PESTISIDA] Dosis selesai.");
// }

// // ============================================================
// //  KONTROL RELAY — SIRKULASI
// // ============================================================

// void nyalakanSirkulasi() { digitalWrite(RELAY_CIRC, LOW);  circOn = true; }
// void matikanSirkulasi()  { digitalWrite(RELAY_CIRC, HIGH); circOn = false; }

// // ============================================================
// //  KIRIM DATA SENSOR
// // ============================================================

// void sendSensorData() {
//     float s   = bacaSuhu();
//     float ph  = bacaPH();
//     float tds = bacaTDS(s);
//     float jr  = bacaJarak();
//     DataListrik list = bacaListrik();

//     if (tds > 0 && tds < ppmMin && !nutrisiOn &&
//         (millis() - lastNutrisiCmdSent >= NUTRISI_CMD_COOLDOWN)) {
//         nyalakanNutrisi();
//     }

//     if (WiFi.status() != WL_CONNECTED) return;

//     HTTPClient http;
//     http.begin(String(SERVER_URL) + "/api/v1/sensor-data");
//     http.addHeader("Content-Type", "application/json");
//     http.addHeader("X-API-Key", API_KEY);

//     DynamicJsonDocument doc(1024);
//     doc["device_id"] = DEVICE_ID;
//     if (s != -127.0) doc["suhu"] = roundf(s * 100) / 100.0;
//     doc["ph"]             = roundf(ph  * 100) / 100.0;
//     doc["ppm"]            = roundf(tds * 100) / 100.0;
//     if (jr > 0) doc["water_level"] = roundf(jr * 100) / 100.0;
//     if (list.valid) {
//         doc["voltage"]   = list.voltage;
//         doc["current"]   = list.current;
//         doc["power"]     = list.power;
//         doc["energy"]    = list.energy;
//     }
//     doc["pump_circ_on"]      = circOn;
//     doc["pump_peri_on"]      = nutrisiOn;
//     doc["pump_pestisida_on"] = pestisidaOn;
//     doc["pump_hama_on"]      = hamaOn;  // ← status hama ikut dikirim

//     String payload;
//     serializeJson(doc, payload);
//     int httpCode = http.POST(payload);
//     Serial.printf("[HTTP] Data Sent, Code: %d\n", httpCode);
//     http.end();
// }

// // ============================================================
// //  CEK ANTREAN DOSIS PESTISIDA
// // ============================================================

// void cekAntreanPestisida() {
//     if (WiFi.status() != WL_CONNECTED) return;
//     if (pestisidaOn) return;

//     HTTPClient http;
//     http.setTimeout(10000);
//     http.begin(String(SERVER_URL) + "/api/v1/pestisida");
//     http.addHeader("X-API-Key", API_KEY);
//     http.addHeader("Content-Type", "application/json");

//     int httpCode = http.POST("");
//     if (httpCode == 200) {
//         String response = http.getString();
//         StaticJsonDocument<256> doc;
//         DeserializationError err = deserializeJson(doc, response);
//         if (!err) {
//             float dosisML = doc["dosis"] | 0.0;
//             if (dosisML > 0) jalankanPompaPestisida(dosisML);
//             else Serial.println("[PESTISIDA] Tidak ada antrean.");
//         }
//     }
//     http.end();
// }

// // ============================================================
// //  WIFI
// // ============================================================

// void connectWiFi() {
//     Serial.print("[WiFi] Menghubungkan");
//     WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
//     int t = 0;
//     while (WiFi.status() != WL_CONNECTED && t < 20) {
//         delay(500); Serial.print("."); t++;
//     }
//     if (WiFi.status() == WL_CONNECTED) {
//         digitalWrite(LED_PIN, HIGH);
//         Serial.println("\n[WiFi] Terhubung! IP: " + WiFi.localIP().toString());
//     } else {
//         Serial.println("\n[WiFi] Gagal terhubung.");
//     }
// }

// // ============================================================
// //  POLL COMMAND SIRKULASI
// // ============================================================

// void pollCirculationCommand() {
//     if (WiFi.status() != WL_CONNECTED) return;
//     HTTPClient http;
//     http.begin(String(SERVER_URL) + "/api/v1/command/" + PUMP_CIRC_ID);
//     http.addHeader("X-API-Key", API_KEY);
//     if (http.GET() == 200) {
//         StaticJsonDocument<256> doc;
//         deserializeJson(doc, http.getString());
//         if (doc["success"]) {
//             const char* cmd = doc["command"] | "";
//             if      (strcmp(cmd, "circulation_on")  == 0) nyalakanSirkulasi();
//             else if (strcmp(cmd, "circulation_off") == 0) matikanSirkulasi();
//         }
//     }
//     http.end();
// }

// // ============================================================
// //  AMBIL KONFIGURASI
// // ============================================================

// void fetchConfigFromServer() {
//     if (WiFi.status() != WL_CONNECTED) return;
//     HTTPClient http;
//     http.begin(String(SERVER_URL) + "/api/v1/configs");
//     http.addHeader("X-API-Key", API_KEY);
//     if (http.GET() == 200) {
//         StaticJsonDocument<1024> doc;
//         deserializeJson(doc, http.getString());
//         JsonObject c = doc["configs"];
//         if (c.containsKey("ph"))             { phMin   = c["ph"]["min_optimal"];               phMax   = c["ph"]["max_optimal"]; }
//         if (c.containsKey("ppm"))            { ppmMin  = c["ppm"]["min_optimal"];              ppmMax  = c["ppm"]["max_optimal"]; }
//         if (c.containsKey("suhu"))           { suhuMin = c["suhu"]["min_optimal"];             suhuMax = c["suhu"]["max_optimal"]; }
//         if (c.containsKey("ketinggian_air")) { jarakMati = c["ketinggian_air"]["min_optimal"]; jarakNyala = c["ketinggian_air"]["max_optimal"]; }
//     }
//     http.end();
// }

// // ============================================================
// //  HEARTBEAT
// // ============================================================

// void sendHeartbeat() {
//     if (WiFi.status() != WL_CONNECTED) return;
//     const char* ids[] = { DEVICE_ID, PUMP_CIRC_ID, PUMP_PERI_ID };
//     for (int i = 0; i < 3; i++) {
//         HTTPClient http;
//         http.begin(String(SERVER_URL) + "/api/v1/heartbeat");
//         http.addHeader("Content-Type", "application/json");
//         http.addHeader("X-API-Key", API_KEY);
//         http.POST("{\"device_id\":\"" + String(ids[i]) + "\"}");
//         http.end();
//     }
// }

// CODE PROJECT GABUNGAN
/*
 * ============================================================
 * Smart Pakcoy Hidroponik — ESP32 WROOM-32 Firmware v3.2.0
 * ============================================================
 * Perubahan dari v3.1.0:
 *   - Penambahan 2x Solenoid Valve (sisi KIRI & KANAN)
 *   - RELAY_SOLENOID_KIRI  → GPIO 25 (Active HIGH)
 *   - RELAY_SOLENOID_KANAN → GPIO 33 (Active HIGH)
 *   - Solenoid terbuka berdasarkan field side_left / side_right
 *     dari response GET /api/v1/pump-status
 *   - Solenoid terbuka selama anomaly terdeteksi (ikut polling)
 *   - Solenoid HANYA aktif saat is_pestisida_pump == true
 *     DAN label_hama == "hama"
 *   - Status solenoid dikirim ke server via sendSensorData()
 * ============================================================
 */


//code yang digunakan saat ini dan berhasil

#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

#include <OneWire.h>
#include <DallasTemperature.h>
#include <PZEM004Tv30.h>
#include <Wire.h>
#include <RTClib.h>

RTC_PCF8563 rtc;
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

DataListrik bacaListrik();

// ============================================================
//  KONFIGURASI JARINGAN & SERVER
// ============================================================

const char* WIFI_SSID     = "YAN PW NYA APA";
const char* WIFI_PASSWORD = "password";
const char* SERVER_URL    = "http://10.42.252.138:8000";
const char* API_KEY       = "hidra-core-secret-key-2026";
const char* DEVICE_ID     = "ESP32-SENSOR-001";
const char* PUMP_CIRC_ID  = "ESP32-PUMP-SIRKULASI";
const char* PUMP_PERI_ID  = "ESP32-PUMP-PERISTALTIK";

// ============================================================
//  PIN MAPPING
// ============================================================

#define ONE_WIRE_BUS          5
#define PH_PIN               34
#define TDS_PIN              35
#define TRIG_PIN             13
#define ECHO_PIN             12
#define RELAY_CIRC           27
#define RELAY_PUMP_HAMA      26   // pompa pestisida/hama (active HIGH)
#define RELAY_PERI_NUTRISI   23
#define RELAY_PERI_PESTISIDA 25
#define LED_PIN               2
#define PZEM_RX_PIN          16
#define PZEM_TX_PIN          17

// ── SOLENOID VALVE ──────────────────────────────────────────
#define RELAY_SOLENOID_KIRI  27   // solenoid sisi KIRI  (active HIGH)
#define RELAY_SOLENOID_KANAN 33   // solenoid sisi KANAN (active HIGH)
// ─────────────────────────────────────────────────────────────

// ============================================================
//  KALIBRASI
// ============================================================

float mlPerdetik = 1.0;

// ============================================================
//  BATAS OPTIMAL (dari server)
// ============================================================

float phMin   = 5.5,  phMax   = 6.5;
float suhuMin = 22.0, suhuMax = 30.0;
float ppmMin  = 500.0, ppmMax = 1200.0;
float jarakNyala = 30.0, jarakMati = 10.0;

// ============================================================
//  STATUS RUNTIME
// ============================================================

bool circOn       = false;
bool nutrisiOn    = false;
bool pestisidaOn  = false;

// --- Status pompa hama (non-blocking) ---
bool hamaOn             = false;
bool hamaCooldownActive = false;
unsigned long hamaStart         = 0;
unsigned long hamaCooldownStart = 0;

const unsigned long HAMA_ON_DURATION = 10000UL;
const unsigned long HAMA_COOLDOWN    = 5000UL;

// ── Status Solenoid Valve ────────────────────────────────────
// Solenoid tidak memiliki timer tetap — terbuka/tutup mengikuti
// hasil polling. Tidak perlu cooldown terpisah karena dikontrol
// langsung dari nilai server setiap INTERVAL_HAMA_POLL.
bool solenoidKiriOn  = false; // ganti jadi kanan
bool solenoidKananOn = false; //ganti jadi kiri
// ─────────────────────────────────────────────────────────────

// ============================================================
//  TIMER MILLIS
// ============================================================

unsigned long lastSendTime        = 0;
unsigned long lastConfigTime      = 0;
unsigned long lastHeartbeatTime   = 0;
unsigned long lastCmdPollTime     = 0;
unsigned long nutrisiStart        = 0;
// unsigned long lastNutrisiCmdSent  = 0;
unsigned long lastPestisidaTime   = 0;
unsigned long lastHamaPollTime    = 0;

// ============================================================
//  INTERVAL
// ============================================================

const unsigned long SEND_INTERVAL         = 60000UL;
const unsigned long CONFIG_INTERVAL       = 300000UL;
const unsigned long HEARTBEAT_INTERVAL    = 30000UL;
const unsigned long CMD_POLL_INTERVAL     = 5000UL;
// const unsigned long NUTRISI_AUTO_DURATION = 60000UL;
// const unsigned long NUTRISI_CMD_COOLDOWN  = 65000UL;
unsigned long nutrisiDurasiMs  = 0;   // durasi aktif dari server (ms)
int           nutrisiDoseId    = -1;  // ID dosis untuk konfirmasi "done"
const unsigned long INTERVAL_NUTRISI_POLL = 10000UL;  // poll tiap 10 detik
unsigned long lastNutrisiPollTime = 0;

const unsigned long INTERVAL_PESTISIDA    = 10000UL;
const unsigned long INTERVAL_SPRAY_POLL  = 5000UL;   // poll spray-command setiap 5 detik
unsigned long lastSprayPollTime          = 0;

// ============================================================
//  LIBRARY
// ============================================================

OneWire oneWire(ONE_WIRE_BUS);
DallasTemperature tempSensor(&oneWire);
PZEM004Tv30 pzem(Serial2, PZEM_RX_PIN, PZEM_TX_PIN);

// ============================================================
//  SETUP
// ============================================================


String getRTCTimestamp() {
    if (rtc.lostPower()) {
        return "";  // Kembalikan kosong jika RTC belum siap
    }
    DateTime now = rtc.now();
    char buf[25];
    snprintf(buf, sizeof(buf),
        "%04d-%02d-%02dT%02d:%02d:%02d",
        now.year(), now.month(),  now.day(),
        now.hour(), now.minute(), now.second()
    );
    return String(buf);
}

void setup() {
    Serial.begin(115200);
    Wire.begin(21,22);

     if (!rtc.begin()) {
        Serial.println("[RTC] PCF8523 tidak ditemukan! Cek wiring SDA/SCL.");
    } else {
        if (rtc.lostPower()) {
            Serial.println("[RTC] Waktu belum diset — sinkronisasi ke waktu compile.");
            rtc.adjust(DateTime(F(__DATE__), F(__TIME__)));
        }
        rtc.start();
        DateTime now = rtc.now();
        Serial.printf("[RTC] Waktu: %04d-%02d-%02d %02d:%02d:%02d\n",
            now.year(), now.month(),  now.day(),
            now.hour(), now.minute(), now.second());
    }

    
    pinMode(TRIG_PIN,             OUTPUT);
    pinMode(ECHO_PIN,             INPUT);
    pinMode(RELAY_CIRC,           OUTPUT);
    pinMode(RELAY_PUMP_HAMA,      OUTPUT);
    pinMode(RELAY_PERI_NUTRISI,   OUTPUT);
    pinMode(RELAY_PERI_PESTISIDA, OUTPUT);
    pinMode(LED_PIN,              OUTPUT);

    // ── Solenoid Valve ──
    pinMode(RELAY_SOLENOID_KIRI,  OUTPUT); 
    pinMode(RELAY_SOLENOID_KANAN, OUTPUT); 
    // ───────────────────

    // Relay active LOW — mulai MATI
    digitalWrite(RELAY_CIRC,           HIGH);
    digitalWrite(RELAY_PUMP_HAMA,      LOW);
    digitalWrite(RELAY_PERI_NUTRISI,   HIGH);
    digitalWrite(RELAY_PERI_PESTISIDA, LOW);

    // Solenoid active HIGH — mulai TUTUP (LOW)
    digitalWrite(RELAY_SOLENOID_KIRI,  LOW);
    digitalWrite(RELAY_SOLENOID_KANAN, LOW);

    analogReadResolution(12);
    // analogSetAttenuation(ADC_11db);
    analogSetAttenuation(ADC_6db);

    tempSensor.begin();
    Serial2.begin(9600, SERIAL_8N1, PZEM_RX_PIN, PZEM_TX_PIN);

    connectWiFi();
    fetchConfigFromServer();

    lastPestisidaTime  = millis() - INTERVAL_PESTISIDA;
    lastSprayPollTime  = millis() - INTERVAL_SPRAY_POLL;

    Serial.println("[OK] Setup selesai. Firmware v3.2.0");
}

// ============================================================
//  LOOP UTAMA
// ============================================================

void loop() {
    if (WiFi.status() != WL_CONNECTED) connectWiFi();

    unsigned long now = millis();

    // Kirim data sensor setiap 60 detik
    if (now - lastSendTime >= SEND_INTERVAL) {
        lastSendTime = now;
        sendSensorData();
    }

    // Update konfigurasi setiap 5 menit
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
    // if (nutrisiOn && (now - nutrisiStart >= NUTRISI_AUTO_DURATION)) {
    //     matikanNutrisi();
    // }

    if (nutrisiOn && (millis() - nutrisiStart >= nutrisiDurasiMs)) {
        matikanNutrisiDanKonfirmasi();
    }

    
    if (!nutrisiOn && (millis() - lastNutrisiPollTime >= INTERVAL_NUTRISI_POLL)) {
        lastNutrisiPollTime = millis();
        pollNutrisiDosis();
    }

    // Cek antrean dosis pestisida setiap 10 detik
    if (now - lastPestisidaTime >= INTERVAL_PESTISIDA) {
        lastPestisidaTime = now;
        cekAntreanPestisida();
    }

    // ── POMPA HAMA ──────────────────────────────────────────
    // 1) Matikan relay setelah 10 detik menyala
    if (hamaOn && (now - hamaStart >= HAMA_ON_DURATION)) {
        matikanPompaHama();
    }

    // 2) Hitung mundur cooldown setelah mati
    if (hamaCooldownActive && (now - hamaCooldownStart >= HAMA_COOLDOWN)) {
        hamaCooldownActive = false;
        Serial.println("[HAMA] Cooldown selesai, siap polling lagi.");
    }

    // 3) Poll spray-command dari server setiap 5 detik
    //    (menangani mode otomatis & manual sekaligus)
    if (now - lastSprayPollTime >= INTERVAL_SPRAY_POLL) {
        lastSprayPollTime = now;
        pollSprayCommand();
    }
    // ─────────────────────────────────────────────────────────

    delay(100);
}

// ============================================================
//  KONTROL POMPA HAMA
// ============================================================

void nyalakanPompaHama() {
    digitalWrite(RELAY_PUMP_HAMA, HIGH);
    hamaOn    = true;
    hamaStart = millis();
    Serial.println("[HAMA] Pompa HAMA ON (10 detik)");
}

void matikanPompaHama() {
    digitalWrite(RELAY_PUMP_HAMA, LOW);
    hamaOn             = false;
    hamaCooldownActive = true;
    hamaCooldownStart  = millis();
    Serial.println("[HAMA] Pompa HAMA OFF — cooldown 5 detik.");
}

// ============================================================
//  KONTROL SOLENOID VALVE
// ============================================================

void nyalakanSolenoidKiri() {
    if (!solenoidKiriOn) {
        digitalWrite(RELAY_SOLENOID_KIRI, HIGH);
        solenoidKiriOn = true;
        Serial.println("[SOLENOID] Kiri TERBUKA");
    }
}

void matikanSolenoidKiri() {
    if (solenoidKiriOn) {
        digitalWrite(RELAY_SOLENOID_KIRI, LOW);
        solenoidKiriOn = false;
        Serial.println("[SOLENOID] Kiri TERTUTUP");
    }
}

void nyalakanSolenoidKanan() {
    if (!solenoidKananOn) {
        digitalWrite(RELAY_SOLENOID_KANAN, HIGH);
        solenoidKananOn = true;
        Serial.println("[SOLENOID] Kanan TERBUKA");
    }
}

void matikanSolenoidKanan() {
    if (solenoidKananOn) {
        digitalWrite(RELAY_SOLENOID_KANAN, LOW);
        solenoidKananOn = false;
        Serial.println("[SOLENOID] Kanan TERTUTUP");
    }
}

// ============================================================
//  POLL SPRAY COMMAND DARI SERVER  (v3.3.0)
// ============================================================
/*
 * GET /api/v1/spray-command
 *
 * Response JSON:
 * {
 *   "auto_mode":   true|false,
 *   "spray_kiri":  true|false,
 *   "spray_kanan": true|false,
 *   "pump_on":     true|false,
 *   "source":      "auto"|"manual"
 * }
 *
 * Logika otomatis vs manual sepenuhnya ditangani di server (Laravel).
 * ESP32 cukup mengikuti instruksi yang diterima.
 */
void pollSprayCommand() {
    if (WiFi.status() != WL_CONNECTED) return;

    HTTPClient http;
    http.setTimeout(5000);
    http.begin(String(SERVER_URL) + "/api/v1/spray-command");
    http.addHeader("X-API-Key", API_KEY);

    int httpCode = http.GET();
    if (httpCode == 200) {
        String response = http.getString();
        DynamicJsonDocument doc(256);
        DeserializationError err = deserializeJson(doc, response);

        if (!err) {
            bool sprayKiri  = doc["spray_kiri"]  | false;
            bool sprayKanan = doc["spray_kanan"] | false;
            bool pumpOn     = doc["pump_on"]     | false;
            const char* src = doc["source"]      | "auto";

            // -- Kontrol Pompa Hama --
            if (pumpOn && !hamaOn && !hamaCooldownActive) {
                nyalakanPompaHama();
            } else if (!pumpOn && hamaOn) {
                matikanPompaHama();
            }

            // -- Kontrol Solenoid --
            if (sprayKiri)  nyalakanSolenoidKiri();  else matikanSolenoidKiri();
            if (sprayKanan) nyalakanSolenoidKanan(); else matikanSolenoidKanan();

            Serial.printf(
                "[SPRAY] src=%s | Pompa=%s | Kiri=%s | Kanan=%s\n",
                src,
                pumpOn          ? "ON"   : "OFF",
                solenoidKiriOn  ? "BUKA" : "TUTUP",
                solenoidKananOn ? "BUKA" : "TUTUP"
            );

        } else {
            Serial.print("[SPRAY] JSON Error: ");
            Serial.println(err.f_str());
        }
    } else {
        Serial.printf("[SPRAY] Gagal GET spray-command. Code: %d - Safety OFF\n", httpCode);
        // Safety fallback: tutup semua
        matikanSolenoidKiri();
        matikanSolenoidKanan();
        if (hamaOn) matikanPompaHama();
    }
    http.end();
}

// ============================================================
//  SENSOR
// ============================================================
//  SENSOR
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

    for (int i = 0; i < 30; i++) {
        sum += analogRead(TDS_PIN);
        delay(5);
    }

    float adcAvg = sum / 30.0;

    float voltage = adcAvg * 3.3 / 4095.0;

    float compVol = voltage / (1.0 + 0.02 * (suhu - 25.0));

    float tds = (133.42 * pow(compVol, 3)
               - 255.86 * pow(compVol, 2)
               + 857.39 * compVol) * 0.5;

    Serial.print("ADC RAW: ");
    Serial.println(adcAvg);

    Serial.print("Voltage: ");
    Serial.println(voltage);

    Serial.print("TDS: ");
    Serial.println(tds);

    return tds;
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

void pollNutrisiDosis() {
    if (WiFi.status() != WL_CONNECTED) return;

    HTTPClient http;
    http.setTimeout(8000);
    http.begin(String(SERVER_URL) + "/api/v1/nutrisi-dose");
    http.addHeader("Content-Type", "application/json");
    http.addHeader("X-API-Key", API_KEY);

    int httpCode = http.POST("");
    if (httpCode == 200) {
        String response = http.getString();
        DynamicJsonDocument doc(512);
        DeserializationError err = deserializeJson(doc, response);

        if (!err) {
            bool hasDose = doc["has_dose"] | false;
            if (hasDose) {
                float durasiDetik = doc["durasi_detik"] | 0.0;
                int   doseId      = doc["dose_id"]      | -1;
                float deficit     = doc["ppm_deficit"]  | 0.0;

                if (durasiDetik > 0 && doseId > 0) {
                    nutrisiDurasiMs = (unsigned long)(durasiDetik * 1000);
                    nutrisiDoseId   = doseId;
                    Serial.printf("[NUTRISI] Dosis diterima: ID=%d | %.2f mL | %.2f detik | Defisit: %.1f ppm\n",
                                  doseId, (float)doc["dosis_ml"], durasiDetik, deficit);
                    nyalakanNutrisi();
                }
            } else {
                Serial.println("[NUTRISI] Tidak ada antrean dosis.");
            }
        }
    } else {
        Serial.printf("[NUTRISI] Gagal poll dosis. Code: %d\n", httpCode);
    }
    http.end();
}


void matikanNutrisiDanKonfirmasi() {
    // Inline — tidak memanggil matikanNutrisi() agar urutan deklarasi tidak masalah
    digitalWrite(RELAY_PERI_NUTRISI, HIGH);
    nutrisiOn = false;
    Serial.println("[NUTRISI] Pompa peristaltik NUTRISI OFF");

    // Konfirmasi ke server bahwa dosis selesai
    if (nutrisiDoseId > 0 && WiFi.status() == WL_CONNECTED) {
        HTTPClient http;
        http.setTimeout(5000);
        String url = String(SERVER_URL) + "/api/v1/nutrisi-dose/" + String(nutrisiDoseId) + "/done";
        http.begin(url);
        http.addHeader("Content-Type", "application/json");
        http.addHeader("X-API-Key", API_KEY);
        int code = http.POST("");
        Serial.printf("[NUTRISI] Konfirmasi done ID=%d | Code: %d\n", nutrisiDoseId, code);
        http.end();
        nutrisiDoseId   = -1;
        nutrisiDurasiMs = 0;
    }
}
void nyalakanNutrisi() {
    digitalWrite(RELAY_PERI_NUTRISI, LOW);
    nutrisiOn          = true;
    nutrisiStart       = millis();
    // lastNutrisiCmdSent = millis();
    Serial.println("[NUTRISI] Pompa peristaltik NUTRISI ON");
}

// void matikanNutrisi() {
//     digitalWrite(RELAY_PERI_NUTRISI, HIGH);
//     nutrisiOn = false;
//     Serial.println("[NUTRISI] Pompa peristaltik NUTRISI OFF");
// }

// ============================================================
//  KONTROL RELAY — PESTISIDA
// ============================================================

void jalankanPompaPestisida(float dosisML) {
    unsigned long durasiMs = (unsigned long)((dosisML / mlPerdetik) * 1500);
    Serial.printf("[PESTISIDA] Eksekusi: %.2f mL | Durasi: %lu ms\n", dosisML, durasiMs);
    pestisidaOn = true;
    digitalWrite(RELAY_PERI_PESTISIDA, HIGH);
    delay(durasiMs);
    digitalWrite(RELAY_PERI_PESTISIDA, LOW);
    pestisidaOn = false;
    Serial.println("[PESTISIDA] Dosis selesai.");
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
    float s   = bacaSuhu();
    float ph  = bacaPH();
    float tds = bacaTDS(s);
    float jr  = bacaJarak();
    DataListrik list = bacaListrik();

    // if (tds > 0 && tds < ppmMin && !nutrisiOn &&
    //     (millis() - lastNutrisiCmdSent >= NUTRISI_CMD_COOLDOWN)) {
    //     nyalakanNutrisi();
    // }

    if (WiFi.status() != WL_CONNECTED) return;

    HTTPClient http;
    http.begin(String(SERVER_URL) + "/api/v1/sensor-data");
    http.addHeader("Content-Type", "application/json");
    http.addHeader("X-API-Key", API_KEY);

    DynamicJsonDocument doc(1024);
    doc["device_id"] = DEVICE_ID;

    // ── Timestamp dari RTC ──────────────────────────────────
    String ts = getRTCTimestamp();
    if (ts.length() > 0) {
        doc["timestamp"] = ts;
    }
    // ────────────────────────────────────────────────────────

    if (s != -127.0) doc["suhu"] = roundf(s * 100) / 100.0;
    doc["ph"]             = roundf(ph  * 100) / 100.0;
    doc["ppm"]            = roundf(tds * 100) / 100.0;
    if (jr > 0) doc["water_level"] = roundf(jr * 100) / 100.0;
    if (list.valid) {
        doc["voltage"]   = list.voltage;
        doc["current"]   = list.current;
        doc["power"]     = list.power;
        doc["energy"]    = list.energy;
    }
    doc["pump_circ_on"]      = circOn;
    doc["pump_peri_on"]      = nutrisiOn;
    doc["pump_pestisida_on"] = pestisidaOn;
    doc["pump_hama_on"]      = hamaOn;
    doc["solenoid_kiri_on"]  = solenoidKiriOn;
    doc["solenoid_kanan_on"] = solenoidKananOn;

    String payload;
    serializeJson(doc, payload);
    int httpCode = http.POST(payload);
    Serial.printf("[HTTP] Data Sent, Code: %d\n", httpCode);
    http.end();
}

// ============================================================
//  CEK ANTREAN DOSIS PESTISIDA
// ============================================================

void cekAntreanPestisida() {
    if (WiFi.status() != WL_CONNECTED) return;
    if (pestisidaOn) return;

    HTTPClient http;
    http.setTimeout(10000);
    http.begin(String(SERVER_URL) + "/api/v1/pestisida");
    http.addHeader("X-API-Key", API_KEY);
    http.addHeader("Content-Type", "application/json");

    int httpCode = http.POST("");
    if (httpCode == 200) {
        String response = http.getString();
        StaticJsonDocument<256> doc;
        DeserializationError err = deserializeJson(doc, response);
        if (!err) {
            float dosisML = doc["dosis"] | 0.0;
            if (dosisML > 0) jalankanPompaPestisida(dosisML);
            else Serial.println("[PESTISIDA] Tidak ada antrean.");
        }
    }
    http.end();
}

// ============================================================
//  WIFI
// ============================================================

void connectWiFi() {
    Serial.print("[WiFi] Menghubungkan");
    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
    int t = 0;
    while (WiFi.status() != WL_CONNECTED && t < 20) {
        delay(500); Serial.print("."); t++;
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
//  AMBIL KONFIGURASI
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
        if (c.containsKey("ph"))             { phMin   = c["ph"]["min_optimal"];               phMax   = c["ph"]["max_optimal"]; }
        if (c.containsKey("ppm"))            { ppmMin  = c["ppm"]["min_optimal"];              ppmMax  = c["ppm"]["max_optimal"]; }
        if (c.containsKey("suhu"))           { suhuMin = c["suhu"]["min_optimal"];             suhuMax = c["suhu"]["max_optimal"]; }
        if (c.containsKey("ketinggian_air")) { jarakMati = c["ketinggian_air"]["min_optimal"]; jarakNyala = c["ketinggian_air"]["max_optimal"]; }
    }
    http.end();
}

// ============================================================
//  HEARTBEAT
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
