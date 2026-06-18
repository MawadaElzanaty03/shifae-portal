<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    // تفريغ الداتابيز الوهمية بعد كل تيست عشان ما تصيرش لخبطة
    use RefreshDatabase;
  //دالة تسجيل الدخول بصلاحية طبيب
    public function test_doctor_can_login_successfully()
    {
        //  نجهز بيانات دكتور وهمي للاختبار 
        $doctorUser = User::create([
            'fullName' => 'Doctor Test',
            'email' => 'doctor@test.com',
            'userName' => 'doctor_test',
            'password' => Hash::make('12345678'),
            'userRole' => 'Doctor',
        ]);

        //  نبعت ريكويست تسجيل الدخول
        $response = $this->post('/login', [
            'userName' => 'doctor_test',
            'password' => '12345678',
        ]);

        // نتأكد انه حوله لصفحة الدكتور صح، وانه تسجل دخوله في النظام
        $response->assertRedirect(route('doctor.dashboard'));
        $this->assertAuthenticatedAs($doctorUser);
    }
    // نسجيل دحول للاستقبال بنجاح
        public function test_receptionist_can_login_successfully()
    {
        //  نجهز بيانات موظف استقبال وهمي
        $receptionistUser = User::create([
            'fullName' => 'Receptionist Test',
            'email' => 'receptionist@test.com',
            'userName' => 'receptionist_test',
            'password' => Hash::make('12345678'),
            'userRole' => 'Receptionist',
        ]);

        // نبعت ريكويست تسجيل الدخول
        $response = $this->post('/login', [
            'userName' => 'receptionist_test',
            'password' => '12345678',
        ]);

        // نتأكد انه حوله لصفحة الاستقبال صح
        $response->assertRedirect(route('receptionist.dashboard'));
        $this->assertAuthenticatedAs($receptionistUser);
    }
   //دالة اختبار الدخول بنجاح للادمن
    public function test_admin_can_login_successfully()
    {
        //  نجهز بيانات مدير وهمي 
        $adminUser = User::create([
            'fullName' => 'Admin Test',
            'email' => 'admin@test.com',
            'userName' => 'admin_test',
            'password' => Hash::make('12345678'),
            'userRole' => 'Admin',
        ]);

        //  نبعت ريكويست تسجيل الدخول
        $response = $this->post('/login', [
            'userName' => 'admin_test',
            'password' => '12345678',
        ]);

        //  نتأكد انه حوله لصفحة المدير صح
        $response->assertRedirect('/admin-dashboard');
        $this->assertAuthenticatedAs($adminUser);
    }
    //اختبار تسجيل دخول خاطئ

    public function test_user_cannot_login_with_wrong_password()
    {
        //  نجهز مستخدم وهمي
        User::create([
            'fullName' => 'Wrong Password Test',
            'email' => 'wrong@test.com',
            'userName' => 'user_test',
            'password' => Hash::make('correct_password'),
            'userRole' => 'Admin',
        ]);

        // نبعت ريكويست بكلمة مرور خاطئة (للتأكد من فرع الفشل)
        $response = $this->post('/login', [
            'userName' => 'user_test',
            'password' => 'wrong_password',
        ]);

        // نتأكد ان النظام منعه ورجعه لنفس الصفحة مع رسالة خطأ، والتأكد أنه غير مسجل دخول (Guest)
        $response->assertSessionHasErrors('userName');
        $this->assertGuest(); 
    }
     //دالة اختبار تسجيل هروج ناجح
        public function test_user_can_logout_successfully()
    {
        // نجهز مستخدم وهمي ونجعله مسجل دخول في النظام للاختبار
        $user = User::create([
            'fullName' => 'Logout Test',
            'email' => 'logout@test.com',
            'userName' => 'logout_test',
            'password' => Hash::make('12345678'),
            'userRole' => 'Doctor',
        ]);
        
        $this->actingAs($user); // هذه الدالة جاهزة في لارفيل لمحاكاة تسجيل الدخول

        // نبعت ريكويست تسجيل الخروج
        $response = $this->post('/logout');

        // نتأكد انه حوله للصفحة الرئيسية، وانه فعلا طلع من النظام وصار Guest
        $response->assertRedirect('/');
        $this->assertGuest();
    }
    //اختبار الفالديشن 
           public function test_validation_fails_when_login_data_is_missing()
    {
        //  التنفيذ: نبعت ريكويست ببيانات فارغة
        $response = $this->post('/login', [
            'userName' => '',
            'password' => '',
        ]);

        //لان ف الكود مدايره ان الخطاء يبداء سيستم ايرور
        $response->assertSessionHasErrors('systemError');
        $this->assertGuest(); 
    }
    //اختبار الكاتش في حالة حدث خطاء اثناء تسجيل الخروج   
    public function test_exception_is_handled_during_logout()
    {
       
        \Illuminate\Support\Facades\Auth::shouldReceive('logout')
            ->once()
            ->andThrow(new \Exception('خطأ متعمد للاختبار'));

        $response = $this->post('/logout');

      
        $response->assertSessionHasErrors('logoutError');
    }
}