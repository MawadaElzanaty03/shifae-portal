<?php





namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // لأن الدكاترة مخزنين في جدول المستخدمين
use App\Models\Schedule;
use Exception;

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
}
