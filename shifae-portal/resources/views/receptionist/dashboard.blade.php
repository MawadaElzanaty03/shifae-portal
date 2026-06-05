<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة تحكم الاستقبال - شفاء</title>
    <style>
        body { font-family: 'Tajawal', sans-serif; margin: 0; display: flex; background-color: #f4f7f6; }
        
        /* نفس ستايل الشريط الجانبي الخاص بالطبيب */
        .sidebar { width: 250px; background-color: #2c3e50; color: white; height: 100vh; position: fixed; right: 0; top: 0; padding-top: 20px; }
        .sidebar h3 { text-align: center; border-bottom: 1px solid #34495e; padding-bottom: 20px; }
        .sidebar a { display: block; color: white; padding: 15px 20px; text-decoration: none; transition: 0.3s; border-right: 4px solid transparent; }
        .sidebar a:hover { background-color: #34495e; border-right: 4px solid #007bb5; }
        
        /* المحتوى الرئيسي */
        .main-content { margin-right: 250px; padding: 40px; width: 100%; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        
        /* ستايل بسيط لفورم البحث */
        .search-form input { padding: 10px; width: 60%; border: 1px solid #ccc; border-radius: 4px; }
        .search-form button { padding: 10px 20px; background-color: #007bb5; color: white; border: none; border-radius: 4px; cursor: pointer; font-family: 'Tajawal', sans-serif; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h3>بوابة الاستقبال</h3>
        <a href="{{ route('receptionist.dashboard') }}">الصفحة الرئيسية</a>
<a href="{{ route('receptionist.dashboard') }}" onclick="if(document.getElementById('searchCard')) { showSearch(); return false; }">البحث عن مريض</a>

        <a href="{{ route('bookings.form') }}">إضافة حجز جديد</a>
        <form action="{{ route('logout') }}" method="POST" style="margin-top: 50px;">
            @csrf
            <button type="submit" style="width:100%; background: #c0392b; color: white; border: none; padding: 15px; cursor: pointer; font-weight: bold; font-family: 'Tajawal', sans-serif;">
                تسجيل الخروج
            </button>
        </form>
    </div>

    <div class="main-content">
        <!-- هنا سيتم عرض تفاصيل المريض عند إتمام البحث بنجاح -->
        @yield('content')

        <!-- هذا الجزء يظهر فقط في الواجهة الرئيسية قبل البحث -->
        @if(Route::is('receptionist.dashboard'))
        <div class="card" id="welcomeCard">
            <h1>أهلاً بك في بوابة الاستقبال</h1>
            <p>من خلال هذه اللوحة يمكنك البحث عن المرضى وإدارة الحجوزات.</p>
        </div>

        <div class="card search-form"   id="searchCard" style="display: none;">
           <h2>البحث عن مريض</h2>
            <!-- فورم البحث يرسل البيانات للباك اند الخاص بك -->
            <form action="{{ route('patients.search') }}" method="GET">
                <!-- تم تعديل النص التوضيحي وإضافة value لعدم فقدان النص -->
                <input type="text" name="searchQuery" required placeholder="أدخل اسم المريض أو رقم الهاتف هنا..." value="{{ old('searchQuery') }}">
                <button type="submit">بحث</button>
            </form>
            
            <!-- عرض أخطاء البحث القادمة من دالة searchPatient -->
            @if($errors->any())
                <div style="color: red; margin-top: 15px;">
                    {{ $errors->first() }}
                </div>
            @endif
        </div>
        @endif
    </div>

    <!-- سكريبت جافاسكريبت للتبديل بين الواجهات -->
    <script>
        function showWelcome() {
            var welcomeCard = document.getElementById('welcomeCard');
            var searchCard = document.getElementById('searchCard');
            if(welcomeCard) welcomeCard.style.display = 'block';
            if(searchCard) searchCard.style.display = 'none';
        }
        function showSearch() {
            var welcomeCard = document.getElementById('welcomeCard');
            var searchCard = document.getElementById('searchCard');
            if(welcomeCard) welcomeCard.style.display = 'none';
            if(searchCard) searchCard.style.display = 'block';
        }
    </script>

</body>
</html>
