<?php
namespace App\Factories;
use App\Models\Doctor;

class ChildBehaviorRecommendation implements DoctorRecommendationInterface 
{
    public function recommend($age, $gender) 
    {
        // نبحث عن طبيب بتخصص معين 
        $doctor = Doctor::where('specialty', 'تعديل سلوك')
            ->whereHas('user', function($query) use ($gender) {
                $query->where('gender', $gender);
            })->first();

        // إذا لم نجد طبيب من نفس الجنس، نأتي بأي طبيب تعديل سلوك متاح
        if (!$doctor) {
            $doctor = Doctor::where('specialty', 'تعديل سلوك')->first();
        }
        
        return $doctor;
    }
}