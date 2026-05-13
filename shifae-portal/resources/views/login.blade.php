<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة شفائي - تسجيل الدخول</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        /* التنسيقات العامة */
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #eef2f5; /* خلفية هادئة تناسب الأنظمة الطبية */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        /* حاوية نموذج تسجيل الدخول */
        .login-container {
            background: #ffffff;
            width: 100%;
            max-width: 400px;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05); /* ظل ناعم جداً */
            border-top: 5px solid #007bb5; /* خط علوي بلون طبي */
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h2 {
            color: #2c3e50;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }

        .login-header p {
            color: #7f8c8d;
            font-size: 14px;
            margin-top: 5px;
        }

        /* تنسيقات حقول الإدخال */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #34495e;
            font-weight: 500;
            font-size: 15px;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #dcdfe6;
            border-radius: 6px;
            box-sizing: border-box;
            font-family: 'Tajawal', sans-serif;
            font-size: 15px;
            transition: border-color 0.3s; /* تأثير حركي خفيف عند الضغط */
        }

        .form-control:focus {
            outline: none;
            border-color: #007bb5;
        }

        /* تنسيق زر الدخول */
        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #007bb5; /* لون الزر متناسق مع الخط العلوي */
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

        .btn-submit:hover {
            background-color: #005f8cc4;
        }

        /* تنسيق رسائل الخطأ */
        .alert-error {
            background-color: #fde8e8;
            color: #c53030;
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            border-right: 4px solid #c53030;
            font-size: 14px;
        }

        .alert-error ul {
            margin: 0;
            padding-right: 20px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-header">
            <h2>بوابة شفائي</h2>
            <p>تسجيل الدخول للكادر الطبي والإداري</p>
        </div>

        @if($errors->any())
            <div class="alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}">
            @csrf

            <div class="form-group">
                <label for="userName">اسم المستخدم:</label>
                <input type="text" id="userName" name="userName" class="form-control" value="{{ old('userName') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">كلمة المرور:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn-submit">تسجيل الدخول</button>
        </form>
    </div>

</body>
</html>