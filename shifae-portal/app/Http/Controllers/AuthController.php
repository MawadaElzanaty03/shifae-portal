<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;

class AuthController extends Controller
{
    /**
     * دالة تسجيل الدخول
     */
    public function login(Request $request)
    {
        // استخدام try و catch إجباري عشان لو صار خطأ النظام ما يوقفش
        try {
            // التأكد من أن المستخدم قام بإدخال اسم المستخدم وكلمة المرور
            $request->validate([
                'userName' => 'required|string',
                'password' => 'required|string',
            ]);

            // تجميع بيانات الدخول في متغير
            $userLoginData = [
                'userName' => $request->userName,
                'password' => $request->password
            ];

            // محاولة تسجيل الدخول باستخدام البيانات اللي دخلها المستخدم
            if (Auth::attempt($userLoginData)) {
                
                // لو البيانات صحيحة، نجيب بيانات المستخدم اللي سجل دخوله
                $authenticatedUser = Auth::user();

                // تجديد الجلسة (Session) كخطوة حماية للنظام
                $request->session()->regenerate();

                // التحقق من نوع المستخدم (طبيب أو مدير) لتوجيهه للصفحة المناسبة
                if ($authenticatedUser->userRole === 'Doctor') {
                    return redirect()->intended('/doctor/add-schedule');
                } else {
                    return redirect()->intended('/admin-dashboard');
                }
            }

            // لو كلمة المرور أو اسم المستخدم خطأ، نرجعه لصفحة الدخول مع رسالة
            return back()->withErrors([
                'userName' => 'بيانات الدخول غير صحيحة، يرجى المحاولة مرة أخرى.',
            ])->onlyInput('userName');

        } catch (Exception $loginError) {
            // التقاط أي خطأ غير متوقع في النظام وإظهاره للمستخدم
            return back()->withErrors([
                'systemError' => 'حدث خطأ في النظام أثناء محاولة تسجيل الدخول: ' . $loginError->getMessage(),
            ]);
        }
    }

    /**
     * دالة تسجيل الخروج
     */
    public function logout(Request $request)
    {
        // استخدام try/catch لتجنب توقف النظام لو صار خطأ وقت الخروج
        try {
            // تسجيل خروج المستخدم من النظام
            Auth::logout();

            // إلغاء الجلسة الحالية عشان ما يقدر حد يستخدمها بعدين
            $request->session()->invalidate();

            // إعادة إنشاء رمز الحماية كخطوة أمنية
            $request->session()->regenerateToken();

            // إرجاع المستخدم للصفحة الرئيسية بعد الخروج
            return redirect('/')->with('status', 'تم تسجيل الخروج بنجاح.');

        } catch (Exception $logoutError) {
            // اصطياد أي خطأ ممكن يصير وقت تسجيل الخروج
            return back()->withErrors([
                'logoutError' => 'حدث خطأ أثناء محاولة تسجيل الخروج: ' . $logoutError->getMessage(),
            ]);
        }
    }
}