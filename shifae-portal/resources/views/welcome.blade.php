<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة شفائي - الصفحة الرئيسية</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        /* التنسيقات العامة للصفحة */
        body { 
            font-family: 'Tajawal', sans-serif; 
            background-color: #eef2f5; 
            margin: 0; 
        }

        /* شريط التصفح العلوي */
        .navbar { 
            background-color: #007bb5; 
            padding: 15px 50px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            color: white; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .navbar h2 { margin: 0; font-size: 24px; }

        /* زر تسجيل الدخول في الشريط العلوي */
        .navbar-btn { 
            color: #007bb5; 
            text-decoration: none; 
            padding: 8px 20px; 
            background-color: white; 
            border-radius: 6px; 
            font-weight: bold; 
            transition: 0.3s;
        }

        .navbar-btn:hover { background-color: #f4f7f6; }

        /* الحاوية الرئيسية للمحتوى */
        .container { 
            max-width: 1000px; 
            margin: 40px auto; 
            padding: 20px; 
        }

        .page-title {
            text-align: center; 
            color: #2c3e50; 
            margin-bottom: 40px;
        }

        /* بطاقة عرض بيانات الطبيب */
        .doctor-card { 
            background: white; 
            border-radius: 10px; 
            padding: 25px; 
            margin-bottom: 25px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); 
            border-right: 6px solid #28a745; 
        }

        .doctor-name { 
            font-size: 22px; 
            color: #34495e; 
            margin-top: 0; 
        }

        /* تصميم جدول المواعيد */
        .schedule-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px; 
        }

        .schedule-table th, .schedule-table td { 
            border: 1px solid #dcdfe6; 
            padding: 12px; 
            text-align: center; 
        }

        .schedule-table th { 
            background-color: #f8f9fa; 
            color: #2c3e50; 
        }

        /* تنسيق زر الحجز */
        .btn-book { 
            background-color: #28a745; 
            color: white; 
            border: none; 
            padding: 8px 16px; 
            border-radius: 5px; 
            cursor: pointer; 
            text-decoration: none; 
            font-size: 14px;
            transition: 0.3s;
        }

        .btn-book:hover { background-color: #218838; }

        /* رسالة عدم وجود بيانات */
        .no-data { 
            color: #95a5a6; 
            text-align: center;
            padding: 10px;
        }
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
                <h3 class="doctor-name"> {{ $doctor->fullName }} </h3>
                
                @if($doctor->schedules->where('isAvailable', true)->count() > 0)
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th>اليوم</th>
                                <th>من</th>
                                <th>إلى</th>
                                <th>الحجز</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($doctor->schedules->where('isAvailable', true) as $schedule)
                                <tr>
                                    <td>{{ $schedule->day }}</td>
                                    <td>{{ $schedule->startTime }}</td>
                                    <td>{{ $schedule->endTime }}</td>
                                    <td>
                                        <a href="#" class="btn-book">حجز موعد</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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