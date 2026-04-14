<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pestisida extends Model
{
    protected $table = 'pestisida_config';
    protected $fillable = [
        'id_preset',
        'dosis',
        'deskripsi',
    ];
    public $timestamps = true;
}
