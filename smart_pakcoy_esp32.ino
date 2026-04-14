/*
 * ============================================================
 *  Smart Pakcoy Hidroponik — ESP32 Firmware v2.0
 *
 *  HARDWARE:
 *    - ESP32
 *    - DS18B20        → Suhu air (OneWire, GPIO 4)
 *    - pH Sensor      → Analog modul BNC (GPIO 34 / ADC1_CH6)
 *    - TDS Sensor     → Analog (GPIO 35 / ADC1_CH7)
 *    - HC-SR04        → Ketinggian air tandon (TRIG=13, ECHO=12)
 *    - Relay 4ch 5V   → IN1=GPIO26 (Sirkulasi), IN2=GPIO27 (Peristaltik)
 *
 *  LOGIKA OTOMATIS:
 *    - PPM/TDS < batas minimal → nyalakan pompa peristaltik 60 detik
 *    - Air tandon rendah (jarak >= batas) → kirim anomali ke server
 *    - Sirkulasi dikontrol dari website (poll command)
 *
 *  LIBRARY (install via Arduino Library Manager):
 *    - OneWire          by Paul Stoffregen
 *    - DallasTemperature by Miles Burton
 *    - ArduinoJson       by Benoit Blanchon (v6.x)
 *    - HTTPClient        (built-in di ESP32 core)
 * ============================================================
 */

#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <OneWire.h>
#include <DallasTemperature.h>

// ============================================================
//  KONFIGURASI — UBAH SESUAI KEBUTUHAN
// ============================================================

const char* WIFI_SSID     = "NAMA_WIFI_KAMU";
const char* WIFI_PASSWORD = "PASSWORD_WIFI_KAMU";

// IP laptop/server (cek dengan 'ipconfig' di CMD)
// Contoh: "http://192.168.1.5:8000"
const char* SERVER_URL    = "http://192.168.X.X:8000";

// API Key (harus sama dengan ESP32_API_KEY di .env Laravel)
const char* API_KEY       = "hidra-core-secret-key-2026";

// Device ID (harus terdaftar / akan auto-dibuat di tabel devices)
const char* DEVICE_ID     = "ESP32-SENSOR-001";
const char* PUMP_CIRC_ID  = "ESP32-PUMP-SIRKULASI";   // mini waterpump
const char* PUMP_PERI_ID  = "ESP32-PUMP-PERISTALTIK"; // pompa peristaltik

// ============================================================
//  PIN MAPPING
// ============================================================

#define ONE_WIRE_BUS    4   // DS18B20 Data
#define PH_PIN         34   // pH sensor analog (ADC1_CH6)
#define TDS_PIN        35   // TDS sensor analog (ADC1_CH7)
#define TRIG_PIN       13   // HC-SR04 Trigger
#define ECHO_PIN       12   // HC-SR04 Echo
#define RELAY_CIRC     26   // IN1 → Mini Waterpump Sirkulasi (aktif LOW)
#define RELAY_PERI     27   // IN2 → Pompa Peristaltik Nutrisi (aktif LOW)
#define LED_PIN         2   // LED builtin ESP32

// ============================================================
//  KALIBRASI SENSOR
// ============================================================

// ----- pH Sensor -----
// Kalibrasi dua titik: gunakan buffer pH 4.0 dan pH 7.0
// Ukur tegangan output pada masing-masing buffer, isi di sini:
#define PH_VOLTAGE_AT_4   2.23   // tegangan (V) saat di pH 4.0
#define PH_VOLTAGE_AT_7   2.51   // tegangan (V) saat di pH 7.0
// Rumus: pH = 7.0 + (V - V7) / ((V4 - V7)/(4.0 - 7.0))
//       = 7.0 - (V - V7) * 3.0 / (V4 - V7)

// ----- TDS Sensor -----
// Rumus: TDS (ppm) = (133.42 * Vc^3 - 255.86 * Vc^2 + 857.39 * Vc) * 0.5
// Vc = tegangan terkoreksi suhu
// Koefisien suhu: 1.0 + 0.02 * (suhu - 25.0)

// ============================================================
//  BATAS DEFAULT (akan diupdate dari server)
// ============================================================

float phMin         = 5.5;
float phMax         = 6.5;
float suhuMin       = 22.0;
float suhuMax       = 30.0;
float ppmMin        = 500.0;
float ppmMax        = 1200.0;
float jarakNyala    = 30.0;  // cm — jika jarak >= ini → air rendah → anomali
float jarakMati     = 10.0;  // cm — jika jarak <= ini → air penuh

