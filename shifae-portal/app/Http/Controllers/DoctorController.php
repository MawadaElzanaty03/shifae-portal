<?php





namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // لأن الدكاترة مخزنين في جدول المستخدمين
use App\Models\Schedule;
use Exception;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class DoctorController extends Controller
{
    /**
     * دالة لعرض جدول الأطباء في الواجهة الرئيسية
     */
    public function viewSchedule()
    {
       
        try {
            // جلب قائمة الأطباء مع جداول المواعيد الخاصة بهم
            
            $doctorsList = User::where('userRole', 'doctor')
                                ->with('schedules') // جلب الجداول المرتبطة بكل دكتور
                                ->get();

            // إرسال البيانات لواجهة المريض الرئيسية (welcome)
            return view('welcome', compact('doctorsList'));

        } catch (Exception $searchError) {
            // في حال حدوث أي خطأ في قاعدة البيانات، نعرض رسالة بسيطة
            return back()->withErrors([
                'error' => 'عذراً، تعذر تحميل جدول الأطباء حالياً: ' . $searchError->getMessage()
            ]);
        }
    }
  


public function dashboard()
{
    // جلب معرف الطبيب المسجل الدخول
    $doctorId = Auth::id(); 
    
    // جلب مواعيد اليوم وترتيبها حسب الوقت تصاعدياً
    $todaysAppointments = Booking::with('patient') // لجلب بيانات المريض المرتبطة بالحجز
        ->where('doctorId', $doctorId)
        ->whereDate('appointmentDate', Carbon::today()) // تصفية المواعيد لتاريخ اليوم فقط
        ->orderBy('appointmentDate', 'asc')
        ->get();

    return view('doctor.dashboard', compact('todaysAppointments'));
}
}
