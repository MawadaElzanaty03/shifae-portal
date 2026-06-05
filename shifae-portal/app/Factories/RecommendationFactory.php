<?php
namespace App\Factories;

class RecommendationFactory 
{
    public static function createRecommendation($age): DoctorRecommendationInterface 
    {
        if ($age < 18) {
            return new ChildBehaviorRecommendation();
        } else {
            return new AdultGeneralRecommendation();
        }
    }
}