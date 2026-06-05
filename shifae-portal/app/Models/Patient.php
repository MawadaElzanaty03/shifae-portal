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
    //علاقة المريض بالحجوزات
    public function bookings() {
   return $this->hasMany(Booking::class, 'patientId');
}

// علاقة المريض بالسجلات الطبية
    public function records() {
        return $this->hasMany(Record::class, 'patientId');
    }
}