// ============================================================
//  STATUS RUNTIME
// ============================================================

bool  circOn            = false;  // status relay sirkulasi
bool  peristalticOn     = false;  // status relay peristaltik

unsigned long lastSendTime      = 0;
unsigned long lastConfigTime    = 0;
unsigned long lastHeartbeatTime = 0;
unsigned long lastCmdPollTime   = 0;
unsigned long peristalticStart  = 0;  // kapan pompa peristaltik dinyalakan

// Durasi pompa peristaltik otomatis (ms)
const unsigned long PERI_AUTO_DURATION = 60000UL; // 60 detik

// Interval
const unsigned long SEND_INTERVAL      = 60000UL;  // 1 menit
const unsigned long CONFIG_INTERVAL    = 300000UL; // 5 menit
const unsigned long HEARTBEAT_INTERVAL = 30000UL;  // 30 detik
const unsigned long CMD_POLL_INTERVAL  = 5000UL;   // poll command setiap 5 detik

// Kapan terakhir kirim command ke server untuk pompa
unsigned long lastPeriCommandSent = 0;
const unsigned long PERI_CMD_COOLDOWN = 65000UL; // cooldown 65 detik setelah pompa dijalankan

// ============================================================
//  INISIALISASI LIBRARY
// ============================================================

OneWire           oneWire(ONE_WIRE_BUS);
DallasTemperature tempSensor(&oneWire);

// ============================================================
//  SETUP
// ============================================================

void setup() {
    Serial.begin(115200);
    delay(500);

    Serial.println("\n====================================");
    Serial.println("  Smart Pakcoy ESP32 v2.0 Booting");
    Serial.println("====================================");

    // Inisialisasi pin
    pinMode(TRIG_PIN,    OUTPUT);
    pinMode(ECHO_PIN,    INPUT);
    pinMode(RELAY_CIRC,  OUTPUT);
    pinMode(RELAY_PERI,  OUTPUT);
    pinMode(LED_PIN,     OUTPUT);

    // Matikan semua relay saat boot (relay aktif LOW → HIGH = MATI)
    digitalWrite(RELAY_CIRC, HIGH);
    digitalWrite(RELAY_PERI, HIGH);
    digitalWrite(LED_PIN,    LOW);

    // Konfigurasi ADC untuk pH dan TDS
    analogReadResolution(12);   // 12-bit (0-4095)
    analogSetAttenuation(ADC_11db); // range 0-3.3V

    // Mulai DS18B20
    tempSensor.begin();
    Serial.println("[OK] Sensor DS18B20 siap.");

    // Koneksi WiFi
    connectWiFi();

    // Ambil konfigurasi awal dari server
    fetchConfigFromServer();

    Serial.println("[OK] Setup selesai. Mulai loop...\n");
}

// ============================================================
//  LOOP UTAMA
// ============================================================

void loop() {
    // Pastikan WiFi tetap terhubung
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("[WiFi] Koneksi terputus, reconnect...");
        connectWiFi();
    }

    unsigned long now = millis();

    // 1. Kirim data sensor (setiap 1 menit)
    if (now - lastSendTime >= SEND_INTERVAL) {
        lastSendTime = now;
        sendSensorData();
    }

    // 2. Ambil konfigurasi dari server (setiap 5 menit)
    if (now - lastConfigTime >= CONFIG_INTERVAL) {
        lastConfigTime = now;
        fetchConfigFromServer();
    }

    // 3. Heartbeat
    if (now - lastHeartbeatTime >= HEARTBEAT_INTERVAL) {
        lastHeartbeatTime = now;
        sendHeartbeat();
    }

    // 4. Poll command sirkulasi dari server (setiap 5 detik)
    if (now - lastCmdPollTime >= CMD_POLL_INTERVAL) {
        lastCmdPollTime = now;
        pollCirculationCommand();
    }

    // 5. Auto-matikan pompa peristaltik setelah durasi habis
    if (peristalticOn && (now - peristalticStart >= PERI_AUTO_DURATION)) {
        matikanPeristaltic();
        Serial.println("[Peristaltik] Selesai 60 detik → MATI otomatis.");
    }

    delay(200);
}

// ============================================================
//  FUNGSI: Koneksi WiFi
// ============================================================

