<?php

namespace App\Strategies;

interface PatientSearchStrategyInterface
{
    // الدالة الموحدة التي ستستخدمها كل الصلاحيات للبحث
    public function executeSearch($searchQuery);
}
