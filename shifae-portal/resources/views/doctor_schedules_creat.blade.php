<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة شفائي - إضافة أوقات الدوام</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #eef2f5; padding: 40px 20px; margin: 0; }
        .form-container { background: white; padding: 30px; border-radius: 10px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-top: 5px solid #007bb5; }
        .page-title { text-align: center; color: #007bb5; margin-bottom: 25px; margin-top: 0; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #2c3e50; }
        input[type="time"] { width: 100%; padding: 12px; border: 1px solid #dcdfe6; border-radius: 6px; font-family: 'Tajawal', sans-serif; font-size: 15px; box-sizing: border-box; }
        
        /* تنسيق أنيق لأيام الأسبوع */
        .checkbox-group { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 10px; }
        .checkbox-item { 
            display: flex; 
            align-items: center; 
            font-weight: 500; 
            font-size: 15px; 
            cursor: pointer; 
            background: #f8f9fa; 
            padding: 10px 15px; 
            border-radius: 6px; 
            border: 1px solid #dcdfe6; 
            transition: 0.3s;
            user-select: none;
        }
        .checkbox-item:hover { background: #e2e6ea; border-color: #adb5bd; }
        .checkbox-item input[type="checkbox"] { margin-left: 8px; transform: scale(1.2); cursor: pointer; }
        
        /* الأزرار والتنبيهات */
        .btn-submit { background-color: #28a745; color: white; border: none; padding: 14px 20px; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: bold; width: 100%; margin-top: 10px; transition: 0.3s; }
        .btn-submit:hover { background-color: #218838; }
        
        .alert-success { background-color: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .alert-error { background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #f5c6cb; }
        .alert-error ul { margin: 0; padding-right: 20px; }
    </style>
</head>
<body>

    <div class="form-container">
        <h2 class="page-title">إضافة أوقات الدوام والمواعيد</h2>

        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('doctor.schedule.add') }}" method="POST">
            @csrf <div class="form-group">
                <label>اختر أيام العمل (يمكنك اختيار أكثر من يوم):</label>
                <div class="checkbox-group">
                    <label class="checkbox-item"><input type="checkbox" name="days[]" value="الأحد"> الأحد</label>
                    <label class="checkbox-item"><input type="checkbox" name="days[]" value="الإثنين"> الإثنين</label>
                    <label class="checkbox-item"><input type="checkbox" name="days[]" value="الثلاثاء"> الثلاثاء</label>
                    <label class="checkbox-item"><input type="checkbox" name="days[]" value="الأربعاء"> الأربعاء</label>
                    <label class="checkbox-item"><input type="checkbox" name="days[]" value="الخميس"> الخميس</label>
                    <label class="checkbox-item"><input type="checkbox" name="days[]" value="الجمعة"> الجمعة</label>
                    <label class="checkbox-item"><input type="checkbox" name="days[]" value="السبت"> السبت</label>
                </div>
                </div>

            <div class="form-group">
                <label for="startTime">من الساعة:</label>
                <input type="time" name="startTime" id="startTime" required>
            </div>

            <div class="form-group">
                <label for="endTime">إلى الساعة:</label>
                <input type="time" name="endTime" id="endTime" required>
            </div>

            <button type="submit" class="btn-submit">حفظ المواعيد</button>
        </form>
    </div>

</body>
</html>