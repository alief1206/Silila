<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataLsd extends Model
{
    use HasFactory;
    protected $table = 'data_lsd';
    protected $guarded = ['id'];
    protected $fillable = [
        'geometri_id',
        'status_lsd',
        'hutan',
        'luas',
        'irigasi',
        'kewenangan',
        'polru',
        'ip',
        'prod',
        'tipehak',
        'investasi',
        'properties',
        'properties_hash',
        'ba',
        'luascea_hm',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function geometri()
    {
        return $this->belongsTo(Geometri::class, 'geometri_id', 'id');
    }
}
