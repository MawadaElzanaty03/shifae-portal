<?php
namespace App\Factories;
use App\Models\Doctor;

class ChildBehaviorRecommendation implements DoctorRecommendationInterface 
{
    public function recommend($age, $gender) 
    {
        try {
            // نبحث عن طبيب بتخصص معين ونحاول مطابقة الجنس
            $doctor = Doctor::where('specialty', 'تعديل سلوك')
                ->whereHas('user', function($query) use ($gender) {
                    $query->where('gender', $gender);
                })->first();

            // إذا لم نجد طبيب من نفس الجنس، نأتي بأي طبيب تعديل سلوك متاح
            if (!$doctor) {
                $doctor = Doctor::where('specialty', 'تعديل سلوك')->first();
            }
            
            return $doctor;
        } catch (\Exception $e) {
            // تسجيل الخطأ وإرجاع قيمة افتراضية لتفادي التوقف
            \Log::error('خطأ في استعلام طبيب الأطفال: ' . $e->getMessage());
            return null;
        }
    }
}