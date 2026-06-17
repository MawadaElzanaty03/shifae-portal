<!DOCTYPE html>
<!-- واجهة لوحة تحكم الإدارة -->
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة تحكم الإدارة - شفاء</title>
    <style>
        /* التنسيقات العامة مطابقة لتصميم لوحات الاستقبال والطبيب */
        body { font-family: 'Tajawal', sans-serif; margin: 0; display: flex; background-color: #f4f7f6; }
        
        /* الشريط الجانبي */
        .sidebar { width: 250px; background-color: #2c3e50; color: white; height: 100vh; position: fixed; right: 0; top: 0; padding-top: 20px; }
        .sidebar h3 { text-align: center; border-bottom: 1px solid #34495e; padding-bottom: 20px; }
        .sidebar a { display: block; color: white; padding: 15px 20px; text-decoration: none; transition: 0.3s; border-right: 4px solid transparent; }
        .sidebar a:hover { background-color: #34495e; border-right: 4px solid #007bb5; }
        
        /* المحتوى الرئيسي */
        .main-content { margin-right: 250px; padding: 40px; width: 100%; }
        .dashboardCard { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        
        /* زر تسجيل الخروج */
        .logoutButton { width: 100%; background: #c0392b; color: white; border: none; padding: 15px; cursor: pointer; font-weight: bold; font-family: 'Tajawal', sans-serif; }
    </style>
</head>
<body>

    <!-- القائمة الجانبية (Sidebar) -->
    <div class="sidebar">
        <h3>بوابة الإدارة</h3>
        
        <!-- رابط الصفحة الرئيسية للوحة الإدارة -->
        <a href="{{ route('admin.dashboard') }}">الصفحة الرئيسية</a>
        
        <!-- رابط توجيه المدير لإضافة موظف جديد -->
        <a href="{{ route('hr.employees.create') }}">إضافة موظف جديد</a>
             
        <!-- رابط التقارير السنوية -->
        <a href="{{ route('admin.reports.annual') }}">التقارير السنوية</a>

        <!-- زر تسجيل الخروج-->
        <form action="{{ route('logout') }}" method="POST" style="margin-top: 50px;">
            @csrf
            <button type="submit" class="logoutButton">
                تسجيل الخروج
            </button>
        </form>
    </div>

    <!-- منطقة عرض المحتوى الرئيسي -->
    <div class="main-content">
        <!-- طباعة أي أخطاء قادمة من النظام -->
        @if($errors->any())
            <div style="background-color: #fde8e8; color: #c53030; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- بطاقة الترحيب في الإدارة -->
        <div class="dashboardCard">
            <h1>أهلاً بك في بوابة الإدارة</h1>
            <p>من خلال هذه اللوحة، تمتلك الصلاحيات الكاملة لإدارة النظام، وبإمكانك الآن إضافة وتوثيق بيانات الموظفين الجدد.</p>
        </div>
    </div>

</body>
</html>
