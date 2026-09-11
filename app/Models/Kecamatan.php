<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kecamatan extends Model
{
    protected $table = 'kecamatan';
    protected $fillable = ['nama'];

    /**
     * Get all of the desa for the Kecamatan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function desa(): HasMany
    {
        return $this->hasMany(Desa::class, 'kecamatan_id', 'id');
    }
}
