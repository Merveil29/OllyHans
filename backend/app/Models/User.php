<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $timestamps = false;

    protected $fillable = [
        'user_nom',
        'user_prenom',
        'user_email',
        'user_login',
        'user_tel',
        'user_password',
        'user_activation_code',
        'user_email_status',
        'user_otp',
        'etape',
        'image_user',
    ];

    protected $hidden = [
        'user_password',
        'user_activation_code',
        'user_otp',
    ];

    protected $casts = [
        'user_password' => 'hashed',
    ];

    // Accessors
    public function getNameAttribute()
    {
        return $this->user_nom . ' ' . $this->user_prenom;
    }

    public function getEmailAttribute()
    {
        return $this->user_email;
    }
}
