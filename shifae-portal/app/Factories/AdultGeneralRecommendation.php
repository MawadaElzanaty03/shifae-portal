<?php
namespace App\Factories;
use App\Models\Doctor;

class AdultGeneralRecommendation implements DoctorRecommendationInterface 
{
    public function recommend($age, $gender) 
    {
        // البالغين يوجهون لأخصائي عام بنفس الجنس لو متوفر
        $doctor = Doctor::where('specialty', 'اخصائي عام')
            ->whereHas('user', function($query) use ($gender) {
                $query->where('gender', $gender);
            })->first();

        if (!$doctor) {
            $doctor = Doctor::where('specialty', 'اخصائي عام')->first();
        }

        return $doctor;
    }
}