<?php
namespace App\Factories;

interface DoctorRecommendationInterface 
{
    public function recommend($age, $gender);
}