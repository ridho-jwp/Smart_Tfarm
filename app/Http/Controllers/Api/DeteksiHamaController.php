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
    // ini coba
    // public function ProsesAnalisis(Request $request)
    // {
    //     if (!$request->hasFile('image')) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Gambar tidak ada'
    //         ], 400);
    //     }

    //     $sessionID = $request->input('session_id') ?? uniqid();

    //     try {
    //         $file = $request->file('image');

    //         // =========================
    //         // 1. KIRIM KE ROBOFLOW
    //         // =========================
    //         $response = Http::attach(
    //             'file',
    //             file_get_contents($file->getRealPath()),
    //             'image.jpg'
    //         )->post("https://detect.roboflow.com/datasetpakcoy/5?api_key=" . env('ROBOFLOW_API_KEY'));

    //         if ($response->failed()) {
    //             return response()->json(['status' => 'error'], 500);
    //         }

    //         $result = $response->json();
    //         $predictions = $result['predictions'] ?? [];
    //         $imgWidth = $result['image']['width'] ?? 0;

    //         // =========================
    //         // 2. LOAD GAMBAR
    //         // =========================
    //         $imgSource = imagecreatefromjpeg($file->getRealPath());

    //         $colorRed = imagecolorallocate($imgSource, 255, 0, 0);
    //         $colorYellow = imagecolorallocate($imgSource, 255, 255, 0);
    //         $colorGreen = imagecolorallocate($imgSource, 0, 255, 0);
    //         $colorWhite = imagecolorallocate($imgSource, 255, 255, 255);

    //         $middle = $imgWidth / 2;
    //         imageline($imgSource, $middle, 0, $middle, imagesy($imgSource), $colorWhite);

    //         // =========================
    //         // 3. INIT
    //         // =========================
    //         // Sistem prioritas per sisi:
    //         // 0 = tidak_terdeteksi
    //         // 1 = sehat / lainnya
    //         // 2 = berlubang
    //         // 3 = hama (ulat / siput)
    //         $kiri = ['label' => 'tidak_terdeteksi', 'conf' => 0, 'priority' => 0];
    //         $kanan = ['label' => 'tidak_terdeteksi', 'conf' => 0, 'priority' => 0];

    //         // =========================
    //         // 4. LOOP DETEKSI
    //         // =========================
    //         foreach ($predictions as $p) {

    //             $x1 = $p['x'] - $p['width'] / 2;
    //             $y1 = $p['y'] - $p['height'] / 2;
    //             $x2 = $p['x'] + $p['width'] / 2;
    //             $y2 = $p['y'] + $p['height'] / 2;

    //             $label = strtolower($p['class']);
    //             $conf = $p['confidence'];

    //             $isHama = str_contains($label, 'ulat') || str_contains($label, 'siput');
    //             $isBerlubang = $label == 'berlubang';

    //             // Tentukan prioritas dan threshold confidence
    //             $priority = $isHama ? 3 : ($isBerlubang ? 2 : 1);
    //             $confThreshold = $isHama ? 0.5 : ($isBerlubang ? 0.3 : 0.0);

    //             // =========================
    //             // VISUAL LABEL & WARNA
    //             // =========================
    //             $displayLabel = $isHama
    //                 ? (str_contains($label, 'ulat') ? 'ulat' : 'siput')
    //                 : $label;

    //             $drawColor = $isHama ? $colorRed :
    //                 ($isBerlubang ? $colorYellow : $colorGreen);

    //             imagerectangle($imgSource, $x1, $y1, $x2, $y2, $drawColor);

    //             imagestring(
    //                 $imgSource,
    //                 5,
    //                 $x1,
    //                 $y1 - 15,
    //                 $displayLabel . ' ' . round($conf * 100) . '%',
    //                 $drawColor
    //             );

    //             // =========================
    //             // SIMPAN PER SISI DENGAN PRIORITAS
    //             // Update hanya jika:
    //             // - prioritas lebih tinggi, ATAU
    //             // - prioritas sama tapi confidence lebih tinggi
    //             // =========================
    //             if ($conf >= $confThreshold) {
    //                 if ($p['x'] < $middle) {
    //                     if (
    //                         $priority > $kiri['priority'] ||
    //                         ($priority === $kiri['priority'] && $conf > $kiri['conf'])
    //                     ) {
    //                         $kiri = [
    //                             'label' => $displayLabel,
    //                             'conf' => $conf,
    //                             'priority' => $priority
    //                         ];
    //                     }
    //                 } else {
    //                     if (
    //                         $priority > $kanan['priority'] ||
    //                         ($priority === $kanan['priority'] && $conf > $kanan['conf'])
    //                     ) {
    //                         $kanan = [
    //                             'label' => $displayLabel,
    //                             'conf' => $conf,
    //                             'priority' => $priority
    //                         ];
    //                     }
    //                 }
    //             }
    //         }

    //         // =========================
    //         // 5. STATUS FINAL BERDASARKAN PRIORITAS TERTINGGI
    //         // - Salah satu sisi hama     → status = hama
    //         // - Salah satu sisi berlubang → status = berlubang
    //         // - Keduanya aman / sehat    → status = aman
    //         // =========================
    //         $maxPriority = max($kiri['priority'], $kanan['priority']);

    //         if ($maxPriority >= 3) {
    //             $status = 'hama';
    //         } elseif ($maxPriority >= 2) {
    //             $status = 'berlubang';
    //         } else {
    //             $status = 'aman';
    //         }

    //         // =========================
    //         // 6. TULIS STATUS KE GAMBAR
    //         // =========================
    //         $colorKiri = match (true) {
    //             $kiri['priority'] === 3 => $colorRed,
    //             $kiri['priority'] === 2 => $colorYellow,
    //             $kiri['priority'] === 1 => $colorGreen,
    //             default => $colorYellow, // tidak_terdeteksi
    //         };

    //         $colorKanan = match (true) {
    //             $kanan['priority'] === 3 => $colorRed,
    //             $kanan['priority'] === 2 => $colorYellow,
    //             $kanan['priority'] === 1 => $colorGreen,
    //             default => $colorYellow, // tidak_terdeteksi
    //         };

    //         imagestring($imgSource, 5, 20, 20, "KIRI: " . strtoupper($kiri['label']), $colorKiri);
    //         imagestring($imgSource, 5, $middle + 20, 20, "KANAN: " . strtoupper($kanan['label']), $colorKanan);

    //         // =========================
    //         // 7. SIMPAN GAMBAR
    //         // =========================
    //         $filename = 'deteksi_' . time() . '.jpg';
    //         $savePath = storage_path('app/public/hasil-coba/' . $filename);

    //         imagejpeg($imgSource, $savePath);
    //         imagedestroy($imgSource);

    //         // =========================
    //         // 8. SIMPAN DATABASE
    //         // =========================
    //         DeteksiHama::create([
    //             'session_id' => $sessionID,
    //             'image_url' => 'hasil-coba/' . $filename,
    //             'confidence' => max($kiri['conf'], $kanan['conf']),
    //             'is_pestisida_pump' => $status == 'hama',
    //             'label_hama' => $status
    //         ]);

    //         // =========================
    //         // 9. RESPONSE
    //         // =========================
    //         return response()->json([
    //             'status' => 'success',
    //             'kiri' => $kiri,
    //             'kanan' => $kanan,
    //             'action' => $status == 'hama' ? 'PUMP_ON' : 'PUMP_OFF',
    //             'image_result' => asset('storage/hasil-coba/' . $filename)
    //         ], 201);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }


    // ini yang benar 
    public function ProsesAnalisis(Request $request)
    {
        if (!$request->hasFile('image')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gambar tidak ada'
            ], 400);
        }
        $sessionID = $request->input('session_id') ?? uniqid();
        try {
            $file = $request->file('image');

            // =========================
            // 1. KIRIM KE ROBOFLOW
            // =========================
            $response = Http::attach(
                'file',
                file_get_contents($file->getRealPath()),
                'image.jpg'
            )->post("https://detect.roboflow.com/datasetpakcoy/6?api_key=" . env('ROBOFLOW_API_KEY'));

            if ($response->failed()) {
                return response()->json(['status' => 'error'], 500);
            }

            $result = $response->json();
            $predictions = $result['predictions'] ?? [];
            $imgWidth = $result['image']['width'] ?? 0;

            // =========================
            // 2. LOAD GAMBAR
            // =========================
            $imgSource = imagecreatefromjpeg($file->getRealPath());

            $colorRed = imagecolorallocate($imgSource, 255, 0, 0);
            $colorYellow = imagecolorallocate($imgSource, 255, 255, 0);
            $colorGreen = imagecolorallocate($imgSource, 0, 255, 0);
            $colorWhite = imagecolorallocate($imgSource, 255, 255, 255);

            $middle = $imgWidth / 2;

            // garis tengah
            imageline($imgSource, $middle, 0, $middle, imagesy($imgSource), $colorWhite);

            // =========================
            // 3. HASIL PER SISI
            // =========================
            $kiri = ['label' => 'sehat', 'conf' => 0];
            $kanan = ['label' => 'sehat', 'conf' => 0];

            $isPestisidaPump = false;

            // =========================
            // 4. LOOP DETEKSI
            // =========================
            foreach ($predictions as $p) {

                $x1 = $p['x'] - $p['width'] / 2;
                $y1 = $p['y'] - $p['height'] / 2;
                $x2 = $p['x'] + $p['width'] / 2;
                $y2 = $p['y'] + $p['height'] / 2;

                $label = strtolower($p['class']);
                $conf = $p['confidence'];
                $isHamaAtauRusak = str_contains($label, 'ulat') || str_contains($label, 'siput');

                if ($isHamaAtauRusak) {
                    $displayLabel = str_contains($label, 'ulat') ? 'ulat' : (str_contains($label, 'siput') ? 'siput' : 'berlubang');
                } else {
                    $displayLabel = $label;
                }
                // warna
                $drawColor = $isHamaAtauRusak ? $colorRed : (($label == 'berlubang') ? $colorYellow : $colorGreen);
                // gambar bounding box
                imagerectangle($imgSource, $x1, $y1, $x2, $y2, $drawColor);
                imagestring(
                    $imgSource,
                    5,
                    $x1,
                    $y1 - 15,
                    $displayLabel . ' ' . round($conf * 100) . '%',
                    $drawColor
                );

                // =========================
                // DETEKSI SISI
                // =========================
                if ($p['x'] < $middle) {
                    if ($conf > $kiri['conf']) {
                        // Simpan label ringkas saja jika mau
                        $kiri = ['label' => $isHamaAtauRusak ? $displayLabel : $label, 'conf' => $conf];
                    }
                } else {
                    if ($conf > $kanan['conf']) {
                        $kanan = ['label' => $isHamaAtauRusak ? $displayLabel : $label, 'conf' => $conf];
                    }
                }

                // trigger pompaff
                if ($conf >= 0.5 && $isHamaAtauRusak) {
                    $isPestisidaPump = true;
                }
            }
            if ($kiri['conf'] == 0) {
                $kiri['label'] = 'tidak_terdeteksi';
            }

            if ($kanan['conf'] == 0) {
                $kanan['label'] = 'tidak_terdeteksi';
            }
            // =========================
            // 5. TULIS LABEL DI GAMBAR
            // =========================
            // $colorKiri = ($kiri['label'] == 'sehat') ? $colorGreen : $colorRed;
            // $colorKanan = ($kanan['label'] == 'sehat') ? $colorGreen : $colorRed;


            $colorKiri = ($kiri['label'] == 'sehat') ? $colorGreen :
                (($kiri['label'] == 'tidak_terdeteksi') ? $colorYellow : $colorRed);

            $colorKanan = ($kanan['label'] == 'sehat') ? $colorGreen :
                (($kanan['label'] == 'tidak_terdeteksi') ? $colorYellow : $colorRed);
            \Log::info($predictions);
            imagestring(
                $imgSource,
                5,
                20,
                20,
                "KIRI: " . strtoupper($kiri['label']),
                $colorKiri
            );

            imagestring(
                $imgSource,
                5,
                $middle + 20,
                20,
                "KANAN: " . strtoupper($kanan['label']),
                $colorKanan
            );

            // =========================
            // 6. SIMPAN GAMBAR
            // =========================
            $filename = 'deteksi_terbaru' . time() . '.jpg';

            $relativePath = 'deteksi/' . $filename;

            // simpan sementara
            $tempPath = storage_path('app/temp_' . $filename);

            imagejpeg($imgSource, $tempPath, 90);

            imagedestroy($imgSource);

            // simpan ke storage laravel
            Storage::disk('public')->put(
                $relativePath,
                file_get_contents($tempPath)
            );

            // hapus file temp
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            // =========================
            // 7. RESPONSE
            // =========================

            // ProsesAnalisis - perbaikan logic status & response
            $status = 'aman';

            // PRIORITAS 1: Cek hama (ulat/siput) atau berlubang
            if (
                in_array($kiri['label'], ['ulat', 'siput']) ||
                in_array($kanan['label'], ['ulat', 'siput'])
            ) {
                $status = 'hama';
            }
            // PRIORITAS 2: Sehat jika kedua sisi sehat
            elseif ($kiri['label'] == 'sehat' || $kanan['label'] == 'sehat') {
                $status = 'sehat';
            }

            // Tentukan sisi mana yang terdeteksi hama
            $hamaLabels = ['ulat', 'siput', 'berlubang'];
            $sideLeft = in_array($kiri['label'], $hamaLabels);
            $sideRight = in_array($kanan['label'], $hamaLabels);

            // Simpan ke database - tambahkan kolom side_left & side_right
            DeteksiHama::create([
                'session_id' => $sessionID,
                'image_url' => 'storage/deteksi/' . $filename,
                'confidence' => max($kiri['conf'], $kanan['conf']),
                'is_pestisida_pump' => $status == 'hama',
                'label_hama' => $status,
                'side_left' => $sideLeft,   // ← kolom baru
                'side_right' => $sideRight,  // ← kolom baru
            ]);

            return response()->json([
                'status' => 'success',
                'kiri' => $kiri,
                'kanan' => $kanan,
                'label_status' => $status,
                'side_left' => $sideLeft,
                'side_right' => $sideRight,
                'action' => $isPestisidaPump ? 'PUMP_ON' : 'PUMP_OFF',
                'image_result' => ('storage/deteksi/' . $filename)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function cekstatuspompa()
    {
        $latest = DeteksiHama::orderBy('created_at', 'desc')->first();

        if (!$latest) {
            return response()->json([
                'is_pestisida_pump' => false,
                'label_hama' => 'aman',
                'side_left' => false,
                'side_right' => false,
            ]);
        }

        return response()->json([
            'is_pestisida_pump' => (bool) $latest->is_pestisida_pump,
            'label_hama' => $latest->label_hama,
            'side_left' => (bool) $latest->side_left,   // ← tambahan
            'side_right' => (bool) $latest->side_right,  // ← tambahan
        ]);
    }






    // coba yang ini
    // public function ProsesAnalisis(Request $request)
    // {
    //     if (!$request->hasFile('image') || !$request->has('side')) {
    //         return response()->json(['status' => 'error', 'message' => 'Data tidak lengkap'], 400);
    //     }

    //     try {
    //         $side = $request->input('side'); // "kiri" atau "kanan"
    //         $file = $request->file('image');

    //         // 1. Kirim GAMBAR ASLI ke Roboflow (jangan di-crop di Laravel)
    //         $response = Http::attach('file', file_get_contents($file->getRealPath()), 'image.jpg')
    //             ->post("https://detect.roboflow.com/datasetpakcoy/5?api_key=" . env('ROBOFLOW_API_KEY'));

    //         if ($response->failed())
    //             return response()->json(['status' => 'error'], 500);

    //         $result = $response->json();
    //         $predictions = $result['predictions'] ?? [];
    //         $imgWidth = $result['image']['width'] ?? 0;

    //         // 2. Filter Prediksi berdasarkan sisi yang diminta (Kiri < Tengah, Kanan >= Tengah)
    //         $filteredPredictions = array_filter($predictions, function ($p) use ($side, $imgWidth) {
    //             $x1 = $p['x'] - $p['width'] / 2;
    //             $x2 = $p['x'] + $p['width'] / 2;
    //             $middle = $imgWidth / 2;

    //             if ($side === 'kiri') {
    //                 return $x1 < $middle;   // sebagian objek masuk kiri
    //             } else {
    //                 return $x2 >= $middle;  // sebagian objek masuk kanan
    //             }
    //         });

    //         // 3. Proses Drawing pada Gambar ASLI
    //         $imgSource = imagecreatefromjpeg($file->getRealPath());
    //         $colorRed = imagecolorallocate($imgSource, 255, 0, 0);
    //         $colorYellow = imagecolorallocate($imgSource, 255, 255, 0);
    //         $colorGreen = imagecolorallocate($imgSource, 0, 255, 0);

    //         $isPestisidaPump = false;
    //         $finalLabel = 'sehat';
    //         $maxConfidence = 0;

    //         foreach ($filteredPredictions as $p) {
    //             $x1 = $p['x'] - $p['width'] / 2;
    //             $y1 = $p['y'] - $p['height'] / 2;
    //             $x2 = $p['x'] + $p['width'] / 2;
    //             $y2 = $p['y'] + $p['height'] / 2;

    //             $label = strtolower($p['class']);
    //             $conf = $p['confidence'];
    //             $drawColor = ($label == 'ulat' || $label == 'siput') ? $colorRed : (($label == 'berlubang') ? $colorYellow : $colorGreen);

    //             imagerectangle($imgSource, $x1, $y1, $x2, $y2, $drawColor);
    //             imagestring($imgSource, 5, $x1, $y1 - 15, $label . ' ' . round($conf * 100) . '%', $drawColor);

    //             if ($conf >= 0.4 && ($label == 'ulat' || $label == 'siput'))
    //                 $isPestisidaPump = true;
    //             if ($conf > $maxConfidence) {
    //                 $maxConfidence = $conf;
    //                 $finalLabel = $label;
    //             }
    //         }
    //         $filename = 'deteksi_' . $side . '_' . time() . '.jpg';
    //         $savePath = storage_path('app/public/deteksi/' . $filename);

    //         if (file_exists($savePath)) {
    //             unlink($savePath);
    //         }

    //         imagejpeg($imgSource, $savePath); // simpan dulu
    //         imagedestroy($imgSource);         // baru hancurkan

    //         return response()->json([
    //             'status' => 'success',
    //             'label' => count($filteredPredictions) > 0 ? $finalLabel : 'sehat',
    //             'confidence' => $maxConfidence,
    //             'action' => $isPestisidaPump ? 'PUMP_ON' : 'PUMP_OFF',
    //             'image_result' => asset('storage/deteksi/' . $filename)
    //         ], 201);

    //     } catch (\Exception $e) {
    //         return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    //     }
    // }


}