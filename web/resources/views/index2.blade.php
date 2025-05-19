<!DOCTYPE html>
<html lang="en">

<head>
    @php
        $metadata = App\Models\Metadata::where('page', 'index')->first();
    @endphp
    <title>{{ $metadata->title ?? 'Trang chủ' }}</title>
    <meta name="description" content="{{ $metadata->description ?? '' }}">
    <meta name="keywords" content="{{ $metadata->keywords ?? '' }}">
    @if ($metadata->favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $metadata->favicon) }}">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        :root {
            --primary-color: #2ecc71;
            /* Xanh lá cây */
            --secondary-color: #f1c40f;
            /* Vàng */
            --background-color: #f9f9f9;
            /* Xám nhạt */
            --text-dark: #333333;
            /* Đen nhạt */
            --text-light: #ffffff;
            /* Trắng */
            --shadow-color: rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            flex: 1;
        }

        /* Header */
        header {
            background-color: var(--primary-color);
            padding: 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: var(--text-light);
            font-size: 1.8rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo i {
            font-size: 2rem;
        }

        .nav-container {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            gap: 1.2rem;
        }

        .nav-menu li a {
            color: var(--text-light);
            text-decoration: none;
            font-size: 1rem;
            font-weight: 400;
            /* padding: 0.5rem 1rem; */
            transition: background 0.3s ease;
        }

        .nav-menu li a:hover {
            background-color: var(--secondary-color);
            color: var(--text-dark);
            border-radius: 5px;
        }

        .login-section button {
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        #loginButton {
            background-color: var(--secondary-color);
            color: var(--text-dark);
        }

        #logoutButton {
            background-color: var(--text-dark);
            color: var(--text-light);
        }

        .login-section button:hover {
            transform: scale(1.05);
        }

        .hamburger {
            display: none;
            font-size: 1.8rem;
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
        }

        /* Banner */
        .banner {
            background-color: var(--primary-color);
            padding: 3rem;
            text-align: center;
            color: var(--text-light);
            border-radius: 10px;
            margin-top: 1rem;
        }

        .banner h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .banner p {
            font-size: 1.2rem;
            font-weight: 300;
        }

        /* News Section */
        .news-section {
            padding: 3rem 0;
        }

        .news-section h1 {
            font-size: 2rem;
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 2rem;
        }

        .news-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .news-item {
            background-color: var(--text-light);
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 6px var(--shadow-color);
            transition: box-shadow 0.3s ease;
        }

        .news-item:hover {
            box-shadow: 0 4px 12px var(--shadow-color);
        }

        .news-content h2 {
            font-size: 1.3rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .news-content p {
            font-size: 0.95rem;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .news-content a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 600;
        }

        /* About Section */
        .about-section {
            background-color: var(--text-light);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 6px var(--shadow-color);
            margin-bottom: 2rem;
        }

        .about-section h2 {
            font-size: 2rem;
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .feature-point {
            margin: 1rem 0;
            padding: 1rem;
            border-left: 4px solid var(--secondary-color);
        }

        .feature-point h3 {
            color: var(--primary-color);
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .feature-point p {
            font-size: 0.95rem;
            color: var(--text-dark);
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
        }

        .benefit-card {
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
        }

        .benefit-card i {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .benefit-card h4 {
            font-size: 1rem;
            margin-bottom: 0.3rem;
        }

        .applications-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .application-tag {
            background-color: var(--secondary-color);
            color: var(--text-dark);
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        /* Footer */
        footer {
            background-color: var(--text-dark);
            color: var(--text-light);
            text-align: center;
            padding: 1rem;
            margin-top: auto;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: var(--text-light);
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 4px 12px var(--shadow-color);
        }

        .modal-content p {
            margin-bottom: 1rem;
            color: var(--text-dark);
        }

        .modal-content button {
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .modal-content button:hover {
            background-color: var(--secondary-color);
            color: var(--text-dark);
        }

        /* Contact Sidebar */
        .contact-sidebar {
            position: fixed;
            top: 0;
            left: -450px;
            width: 450px;
            height: 100%;
            background-color: var(--text-dark);
            color: var(--text-light);
            padding: 2rem;
            z-index: 1500;
            transition: left 0.3s ease;
        }

        .contact-sidebar.active {
            left: 0;
            white-space: pre-line;
            overflow-y: auto;
            text-overflow: ellipsis;
        }

        .contact-sidebar h3 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .contact-sidebar ul {
            list-style: none;
        }

        .contact-sidebar li {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;

        }

        .contact-sidebar i {
            font-size: 1.2rem;
        }

        .contact-sidebar a {
            color: var(--secondary-color);
            text-decoration: none;
            overflow: hidden;
        }

        .contact-sidebar a:hover {
            color: var(--primary-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hamburger {
                display: block;
            }

            .nav-container {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background-color: var(--primary-color);
                padding: 1rem;
            }

            .nav-container.active {
                display: block;
            }

            .nav-menu {
                flex-direction: column;
                text-align: center;
            }

            .login-section {
                flex-direction: column;
                gap: 0.5rem;
            }

            .banner h1 {
                font-size: 2rem;
            }

            .banner p {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .logo {
                font-size: 1.5rem;
            }

            .logo i {
                font-size: 1.5rem;
            }

            .banner h1 {
                font-size: 1.5rem;
            }

            .banner p {
                font-size: 0.9rem;
            }
        }

        /* Phần Form Liên hệ */
        .contact-form {
            margin-top: 1.5rem;
        }

        .contact-form h4 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: var(--secondary-color);
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 0.6rem;
            margin-bottom: 0.8rem;
            border: none;
            border-radius: 5px;
            background-color: var(--text-light);
            color: var(--text-dark);
            font-size: 0.9rem;
        }

        .contact-form textarea {
            height: 100px;
            resize: none;
        }

        .contact-form button {
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
        }

        .contact-form button:hover {
            background-color: var(--secondary-color);
            color: var(--text-dark);
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <div class="header-content">
                <a href="#" class="logo">
                    <i class="fas fa-leaf"></i> Ceramic Classification
                </a>
                <button class="hamburger" aria-label="Toggle menu">☰</button>
                <div class="nav-container">
                    <ul class="nav-menu">
                        <li><a href="#home">Trang chủ</a></li>
                        <li><a href="gallery">Thư viện đồ gốm</a></li>
                        <li><a href="#" id="classificationLink">Nhận diện</a></li>
                        <li><a href="#market">Mua bán</a></li>
                        <li><a href="#" id="contactLink">Liên hệ</a></li>
                    </ul>
                    <div class="login-section">
                        <button id="loginButton" onclick="redirectToLogin()">Try It Out <i
                                class="fa-solid fa-arrow-up-from-bracket"></i></button>
                        <button id="logoutButton" onclick="logout()" style="display:none;">Đăng xuất</button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Banner Section -->
        <div class="banner">
            <h1>Khám phá Ceramic AI</h1>
            <p>Hệ thống AI tiên tiến giúp nhận diện và phân loại gốm sứ một cách nhanh chóng</p>
        </div>

        <!-- News Section -->
        <!-- News Section -->
        <section class="news-section">
            <h1>Tin tức về gốm sứ</h1>
            <div class="news-list">
                @if ($news->isEmpty())
                    <p>Chưa có tin tức nào.</p>
                @else
                    @foreach ($news as $article)
                        <article class="news-item">
                            <div class="news-content">
                                <h2>{{ $article->title }}</h2>
                                <p>{{ $article->excerpt ?? Str::limit($article->content, 100) }}</p>
                                <a href="{{ route('news.detail', $article->id) }}">Đọc thêm</a>
                            </div>
                        </article>
                    @endforeach
                @endif
            </div>
        </section>
        <!-- <section class="news-section">
            <h1>Tin tức về gốm sứ</h1>
            <div class="news-list">
                <article class="news-item">
                    <div class="news-content">
                        <h2>Triển lãm gốm sứ 2023</h2>
                        <p>Triển lãm gốm sứ quốc tế diễn ra tại Hà Nội, thu hút hàng trăm nghệ nhân và nhà sưu tập...</p>
                        <a href="#news1">Đọc thêm</a>
                    </div>
                </article>
                <article class="news-item">
                    <div class="news-content">
                        <h2>Kỹ thuật làm gốm cổ truyền</h2>
                        <p>Tìm hiểu về các phương pháp làm gốm truyền thống đang được bảo tồn tại Việt Nam...</p>
                        <a href="#news2">Đọc thêm</a>
                    </div>
                </article>
                <article class="news-item">
                    <div class="news-content">
                        <h2>Xu hướng gốm sứ hiện đại</h2>
                        <p>Gốm sứ không chỉ là nghệ thuật mà còn là xu hướng trang trí nội thất mới...</p>
                        <a href="#news3">Đọc thêm</a>
                    </div>
                </article>
            </div>
        </section> -->

        <!-- About Section -->
        <section class="about-section">
            <!-- APK Update Section -->
<section class="apk-update-section">
    <h2>Cập nhật APK</h2>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <!-- Hiển thị thông tin APK hiện tại -->
    @if ($apk)
        <div class="apk-info">
            <h3>Phiên bản hiện tại: {{ $apk->version }}</h3>
            <p>Tên file: {{ $apk->file_name }}</p>
            <p>Ngày cập nhật: {{ $apk->updated_at->format('d/m/Y H:i:s') }}</p>
            <a href="{{ asset('storage/apk/' . $apk->file_name) }}" download>Tải xuống APK hiện tại</a>
        </div>
    @else
        <p>Chưa có APK nào được tải lên.</p>
    @endif

    <!-- Form để tải lên APK mới -->
    <div class="apk-upload-form">
        <h3>Tải lên APK mới</h3>
        <form id="apkUploadForm" method="POST" action="{{ route('apk.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="version">Phiên bản APK:</label>
                <input type="text" name="version" placeholder="Nhập phiên bản (VD: 1.0.0)" required>
            </div>
            <div class="form-group">
                <label for="apk_file">Chọn file APK:</label>
                <input type="file" name="apk_file" accept=".apk" required>
            </div>
            <button type="submit">Cập nhật APK</button>
        </form>
    </div>
</section>
            <h2>Giới thiệu tổng quan về Ceramic AI</h2>
            <div class="feature-point">
                <h3><i class="fas fa-question-circle"></i> Ceramic AI là gì?</h3>
                <p>Ceramic AI là hệ thống trí tuệ nhân tạo tiên tiến chuyên về nhận dạng và phân loại đồ gốm sứ. Công
                    nghệ của chúng tôi kết hợp học máy và cơ sở dữ liệu phong phú để xác định chính xác niên đại, xuất
                    xứ và loại hình gốm sứ chỉ từ hình ảnh đầu vào.</p>
            </div>
            <div class="feature-point">
                <h3><i class="fas fa-star"></i> Lợi ích nổi bật</h3>
                <div class="benefits-grid">
                    <div class="benefit-card">
                        <i class="fas fa-bolt"></i>
                        <h4>Nhanh chóng</h4>
                        <p>Kết quả phân tích chỉ trong vài giây, tiết kiệm thời gian nghiên cứu</p>
                    </div>
                    <div class="benefit-card">
                        <i class="fas fa-bullseye"></i>
                        <h4>Chính xác</h4>
                        <p>Độ chính xác lên đến 75% nhờ thuật toán AI được đào tạo chuyên sâu</p>
                    </div>
                    <div class="benefit-card">
                        <i class="fas fa-gem"></i>
                        <h4>Miễn phí/Có gói</h4>
                        <p>Sử dụng cơ bản miễn phí hoặc nâng cấp gói cao cấp với nhiều tính năng</p>
                    </div>
                </div>
            </div>
            <div class="feature-point">
                <h3><i class="fas fa-cubes"></i> Ứng dụng đa dạng</h3>
                <div class="applications-list">
                    <span class="application-tag">Bảo tàng</span>
                    <span class="application-tag">Nhà nghiên cứu</span>
                    <span class="application-tag">Nhà sưu tầm</span>
                    <span class="application-tag">Thương mại</span>
                    <span class="application-tag">Giáo dục</span>
                    <span class="application-tag">Bảo tồn</span>
                </div>
            </div>
            <div class="feature-point">
                <h3><i class="fas fa-book-open"></i> Hướng dẫn sử dụng</h3>
                <p>1. Tải lên hình ảnh đồ gốm cần phân tích<br>
                    2. Hệ thống AI sẽ tự động nhận diện và phân loại<br>
                    3. Nhận kết quả chi tiết về niên đại, xuất xứ và đặc điểm<br>
                    4. Lưu trữ hoặc chia sẻ kết quả phân tích</p>
                <h4 id="guideButton" onclick="redirectToGuide()">Xem hướng dẫn chi tiết</h4>
            </div>
        </section>
    </div>

    <!-- Modal -->
    <div class="modal" id="loginPrompt">
        <div class="modal-content">
            <p>Vui lòng đăng nhập để sử dụng tính năng này</p>
            <button onclick="redirectToLogin()">Đăng Nhập</button>
        </div>
    </div>

    <!-- Contact Sidebar -->
    <div class="contact-sidebar" id="contactSidebar">
        <h3>Liên hệ với chúng tôi</h3>
        <ul>
            <li><i class="fas fa-phone"></i> <span>SĐT: 0982638519</span></li>
            <li><i class="fas fa-envelope"></i> <a href="mailto:khangkhang1111777@gmail.com">Email:
                    khangkhang1111777@gmail.com</a></li>
            <li><i class="fab fa-facebook-f"></i> <a href="https://facebook.com/ceramic" target="_blank">Facebook</a>
            </li>
            <li><i class="fab fa-instagram"></i> <a href="https://instagram.com/ceramic" target="_blank">Instagram</a>
            </li>
            <li><i class="fa-brands fa-x-twitter"></i> <a href="https://twitter.com/ceramic" target="_blank">Twitter</a>
            </li>
            <li><i class="fas fa-map-marker-alt"></i> <span>Địa chỉ: 123 Đường Gốm, TP. Hà Nội</span></li>
        </ul>
        <div class="contact-form">
            <h4>Gửi liên hệ</h4>
            <form id="contactForm" method="POST" action="{{ route('contact.submit') }}">
                @csrf
                <h8>Nhập họ tên:</h8>
                <input type="text" name="name" placeholder="Họ tên" required>
                <h8>Nhập SĐT:</h8>
                <input type="tel" name="phone" placeholder="Số điện thoại" required>
                <h8>Nhập email:</h8>
                <input type="email" name="email" placeholder="Email" required>
                <h8>Nhập nội dung:</h8>
                <textarea name="message" placeholder="Nội dung" required></textarea>
                <button type="submit">Gửi</button>
            </form>
        </div>
    </div>

    <footer>
        <p>© 2023 Ceramic Classification System. All rights reserved.</p>
    </footer>

    <script>
        // Toggle menu hamburger
        const hamburger = document.querySelector('.hamburger');
        const navContainer = document.querySelector('.nav-container');
        const classificationLink = document.querySelector('#classificationLink');
        const contactLink = document.querySelector('#contactLink');
        const loginPrompt = document.querySelector('#loginPrompt');
        const contactSidebar = document.querySelector('#contactSidebar');
        let isAuthenticated = false;

        hamburger.addEventListener('click', () => {
            navContainer.classList.toggle('active');
        });

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

        function redirectToLogin() {
            window.location.href = "http://localhost:8000/login";
        }

        function redirectToGuide() {
            window.location.href = "http://localhost:8000/guide";
        }

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

        classificationLink.addEventListener('click', (e) => {
            if (!isAuthenticated) {
                e.preventDefault();
                loginPrompt.style.display = 'flex';
            }
        });

        loginPrompt.addEventListener('click', (e) => {
            if (e.target === loginPrompt) {
                loginPrompt.style.display = 'none';
            }
        });

        contactLink.addEventListener('click', (e) => {
            e.preventDefault();
            contactSidebar.classList.add('active');
        });

        document.addEventListener('click', (e) => {
            if (contactSidebar.classList.contains('active') &&
                !contactSidebar.contains(e.target) &&
                e.target !== contactLink &&
                !navContainer.contains(e.target)) {
                contactSidebar.classList.remove('active');
            }
        });
        //Form liên hệ
        // Thêm vào cuối phần <script>
        const contactForm = document.getElementById('contactForm');

        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(contactForm);

            try {
                const response = await fetch("{{ route('contact.submit') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();
                if (response.ok) {
                    alert('Thông tin liên hệ đã được gửi thành công!');
                    contactForm.reset();
                    contactSidebar.classList.remove('active');
                } else {
                    alert('Có lỗi xảy ra: ' + result.message);
                }
            } catch (error) {
                console.error('Lỗi gửi liên hệ:', error);
                alert('Không thể gửi liên hệ. Vui lòng thử lại sau.');
            }
        });
        window.onload = checkLoginStatus;
    </script>
</body>

</html>