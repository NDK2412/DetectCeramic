<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/resetpass.css">
    <!-- Thêm Font Awesome để sử dụng icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* CSS cơ bản để giao diện trông đẹp hơn */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 0 0 rgba(24, 119, 242, 0.5);
            }

            50% {
                box-shadow: 0 0 15px rgba(24, 119, 242, 0.5);
            }
        }

        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(to bottom right, #4facfe, rgb(255, 255, 255));
            margin: 0;
        }

        .reset-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        h2 {
            color: #1a1a1a;
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: bold;
        }

        .alert-success {
            color: #155724;
            background: #d4edda;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            animation: fadeIn 0.5s ease-in-out;
        }

        .alert-danger {
            color: #dc3545;
            background: #f8d7da;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            animation: fadeIn 0.5s ease-in-out;
        }

        .alert-danger ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .alert-danger li {
            margin-bottom: 5px;
        }

        form div {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #606770;
            font-weight: 500;
        }

        /* Style cho input với icon */
        .input-group {
            position: relative;
        }

        input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            /* Thêm padding bên trái để chừa chỗ cho icon */
            border: 1px solid #dddfe2;
            border-radius: 6px;
            font-size: 16px;
            box-sizing: border-box;
            transition: all 0.3s;
        }

        input:focus {
            border-color: #1877f2;
            box-shadow: 0 0 8px rgba(24, 119, 242, 0.5);
            outline: none;
        }

        .input-group i {
            position: absolute;
            left: 12px;
            top: 65%;
            /* Căn giữa icon theo chiều dọc so với input */
            transform: translateY(-50%);
            color: #606770;
            font-size: 16px;
        }

        /* Style cho nút Reset Password với icon */
        button {
            width: 100%;
            padding: 12px;
            background: #1877f2;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            /* Khoảng cách giữa icon và chữ */
        }

        button:hover {
            background: #166fe5;
            animation: pulse 1s infinite;
        }

        button i {
            font-size: 18px;
            /* Kích thước icon trong nút */
        }
    </style>
</head>

<body>
    <div class="reset-container">
        <h2>ĐỔI MẬT KHẨU</h2>
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('password.update') }}" class="form-animation">
            @csrf
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
                <i class="fas fa-envelope"></i>
            </div>
            <div class="input-group">
                <label for="password">Mật khẩu mới:</label>
                <input type="password" name="password" id="password" required>
                <i class="fas fa-lock"></i>
            </div>
            <div class="input-group">
                <label for="password_confirmation">Nhập lại mật khẩu mới:</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required>
                <i class="fas fa-lock"></i>
            </div>
            <button type="submit">
                <i class="fas fa-key"></i> ĐỔI MẬT KHẨU
            </button>
        </form>
    </div>
</body>

</html>