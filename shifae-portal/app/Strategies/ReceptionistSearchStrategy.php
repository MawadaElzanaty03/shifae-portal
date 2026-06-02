<?php

namespace App\Strategies;

use App\Models\Patient;

class ReceptionistSearchStrategy implements PatientSearchStrategyInterface
{
    public function executeSearch($searchQuery)
    {
        // البحث عن المريض
        $targetPatient = Patient::where('patientName', 'LIKE', '%' . $searchQuery . '%')
                                ->orWhere('phoneNumber', $searchQuery)
                                ->first();

        if ($targetPatient) {
            // الاستقبال لا يرى السجلات، يرى المواعيد القادمة فقط
            // نستخدم علاقة bookings التي أنشأتيها في مودل Patient
            $targetPatient->load(['bookings' => function($query) {
                $query->where('status', 'pending');
            }]); 
        }

        return $targetPatient;
    }
}