void connectWiFi() {
    Serial.printf("[WiFi] Menghubungkan ke %s ", WIFI_SSID);
    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

    int tries = 0;
    while (WiFi.status() != WL_CONNECTED && tries < 30) {
        delay(500);
        Serial.print(".");
        tries++;
    }

    if (WiFi.status() == WL_CONNECTED) {
        Serial.println();
        Serial.printf("[WiFi] Terhubung! IP: %s\n", WiFi.localIP().toString().c_str());
        digitalWrite(LED_PIN, HIGH);
    } else {
        Serial.println("\n[WiFi] GAGAL! Cek SSID/Password.");
        digitalWrite(LED_PIN, LOW);
    }
}

// ============================================================
//  FUNGSI: Baca Suhu DS18B20
// ============================================================

float bacaSuhu() {
    tempSensor.requestTemperatures();
    float suhu = tempSensor.getTempCByIndex(0);

    if (suhu == DEVICE_DISCONNECTED_C) {
        Serial.println("[Sensor] DS18B20 tidak terdeteksi!");
        return -127.0;
    }

    Serial.printf("[Sensor] Suhu: %.2f°C\n", suhu);
    return suhu;
}

// ============================================================
//  FUNGSI: Baca pH Sensor (Analog)
// ============================================================

float bacaPH() {
    // Rata-rata 30 sample untuk mengurangi noise
    long sum = 0;
    for (int i = 0; i < 30; i++) {
        sum += analogRead(PH_PIN);
        delay(10);
    }
    float raw = sum / 30.0;

    // Konversi ADC → Tegangan (3.3V referensi, 12-bit = 4095)
    float voltage = raw * 3.3 / 4095.0;

    // Kalibrasi dua titik (pH 4 dan pH 7)
    // slope = (4.0 - 7.0) / (PH_VOLTAGE_AT_4 - PH_VOLTAGE_AT_7)
    float slope = 3.0 / (PH_VOLTAGE_AT_4 - PH_VOLTAGE_AT_7);
    float ph    = 7.0 + slope * (voltage - PH_VOLTAGE_AT_7);

    // Batasi dalam range 0-14
    ph = constrain(ph, 0.0, 14.0);

    Serial.printf("[Sensor] pH: %.2f (ADC raw: %.0f, V: %.3fV)\n", ph, raw, voltage);
    return ph;
}

// ============================================================
//  FUNGSI: Baca TDS Sensor (Analog)
// ============================================================

float bacaTDS(float suhu) {
    // Jika suhu tidak valid, gunakan 25°C sebagai default
    if (suhu < -50) suhu = 25.0;

    // Rata-rata 30 sample
    long sum = 0;
    for (int i = 0; i < 30; i++) {
        sum += analogRead(TDS_PIN);
        delay(10);
    }
    float raw = sum / 30.0;

    // Konversi ADC → Tegangan
    float voltage = raw * 3.3 / 4095.0;

    // Koreksi suhu
    float compensationCoeff = 1.0 + 0.02 * (suhu - 25.0);
    float compVoltage       = voltage / compensationCoeff;

    // Rumus DFRobot TDS
    float tds = (133.42 * pow(compVoltage, 3)
               - 255.86 * pow(compVoltage, 2)
               + 857.39 * compVoltage) * 0.5;

    tds = constrain(tds, 0.0, 5000.0);

    Serial.printf("[Sensor] TDS: %.2f ppm (V: %.3fV, Suhu: %.1f°C)\n", tds, voltage, suhu);
    return tds;
}

// ============================================================
//  FUNGSI: Baca Jarak Ultrasonik HC-SR04
// ============================================================

float bacaJarak() {
    digitalWrite(TRIG_PIN, LOW);
    delayMicroseconds(2);
    digitalWrite(TRIG_PIN, HIGH);
    delayMicroseconds(10);
    digitalWrite(TRIG_PIN, LOW);

    long durasi = pulseIn(ECHO_PIN, HIGH, 30000);

    if (durasi == 0) {
        Serial.println("[Sensor] Ultrasonik timeout!");
        return -1.0;
    }

    float jarak = (durasi * 0.0343) / 2.0;
    Serial.printf("[Sensor] Jarak ke air: %.2f cm\n", jarak);
    return jarak;
}

// ============================================================
//  FUNGSI: Kontrol Relay Sirkulasi
// ============================================================

void nyalakanSirkulasi() {
    if (!circOn) {
        digitalWrite(RELAY_CIRC, LOW);
        circOn = true;
        Serial.println("[Sirkulasi] >>> NYALA");
    }
}

