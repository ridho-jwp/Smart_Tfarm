<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SprayManualState extends Model
{
    protected $table = 'spray_manual_state';

    protected $fillable = [
        'auto_mode',
        'manual_kiri',
        'manual_kanan',
        'updated_by',
    ];

    protected $casts = [
        'auto_mode'    => 'boolean',
        'manual_kiri'  => 'boolean',
        'manual_kanan' => 'boolean',
    ];

    /**
     * Ambil state global (singleton — selalu satu baris ID=1).
     */
    public static function getState(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            ['auto_mode' => true, 'manual_kiri' => false, 'manual_kanan' => false]
        );
    }
}
