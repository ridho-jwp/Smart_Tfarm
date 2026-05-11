<?php
// app/Models/NutrisiDose.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NutrisiDosis extends Model
{
    protected $table = 'nutrisi_dosis';

    protected $fillable = [
        'device_id',
        'ppm_saat_ini',
        'ppm_target',
        'ppm_deficit',
        'volume_tandon_liter',
        'ppm_per_ml',
        'ml_per_detik',
        'dosis_ml',
        'durasi_detik',
        'status',
        'dispatched_at',
        'done_at',
    ];

    protected function casts(): array
    {
        return [
            'ppm_saat_ini' => 'decimal:2',
            'ppm_target' => 'decimal:2',
            'ppm_deficit' => 'decimal:2',
            'volume_tandon_liter' => 'decimal:2',
            'ppm_per_ml' => 'decimal:4',
            'ml_per_detik' => 'decimal:4',
            'dosis_ml' => 'decimal:2',
            'durasi_detik' => 'decimal:2',
            'dispatched_at' => 'datetime',
            'done_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    // ── Helper: hitung dosis dari parameter ─────────────────
    /**
     * Rumus:
     *   deficit_ppm  = ppm_target - ppm_saat_ini
     *   dosis_ml     = deficit_ppm / (ppm_per_ml_per_liter * volume_tandon_liter)
     *   durasi_detik = dosis_ml / ml_per_detik
     *
     * Contoh:
     *   ppm_saat_ini = 212, ppm_target = 400
     *   volume_tandon = 100 L, ppm_per_ml_per_liter = 0.7
     *   deficit = 188 ppm
     *   dosis   = 188 / (0.7 * 100) = 2.686 mL
     *   durasi  = 2.686 / 1.0       = 2.686 detik
     */
    public static function hitungDosis(
        float $ppmSaatIni,
        float $ppmTarget,
        float $volumeTandonLiter,
        float $ppmPerMlPerLiter,
        float $mlPerDetik
    ): array {
        $deficit = max(0.0, $ppmTarget - $ppmSaatIni);

        // Hindari division by zero
        if ($ppmPerMlPerLiter <= 0 || $volumeTandonLiter <= 0 || $mlPerDetik <= 0) {
            return ['dosis_ml' => 0, 'durasi_detik' => 0, 'deficit' => $deficit];
        }

        $dosisMl = $deficit / ($ppmPerMlPerLiter * $volumeTandonLiter);
        $durasiDetik = $dosisMl / $mlPerDetik;

        return [
            'deficit' => $deficit,
            'dosis_ml' => round($dosisMl, 2),
            'durasi_detik' => round($durasiDetik, 2),
        ];
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
    public function isDispatched(): bool
    {
        return $this->status === 'dispatched';
    }
}