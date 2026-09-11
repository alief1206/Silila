<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Desa extends Model
{
    protected $table = 'desa';
    protected $fillable = ['nama', 'kecamatan_id'];

    /**
     * Get the kecamatan that owns the Desa
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id', 'id');
    }
}
