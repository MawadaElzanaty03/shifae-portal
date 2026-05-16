<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $primaryKey = 'doctorId';
    protected $fillable = [
        'userId',            // ضروري يكون هنا باش تربطيه باليوزر اللي انشأتيه
        'specialty',         // التخصص 
        'profitPercentage',  // نسبة الأرباح 
    ];
    public function schedules()
{
    // هكي تخبري لارفل ان الدكتور عنده مواعيد مرتبطة بيه
    return $this->hasMany(DoctorSchedule::class, 'doctorId'); 
}
//علاقة الوراثة الطبيب يرث من المستخدم
public function user()
    {
      
        return $this->belongsTo(User::class, 'userId','userId');
    }
}
