<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Riwayat extends Model
{
    use HasFactory;
    protected $table = 'riwayat';
    protected $fillable = ['user_id', 'geometri_id', 'koordinat'];

    // protected $primaryKey = 'id';
    public function geometri(): HasMany
    {
        return $this->hasMany(Geometri::class, 'id', 'geometri_id');
    }
}
