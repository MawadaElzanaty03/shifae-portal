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
     */
    public function login(Request $request)
    {
        // Try/catch block MUST exist for all methods to handle unexpected errors
        try {
            // Validate the incoming request data
            $request->validate([
                'userName' => 'required|string',
                'password' => 'required|string',
            ]);

            // Extract credentials into a descriptive camelCase variable
            $userCredentials = [
                'userName' => $request->userName,
                'password' => $request->password
            ];

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
}