void matikanSirkulasi() {
    if (circOn) {
        digitalWrite(RELAY_CIRC, HIGH);
        circOn = false;
        Serial.println("[Sirkulasi] >>> MATI");
    }
}

// ============================================================
//  FUNGSI: Kontrol Relay Pompa Peristaltik
// ============================================================

void nyalakanPeristaltic() {
    if (!peristalticOn) {
        digitalWrite(RELAY_PERI, LOW);
        peristalticOn   = true;
        peristalticStart = millis();
        lastPeriCommandSent = millis();
        Serial.println("[Peristaltik] >>> NYALA (60 detik)");
    }
}

void matikanPeristaltic() {
    if (peristalticOn) {
        digitalWrite(RELAY_PERI, HIGH);
        peristalticOn = false;
        Serial.println("[Peristaltik] >>> MATI");
    }
}

// ============================================================
//  FUNGSI: Kirim Data Sensor ke Server
//  POST /api/v1/sensor-data
// ============================================================

void sendSensorData() {
    float suhu  = bacaSuhu();
    float ph    = bacaPH();
    float ppm   = bacaTDS(suhu);
    float jarak = bacaJarak();

    Serial.println("\n===== [KIRIM DATA SENSOR] =====");
    Serial.printf("  Suhu   : %.2f°C\n", suhu);
    Serial.printf("  pH     : %.2f\n", ph);
    Serial.printf("  TDS    : %.2f ppm\n", ppm);
    Serial.printf("  Jarak  : %.2f cm\n", jarak);
    Serial.println("================================");

    // ── Logika: PPM rendah → nyalakan pompa peristaltik otomatis ──
    unsigned long now = millis();
    if (ppm > 0 && ppm < ppmMin) {
        // Cek cooldown agar tidak terus-menerus menyalakan
        if (!peristalticOn && (now - lastPeriCommandSent >= PERI_CMD_COOLDOWN)) {
            Serial.printf("[LOG] PPM %.1f < batas minimum %.1f → Nyalakan peristaltik!\n", ppm, ppmMin);
            nyalakanPeristaltic();
        }
    }

    // ── Kirim ke Laravel ──
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("[HTTP] Tidak ada WiFi, skip kirim data.");
        return;
    }

    HTTPClient http;
    String url = String(SERVER_URL) + "/api/v1/sensor-data";
    http.begin(url);
    http.addHeader("Content-Type", "application/json");
    http.addHeader("X-API-Key", API_KEY);
    http.setTimeout(10000);

    StaticJsonDocument<512> doc;
    doc["device_id"] = DEVICE_ID;

    if (suhu != -127.0)   doc["suhu"]        = roundf(suhu * 100) / 100.0;
    if (ph >= 0)          doc["ph"]           = roundf(ph * 100) / 100.0;
    if (ppm >= 0)         doc["ppm"]          = roundf(ppm * 100) / 100.0;
    if (jarak > 0)        doc["water_level"]  = roundf(jarak * 100) / 100.0;

    // Status relay
    doc["pump_circ_on"] = circOn;
    doc["pump_peri_on"] = peristalticOn;

    String payload;
    serializeJson(doc, payload);

    Serial.printf("[HTTP] POST → %s\n  Payload: %s\n", url.c_str(), payload.c_str());

    int httpCode = http.POST(payload);

    if (httpCode > 0) {
        String response = http.getString();
        Serial.printf("[HTTP] Response %d: %s\n", httpCode, response.c_str());
    } else {
        Serial.printf("[HTTP] GAGAL! Error: %s\n", http.errorToString(httpCode).c_str());
    }

    http.end();
}

// ============================================================
//  FUNGSI: Poll Command Sirkulasi dari Server
//  GET /api/v1/command/{deviceId}
// ============================================================

void pollCirculationCommand() {
    if (WiFi.status() != WL_CONNECTED) return;

    HTTPClient http;
    String url = String(SERVER_URL) + "/api/v1/command/" + PUMP_CIRC_ID;
    http.begin(url);
    http.addHeader("X-API-Key", API_KEY);
    http.setTimeout(5000);

    int httpCode = http.GET();

    if (httpCode == 200) {
        String response = http.getString();

        StaticJsonDocument<256> doc;
        DeserializationError err = deserializeJson(doc, response);

        if (!err && doc["success"].as<bool>()) {
            const char* cmd = doc["command"] | "";

            if (strcmp(cmd, "circulation_on") == 0) {
                nyalakanSirkulasi();
            } else if (strcmp(cmd, "circulation_off") == 0) {
                matikanSirkulasi();
            }
        }
    }

    http.end();
}

