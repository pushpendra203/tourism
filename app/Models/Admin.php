<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
// use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model implements AuthenticatableContract
{
    // Use the HasFactory trait to enable the model factory features
    use HasFactory;

    // Use the Authenticatable trait to implement authentication methods
    use Authenticatable;

    // Assuming the table name is 'admin'
    protected $table = 'admin';

    protected $fillable = [
        'admin_name',
        'admin_email',
        'username',
        'password',
    ];
}
