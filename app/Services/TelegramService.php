<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $token;
    private string $chatId;

    public function __construct()
    {
        $this->token  = config('services.telegram.bot_token', '');
        $this->chatId = config('services.telegram.chat_id', '');
    }

    /**
     * Kirim pesan teks ke Telegram.
     */
    public function send(string $message): bool
    {
        if (empty($this->token) || empty($this->chatId)) {
            Log::warning('[Telegram] BOT_TOKEN atau CHAT_ID belum dikonfigurasi di .env');
            return false;
        }

        $url = "https://api.telegram.org/bot{$this->token}/sendMessage";

        $payload = http_build_query([
            'chat_id'    => $this->chatId,
            'text'       => $message,
            'parse_mode' => 'HTML',
        ]);

        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $payload,
                'timeout' => 5,
            ],
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
        ]);

        $result = @file_get_contents($url, false, $context);

        if ($result === false) {
            Log::error('[Telegram] Gagal kirim pesan — periksa BOT_TOKEN dan koneksi internet.');
            return false;
        }

        $response = json_decode($result, true);
        if (!($response['ok'] ?? false)) {
            Log::error('[Telegram] API error: ' . ($response['description'] ?? 'Unknown'));
            return false;
        }

        Log::info('[Telegram] Pesan berhasil dikirim.');
        return true;
    }

    /**
     * Kirim notifikasi air tandon rendah — dengan cooldown agar tidak spam.
     *
     * @param float $jarak       Jarak sensor saat ini (cm)
     * @param float $batas       Batas jarak maksimal yang dianggap aman (cm)
     * @param int   $cooldownSec Jeda minimum antar notifikasi (detik)
     */
    public function notifikasiAirRendah(float $jarak, float $batas, int $cooldownSec = 1800): bool
    {
        $cacheKey = 'telegram_water_notif_sent';

        // Cek cooldown — jika sudah kirim dalam periode ini, skip
        if (Cache::has($cacheKey)) {
            Log::info('[Telegram] Notifikasi air rendah di-skip (cooldown aktif).');
            return false;
        }

        $waktu   = now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s');
        $pesan   = "🚨 <b>PERINGATAN — Smart Pakcoy</b>\n\n"
                 . "💧 <b>Air Tandon Rendah!</b>\n\n"
                 . "📏 Jarak sensor ke air: <b>{$jarak} cm</b>\n"
                 . "⚠️ Batas aman: <b>{$batas} cm</b>\n\n"
                 . "🕐 Waktu: {$waktu}\n\n"
                 . "Segera tambahkan air ke tandon hidroponik.";

        $berhasil = $this->send($pesan);

        if ($berhasil) {
            // Simpan penanda cooldown agar tidak kirim ulang dalam rentang waktu tersebut
            Cache::put($cacheKey, true, $cooldownSec);
        }

        return $berhasil;
    }
}
