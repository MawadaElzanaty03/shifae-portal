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
    public static function getGenderStatistics($year){//دالة مخصصة لجلب عدد الذكور و الاناث
    return [
        'maleCount' => self::where('gender', 'male')->whereYear('created_at',$year)->count(),
        'femaleCount' => self::where('gender', 'female')->whereYear('created_at',$year)->count(),
    ];
}
   public static function getAgeStatistics($year)
{
//دالة مخصصه لجلب عدد الاطفال و الكبار
    $eighteenYearsAgo = \Carbon\Carbon::now()->subYears(15);//يعتبر المريض طفل اذا كان تحت ال 15
    return [
        'childrenCount' => self::whereDate('dateOfBirth', '>', $eighteenYearsAgo)->whereYear('created_at',$year)->count(),
        'adultsCount' => self::whereDate('dateOfBirth', '<=', $eighteenYearsAgo)->whereYear('created_at',$year)->count(),
    ];
}
}

