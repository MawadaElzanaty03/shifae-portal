{{-- resources/views/doctor/schedules/index.blade.php --}}
@extends('doctor.dashboard')//باش يستخدم القالب التابت لكل صفحات الدكتور

@section('content')
<style>
    /* التنسيقات العامة للحاوية */
    .schedule-container {
        max-width: 800px;
        margin: auto;
        padding: 20px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f8f9fa;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    .page-title {
        color: #0056b3;
        text-align: center;
        margin-bottom: 30px;
        font-weight: bold;
    }

    /* تنسيقات رسائل النجاح والخطأ */
    .success-msg {
        background-color: #d4edda;
        color: #155724;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        border: 1px solid #c3e6cb;
        text-align: center;
    }
    .error-msg {
        background-color: #f8d7da;
        color: #721c24;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        border: 1px solid #f5c6cb;
    }
    .error-list {
        margin: 0;
        padding-right: 20px;
    }

    /* تنسيقات بطاقة الموعد */
    .schedule-card {
        background-color: #ffffff;
        border: 1px solid #e0e0e0;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
    }

    /* تنسيقات النماذج (Forms) والحقول */
    .update-form {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-grow: 1;
    }
    .delete-form {
        margin: 0;
    }
    .time-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .time-label {
        color: #333;
        font-weight: bold;
    }
    .time-input {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        outline: none;
    }

    /* تنسيقات الأزرار */
    .btn-update {
        background-color: #28a745;
        color: white;
        padding: 8px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }
    .btn-delete {
        background-color: #dc3545;
        color: white;
        padding: 8px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }
    .day{
        width: 100%; margin-bottom: 10px; border-bottom: 1px dashed #eee; padding-bottom: 5px;
    }
    .day-span{
        background-color: #0056b3; color: white; padding: 2px 12px; border-radius: 15px; font-size: 0.9em; font-weight: bold;
    }
</style>

<div class="container schedule-container" dir="rtl">
    
    <h2 class="page-title">إدارة أوقات الدوام والمواعيد</h2>

    {{-- عرض رسائل النجاح --}}
    @if(session('success'))
        <div class="success-msg">
            {{ session('success') }}
        </div>
    @endif

    {{-- عرض رسائل الخطأ --}}
    @if($errors->any())
        <div class="error-msg">
            <ul class="error-list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- حلقة التكرار لعرض كل المواعيد --}}
    @foreach($mySchedules as $schedule)
        <div class="schedule-card">
            <div class="day" >
            <span class="day-span">
                {{ $schedule->day }} </span>
        </div>
            {{-- نموذج التعديل (Update Form) --}}
            <form action="{{ route('doctor.schedules.update', $schedule->scheduleId) }}" method="POST" class="update-form">
                @csrf
                @method('PUT') 
                
                <div class="time-group">
                    <label class="time-label">من الساعة:</label>
                    <input type="time" name="startTime" value="{{ $schedule->startTime }}" required class="time-input">
                </div>

                <div class="time-group">
                    <label class="time-label">إلى الساعة:</label>
                    <input type="time" name="endTime" value="{{ $schedule->endTime }}" required class="time-input">
                </div>

                <button type="submit" class="btn-update">تحديث</button>
            </form>

            {{-- نموذج الحذف (Delete Form) --}}
            <form action="{{ route('doctor.schedules.delete', $schedule->scheduleId) }}" method="POST" class="delete-form">
                @csrf
                @method('DELETE') 
                <button type="submit" onclick="return confirm('هل أنت متأكد من حذف هذا الموعد؟')" class="btn-delete">حذف</button>
            </form>
            
        </div>
    @endforeach

</div>
@endsection