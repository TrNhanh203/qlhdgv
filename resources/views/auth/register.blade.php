<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            /*background: linear-gradient(to bottom right, #c3dafe, #fbcfe8, #fcd34d);*/
            background: url('/images/university.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-box {
            background: linear-gradient(to bottom right, #edf2faff, #f8e8f1ff, #ece0b9ff);
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
        }

        .register-box h2 {
            text-align: center;
            color: #4f46e5;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #333;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 94%;
            padding: 10px 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            transition: 0.3s;
        }

        input:focus {
            border-color: #6366f1;
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .error-message {
            color: red;
            font-size: 13px;
            margin-top: 5px;
        }

        .actions {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .actions a {
            font-size: 14px;
            color: #4b5563;
            text-decoration: underline;
        }

        .actions a:hover {
            color: #111827;
        }

        button {
            background-color: #4f46e5;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
        }

        button:hover {
            background-color: #4338ca;
        }
    </style>
</head>
<body>
    <div class="register-box">
        <h2>Đăng ký tài khoản</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Tên -->
            <div class="form-group">
                <label for="name">Họ tên</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Địa chỉ Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Mật khẩu -->
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input id="password" type="password" name="password" required>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Xác nhận mật khẩu -->
            <div class="form-group">
                <label for="password_confirmation">Xác nhận mật khẩu</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
                @error('password_confirmation')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="actions">
                <a href="{{ route('login') }}">Đã có tài khoản?</a>
                <button type="submit">Đăng ký</button>
            </div>
        </form>
    </div>
</body>
</html>
