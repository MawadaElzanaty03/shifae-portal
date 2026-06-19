<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Document;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmployeeMail;
use Illuminate\Support\Str;
use Exception;


class EmployeeController extends Controller
{
    // عرض صفحة إضافة موظف
    public function createEmployeeForm()
    {
        // إرجاع واجهة نموذج إضافة الموظف
        return view('hr.add-employee');
    }

    //دالة إضافة موظف جديد
    public function addEmployee(Request $request){
        //التحقق من صحة المدخلات الرئيسية
         $request->validate([
            'fullName'    => 'required|string|max:255',
            'userName'    => 'required|string|max:255|unique:users,userName',
            'email'       => 'required|email|unique:users,email',
            'userRole'    => 'required|in:Doctor,Receptionist',
            'certificate' => 'required|file|mimes:pdf,jpg,png,jpeg|max:2048', 
        ]);
        try {
            // توليد كلمة مرور عشوائية مبدئية
            $randomPassword = Str::random(8);
            // إنشاء حساب المستخدم
            $newEmployeeUser = User::create([
                'fullName' => $request->fullName,
                'userName' => $request->userName,
                'email'    => $request->email,
                'password' => Hash::make($randomPassword),
                'userRole' => $request->userRole,
            ]);
            
            // إنشاء سجل مخصص في حال كان الموظف طبيباً
            if ($request->userRole === 'Doctor') {
                Doctor::create([
                    'userId'           => $newEmployeeUser->userId,
                    'specialty'        => $request->specialty ?? 'عام',
                    'profitPercentage' => $request->profitPercentage ?? 60.00,
                ]);
            }
            // معالجة ورفع المرفقات
            if ($request->hasFile('certificate')) {
                $uploadedCertificate = $request->file('certificate');
                $savedFilePath = $uploadedCertificate->store('documents', 'public');
                Document::create([
                    'userId'       => $newEmployeeUser->userId,
                    'documentName' => $uploadedCertificate->getClientOriginalName(),
                    'filePath'     => $savedFilePath,
                ]);
            }
                         // إرسال الإيميل الترحيبي بكلمة المرور
            Mail::to($newEmployeeUser->email)->send(new WelcomeEmployeeMail($newEmployeeUser, $randomPassword));

            return redirect()->back()->with('success', 'تمت إضافة الموظف وإرسال بيانات الدخول إلى بريده الإلكتروني بنجاح.');
        } catch (Exception $e) {
            \Log::error('خطأ أثناء تسجيل الموظف الجديد: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ غير متوقع أثناء حفظ البيانات.');
        }
    }
    }


