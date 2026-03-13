<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    /**
     * Verifikasi API key dari header X-API-Key.
     * Key harus cocok dengan ESP32_API_KEY di .env.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');
        $validKey = config('services.esp32.api_key');

        if (!$apiKey || $apiKey !== $validKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key tidak valid atau tidak ditemukan.',
            ], 401);
        }

        return $next($request);
    }
}
