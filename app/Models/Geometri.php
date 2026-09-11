<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Geometri extends Model
{
    protected $table = 'geometri';
    protected $fillable = ['object_id', 'desa_id', 'kp2b', 'ket', 'luas', 'koordinat', 'tipe'];
    protected $primaryKey = 'id';
    /**
     * Get the desa that owns the Geometri
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class, 'desa_id', 'id');
    }
    public function riwayat(): BelongsTo
    {
        return $this->belongsTo(Riwayat::class, 'geometri_id', 'id');
    }
    public function lsd(): HasMany
    {
        return $this->HasMany(DataLsd::class, 'geometri_id', 'id');
    }
    public function lp2b(): HasMany
    {
        return $this->HasMany(DataLp2b::class, 'geometri_id', 'id');
    }
}
