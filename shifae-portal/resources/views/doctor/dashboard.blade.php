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
        /* ---------------- تنسيقات الجدول فقط ---------------- */
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #dee2e6; padding: 15px; text-align: center; vertical-align: middle; }
        .table-light th { background-color: #f8f9fa; color: #212529; font-weight: bold; }
        .table-hover tbody tr:hover { background-color: #f1f5f8; transition: 0.2s; }
        
        /* ---------------- تنسيق وقت الجلسة (Badge) ---------------- */
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 14px; display: inline-block; }
        .bg-secondary { background-color: #6c757d; color: white; }

        /* ---------------- تنسيق زر الإجراء (Button) ---------------- */
        .btn { padding: 8px 16px; border-radius: 5px; font-size: 14px; text-decoration: none; display: inline-block; transition: 0.3s; border: none; cursor: pointer; }
        .btn-success { background-color: #198754; color: white; }
        .btn-success:hover { background-color: #157347; }

        /* ---------------- تنسيق رسالة "لا توجد مواعيد" (Alert) ---------------- */
        .alert { padding: 20px; border-radius: 8px; margin-top: 20px; border: 1px solid transparent; }
        .alert-info { background-color: #cff4fc; color: #055160; border-color: #b6effb; }
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
            <p>من خلال هذه اللوحة يمكنك إدارة جدول مواعيدك في عيادة شفائي بطرابلس.</p>
    @if($todaysAppointments->isEmpty())
        <div class="alert alert-info text-center" style="font-size: 18px; font-weight: bold;">
            لا توجد مواعيد مجدولة لهذا اليوم.
        </div>
    @else
     <table class="table table-hover table-bordered text-center align-middle">
    <thead class="table-light">
        <tr>
            <th>وقت الجلسة</th>
            <th>اسم المريض</th>
            <th>العمر</th>
            <th>رقم الهاتف</th>
            <th>الإجراء</th> </tr>
    </thead>
    <tbody>
        @foreach($todaysAppointments as $appointment)
            <tr>
                <td>
                    <span class="badge bg-secondary">
                        {{ \Carbon\Carbon::parse($appointment->appointmentDate)->format('h:i A') }}
                    </span>
                </td>
                
                <td>{{ $appointment->patient->patientName ?? 'غير متوفر' }}</td>
                
               <td>
    @if(isset($appointment->patient->dateOfBirth))
        {{ \Carbon\Carbon::parse($appointment->patient->dateOfBirth)->age }} سنة
    @else
        -
    @endif
</td>
                
                <td>{{ $appointment->patient->phoneNumber ?? '-' }}</td>
                
                <td>
                    <a href="{{ route('record.show', $appointment->patientId) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-file-medical"></i> فتح السجل الطبي
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
        </div>
        @endif
    </div>
   @endif
</body>
</html>