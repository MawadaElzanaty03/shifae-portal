<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Patient;
use App\Models\Booking;

class ReportController extends Controller
{
    /**
     * واجهة التقارير السنوية للإدارة
     */
    public function annualReport(Request $request)
    {
        // سأقوم بتجهيز الكلاس واستدعاء الموديلات التي سنحتاجها (Patient و Booking)
        // وسنقوم بكتابة الخوارزميات هنا في الخطوات القادمة
        
        return view('reports.annual');
    }
}
