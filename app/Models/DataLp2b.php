<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataLp2b extends Model
{
    use HasFactory;
    protected $table = 'data_lp2b';
    protected $primaryKey = 'id';
    protected $fillable = ['geometri_id', 'desa_id', 'kp2b', 'ket', 'luas'];
    public function geometri(): BelongsTo
    {
        return $this->BelongsTo(Geometri::class, 'geometri_id', 'id');
    }
}
