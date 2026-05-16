<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة تحكم الطبيب - شفاء</title>
    <style>
        body { font-family: 'Tajawal', sans-serif; margin: 0; display: flex; background-color: #f4f7f6; }
        
        /* الشريط الجانبي */
        .sidebar { width: 250px; background-color: #2c3e50; color: white; height: 100vh; position: fixed; right: 0; top: 0; padding-top: 20px; }
        .sidebar h3 { text-align: center; border-bottom: 1px solid #34495e; padding-bottom: 20px; }
        .sidebar a { display: block; color: white; padding: 15px 20px; text-decoration: none; transition: 0.3s; border-right: 4px solid transparent; }
        .sidebar a:hover { background-color: #34495e; border-right: 4px solid #007bb5; }
        
        /* المحتوى الرئيسي */
        .main-content { margin-right: 250px; padding: 40px; width: 100%; }
        
        .logout-btn { background-color: #e74c3c; margin-top: 50px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    </style>
    <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->

</head>
<body>

    <div class="sidebar">
        <h3>بوابة الطبيب</h3>
        <a href="{{ route('doctor.dashboard') }}">الرئيسية</a>
        <a href="{{ route('doctor.schedule.create') }}">إضافة مواعيد جديدة</a>
        <a href="{{ route('doctor.schedules.index') }}">تعديل وحذف المواعيد</a>
        
        <form action="{{ route('logout') }}" method="POST" style="margin-top: 50px;">
            @csrf
            <button type="submit" style="width:100%; background: #c0392b; color: white; border: none; padding: 15px; cursor: pointer; font-weight: bold;">
                تسجيل الخروج
            </button>
        </form>
    </div>

    <div class="main-content">
        @yield('content')

        @if(Route::is('doctor.dashboard'))
        <div class="card">
            <h1>أهلاً دكتور {{ auth()->user()->fullName }}</h1>
            <p>من خلال هذه اللوحة يمكنك إدارة جدول مواعيدك في عيادة شفاء بطرابلس.</p>
        </div>
        @endif
    </div>

</body>
</html>