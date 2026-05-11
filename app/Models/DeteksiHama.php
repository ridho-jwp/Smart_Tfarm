<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeteksiHama extends Model
{
    protected $primaryKey = 'id_analisis';
    protected $table = 'hamadetection';
    protected $fillable = [
        'image_url',
        'confidence',
        'is_pestisida_pump',
        'label_hama',
        'side_left',
        'side_right',
    ];
    public $timestamps = true;

}