<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجز موعد</title>
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
</head>
<body>
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
       <input type="date" id="patientDob" name="dateOfBirth" class="form-control" required>
    </div>
    <div class="form-group">
        <label>الجنس</label>
        <select id="patientGender" name="gender" class="form-control" required>
            <option value="">-- اختر الجنس --</option>
            <option value="male">ذكر</option>
            <option value="female">أنثى</option>
        </select>
    </div>
    <!-- تمرير بيانات الأطباء من الكنترولر للجافاسكربت عشان نتحكم في القوائم -->
    <script>
        const doctorsData = @json($doctorsData);
    </script>

    <!-- زر يقترح طبيب بناءً على العمر والجنس باستخدام الـ API -->
    <div class="form-group" style="margin-top: 20px;">
        <button type="button" onclick="autoSuggestDoctor()" class="btn btn-primary" style="background-color: #8e44ad; font-size: 1rem; margin-bottom: 5px;">
            اقتراح طبيب مناسب
        </button>
        <div id="suggestionMessage" style="color: green; font-weight: bold; margin-bottom: 10px; text-align: center;"></div>
    </div>

    <!-- قوائم اختيار الطبيب واليوم والساعة (تتغير عن طريق الجافاسكربت) -->
    <div class="form-group">
        <label>اختر الطبيب:</label>
        <select id="doctorSelect" name="doctorId" class="form-control" required onchange="updateDays()">
            <option value="">-- يرجى اختيار الطبيب --</option>
            @if(!empty($doctorsData))
                @foreach($doctorsData as $id => $doctor)
                    <option value="{{ $id }}">{{ $doctor['name'] }}</option>
                @endforeach
            @endif
        </select>
    </div>

    <div class="form-group">
        <label>اختر اليوم:</label>
        <select id="daySelect" name="selectedDate" class="form-control" required onchange="updateTimes()" disabled>
            <option value="">-- اختر الطبيب أولاً --</option>
        </select>
    </div>

    <div class="form-group">
        <label>اختر الساعة:</label>
        <select id="timeSelect" name="slotTime" class="form-control" required disabled>
            <option value="">-- اختر اليوم أولاً --</option>
        </select>
    </div>
    
    <!-- حقل مخفي يدمج البيانات للكنترولر -->
    <input type="hidden" name="appointment_data" id="appointment_data">

    <!-- دوال الجافاسكربت للتحكم في القوائم المنسدلة وربط الـ API -->
    <script>
        function updateDays() {
            const doctorId = document.getElementById('doctorSelect').value;
            const daySelect = document.getElementById('daySelect');
            const timeSelect = document.getElementById('timeSelect');
            
            daySelect.innerHTML = '<option value="">-- يرجى اختيار اليوم --</option>';
            timeSelect.innerHTML = '<option value="">-- اختر اليوم أولاً --</option>';
            daySelect.disabled = true;
            timeSelect.disabled = true;

            if (doctorId && doctorsData[doctorId]) {
                const days = doctorsData[doctorId].days;
                for (const [dateString, dayInfo] of Object.entries(days)) {
                    daySelect.innerHTML += `<option value="${dateString}">${dayInfo.dateLabel}</option>`;
                }
                daySelect.disabled = false;
            }
        }

        function updateTimes() {
            const doctorId = document.getElementById('doctorSelect').value;
            const selectedDate = document.getElementById('daySelect').value;
            const timeSelect = document.getElementById('timeSelect');
            
            timeSelect.innerHTML = '<option value="">-- يرجى اختيار الساعة --</option>';
            timeSelect.disabled = true;

            if (doctorId && selectedDate && doctorsData[doctorId].days[selectedDate]) {
                const slots = doctorsData[doctorId].days[selectedDate].slots;
                slots.forEach(slot => {
                    const formattedTime = slot.substring(0, 5); 
                    timeSelect.innerHTML += `<option value="${slot}">${formattedTime}</option>`;
                });
                timeSelect.disabled = false;
            }
        }
        
        function autoSuggestDoctor() {
            const dob = document.getElementById('patientDob').value;
            const gender = document.getElementById('patientGender').value;
            
            if(!dob) {
                alert('الرجاء إدخال تاريخ الميلاد أولاً لكي يستطيع النظام حساب العمر.');
                return;
            }

            const currentYear = new Date().getFullYear();
            const birthYear = new Date(dob).getFullYear();
            const age = currentYear - birthYear;

            fetch(`/api/recommend-doctor?age=${age}&gender=${gender}`)
                .then(response => response.json())
                .then(data => {
                    const msgBox = document.getElementById('suggestionMessage');
                    if(data.success) {
                        msgBox.style.color = 'green';
                        msgBox.innerText = data.message;
                        document.getElementById('doctorSelect').value = data.doctor_id;
                        updateDays();
                    } else {
                        msgBox.style.color = 'red';
                        msgBox.innerText = data.message;
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // تجميع البيانات في الحقل المخفي قبل الإرسال عشان الكنترولر يقدر يتعامل معاها
        document.querySelector('form').addEventListener('submit', function(e) {
            const doctorId = document.getElementById('doctorSelect').value;
            const selectedDate = document.getElementById('daySelect').value;
            const slotTime = document.getElementById('timeSelect').value;
            
            if(doctorId && selectedDate && slotTime) {
                document.getElementById('appointment_data').value = `${doctorId}|${slotTime}|${selectedDate}`;
            }
        });
    </script>
      

    <div style="display: flex; gap: 10px; margin-top: 20px;">
        <button type="submit" class="btn btn-primary" style="margin-top: 0; flex: 1;">تأكيد الحجز اللحظي</button>
        <a href="{{ route('home') }}" class="btn btn-secondary" style="flex: 1; text-align: center; background-color: #95a5a6; color: white; padding: 14px; border-radius: 10px; font-size: 1.1rem; font-weight: bold; text-decoration: none; transition: background-color 0.3s, transform 0.2s; box-sizing: border-box;">العودة للصفحة الرئيسية</a>
    </div>
</form>
</body>
</html>