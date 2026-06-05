<?php
namespace App\Factories;
use App\Models\Doctor;

class AdultGeneralRecommendation implements DoctorRecommendationInterface 
{
    public function recommend($age, $gender) 
    {
        try {
            // البالغين يوجهون لأخصائي عام بنفس الجنس لو متوفر
            $doctor = Doctor::where('specialty', 'اخصائي عام')
                ->whereHas('user', function($query) use ($gender) {
                    $query->where('gender', $gender);
                })->first();

            // إذا لم يتوفر من نفس الجنس، نختار أي أخصائي عام آخر
            if (!$doctor) {
                $doctor = Doctor::where('specialty', 'اخصائي عام')->first();
            }

            return $doctor;
        } catch (\Exception $e) {
            // التقاط الخطأ لتجنب تعطل التطبيق
            \Log::error('خطأ في مصنع توصية البالغين: ' . $e->getMessage());
            return null;
        }
    }
}