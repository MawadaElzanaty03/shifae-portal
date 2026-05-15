<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة شفائي - الصفحة الرئيسية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #eef2f5; margin: 0; }
        .navbar { background-color: #007bb5; padding: 15px 50px; display: flex; justify-content: space-between; align-items: center; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .navbar h2 { margin: 0; font-size: 24px; }
        .navbar-btn { color: #007bb5; text-decoration: none; padding: 8px 20px; background-color: white; border-radius: 6px; font-weight: bold; transition: 0.3s; }
        .container { max-width: 1000px; margin: 40px auto; padding: 20px; }
        .page-title { text-align: center; color: #2c3e50; margin-bottom: 40px; }

        /* بطاقة الطبيب */
        .doctor-card { background: white; border-radius: 10px; padding: 25px; margin-bottom: 25px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-right: 6px solid #007bb5; }
        .doctor-name { font-size: 22px; color: #34495e; margin-top: 0; }

        /* الجدول */
        .schedule-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .schedule-table th, .schedule-table td { border: 1px solid #dcdfe6; padding: 12px; text-align: center; }
        .schedule-table th { background-color: #f8f9fa; color: #2c3e50; }

        /* منطقة الأزرار المنفصلة أسفل الجدول */
        .doctor-actions { 
            display: flex; 
            justify-content: flex-start; 
            gap: 15px; 
            margin-top: 20px; 
            padding-top: 15px; 
            border-top: 1px solid #eee; 
        }

        .btn-main { padding: 10px 25px; border-radius: 6px; font-weight: bold; text-decoration: none; font-size: 15px; transition: 0.3s; cursor: pointer; border: none; }
        
        /* زر الحجز (أخضر) */
        .btn-booking { background-color: #28a745; color: white; }
        .btn-booking:hover { background-color: #218838; }

        /* زر التعديل والحذف (رمادي/أزرق فاتح) */
        .btn-manage { background-color: #b41515; color: white; }
        .btn-manage:hover { background-color: #c80808; }

        .no-data { color: #95a5a6; text-align: center; padding: 10px; }
    </style>
</head>
<body>

    <div class="navbar">
        <h2>بوابة شفائي</h2>
        <a href="{{ route('login') }}" class="navbar-btn">تسجيل الدخول للكادر</a>
    </div>

    <div class="container">
        <h1 class="page-title">جدول الأطباء والمواعيد المتاحة</h1>

        @forelse($doctorsList as $doctor)
            <div class="doctor-card">
                <h3 class="doctor-name"> {{ $doctor->user->fullName }} </h3>
                
                @if($doctor->schedules->where('isAvailable', true)->count() > 0)
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th>اليوم</th>
                                <th>من</th>
                                <th>إلى</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($doctor->schedules->where('isAvailable', true) as $schedule)
                                <tr>
                                    <td>{{ $schedule->day }}</td>
                                    <td>{{ $schedule->startTime }}</td>
                                    <td>{{ $schedule->endTime }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- هنا الأزرار في مكان منفصل -->
                    <div class="doctor-actions"><a href="{{ route('bookings.form') }}" class="btn-main btn-booking">حجز موعد جديد</a>
                      <a href="{{ route('bookings.create') }}" class="btn-main btn-booking">حجز موعد جديد</a>
                        <a href="#" class="btn-main btn-manage">تعديل أو حذف حجز سابق</a>
                    </div>
                @else
                    <p class="no-data">لا توجد مواعيد متاحة حالياً.</p>
                @endif
            </div>
        @empty
            <p style="text-align: center;">لا يوجد أطباء مسجلين حالياً.</p>
        @endforelse
    </div>

</body>
</html>