<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
 protected $fillable = [
        'patientId',
        'doctorId',
        'appointmentDate',
        'roomNumber',
        'status',
    ];

public function patient() {
    return $this->belongsTo(Patient::class, 'patientId');
}

public function doctor() {
    return $this->belongsTo(User::class, 'user_id');
}

public static function checkRoomAvailability($dateTime, $room)
{
    // التحقق من تعارض الحجرات لمنع أي تعارض مكاني بنسبة 100%
    $collision = self::where('appointmentDate', $day)
                     ->where('roomNumber', $room)
                     ->where('status', 'confirmed')
                     ->exists();

    return !$collision;
}
}
