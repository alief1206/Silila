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
        'lsd',
        'hutan',
        'luas',
        'ket',
        'irigasi_pr',
        'kewenangan',
        'ip',
        'prod',
        'irigasi',
        'kondisigab',
        'kontamgab',
        'polru',
        'asalrtr',
        'fpgab_1',
        'ba',
        'tipehak',
        'luascea_hm',
        'golluas_hm',
        'golluas_hm2',
        'hmkeluar',
        'investasi',
    ];
}
