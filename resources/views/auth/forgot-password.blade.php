<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khôi phục mật khẩu</title>
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

        .reset-box {
            background: linear-gradient(to bottom right, #edf2faff, #f8e8f1ff, #ece0b9ff);
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .reset-box h2 {
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

        input[type="email"] {
            width: 94%;
            padding: 10px 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            transition: 0.3s;
        }

        input[type="email"]:focus {
            border-color: #6366f1;
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .actions {
            text-align: right;
            margin-top: 20px;
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

        .message {
            color: green;
            font-size: 14px;
            margin-bottom: 15px;
            text-align: center;
        }

        .error-message {
            color: red;
            font-size: 13px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="reset-box">
        <h2>KHÔI PHỤC MẬT KHẨU</h2>

        <!-- Hiển thị thông báo session -->
        @if (session('status'))
            <div class="message">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email của bạn</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="actions">
                <button type="submit">Gửi liên kết đặt lại mật khẩu</button>
            </div>
        </form>
    </div>
</body>
</html>
