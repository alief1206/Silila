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
        'hutan',
        'luas',
        'ba',
        'luascea_hm',
    ];

    public function geometri()
    {
        return $this->belongsTo(Geometri::class, 'geometri_id', 'id');
    }
}
