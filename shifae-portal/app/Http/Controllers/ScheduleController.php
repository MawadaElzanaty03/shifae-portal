<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\DoctorSchedule;
use Illuminate\Support\Facades\Auth;



class ScheduleController extends Controller
{
  // دالة لعرض صفحة إضافة المواعيد
    public function create()
    {
        return view('doctor.schedules.create');
    }
    // دالة لاستقبال البيانات وحفظها في قاعدة البيانات
    public function store(Request $request)
    {
        // 1. التحقق من صحة البيانات المدخلة
        $request->validate([
            'day' => 'required|string',
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i|after:startTime',
        ]);

        // 2. الحصول على حساب الطبيب المرتبط بالمستخدم الحالي
        $doctor = Auth::user()->doctor;

        // 3. تخزين الموعد في قاعدة البيانات
        DoctorSchedule::create([
            'doctorId' => $doctor->doctorId,
            'day' => $request->day,
            'startTime' => $request->startTime,
            'endTime' => $request->endTime,
            'isAvailable' => true,
        ]);

        // 4. إرجاع الطبيب للصفحة مع رسالة نجاح
        return redirect()->back()->with('success', 'تمت إضافة الموعد بنجاح!');
}}
