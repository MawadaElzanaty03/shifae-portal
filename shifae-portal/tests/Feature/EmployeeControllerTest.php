<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class EmployeeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // إنشاء مستخدم لإدارة الموارد البشرية
        $this->adminUser = User::create([
            'userName' => 'adminUser',
            'fullName' => 'HR Manager',
            'email' => 'hr@test.com',
            'password' => bcrypt('password123'),
            'userRole' => 'Admin', 
        ]);
    }

    // اختبار عرض صفحة إضافة الموظف بنجاح
    public function test_can_view_add_employee_form_successfully()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/hr/employees/create');

        $response->assertStatus(200);
        $response->assertViewIs('hr.add-employee');
    }

        // اختبار رفض إضافة الموظف إذا كانت البيانات ناقصة أو خاطئة
    public function test_add_employee_validation_fails_with_invalid_data()
    {
        $this->actingAs($this->adminUser);

        // إرسال بيانات ناقصة عمداً (بدون ملف الشهادة، وبإيميل غير صالح)
        $invalidData = [
            'fullName' => 'Ahmed Ali',
            'userName' => 'ahmed123',
            'email' => 'invalid-email-format', // خطأ: ليس بصيغة إيميل
            'userRole' => 'Doctor',
            // خطأ: لم نقم بإرفاق ملف certificate
        ];

        // إرسال طلب POST
        // المسار حسب web.php هو: /hr/employees/store
        $response = $this->post('/hr/employees/store', $invalidData);

        // التأكد من أن النظام التقط الأخطاء وأعاد المستخدم
        $response->assertSessionHasErrors(['email', 'certificate']);
        
        // التأكد من عدم إنشاء المستخدم في قاعدة البيانات
        $this->assertDatabaseMissing('users', [
            'userName' => 'ahmed123',
        ]);
    }

        // اختبار إضافة موظف بصلاحية طبيب بنجاح (وتجربة رفع الملفات وإرسال الإيميل)
    public function test_add_doctor_successfully_with_file_and_email()
    {
        $this->actingAs($this->adminUser);

        // 1. إيقاف الإرسال الفعلي للإيميلات لتسريع الاختبار (Mocking)
        \Illuminate\Support\Facades\Mail::fake();
        // 2. إعداد مجلد تخزين وهمي لكي لا تتراكم الملفات الحقيقية
        \Illuminate\Support\Facades\Storage::fake('public');

        // 3. إنشاء ملف شهادة PDF وهمي بحجم 100 كيلوبايت
        $fakeCertificate = \Illuminate\Http\UploadedFile::fake()->create('degree.pdf', 100, 'application/pdf');

        // 4. تجهيز البيانات الكاملة
        $validData = [
            'fullName' => 'Dr. Khalid',
            'userName' => 'drkhalid',
            'email' => 'khalid@test.com',
            'userRole' => 'Doctor',
            'specialty' => 'باطنة', // هذا ليدخل في فرع إنشاء الطبيب
            'profitPercentage' => 70.00,
            'certificate' => $fakeCertificate, // إرفاق الملف الوهمي
        ];

        // 5. إرسال الطلب
        $response = $this->post('/hr/employees/store', $validData);

        // التأكد من نجاح العملية والتحويل
        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // جلب المستخدم من قاعدة البيانات للتحقق
        $user = \App\Models\User::where('userName', 'drkhalid')->first();
        $this->assertNotNull($user);

        // 6. التأكد من إنشاء سجل الطبيب (تغطية الجملة الشرطية الأولى if Doctor)
        $this->assertDatabaseHas('doctors', [
            'userId' => $user->userId,
            'specialty' => 'باطنة',
        ]);

        // 7. التأكد من حفظ الشهادة في جدول المستندات (تغطية الجملة الشرطية الثانية if hasFile)
        $this->assertDatabaseHas('documents', [
            'userId' => $user->userId,
            'documentName' => 'degree.pdf',
        ]);

        // 8. التأكد من أن النظام قام باستدعاء كلاس الإيميل (تغطية أمر إرسال البريد)
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\WelcomeEmployeeMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

        // اختبار إضافة موظف استقبال (التأكد من عدم دخوله لشرط إنشاء الطبيب)
    public function test_add_receptionist_successfully_without_creating_doctor_record()
    {
        $this->actingAs($this->adminUser);

        \Illuminate\Support\Facades\Mail::fake();
        \Illuminate\Support\Facades\Storage::fake('public');
        
        $fakeCertificate = \Illuminate\Http\UploadedFile::fake()->create('receptionist_cv.pdf', 100, 'application/pdf');

        $validData = [
            'fullName' => 'Ali Ahmed',
            'userName' => 'ali_rec',
            'email' => 'ali@test.com',
            'userRole' => 'Receptionist', // لاحظي: اخترنا موظف استقبال
            'certificate' => $fakeCertificate,
        ];

        // إرسال الطلب
        $response = $this->post('/hr/employees/store', $validData);

        $response->assertStatus(302);
        
        // جلب المستخدم لمعرفة رقمه
        $user = \App\Models\User::where('userName', 'ali_rec')->first();

        // 1. الأهم: التأكد من أن النظام **لم يقم** بإنشاء سجل في جدول الأطباء!
        $this->assertDatabaseMissing('doctors', [
            'userId' => $user->userId,
        ]);

        // 2. التأكد من إنشاء الحساب بشكل طبيعي
        $this->assertDatabaseHas('users', [
            'userName' => 'ali_rec',
            'userRole' => 'Receptionist',
        ]);

        // 3. التأكد من أن الشهادة تم حفظها
        $this->assertDatabaseHas('documents', [
            'userId' => $user->userId,
            'documentName' => 'receptionist_cv.pdf',
        ]);
    }


    // اختبار الدخول لمسار الـ Catch في دالة الإضافة
    public function test_add_employee_throws_exception()
    {
        $this->actingAs($this->adminUser);

        // محاكاة حدوث خطأ أثناء التشفير (Hash) لندخل في مسار الـ catch
        \Illuminate\Support\Facades\Hash::shouldReceive('make')
            ->andThrow(new \Exception('Mocked DB Error'));

        \Illuminate\Support\Facades\Storage::fake('public');
        $fakeCertificate = \Illuminate\Http\UploadedFile::fake()->create('degree.pdf', 100, 'application/pdf');

        $validData = [
            'fullName' => 'Ali Ahmed',
            'userName' => 'ali_error',
            'email' => 'ali_error@test.com',
            'userRole' => 'Receptionist',
            'certificate' => $fakeCertificate,
        ];

        $response = $this->post('/hr/employees/store', $validData);

        // التأكد من أن النظام التقط الخطأ (Catch) وأعاد توجيهنا برسالة خطأ
        $response->assertStatus(302);
        $response->assertSessionHas('error', 'حدث خطأ غير متوقع أثناء حفظ البيانات.');
    }

    
}
