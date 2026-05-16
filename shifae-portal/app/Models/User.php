<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $primaryKey = 'userId';
    protected $fillable = [
        'userName',    
        'fullName',
        'password',
        'userRole',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * 
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    public function doctor()
{
    return $this->hasOne(Doctor::class, 'userId');
}
    
}
