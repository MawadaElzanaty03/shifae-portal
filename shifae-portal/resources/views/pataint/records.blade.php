<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>السجل الطبي - بوابة الطبيب</title>
    <style>
        body { font-family: 'Tajawal', sans-serif; margin: 0; display: flex; background-color: #f4f7f6; }
        
        /* الشريط الجانبي */
        .sidebar { width: 250px; background-color: #2c3e50; color: white; height: 100vh; position: fixed; right: 0; top: 0; padding-top: 20px; }
        .sidebar h3 { text-align: center; border-bottom: 1px solid #34495e; padding-bottom: 20px; }
        .sidebar a { display: block; color: white; padding: 15px 20px; text-decoration: none; transition: 0.3s; border-right: 4px solid transparent; }
        .sidebar a:hover { background-color: #34495e; border-right: 4px solid #007bb5; }
        
        /* المحتوى الرئيسي */
        .main-content { margin-right: 250px; padding: 40px; width: 100%; box-sizing: border-box; }
        
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        
        /* تنسيقات عناصر الإدخال والفورم */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-family: 'Tajawal', sans-serif; font-size: 16px; }
        
        /* الأزرار */
        .btn { padding: 12px 24px; border-radius: 5px; font-size: 16px; text-decoration: none; display: inline-block; transition: 0.3s; border: none; cursor: pointer; font-weight: bold; }
        .btn-primary { background-color: #007bb5; color: white; }
        .btn-primary:hover { background-color: #005f8d; }
        .btn-success { background-color: #28a745; color: white; }
        .btn-success:hover { background-color: #218838; }
        
        /* التنبيهات */
        .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; font-weight: bold; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .patient-info { background-color: #e9ecef; padding: 15px; border-radius: 6px; margin-bottom: 25px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h3>بوابة الطبيب</h3>
        <a href="{{ route('doctor.dashboard') }}">الرئيسية</a>
        <a href="{{ route('doctor.schedule.create') }}">إضافة مواعيد جديدة</a>
        <a href="{{ route('doctor.schedules.index') }}">تعديل وحذف المواعيد</a>
        
        <form action="{{ route('logout') }}" method="POST" style="margin-top: 50px;">
            @csrf
            <button type="submit" style="width:100%; background: #c0392b; color: white; border: none; padding: 15px; cursor: pointer; font-weight: bold; font-family: 'Tajawal';">
                تسجيل الخروج
            </button>
        </form>
    </div>

 <div class="main-content">
<div class="container mt-4">
    <h2>إدارة السجلات الطبية للمريض</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(!isset($record))
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            إنشاء سجل طبي جديد
        </div>
        <div class="card-body">
            <form action="{{ route('record.create') }}" method="POST">
                @csrf
                <input type="hidden" name="patientId" value="{{ $patient->id }}">

                <div class="form-group mb-3">
                    <label for="diagnosis" class="form-label">التشخيص المبدئي:</label>
                    <input type="text" name="diagnosis" id="diagnosis" class="form-control" placeholder="أدخل تشخيص الحالة هنا..." value="{{ old('diagnosis') }}" required>
                </div>

                <button type="submit" class="btn btn-primary">حفظ التشخيص وفتح السجل</button>
            </form>
        </div>
    </div>
    @endif

    @if(isset($record))
    <div class="card">
        <div class="card-header bg-success text-white">
            الملاحظات السريرية وتفاصيل الجلسة
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>التشخيص الحالي:</strong> {{ $record->diagnosis }}
            </div>

            <form action="{{ route('record.updateNotes', $record->recordId) }}" method="POST">
                @csrf
                @method('PUT') <div class="form-group mb-3">
                    <label for="clinicalNotes" class="form-label">تفاصيل الجلسة والملاحظات:</label>
                    <textarea name="clinicalNotes" id="clinicalNotes" class="form-control" rows="6" placeholder="اكتب ملاحظات الجلسة هنا..." required>{{ old('clinicalNotes', $record->clinicalNotes) }}</textarea>
                </div>

                <button type="submit" class="btn btn-success">حفظ السجل الطبي (حفظ كمسودة/نهائي)</button>
            </form>
        </div>
    </div>
    @endif

</div>
