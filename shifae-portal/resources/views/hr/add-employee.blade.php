<!DOCTYPE html>
<!-- بداية هيكل صفحة إضافة الموظف -->
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- عنوان الصفحة -->
    <title>إضافة موظف جديد</title>
</head>
<body>

    <!-- حاوية النموذج الأساسية -->
    <div class="employeeFormContainer">
        <h2>إدارة الموارد البشرية - إضافة موظف جديد</h2>

        <!-- التحقق من رسائل النجاح أو الخطأ وعرضها -->
        @if(session('success'))
            <div style="color: green;" class="successMessage">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="color: red;" class="errorMessage">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div style="color: red;" class="validationErrors">
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
                <select name="userRole" id="employeeRole" required>
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

</body>
</html>
