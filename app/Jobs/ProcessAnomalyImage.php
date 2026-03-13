<?php

namespace App\Jobs;

use App\Models\Anomaly;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAnomalyImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 7;

    public function __construct(
        public Anomaly $anomaly
    ) {}

    public function handle(): void
    {
        Log::info("ProcessAnomalyImage: Memproses anomaly ID {$this->anomaly->id}");

        // Placeholder — integrasi model AI di sini
        // Contoh: TensorFlow Lite, Roboflow, atau model custom
        $this->anomaly->update([
            'type' => 'leaf_spot',
            'description' => 'Terdeteksi bercak daun pada tanaman pakcoy (dummy result)',
            'severity' => 'medium',
            'confidence' => 0.87,
        ]);

        Log::info("ProcessAnomalyImage: Selesai memproses anomaly ID {$this->anomaly->id}");
    }
}
