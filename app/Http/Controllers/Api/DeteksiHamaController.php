<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeteksiHama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
class DeteksiHamaController extends Controller
{
    public function ProsesAnalisis(Request $request)
    {
        if (!$request->hasFile('image')) {
            return response()->json(['status' => 'error', 'message' => 'File tidak ditemukan'], 400);
        }

        try {
            $now = Carbon::now('Asia/Jakarta');
            $file = $request->file('image');
            $modelId = 'datasetpakcoy';
            $version = '3';
            $apiKey = env('ROBOFLOW_API_KEY');

            // 1. Kirim ke Roboflow
            $response = Http::attach('file', file_get_contents($file->getRealPath()), 'image.jpg')->post("https://detect.roboflow.com/{$modelId}/{$version}?api_key={$apiKey}");

            if ($response->failed()) {
                return response()->json(['success' => false, 'detail' => $response->json()], $response->status());
            }

            $result = $response->json();
            $predictions = $result['predictions'] ?? [];

            // 2. Proses Gambar dengan GD Library
            $img = imagecreatefromjpeg($file->getRealPath());

            // Warna Bounding Box (R, G, B)
            $colorRed = imagecolorallocate($img, 255, 0, 0); // Untuk Hama (Ulat/Siput)
            $colorYellow = imagecolorallocate($img, 255, 255, 0); // Untuk Bolong
            $colorGreen = imagecolorallocate($img, 0, 255, 0); // Untuk Sehat

            $isPestisidaPump = false;
            $finalLabel = 'sehat';
            $maxConfidence = 0;

            foreach ($predictions as $prediction) {
                $label = strtolower($prediction['class']);
                $conf = $prediction['confidence'];

                // Tentukan warna berdasarkan urutan prioritas kamu
                $drawColor = $colorGreen;
                if (in_array($label, ['ulat', 'siput'])) {
                    $drawColor = $colorRed;
                    if ($conf >= 0.4) {
                        $isPestisidaPump = true;
                    }
                } elseif ($label == 'berlubang' || $label == 'berlubang') {
                    $drawColor = $colorYellow;
                }

                // Hitung Koordinat (Roboflow: center_x, center_y, width, height)
                $x1 = $prediction['x'] - $prediction['width'] / 2;
                $y1 = $prediction['y'] - $prediction['height'] / 2;
                $x2 = $prediction['x'] + $prediction['width'] / 2;
                $y2 = $prediction['y'] + $prediction['height'] / 2;

                // Gambar Kotak
                imagerectangle($img, $x1, $y1, $x2, $y2, $drawColor);

                // Tulis Teks Label & Confidence
                $txt = $label . ' ' . round($conf * 100) . '%';
                imagestring($img, 5, $x1, $y1 - 15, $txt, $drawColor);

                // Ambil info untuk DB (prioritas tertinggi ulat)
                if ($conf > $maxConfidence) {
                    $maxConfidence = $conf;
                    $finalLabel = $label;
                }
            }

            // 3. Simpan Gambar yang sudah di-bounding box
            $filename = 'deteksi_' . time() . '.jpg';
            $savePath = storage_path('app/public/deteksi/' . $filename);
            imagejpeg($img, $savePath);
            imagedestroy($img);

            // 4. Simpan ke Database
            $dataDeteksi = DeteksiHama::create([
                'image_url' => 'deteksi/' . $filename,
                'label_hama' => count($predictions) > 0 ? $finalLabel : 'tidak terdeteksi',
                'confidence' => $maxConfidence,
                'is_pestisida_pump' => $isPestisidaPump ? 1 : 0,
                'created_at'=>$now,
                'updated_at'=>$now,
            ]);

            return response()->json(
                [
                    'status' => 'success',
                    'label' => $finalLabel,
                    'confidence' => $maxConfidence,
                    'action' => $isPestisidaPump ? 'PUMP_ON' : 'PUMP_OFF',
                    'image_result' => asset('storage/deteksi/' . $filename),
                    'data' => $dataDeteksi,
                ],
                201,
            );
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
