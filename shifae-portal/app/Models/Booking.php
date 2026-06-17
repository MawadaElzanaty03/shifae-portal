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
         'amount_paid',
         'payment_method'
    ];

public function patient() {
    return $this->belongsTo(Patient::class, 'patientId');
}

public function doctor() {
    return $this->belongsTo(Doctor::class, 'doctorId');
}

public function room()
    {
        try {
            // المعامل الثاني هو اسم العمود الموجود في جدول الحجوزات (المفتاح الأجنبي)
            // المعامل الثالث هو اسم العمود في جدول الحجرات إذا لم يكن id
            return $this->belongsTo(Room::class, 'roomNumber', 'id'); 
            
          
            
        } catch (\Exception $exceptionError) {
            \Illuminate\Support\Facades\Log::error('Error in Booking room relation: ' . $exceptionError->getMessage());
            return null;
        }
    }

public static function checkRoomAvailability($dateTime, $room)
{
    // التحقق من تعارض الحجرات لمنع أي تعارض مكاني بنسبة 100%
    $collision = self::where('appointmentDate', $dateTime)
                     ->where('roomNumber', $room)
                     ->whereIn('status', ['Confirmed', 'Pending'])
                     ->exists();

    return !$collision;
}
    public static function getBusiestMonths()
    {
        // دالة مخصصة لجلب أكثر الشهور ازدحاماً
        return self::selectRaw('MONTH(appointmentDate) as month, count(*) as totalBookings')->whereYear('created_at',$year)//تحديد سنة الحجوزات
                   ->groupBy('month')
                   ->orderBy('totalBookings', 'desc')
                   ->get();
    }


}
