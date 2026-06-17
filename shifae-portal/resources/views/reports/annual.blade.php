<!DOCTYPE html>
<!-- واجهة التقارير السنوية -->
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التقارير السنوية - شفاء</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* التنسيقات العامة */
        body { font-family: 'Tajawal', sans-serif; margin: 0; display: flex; background-color: #f4f7f6; }
        
        /* الشريط الجانبي */
        .sidebar { width: 250px; background-color: #2c3e50; color: white; height: 100vh; position: fixed; right: 0; top: 0; padding-top: 20px; }
        .sidebar h3 { text-align: center; border-bottom: 1px solid #34495e; padding-bottom: 20px; }
        .sidebar a { display: block; color: white; padding: 15px 20px; text-decoration: none; transition: 0.3s; border-right: 4px solid transparent; }
        .sidebar a:hover, .sidebar a.active { background-color: #34495e; border-right: 4px solid #007bb5; }
        
        /* المحتوى الرئيسي */
        .main-content { margin-right: 250px; padding: 40px; width: 100%; box-sizing: border-box; }
        
        .header { margin-bottom: 30px; }
        .header h1 { color: #2c3e50; margin-bottom: 10px; }
        .header p { color: #7f8c8d; }
        
        /* الإحصائيات (الكروت) */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: transform 0.3s ease; display: flex; align-items: center; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon { background-color: #e0f2fe; color: #0284c7; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-left: 20px; font-weight: bold;}
        .stat-info h4 { margin: 0 0 5px 0; color: #64748b; font-size: 16px; font-weight: 500;}
        .stat-info p { margin: 0; color: #0f172a; font-size: 24px; font-weight: bold; }
        .stat-progress { margin-top: 15px; width: 100%; background-color: #e2e8f0; border-radius: 999px; height: 8px; overflow: hidden; display: flex;}
        .progress-bar-male { background-color: #0ea5e9; height: 100%; }
        .progress-bar-female { background-color: #ec4899; height: 100%; }
        .progress-bar-adults { background-color: #10b981; height: 100%; }
        .progress-bar-children { background-color: #f59e0b; height: 100%; }
        
        .ratio-labels { display: flex; justify-content: space-between; margin-top: 8px; font-size: 14px; color: #64748b; width: 100%;}
        
        /* الشهور الازدحاماً */
        .months-table { width: 100%; background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; border-collapse: collapse; }
        .months-table th, .months-table td { padding: 15px 20px; text-align: right; border-bottom: 1px solid #f1f5f9; }
        .months-table th { background-color: #f8fafc; color: #475569; font-weight: 600; }
        .months-table tr:hover { background-color: #f8fafc; }
        .months-table td { color: #334155; }
        .badge { background-color: #fee2e2; color: #ef4444; padding: 5px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        
        /* زر تسجيل الخروج */
        .logoutButton { width: 100%; background: #c0392b; color: white; border: none; padding: 15px; cursor: pointer; font-weight: bold; font-family: 'Tajawal', sans-serif; transition: background 0.3s;}
        .logoutButton:hover { background: #e74c3c; }
        
    </style>
</head>
<body>

    <!-- القائمة الجانبية -->
    <div class="sidebar">
        <h3>بوابة الإدارة</h3>
        <a href="{{ route('admin.dashboard') }}">الصفحة الرئيسية</a>
        <a href="{{ route('hr.employees.create') }}">إضافة موظف جديد</a>
        <a href="{{ route('admin.reports.annual') }}" class="active">التقارير السنوية</a>
        
        <form action="{{ route('logout') }}" method="POST" style="margin-top: 50px;">
            @csrf
            <button type="submit" class="logoutButton">تسجيل الخروج</button>
        </form>
    </div>

    <!-- المحتوى الرئيسي -->
    <div class="main-content">
        @if($errors->any())
            <div style="background-color: #fde8e8; color: #c53030; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="header">
            <h1>إحصائيات التقارير السنوية (لعام {{ $year }})</h1>
            <p>نظرة عامة على البيانات الإحصائية للمرضى والحجوزات لنهاية العام.</p>
        </div>

        @php
            $monthsNames = [
                1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل', 
                5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس', 
                9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
            ];
            
            $totalGender = $genderStats['maleCount'] + $genderStats['femaleCount'];
            $totalAge = $ageStats['childrenCount'] + $ageStats['adultsCount'];
        @endphp

        <div class="stats-grid">
            <!-- كرت نسبة الجنس -->
            <div class="stat-card" style="flex-direction: column; align-items: flex-start;">
                <div style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
                    <div class="stat-icon" style="background-color: #f0fdf4; color: #16a34a;">👥</div>
                    <div class="stat-info">
                        <h4>نسبة الذكور للإناث</h4>
                        <p>الإجمالي: {{ $totalGender }} مريض</p>
                    </div>
                </div>
                
                <div class="stat-progress">
                    <div class="progress-bar-male" style="width: {{ $maleRatio }}%;" title="ذكور: {{ $maleRatio }}%"></div>
                    <div class="progress-bar-female" style="width: {{ $femaleRatio }}%;" title="إناث: {{ $femaleRatio }}%"></div>
                </div>
                <div class="ratio-labels">
                    <span style="color: #0ea5e9;">الذكور ({{ $maleRatio }}%) - {{ $genderStats['maleCount'] }}</span>
                    <span style="color: #ec4899;">الإناث ({{ $femaleRatio }}%) - {{ $genderStats['femaleCount'] }}</span>
                </div>
            </div>

            <!-- كرت الفئة العمرية -->
            <div class="stat-card" style="flex-direction: column; align-items: flex-start;">
                <div style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
                    <div class="stat-icon" style="background-color: #fffbeb; color: #d97706;">👶</div>
                    <div class="stat-info">
                        <h4>نسبة الأطفال للكبار</h4>
                        <p>الإجمالي: {{ $totalAge }} مريض</p>
                    </div>
                </div>
                
                <div class="stat-progress">
                    <div class="progress-bar-adults" style="width: {{ $adultsRatio }}%;" title="كبار: {{ $adultsRatio }}%"></div>
                    <div class="progress-bar-children" style="width: {{ $childrenRatio }}%;" title="أطفال: {{ $childrenRatio }}%"></div>
                </div>
                <div class="ratio-labels">
                    <span style="color: #10b981;">الكبار ({{ $adultsRatio }}%) - {{ $ageStats['adultsCount'] }}</span>
                    <span style="color: #f59e0b;">الأطفال ({{ $childrenRatio }}%) - {{ $ageStats['childrenCount'] }}</span>
                </div>
            </div>
        </div>

        <!-- أكثر الشهور ازدحاماً -->
        <h3 style="color: #334155; margin-bottom: 15px;">أكثر الشهور ازدحاماً (لعام {{ $year }})</h3>
        <table class="months-table">
            <thead>
                <tr>
                    <th>الترتيب</th>
                    <th>الشهر</th>
                    <th>عدد الحجوزات</th>
                    <th>حالة الازدحام</th>
                </tr>
            </thead>
            <tbody>
                @forelse($busiestMonths as $index => $data)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $monthsNames[$data->month] ?? 'غير محدد' }}</td>
                        <td>{{ $data->totalBookings }} حجز</td>
                        <td>
                            @if($index === 0)
                                <span class="badge">الأكثر ازدحاماً 🔥</span>
                            @else
                                <span style="color: #64748b; font-size: 14px;">اعتيادي</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #94a3b8;">لا توجد بيانات حجوزات متاحة لهذه السنة.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>
</html>
