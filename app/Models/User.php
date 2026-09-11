<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;

class User extends Authenticatable
{
    use Notifiable, AuthenticationLoggable;
    protected $table = 'users';
    protected $fillable = ['email', 'password', 'nama', 'foto', 'nip', 'telp', 'role'];
}
