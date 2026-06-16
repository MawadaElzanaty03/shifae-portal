<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مرحباً بك في نظام شفاء</title>
</head>
<body style="font-family: Tahoma, sans-serif; direction: rtl; text-align: right; background-color: #f4f7f6; padding: 20px;">
    
    <div style="background-color: #ffffff; padding: 30px; border-radius: 8px; max-width: 600px; margin: auto;">
        <h2 style="color: #0288d1;">مرحباً بك، {{ $employee->fullName }}!</h2>
        <p>يسعدنا انضمامك إلى فريق عمل <strong>نظام شفاء</strong>.</p>
        <p>تم إنشاء حسابك بنجاح، ويمكنك الآن تسجيل الدخول إلى النظام باستخدام البيانات التالية:</p>
        
        <div style="background-color: #e1f5fe; padding: 15px; margin: 20px 0;">
            <p><strong>اسم المستخدم:</strong> {{ $employee->userName }}</p>
            <p><strong>كلمة المرور المبدئية:</strong> <span>{{ $tempPassword }}</span></p>
        </div>
        
        <p>ملاحظة: نرجو منك تغيير كلمة المرور المبدئية فور تسجيل دخولك لأول مرة.</p>
        
        <hr style="border: 0; border-top: 1px solid #eee; margin-top: 30px;">
        <p style="font-size: 12px; color: #777; text-align: center;">إدارة الموارد البشرية - نظام شفاء</p>
    </div>

</body>
</html>
