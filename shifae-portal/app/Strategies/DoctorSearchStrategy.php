<?php

namespace App\Strategies;

use App\Models\Patient;

class DoctorSearchStrategy implements PatientSearchStrategyInterface
{
    public function executeSearch($searchQuery)
    {
        // البحث عن المريض
        $targetPatient = Patient::where('patientName', 'LIKE', '%' . $searchQuery . '%')
                                ->orWhere('phoneNumber', $searchQuery)
                                ->first();

        if ($targetPatient) {
            // الطبيب يحتاج لرؤية السجلات الطبية (بناءً على علاقة records)
            $targetPatient->load('records'); 
        }

        return $targetPatient;
    }
}
