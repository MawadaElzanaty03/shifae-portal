<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable=[
        'patientName',
        'phoneNumber',
        'dateOfBirth',
        'gender'

    ];
    public function bookings() {
    return $this->hasMany(Booking::class);
}
}
