<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
 protected $fillable = [
        'scheduleId',   // رقم الجدول الفريد
        'doctorId',     // لربط الجدول بالطبيب المعين
        'day',          // اليوم
        'startTime',    // وقت البداية
        'endTime',      // وقت النهاية
        'isAvailable',  // حالة التوفر (متاح/غير متاح)
    ];
protected $primaryKey = 'scheduleId';

private function checkAvailability($scheduleId)
{
    // البحث عن الموعد في جدول المواعيد المتاحة للأطباء
    $schedule = \App\Models\DoctorSchedule::find($scheduleId);

    // إذا كان الموعد موجوداً وحالته "متاح" (true) يرجع true
    return $schedule && $schedule->isAvailable;
}
}
