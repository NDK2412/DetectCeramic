<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(to bottom right, rgb(255, 255, 255), rgb(2, 247, 247));
            margin: 0;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .register-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
            animation: slideIn 0.8s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        h2 {
            color: #1a1a1a;
            margin-bottom: 10px;
            font-size: 28px;
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

        .input-group {
            position: relative;
        }

        input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 1px solid #dddfe2;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s ease-in-out;
            box-sizing: border-box;
        }

        input:focus {
            border-color: #1877f2;
            box-shadow: 0 0 5px rgba(24, 119, 242, 0.5);
            transform: scale(1.02);
        }

        .input-group i {
            position: absolute;
            left: 12px;
            top: 65%;
            transform: translateY(-50%);
            color: #606770;
            font-size: 16px;
        }

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
            margin-top: 15px;
            transition: background 0.3s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        button:hover {
            background: #166fe5;
        }

        button i {
            font-size: 18px;
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

        .links {
            margin-top: 20px;
        }

        a {
            color: #1877f2;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        a:hover {
            color: #166fe5;
            text-decoration: underline;
        }

        .terms-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
        }

        .terms-checkbox input[type="checkbox"] {
            width: auto;
            padding: 0;
        }

        .terms-checkbox label {
            margin-bottom: 0;
            font-size: 14px;
        }

        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .popup-overlay.show {
            opacity: 1;
        }

        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.9);
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            width: 600px;
            max-width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            z-index: 1000;
            opacity: 0;
            transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
        }

        .popup.show {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }

        .popup h3 {
            color: #1a1a1a;
            margin-bottom: 20px;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .popup h3 i {
            color: #1877f2;
            font-size: 1.6rem;
        }

        .popup .terms-content {
            color: #333;
            line-height: 1.7;
            font-size: 1rem;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 8px;
            border-left: 4px solid #1877f2;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.05);
        }

        .popup .terms-content h4 {
            color: #1877f2;
            font-size: 1.1rem;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 5px;
        }

        .popup .terms-content p {
            margin-bottom: 15px;
        }

        .popup .terms-content ul,
        .popup .terms-content ol {
            margin-bottom: 15px;
            padding-left: 20px;
        }

        .popup .terms-content li {
            margin-bottom: 5px;
        }

        .popup .terms-content strong {
            color: #333;
        }

        .popup .terms-content blockquote {
            margin: 15px 0;
            padding: 10px 15px;
            border-left: 3px solid #1877f2;
            background-color: #f0f7ff;
            font-style: italic;
        }

        .popup button {
            background: #1877f2;
            width: auto;
            padding: 10px 25px;
            margin: 15px auto 0;
            display: flex;
            align-items: center;
            gap: 8px;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .popup button:hover {
            background: #166fe5;
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .popup button i {
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .popup {
                width: 90%;
                padding: 20px;
            }

            .popup h3 {
                font-size: 1.3rem;
            }

            .popup h3 i {
                font-size: 1.4rem;
            }

            .popup .terms-content {
                font-size: 0.9rem;
                padding: 10px;
            }

            .popup button {
                padding: 8px 20px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h2>Register</h2>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="input-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                <i class="fas fa-user"></i>
            </div>
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                <i class="fas fa-envelope"></i>
            </div>
            <div class="input-group">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}">
                <i class="fas fa-phone"></i>
            </div>
            <div class="input-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="{{ old('address') }}">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="input-group">
                <label for="id_number">ID card number:</label>
                <input type="text" id="id_number" name="id_number" value="{{ old('id_number') }}">
                <i class="fas fa-id-card"></i>
            </div>
            <div class="input-group">
                <label for="passport">Passport:</label>
                <input type="text" id="passport" name="passport" value="{{ old('passport') }}" placeholder="Can be ignored....">
                <i class="fas fa-passport"></i>
            </div>
            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <i class="fas fa-lock"></i>
            </div>
            <div class="input-group">
                <label for="password_confirmation">Re-enter password:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
                <i class="fas fa-lock"></i>
            </div>

            <div class="terms-checkbox">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">I agree to the <a href="#" id="termsLink">terms and conditions</a></label>
            </div>

            <button type="submit">
                <i class="fas fa-user-plus"></i> Register
            </button>
        </form>

        <p class="links">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
    </div>

    <div class="popup-overlay" id="termsOverlay"></div>
    <div class="popup" id="termsPopup">
        <h3><i class="fas fa-file-alt"></i> Chính sách và điều khoản</h3>
        <div class="terms-content" id="termsContent">Đang tải...</div>
        <button onclick="hideTermsPopup()"><i class="fas fa-times"></i> Đóng</button>
    </div>

    <script>
        document.getElementById('termsLink').addEventListener('click', function (e) {
            e.preventDefault();
            showTermsPopup();
        });

        async function showTermsPopup() {
            const popup = document.getElementById('termsPopup');
            const overlay = document.getElementById('termsOverlay');
            const termsContent = document.getElementById('termsContent');

            try {
                const response = await fetch("{{ route('terms.show') }}");
                const data = await response.json();

                const formattedContent = formatTermsContent(data.content);
                termsContent.innerHTML = formattedContent;
            } catch (error) {
                termsContent.textContent = 'Không thể tải chính sách và điều khoản. Vui lòng thử lại sau.';
                console.error('Lỗi:', error);
            }

            popup.style.display = 'block';
            overlay.style.display = 'block';
            setTimeout(() => {
                popup.classList.add('show');
                overlay.classList.add('show');
            }, 10);
        }

        function hideTermsPopup() {
            const popup = document.getElementById('termsPopup');
            const overlay = document.getElementById('termsOverlay');

            popup.classList.remove('show');
            overlay.classList.remove('show');
            setTimeout(() => {
                popup.style.display = 'none';
                overlay.style.display = 'none';
            }, 300);
        }

        document.getElementById('termsOverlay').addEventListener('click', hideTermsPopup);

        function formatTermsContent(content) {
            if (!content) return '';

            content = content.replace(/^(#{1,6})\s+(.+)$/gm, function (match, hashes, title) {
                const level = hashes.length;
                return `<h${level + 3}>${title}</h${level + 3}>`;
            });

            content = content.replace(/\n\n(.+?)(?=\n\n|\n*$)/gs, '<p>$1</p>');

            content = content.replace(/(?:^|\n)((?:\s*[-*+]\s+.+\n?)+)/g, function (match, list) {
                const items = list.trim().split(/\n\s*[-*+]\s+/).filter(Boolean);
                return '<ul>' + items.map(item => `<li>${item.trim()}</li>`).join('') + '</ul>';
            });

            content = content.replace(/(?:^|\n)((?:\s*\d+\.\s+.+\n?)+)/g, function (match, list) {
                const items = list.trim().split(/\n\s*\d+\.\s+/).filter(Boolean);
                return '<ol>' + items.map(item => `<li>${item.trim()}</li>`).join('') + '</ol>';
            });

            content = content.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            content = content.replace(/__(.*?)__/g, '<strong>$1</strong>');

            content = content.replace(/\*(.*?)\*/g, '<em>$1</em>');
            content = content.replace(/_(.*?)_/g, '<em>$1</em>');

            content = content.replace(/^\s*>\s*(.+)$/gm, '<blockquote>$1</blockquote>');

            content = content.replace(/\n/g, '<br>');

            return content;
        }
    </script>
</body>

</html>