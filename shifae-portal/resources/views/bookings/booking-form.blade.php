<style>
    /* تنسيقات الجو الفايح لبوابة شفائي */
body {
    background-color: #f0f4f8; /* لون خلفية هادئ ومريح للعين */
    direction: rtl;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

form {
    background: #ffffff;
    max-width: 550px;
    margin: 40px auto;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05); /* ظل ناعم يعطي عمق */
    border: 1px solid #e1e8ed;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.95rem;
}

/* تحسين شكل حقول الإدخال */
.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #edf2f7;
    border-radius: 10px;
    background-color: #fdfdfd;
    transition: all 0.3s ease;
    box-sizing: border-box; /* لضمان عدم خروج الحقول عن الإطار */
}

.form-control:focus {
    outline: none;
    border-color: #3498db;
    background-color: #fff;
    box-shadow: 0 0 8px rgba(52, 152, 219, 0.2);
}

/* تنسيق خاص لقائمة اختيار الطبيب والموعد */
select.form-control {
    cursor: pointer;
    appearance: none; /* إزالة سهم المتصفح الافتراضي */
    background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%232c3e50' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: left 15px center; /* السهم جهة اليسار لأن النص عربي */
    background-size: 15px;
}

/* زر تأكيد الحجز اللحظي */
.btn-primary {
    width: 100%;
    background-color: #27ae60; /* لون أخضر طبي يوحي بالثقة */
    color: white;
    padding: 14px;
    border: none;
    border-radius: 10px;
    font-size: 1.1rem;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s transform 0.2s;
    margin-top: 10px;
}

.btn-primary:hover {
    background-color: #219150;
    transform: translateY(-2px); /* حركة خفيفة عند التمرير */
}

.btn-primary:active {
    transform: translateY(0);
}

/* تحسين شكل المجموعات في القائمة المنسدلة */
optgroup {
    font-weight: bold;
    color: #2c3e50;
    background: #f8f9fa;
}

option {
    padding: 10px;
    color: #34495e;
    background: #fff;
}
    </style>
    {{-- كود إظهار رسالة النجاح الخضراء --}}
@if(session('success'))
    <div style="background-color: #27ae60; color: white; padding: 15px; border-radius: 10px; margin: 15px auto; max-width: 550px; text-align: center; font-weight: bold; box-shadow: 0 4px 15px rgba(39, 174, 96, 0.2);">
        {{ session('success') }}
    </div>
@endif

{{-- لإظهار أي أخطاء قد تحدث في الإدخال مستقبلاً \--}}
@if($errors->any())
    <div style="background-color: #e74c3c; color: white; padding: 15px; border-radius: 10px; margin: 15px auto; max-width: 550px; box-shadow: 0 4px 15px rgba(231, 76, 60, 0.2);">
        <ul style="margin: 0; padding-right: 20px; font-weight: 600;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form action="{{ route('bookings.store') }}" method="POST">
    @csrf 
    
    <div class="form-group">
        <label>الاسم الكامل</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="form-group">
        <label>رقم الهاتف</label>
        <input type="text" name="phoneNumber" class="form-control" required>
    </div>


   <div class="form-group">
        <label>تاريخ الميلاد</label>
        <input type="date" name="dateOfBirth" class="form-control" required>
    </div>
    <div class="form-group">
        <label>الجنس</label>
        <select name="gender" class="form-control" required>
            <option value="">-- اختر الجنس --</option>
            <option value="male">ذكر</option>
            <option value="female">أنثى</option>
        </select>
    </div>
    <div class="form-group">
        <label for="appointment_data">اختر الطبيب والموعد المتاح من الفترات القادمة:</label>
        
        @if(count($availableSlots) > 0)
            <select name="appointment_data" id="appointment_data" class="form-control" required>
                <option value="">-- اختر الطبيب والموعد المناسب --</option>
                
                @foreach($availableSlots as $doctorName => $slots)
                    <optgroup label=" {{ $doctorName }}">
                        @foreach($slots as $slot)
                            {{-- استخدام الـ CamelCase هنا ليتوافق مع مصفوفتك --}}
                            <option value="{{ $slot['doctorId'] }}|{{ $slot['fullTime'] }}|{{ $slot['bookingDate'] }}">
                                {{ $slot['dateLabel'] }} - الساعة {{ $slot['timeLabel'] }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
                
            </select>
        @else
            <div class="alert alert-warning text-center mt-2">
                عذراً، لا توجد مواعيد متاحة حالياً.
            </div>
        @endif
    </div>

    <input type="hidden" name="roomNumber" value="101">

    <button type="submit" class="btn btn-primary">تأكيد الحجز اللحظي</button>
</form>