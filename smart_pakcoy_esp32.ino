/*
 * ============================================================
 *  Smart Pakcoy Hidroponik — ESP32 Firmware
 *  Sensor : DS18B20 (Suhu Air) + HC-SR04 (Ketinggian Tandon)
 *  Aktuator: Relay → Mini Water Pump (Pompa Tandon)
 *
 *  Library yang dibutuhkan (install via Library Manager):
 *    - OneWire          by Paul Stoffregen
 *    - DallasTemperature by Miles Burton
 *    - ArduinoJson       by Benoit Blanchon
 *    - HTTPClient        (sudah built-in di ESP32 core)
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

// -- WiFi --
const char* WIFI_SSID     = "NAMA_WIFI_KAMU";
const char* WIFI_PASSWORD = "PASSWORD_WIFI_KAMU";

// -- Server Laravel --
// Jika menggunakan HP sebagai hotspot dan laptop sebagai server,
// ganti dengan IP laptop (cek dengan 'ipconfig' di CMD).
// Contoh: "http://192.168.1.5:8000"
const char* SERVER_URL    = "http://192.168.X.X:8000";

// -- API Key (harus sama dengan .env ESP32_API_KEY di Laravel) --
const char* API_KEY       = "hidra-core-secret-key-2026";

// -- Device ID (harus terdaftar di tabel devices) --
const char* DEVICE_ID     = "ESP32-SENSOR-001";   // device sensor
const char* PUMP_DEVICE_ID = "ESP32-PUMP-001";     // device pompa tandon

// ============================================================
//  PIN ARDUINO
// ============================================================

// DS18B20 → Data pin ke GPIO 4
#define ONE_WIRE_BUS  4

// HC-SR04 Ultrasonik
#define TRIG_PIN  13
#define ECHO_PIN  12

// Relay Pompa Tandon (aktif LOW → saat LOW pompa NYALA)
#define RELAY_PIN 14

// LED indikator onboard (opsional, LED biru ESP32 = GPIO 2)
#define LED_PIN   2

// ============================================================
//  KONFIGURASI JARAK SENSOR
// ============================================================

// SENSOR DIPASANG DI ATAS TANDON, MENGHADAP KE BAWAH.
// Yang diukur adalah JARAK dari sensor ke permukaan air.
//
//  [SENSOR HC-SR04]  ← di sini
//       ↕ jarak kecil  = air PENUH (pompa MATI)
//       ↕ jarak besar  = air RENDAH (pompa NYALA)
//  ══════════════════   ← permukaan air
//  |                |
//  ██████████████████   ← dasar tandon

// ============================================================
//  STATUS APLIKASI
// ============================================================

// Batas JARAK yang diambil dari server (default)
//   jarakNyala: jika jarak sensor >= nilai ini → pompa NYALA (air rendah)
//   jarakMati : jika jarak sensor <= nilai ini → pompa MATI  (air penuh)
float suhuMin    = 20.0;
float suhuMax    = 30.0;
float jarakNyala = 30.0;  // cm — jika jarak >= 30cm → pompa NYALA
float jarakMati  = 10.0;  // cm — jika jarak <= 10cm → pompa MATI

bool  pumpIsOn        = false;
unsigned long lastSendTime     = 0;
unsigned long lastConfigTime   = 0;
unsigned long lastHeartbeatTime = 0;

// Interval waktu (ms)
const unsigned long SEND_INTERVAL      = 10000;  // kirim data sensor tiap 10 detik
const unsigned long CONFIG_INTERVAL    = 60000;  // ambil config dari server tiap 1 menit
const unsigned long HEARTBEAT_INTERVAL = 30000;  // heartbeat tiap 30 detik

// ============================================================
//  INISIALISASI LIBRARY
// ============================================================
OneWire           oneWire(ONE_WIRE_BUS);
DallasTemperature sensors(&oneWire);

// ============================================================
//  SETUP
// ============================================================
void setup() {
    Serial.begin(115200);
    delay(500);

    Serial.println("\n====================================");
    Serial.println("  Smart Pakcoy ESP32 Booting...");
    Serial.println("====================================");

    // Inisialisasi pin
    pinMode(TRIG_PIN,  OUTPUT);
    pinMode(ECHO_PIN,  INPUT);
    pinMode(RELAY_PIN, OUTPUT);
    pinMode(LED_PIN,   OUTPUT);

    // Pastikan pompa MATI saat mulai (relay aktif LOW → HIGH = mati)
    digitalWrite(RELAY_PIN, HIGH);
    digitalWrite(LED_PIN, LOW);

    // Mulai sensor suhu
    sensors.begin();
    Serial.println("[OK] Sensor DS18B20 diinisialisasi.");

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
        Serial.println("[WiFi] Koneksi terputus, mencoba reconnect...");
        connectWiFi();
    }

    unsigned long now = millis();

    // 1. Kirim data sensor ke server
    if (now - lastSendTime >= SEND_INTERVAL) {
        lastSendTime = now;
        sendSensorData();
    }

    // 2. Ambil konfigurasi min/max dari server
    if (now - lastConfigTime >= CONFIG_INTERVAL) {
        lastConfigTime = now;
        fetchConfigFromServer();
    }

    // 3. Heartbeat ke server
    if (now - lastHeartbeatTime >= HEARTBEAT_INTERVAL) {
        lastHeartbeatTime = now;
        sendHeartbeat();
    }

    delay(500);
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
        digitalWrite(LED_PIN, HIGH);  // LED nyala = WiFi OK
    } else {
        Serial.println("\n[WiFi] GAGAL terhubung! Cek SSID/Password.");
        digitalWrite(LED_PIN, LOW);
    }
}

// ============================================================
//  FUNGSI: Baca Suhu DS18B20
//  Return: suhu dalam Celcius, atau -127 jika error
// ============================================================
float bacaSuhu() {
    sensors.requestTemperatures();
    float suhu = sensors.getTempCByIndex(0);

    if (suhu == DEVICE_DISCONNECTED_C) {
        Serial.println("[Sensor] DS18B20 tidak terdeteksi atau error!");
        return -127.0;
    }

    return suhu;
}

// ============================================================
//  FUNGSI: Baca Jarak Ultrasonik HC-SR04
//  Sensor dipasang di ATAS, menghadap ke bawah ke permukaan air.
//  Return: jarak dari sensor ke permukaan air (cm), atau -1 jika error
// ============================================================
float bacaJarak() {
    // Kirim pulsa TRIG
    digitalWrite(TRIG_PIN, LOW);
    delayMicroseconds(2);
    digitalWrite(TRIG_PIN, HIGH);
    delayMicroseconds(10);
    digitalWrite(TRIG_PIN, LOW);

    // Ukur durasi ECHO (timeout 30ms = maksimal ~5 meter)
    long durasi = pulseIn(ECHO_PIN, HIGH, 30000);

    if (durasi == 0) {
        Serial.println("[Sensor] Ultrasonik timeout — tidak terdeteksi!");
        return -1.0;
    }

    // Hitung jarak: speed of sound = 0.0343 cm/µs
    float jarak = (durasi * 0.0343) / 2.0;

    Serial.printf("[Sensor] Jarak ke air: %.2f cm\n", jarak);
    return jarak;
}

// ============================================================
//  FUNGSI: Kontrol Relay Pompa
// ============================================================
void nyalakanPompa() {
    if (!pumpIsOn) {
        digitalWrite(RELAY_PIN, LOW);  // Relay aktif LOW → pompa NYALA
        pumpIsOn = true;
        Serial.println("[Pompa] >>> NYALA (air rendah)");
    }
}

void matikanPompa() {
    if (pumpIsOn) {
        digitalWrite(RELAY_PIN, HIGH);  // Relay HIGH → pompa MATI
        pumpIsOn = false;
        Serial.println("[Pompa] >>> MATI (air sudah cukup)");
    }
}

// ============================================================
//  FUNGSI: Kirim Data Sensor ke Laravel
//  POST /api/v1/sensor-data
// ============================================================
void sendSensorData() {
    float suhu  = bacaSuhu();
    float jarak = bacaJarak();  // jarak sensor ke permukaan air

    Serial.printf("\n[Data] Suhu: %.2f°C | Jarak ke Air: %.2f cm\n", suhu, jarak);
    Serial.printf("       Pompa NYALA jika jarak >= %.1fcm | MATI jika jarak <= %.1fcm\n",
                  jarakNyala, jarakMati);
    Serial.printf("       Status pompa saat ini: %s\n", pumpIsOn ? "NYALA" : "MATI");

    // ── Logika kontrol pompa berdasarkan JARAK (bukan ketinggian) ──
    //
    //   Jarak BESAR  = air JAUH dari sensor = air RENDAH → pompa NYALA
    //   Jarak KECIL  = air DEKAT ke sensor  = air PENUH  → pompa MATI
    //
    if (jarak > 0) {
        if (jarak >= jarakNyala) {
            nyalakanPompa();       // air rendah → nyalakan pompa
        } else if (jarak <= jarakMati) {
            matikanPompa();        // air sudah penuh → matikan pompa
        }
        // Di antara jarakMati dan jarakNyala: pompa dibiarkan pada kondisi sebelumnya (hysteresis)
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

    // Buat JSON payload
    StaticJsonDocument<256> doc;
    doc["device_id"] = DEVICE_ID;

    if (suhu != -127.0) {
        doc["suhu"] = round(suhu * 100.0) / 100.0;
    }
    if (jarak > 0) {
        // Kirim jarak mentah ke server sebagai water_level
        doc["water_level"] = round(jarak * 100.0) / 100.0;
    }

    String payload;
    serializeJson(doc, payload);

    Serial.printf("[HTTP] POST %s\n       Payload: %s\n", url.c_str(), payload.c_str());

    int httpCode = http.POST(payload);

    if (httpCode > 0) {
        String response = http.getString();
        Serial.printf("[HTTP] Response %d: %s\n", httpCode, response.c_str());

        // Parse response untuk cek apakah server sukses
        if (httpCode == 201) {
            Serial.println("[HTTP] Data berhasil dikirim ke server!");
        }
    } else {
        Serial.printf("[HTTP] GAGAL! Error: %s\n", http.errorToString(httpCode).c_str());
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

    Serial.println("[Config] Mengambil konfigurasi dari server...");

    int httpCode = http.GET();

    if (httpCode == 200) {
        String response = http.getString();

        StaticJsonDocument<1024> doc;
        DeserializationError error = deserializeJson(doc, response);

        if (!error) {
            JsonObject configs = doc["configs"];

            // Update batas suhu
            if (configs.containsKey("suhu")) {
                suhuMin = configs["suhu"]["min_optimal"] | suhuMin;
                suhuMax = configs["suhu"]["max_optimal"] | suhuMax;
            }

            // Update batas jarak ultrasonik
            // min_optimal di database = jarak pompa MATI (air penuh)
            // max_optimal di database = jarak pompa NYALA (air rendah)
            if (configs.containsKey("ketinggian_air")) {
                jarakMati  = configs["ketinggian_air"]["min_optimal"] | jarakMati;
                jarakNyala = configs["ketinggian_air"]["max_optimal"] | jarakNyala;
            }

            Serial.printf("[Config] Update berhasil! Suhu: %.1f–%.1f°C | Jarak nyala: %.1fcm | Jarak mati: %.1fcm\n",
                          suhuMin, suhuMax, jarakNyala, jarakMati);
        } else {
            Serial.printf("[Config] Gagal parse JSON: %s\n", error.c_str());
        }
    } else {
        Serial.printf("[Config] Gagal ambil config! HTTP %d\n", httpCode);
    }

    http.end();
}

// ============================================================
//  FUNGSI: Heartbeat ke Server
//  POST /api/v1/heartbeat
// ============================================================
void sendHeartbeat() {
    if (WiFi.status() != WL_CONNECTED) return;

    HTTPClient http;
    String url = String(SERVER_URL) + "/api/v1/heartbeat";
    http.begin(url);
    http.addHeader("Content-Type", "application/json");
    http.addHeader("X-API-Key", API_KEY);

    StaticJsonDocument<64> doc;
    doc["device_id"] = DEVICE_ID;
    String payload;
    serializeJson(doc, payload);

    int httpCode = http.POST(payload);
    Serial.printf("[Heartbeat] HTTP %d\n", httpCode);
    http.end();
}
