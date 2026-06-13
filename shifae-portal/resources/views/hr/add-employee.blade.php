<!DOCTYPE html>
<!-- بداية هيكل صفحة إضافة الموظف -->
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- عنوان الصفحة -->
    <title>إضافة موظف جديد</title>
    <!-- استيراد الخطوط المعتمدة في النظام -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        /* التنسيقات العامة مطابقة لتصميم النظام (كما في صفحة تسجيل الدخول) */
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #eef2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        /* حاوية النموذج الأساسية */
        .employeeFormContainer {
            background: #ffffff;
            width: 100%;
            max-width: 500px;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            border-top: 5px solid #007bb5;
        }

        .employeeFormContainer h2 {
            text-align: center;
            color: #2c3e50;
            margin-top: 0;
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: 700;
        }

        /* تنسيقات حقول الإدخال */
        .inputGroup {
            margin-bottom: 20px;
        }

        .inputGroup label {
            display: block;
            margin-bottom: 8px;
            color: #34495e;
            font-weight: 500;
            font-size: 15px;
        }

        .inputGroup input[type="text"],
        .inputGroup input[type="email"],
        .inputGroup input[type="number"],
        .inputGroup select,
        .inputGroup input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #dcdfe6;
            border-radius: 6px;
            box-sizing: border-box;
            font-family: 'Tajawal', sans-serif;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        .inputGroup input:focus,
        .inputGroup select:focus {
            outline: none;
            border-color: #007bb5;
        }

        /* تنسيق زر الإرسال */
        .submitButton {
            width: 100%;
            padding: 12px;
            background-color: #007bb5;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Tajawal', sans-serif;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .submitButton:hover {
            background-color: #005f8cc4;
        }

        /* تنسيقات رسائل التنبيه والخطأ */
        .successMessage {
            background-color: #def7ec;
            color: #03543f;
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            border-right: 4px solid #03543f;
            font-size: 14px;
        }

        .errorMessage, .validationErrors {
            background-color: #fde8e8;
            color: #c53030;
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            border-right: 4px solid #c53030;
            font-size: 14px;
        }

        .validationErrors ul {
            margin: 0;
            padding-right: 20px;
        }
    </style>
</head>
<body>

    <!-- حاوية النموذج الأساسية -->
    <div class="employeeFormContainer">
        <h2>إضافة موظف جديد</h2>

        <!-- التحقق من رسائل النجاح أو الخطأ وعرضها -->
        @if(session('success'))
            <div class="successMessage">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="errorMessage">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="validationErrors">
                <ul>
                    @foreach($errors->all() as $validationError)
                        <li>{{ $validationError }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- نموذج الإرسال إلى مسار تخزين الموظف -->
        <form method="POST" action="{{ route('hr.employees.store') }}" enctype="multipart/form-data" class="employeeSubmissionForm">
            @csrf
            
            <!-- حقل الاسم الكامل -->
            <div class="inputGroup">
                <label for="fullName">الاسم الكامل:</label>
                <input type="text" id="fullName" name="fullName" value="{{ old('fullName') }}" required>
            </div>

            <!-- حقل اسم المستخدم -->
            <div class="inputGroup">
                <label for="userName">اسم المستخدم:</label>
                <input type="text" id="userName" name="userName" value="{{ old('userName') }}" required>
            </div>

            <!-- حقل البريد الإلكتروني -->
            <div class="inputGroup">
                <label for="emailAddress">البريد الإلكتروني:</label>
                <!-- اسم الحقل يجب أن يطابق الباك إند: email -->
                <input type="email" id="emailAddress" name="email" value="{{ old('email') }}" required>
            </div>

            <!-- حقل الدور الوظيفي -->
            <div class="inputGroup">
                <label for="employeeRole">الدور الوظيفي:</label>
                <select name="userRole" id="employeeRole" required onchange="toggleDoctorFields()">
                    <option value="">-- اختر الدور --</option>
                    <option value="Doctor" {{ old('userRole') == 'Doctor' ? 'selected' : '' }}>طبيب</option>
                    <option value="Receptionist" {{ old('userRole') == 'Receptionist' ? 'selected' : '' }}>موظف استقبال</option>
                </select>
            </div>

            <!-- حقول مخصصة للطبيب فقط -->
            <div id="doctorSpecificFields" style="display: none;">
                <!-- حقل التخصص -->
                <div class="inputGroup">
                    <label for="medicalSpecialty">التخصص (للطبيب فقط):</label>
                    <input type="text" id="medicalSpecialty" name="specialty" value="{{ old('specialty') }}">
                </div>

                <!-- حقل نسبة الربح -->
                <div class="inputGroup">
                    <label for="profitPercentageValue">نسبة الربح (للطبيب فقط):</label>
                    <input type="number" step="0.01" id="profitPercentageValue" name="profitPercentage" value="{{ old('profitPercentage') }}">
                </div>
            </div>

            <!-- حقل المرفقات -->
            <div class="inputGroup">
                <label for="uploadedCertificate">المرفقات (السيرة الذاتية أو الشهادة):</label>
                <input type="file" id="uploadedCertificate" name="certificate" accept=".pdf,.jpg,.png,.jpeg" required>
            </div>

            <!-- زر الإرسال -->
            <div class="submitGroup">
                <button type="submit" class="submitButton">إضافة الموظف</button>
            </div>
        </form>
    </div>

    <!-- سكربتات تفاعلية لمعالجة ظهور وإخفاء الحقول -->
    <script>
        /**
         * دالة للتحقق من الدور الوظيفي وإظهار أو إخفاء حقول الطبيب
         * يتم استدعاؤها عند تحميل الصفحة وعند تغيير قيمة القائمة المنسدلة
         */
        function toggleDoctorFields() {
            try {
                // استخراج عنصر اختيار الدور الوظيفي
                const roleSelectionDropdown = document.getElementById('employeeRole');
                // استخراج حاوية حقول الطبيب
                const doctorFieldsContainer = document.getElementById('doctorSpecificFields');
                
                // التحقق من القيمة المحددة
                if (roleSelectionDropdown.value === 'Doctor') {
                    // إظهار الحقول الخاصة بالطبيب
                    doctorFieldsContainer.style.display = 'block';
                } else {
                    // إخفاء الحقول الخاصة بالطبيب
                    doctorFieldsContainer.style.display = 'none';
                }
            } catch (executionError) {
                // طباعة الخطأ في مشغل الأوامر في المتصفح لأغراض الصيانة
                console.error("حدث خطأ أثناء معالجة تبديل الحقول: ", executionError);
            }
        }

        // تنفيذ الدالة عند تحميل الصفحة للتعامل مع حالة الـ old('userRole') في حال فشل الإرسال
        document.addEventListener('DOMContentLoaded', function() {
            try {
                toggleDoctorFields();
            } catch (initializationError) {
                console.error("حدث خطأ أثناء تحميل تفاعلات الصفحة: ", initializationError);
            }
        });
    </script>

</body>
</html>
