<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;

// Class name starts with an uppercase letter
class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     * دالة تسجيل الدخول
     */
    public function login(Request $request)
    {
        // Try/catch block MUST exist for all methods to handle unexpected errors
        try {
            // Validate the incoming request data
            $request->validate([// التأكد من أن المستخدم قام بإدخال اسم المستخدم وكلمة المرور
                'userName' => 'required|string',
                'password' => 'required|string',
            ]);

            // Extract credentials into a descriptive camelCase variable
            $userCredentials = [//تجميع بيانات الدخول
                'userName' => $request->userName,
                'password' => $request->password
            ];
// محاولة تسجيل الدخول باستخدام البيانات اللي دخلها المستخدم
            // Attempt to authenticate the user using the provided credentials
            if (Auth::attempt($userCredentials)) {
                
                // Authentication passed, retrieve the authenticated user object
                $loggedInUser = Auth::user();

                // Regenerate session to prevent session fixation attacks
                $request->session()->regenerate();

                // Check user role to redirect them to the correct dashboard (Doctor or Admin)
                if ($loggedInUser->role === 'doctor') {
                    return redirect()->intended('/doctor-dashboard');
                } else {
                    return redirect()->intended('/admin-dashboard');
                }
            }

            // If authentication fails, return back with an error message
            return back()->withErrors([
                'userName' => 'بيانات الدخول غير صحيحة، يرجى المحاولة مرة أخرى.',
            ])->onlyInput('userName');

        } catch (Exception $systemException) {
            // Catch any unexpected system errors during login
            return back()->withErrors([
                'systemError' => 'حدث خطأ في النظام أثناء محاولة تسجيل الدخول: ' . $systemException->getMessage(),
            ]);
        }
    }
    /**
     * Log the user out of the application.
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