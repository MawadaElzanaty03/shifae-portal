<?php
namespace App\Factories;

class RecommendationFactory 
{
    public static function createRecommendation($age): DoctorRecommendationInterface 
    {
        try {
            // اختيار المصنع المناسب حسب الفئة العمرية
            if ($age < 18) {
                return new ChildBehaviorRecommendation();
            } else {
                return new AdultGeneralRecommendation();
            }
        } catch (\Exception $e) {
            // في حالة الخطأ نعيد تصنيف البالغين كإجراء افتراضي آمن
            \Log::error('خطأ في مصنع التوصيات الرئيسي: ' . $e->getMessage());
            return new AdultGeneralRecommendation();
        }
    }
}