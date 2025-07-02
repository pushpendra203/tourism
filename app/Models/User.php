<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'user_name',
        'image',
        'email',
        'password',
        'phone',
        'country',
        'city',
        'state',
        'status'
    ];

    protected $casts = [
        //  'email_verified_at' => 'datetime',
          'password' => 'hashed',
    ];
}
