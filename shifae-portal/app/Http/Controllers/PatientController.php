<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Strategies\DoctorSearchStrategy;
use App\Strategies\ReceptionistSearchStrategy;

class PatientController extends Controller
{
    public function searchPatient(Request $request){
         try {
            // 1. التحقق من صحة البيانات
            $validatedData = $request->validate([
                'searchQuery' => 'required|string|max:255',
            ]);
            $searchQuery = $validatedData['searchQuery'];
            $currentUserRole = Auth::user()->userRole;
            // 2. تطبيق الـ Strategy Pattern
            $searchStrategy = null;
            if ($currentUserRole === 'doctor') {
                $searchStrategy = new DoctorSearchStrategy();
            } elseif ($currentUserRole === 'receptionist') {
                $searchStrategy = new ReceptionistSearchStrategy();
            } else {
                return back()->withErrors(['accessError' => 'ليس لديك صلاحية للبحث.']);
            }
           //البحث سيتم حسب نوع الاستراتجية (صلاحيته ) طبيب ام موظف استقبال
              $patientData = $searchStrategy->executeSearch($searchQuery);

           // التحقق في حال لم يتم العثور على المريض في قاعدة البيانات
            if (!$patientData) {
                return back()->withErrors(['searchError' => 'لم يتم العثور على المريض.'])->withInput();
            }
            // إذا كان المستخدم طبيباً، نوجهه لصفحة تفاصيل المريض الخاصة بالطبيب
            if ($currentUserRole === 'doctor') {
                return view('doctor.patient_details', [
                    'patientData' => $patientData
                ]);
            } 
            // إذا كان المستخدم موظف استقبال، نوجهه لصفحة تفاصيل المريض الخاصة بالاستقبال
            else {
                return view('receptionist.patient_details', [
                    'patientData' => $patientData
                ]);
            }
        } catch (\Exception $searchException) { // إغلاق الـ Try واصطياد الأخطاء
            // في حال حدوث أي خطأ برمجي أو في الاتصال، نعود للخلف مع رسالة خطأ
            return back()->withErrors(['systemError' => 'حدث خطأ غير متوقع أثناء البحث. الرجاء المحاولة مجدداً.']);
        }


        
    }
    
}

