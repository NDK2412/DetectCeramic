<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thư viện đồ gốm - Ceramic Classification</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        :root {
            --primary-color: #b3cde0;
            --secondary-color: #6497b1;
            --accent-color: #e6f0fa;
            --light-color: #f5faff;
            --dark-color: #03396c;
            --text-light: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--light-color);
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            flex: 1;
        }

        header {
            background-color: var(--primary-color);
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            width: 100%;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            display: flex;
            align-items: center;
            color: var(--dark-color);
            font-size: clamp(1.2rem, 2.5vw, 1.8rem);
            font-weight: 600;
            text-decoration: none;
            flex-shrink: 0;
        }

        .logo img {
            height: clamp(30px, 5vw, 40px);
            margin-right: 10px;
        }

        .nav-container {
            display: flex;
            align-items: center;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            gap: clamp(1rem, 2vw, 1.5rem);
        }

        .nav-menu li a {
            color: var(--dark-color);
            text-decoration: none;
            font-weight: 500;
            font-size: clamp(0.9rem, 1.5vw, 1rem);
            transition: color 0.3s ease;
        }

        .nav-menu li a:hover {
            color: var(--secondary-color);
        }

        .login-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .login-section button {
            padding: clamp(0.5rem, 1vw, 0.6rem) clamp(1rem, 2vw, 1.2rem);
            border: none;
            border-radius: 20px;
            font-weight: 500;
            font-size: clamp(0.8rem, 1.2vw, 1rem);
            cursor: pointer;
            transition: background-color 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        #loginButton {
            background-color: var(--secondary-color);
            color: var(--text-light);
        }

        #logoutButton {
            background-color: var(--dark-color);
            color: var(--text-light);
        }

        .login-section button:hover {
            background-color: var(--dark-color);
        }

        .hamburger {
            display: none;
            font-size: 2rem;
            background: none;
            border: none;
            color: var(--dark-color);
            cursor: pointer;
            padding: 0.5rem;
        }

        .hamburger.active {
            transform: rotate(90deg);
        }

        /* Gallery Section */
        .gallery-section {
            padding: 2rem 0;
        }

        .gallery-section h1 {
            font-size: 2.5rem;
            color: var(--dark-color);
            text-align: center;
            margin-bottom: 2rem;
            animation: fadeInUp 0.4s ease-out;
        }

        .filter-section {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
            justify-content: center;
        }

        .filter-section select,
        .filter-section button {
            padding: 0.5rem;
            border: 1px solid var(--secondary-color);
            border-radius: 5px;
            font-size: 1rem;
            color: var(--dark-color);
            background-color: var(--accent-color);
        }

        .filter-section button {
            padding: 0.5rem 1rem;
            background-color: var(--secondary-color);
            color: var(--text-light);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .filter-section button:hover {
            background-color: var(--dark-color);
        }

        .gallery-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .gallery-item {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
            animation: fadeInUp 0.4s ease-out;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 300px; /* Kích thước cố định chiều rộng */
            min-height: 400px; /* Kích thước cố định chiều cao tối thiểu */
            padding: 1rem;
        }

        .gallery-item:hover {
            transform: translateY(-5px);
        }

        .gallery-item img {
            width: 100%;
            height: 200px; /* Chiều cao cố định cho khu vực hình ảnh */
            object-fit: cover; /* Đảm bảo hình ảnh lấp đầy mà không méo */
            display: block;
            margin: 0 auto;
            border-radius: 5px;
            transition: transform 0.3s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.05);
        }

        .gallery-content {
            padding: 1.5rem;
            text-align: center;
            flex: 1; /* Đảm bảo nội dung chiếm phần còn lại của thẻ */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .gallery-content h2 {
            font-size: 1.5rem;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .gallery-content p {
            font-size: 1rem;
            color: #666;
            line-height: 1.6;
            margin-bottom: 0.5rem;
        }

        .gallery-content a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .gallery-content a:hover {
            color: var(--dark-color);
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }

        .pagination a,
        .pagination span {
            padding: 0.5rem 1rem;
            margin: 0 0.2rem;
            border: 1px solid var(--secondary-color);
            border-radius: 5px;
            text-decoration: none;
            color: var(--dark-color);
            transition: background-color 0.3s ease;
        }

        .pagination a:hover {
            background-color: var(--secondary-color);
            color: var(--text-light);
        }

        .pagination .current {
            background-color: var(--dark-color);
            color: var(--text-light);
            border-color: var(--dark-color);
        }

        footer {
            text-align: center;
            padding: 1.5rem;
            background-color: var(--primary-color);
            color: var(--dark-color);
            margin-top: auto;
            width: 100%;
            font-size: clamp(0.8rem, 1.5vw, 1rem);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                transform: translateY(10px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .hamburger {
                display: block;
            }

            .nav-container {
                display: none;
                flex-direction: column;
                width: 100%;
                position: absolute;
                top: 100%;
                left: 0;
                background-color: var(--primary-color);
                padding: 1rem;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            }

            .nav-container.active {
                display: flex;
            }

            .nav-menu {
                flex-direction: column;
                width: 100%;
                gap: 1rem;
                text-align: center;
            }

            .nav-menu li {
                width: 100%;
            }

            .login-section {
                width: 100%;
                justify-content: center;
                flex-direction: column;
                gap: 0.8rem;
            }

            .login-section button {
                width: 100%;
            }

            .header-content {
                justify-content: space-between;
            }

            .gallery-section {
                padding: 1.5rem;
            }

            .gallery-section h1 {
                font-size: 1.5rem;
            }

            .gallery-content h2 {
                font-size: 1.3rem;
            }

            .gallery-content p {
                font-size: 1rem;
            }

            .gallery-item {
                width: 100%; /* Điều chỉnh kích thước trên màn hình nhỏ */
                min-height: 350px; /* Giảm chiều cao tối thiểu */
            }

            .gallery-item img {
                height: 180px; /* Giảm chiều cao hình ảnh */
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 1rem;
            }

            .logo {
                font-size: 1.2rem;
            }

            .logo img {
                height: 25px;
            }

            .gallery-item {
                width: 100%;
                min-height: 300px; /* Giảm thêm chiều cao tối thiểu */
            }

            .gallery-item img {
                height: 150px; /* Giảm chiều cao hình ảnh */
            }

            .gallery-content h2 {
                font-size: 1.2rem;
            }

            .gallery-content p {
                font-size: 0.85rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <div class="header-content">
                <a href="#" class="logo"><img src="http://localhost:8000/storage/ceramics/logo2.webp" alt="Logo">
                    Ceramic Classification
                </a>
                <button class="hamburger" aria-label="Toggle menu">☰</button>
                <div class="nav-container">
                    <ul class="nav-menu">
                        <li><a href="/">Trang chủ</a></li>
                        <li><a href="/gallery">Thư viện đồ gốm</a></li>
                        <li><a href="/dashboard" id="classificationLink">Nhận diện</a></li>
                        <li><a href="#market">Mua bán</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Gallery Section -->
        <section class="gallery-section">
            <h1>Thư viện đồ gốm</h1>

            <!-- Filter Section -->
            <form class="filter-section" method="GET" action="{{ route('gallery') }}">
                <select name="category">
                    <option value="">Tất cả danh mục</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                <select name="origin">
                    <option value="">Tất cả nguồn gốc</option>
                    @foreach ($origins as $org)
                        <option value="{{ $org }}" {{ request('origin') == $org ? 'selected' : '' }}>{{ $org }}</option>
                    @endforeach
                </select>
                <button type="submit">Lọc</button>
            </form>

            <!-- Gallery List -->
            <div class="gallery-list">
                @forelse ($ceramics as $key => $ceramic)
                    <article class="gallery-item">
                        <img src="{{ $ceramic->image ? asset('storage/' . $ceramic->image) : 'https://via.placeholder.com/300x200' }}"
                            alt="{{ $ceramic->name }}">
                        <div class="gallery-content">
                            <h2>{{ $ceramic->name }}</h2>
                            <p><strong>Danh mục:</strong> {{ $ceramic->category ?? 'Không có' }}</p>
                            <p><strong>Nguồn gốc:</strong> {{ $ceramic->origin ?? 'Không có' }}</p>
                            <a href="{{ route('ceramics.show', $ceramic->id) }}">Xem chi tiết</a>
                        </div>
                    </article>
                @empty
                    <p>Không tìm thấy đồ gốm nào.</p>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="pagination">
                {{ $ceramics->links() }}
            </div>
        </section>
    </div>

    <footer>
        <p>© 2023 Ceramic Classification System. All rights reserved.</p>
</footer>

    <script>
        // Toggle menu hamburger
        const hamburger = document.querySelector('.hamburger');
        const navContainer = document.querySelector('.nav-container');
        const classificationLink = document.querySelector('#classificationLink');

        hamburger.addEventListener('click', () => {
            navContainer.classList.toggle('active');
            hamburger.classList.toggle('active');
        });

        // Biến kiểm tra trạng thái đăng nhập
        let isAuthenticated = false;

        // Hàm kiểm tra trạng thái đăng nhập
        async function checkLoginStatus() {
            try {
                let response = await fetch("http://localhost:8000/api/check-auth", {
                    credentials: "include"
                });
                let data = await response.json();

                if (data.authenticated) {
                    isAuthenticated = true;
                    document.getElementById("loginButton").style.display = "none";
                    document.getElementById("logoutButton").style.display = "block";
                } else {
                    isAuthenticated = false;
                    document.getElementById("loginButton").style.display = "block";
                    document.getElementById("logoutButton").style.display = "none";
                }
            } catch (error) {
                console.error("Lỗi kiểm tra đăng nhập:", error);
            }
        }

        // Chuyển hướng đến trang đăng nhập
        function redirectToLogin() {
            window.location.href = "http://localhost:8000/login";
        }

        // Đăng xuất người dùng
        async function logout() {
            try {
                document.getElementById('logoutButton').innerHTML = '<span class="loading"></span> Processing...';
                await fetch("http://localhost:8000/api/logout", {
                    method: "POST",
                    credentials: "include"
                });
                window.location.reload();
            } catch (error) {
                console.error("Lỗi đăng xuất:", error);
                document.getElementById('logoutButton').textContent = 'Đăng xuất';
            }
        }

        // Kiểm tra trạng thái đăng nhập khi trang tải
        window.onload = checkLoginStatus;
    </script>
</body>

</html>