// ============================================================
//  FUNGSI: Ambil Konfigurasi dari Server
//  GET /api/v1/configs
// ============================================================

void fetchConfigFromServer() {
    if (WiFi.status() != WL_CONNECTED) return;

    HTTPClient http;
    String url = String(SERVER_URL) + "/api/v1/configs";
    http.begin(url);
    http.addHeader("X-API-Key", API_KEY);
    http.setTimeout(10000);

    Serial.println("[Config] Mengambil konfigurasi dari server...");

    int httpCode = http.GET();

    if (httpCode == 200) {
        String response = http.getString();

        StaticJsonDocument<1024> doc;
        DeserializationError error = deserializeJson(doc, response);

        if (!error) {
            JsonObject configs = doc["configs"];

            if (configs.containsKey("ph")) {
                phMin = configs["ph"]["min_optimal"] | phMin;
                phMax = configs["ph"]["max_optimal"] | phMax;
            }
            if (configs.containsKey("suhu")) {
                suhuMin = configs["suhu"]["min_optimal"] | suhuMin;
                suhuMax = configs["suhu"]["max_optimal"] | suhuMax;
            }
            if (configs.containsKey("ppm")) {
                ppmMin = configs["ppm"]["min_optimal"] | ppmMin;
                ppmMax = configs["ppm"]["max_optimal"] | ppmMax;
            }
            if (configs.containsKey("ketinggian_air")) {
                jarakMati   = configs["ketinggian_air"]["min_optimal"] | jarakMati;
                jarakNyala  = configs["ketinggian_air"]["max_optimal"] | jarakNyala;
            }

            Serial.printf("[Config] Update berhasil!\n");
            Serial.printf("  pH     : %.1f – %.1f\n", phMin, phMax);
            Serial.printf("  Suhu   : %.1f – %.1f°C\n", suhuMin, suhuMax);
            Serial.printf("  PPM    : %.0f – %.0f ppm\n", ppmMin, ppmMax);
            Serial.printf("  Jarak  : %.1f cm (mati) | %.1f cm (rendah)\n", jarakMati, jarakNyala);
        } else {
            Serial.printf("[Config] Gagal parse JSON: %s\n", error.c_str());
        }
    } else {
        Serial.printf("[Config] Gagal! HTTP %d\n", httpCode);
    }

    http.end();
}

// ============================================================
//  FUNGSI: Heartbeat ke Server
//  POST /api/v1/heartbeat
// ============================================================

void sendHeartbeat() {
    if (WiFi.status() != WL_CONNECTED) return;

    // Heartbeat untuk device sensor
    {
        HTTPClient http;
        String url = String(SERVER_URL) + "/api/v1/heartbeat";
        http.begin(url);
        http.addHeader("Content-Type", "application/json");
        http.addHeader("X-API-Key", API_KEY);
        http.setTimeout(5000);

        StaticJsonDocument<128> doc;
        doc["device_id"] = DEVICE_ID;
        String payload;
        serializeJson(doc, payload);

        int httpCode = http.POST(payload);
        Serial.printf("[Heartbeat] Sensor HTTP %d\n", httpCode);
        http.end();
    }

    // Heartbeat/registrasi untuk pompa sirkulasi
    {
        HTTPClient http;
        String url = String(SERVER_URL) + "/api/v1/heartbeat";
        http.begin(url);
        http.addHeader("Content-Type", "application/json");
        http.addHeader("X-API-Key", API_KEY);
        http.setTimeout(5000);

        StaticJsonDocument<128> doc;
        doc["device_id"] = PUMP_CIRC_ID;
        String payload;
        serializeJson(doc, payload);

        int httpCode = http.POST(payload);
        Serial.printf("[Heartbeat] Pump Sirkulasi HTTP %d\n", httpCode);
        http.end();
    }

    // Heartbeat/registrasi untuk pompa peristaltik
    {
        HTTPClient http;
        String url = String(SERVER_URL) + "/api/v1/heartbeat";
        http.begin(url);
        http.addHeader("Content-Type", "application/json");
        http.addHeader("X-API-Key", API_KEY);
        http.setTimeout(5000);

        StaticJsonDocument<128> doc;
        doc["device_id"] = PUMP_PERI_ID;
        String payload;
        serializeJson(doc, payload);

        int httpCode = http.POST(payload);
        Serial.printf("[Heartbeat] Pump Peristaltik HTTP %d\n", httpCode);
        http.end();
    }
}
