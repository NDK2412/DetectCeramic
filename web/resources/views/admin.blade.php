<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        // Lấy metadata cho trang admin
        $metadataForAdmin = App\Models\Metadata::where('page', 'admin')->first();
        // Lấy APK mới nhất (giữ nguyên nếu cần)
        $latestApk = App\Models\Apk::latest()->first();
    @endphp

    <!-- Sử dụng metadata cho title, description, keywords, favicon -->
    <title>{{ $metadataForAdmin->title ?? 'Trang chủ' }}</title>
    <meta name="description" content="{{ $metadataForAdmin->description ?? '' }}">
    <meta name="keywords" content="{{ $metadataForAdmin->keywords ?? '' }}">

    @if ($metadataForAdmin->favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $metadataForAdmin->favicon) }}">
    @endif
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('css/settings.css') }}">
    <link rel="stylesheet" href="{{ asset('css/HistoryDetection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Terms.css') }}">
    <!-- <link rel="stylesheet" href="{{ asset('css/llm.css') }}"> -->
    <style>
 
        :root {
            --primary-color: rgb(0, 0, 0);
            --secondary-color: #42a5f5;
            --accent-color: #eceff1;
            --light-color: #f5f7fa;
            --dark-color: #263238;
            --success-color: #00c853;
            --warning-color: #ffca28;
            --error-color: #f44336;
            --gradient: #42a5f5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--light-color);
            color: var(--dark-color);
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: 285px;
            background: var(--dark-color);
            color: white;
            height: 100vh;
            position: fixed;
            padding: 20px;
            transition: width 0.3s;
        }

        .sidebar h2 {
            font-size: 1.5rem;
            margin-bottom: 30px;
            text-align: center;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar li {
            margin-bottom: 15px;
        }

        input#userSearch {
            width: 300px;
            height: 45px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            font-size: 1rem;
            display: flex;
            align-items: center;
            padding: 5px;
            border-radius: 8px;
            transition: background 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: var(--gradient);
        }

        .sidebar a i {
            margin-right: 10px;
        }

        .content {
            margin-left: 250px;
            flex: 1;
            padding: 20px;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in;
        }

        h1 {
            font-size: 2rem;
            font-weight: 600;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
            text-align: center;
        }

        h3 {
            font-size: 1.4rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card canvas {
            width: 100% !important;
            height: 150px !important;
        }

        .stat-card {
            flex: 1;
            padding: 15px;

            color: white;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            margin: 0 10px;
            min-width: 200px;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .stat-card p {
            font-size: 1.5rem;
            font-weight: 600;
            color: black;
        }

        @media (max-width: 900px) {
            .stat-card {
                flex: 0 0 48%;
                margin-bottom: 20px;
            }
        }

        @media (max-width: 600px) {
            .stat-card {
                flex: 0 0 100%;
            }
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--accent-color);
        }

        th {
            background: var(--primary-color);
            color: white;
        }

        tr:hover {
            background: var(--light-color);
        }

        .actions {
            display: flex;
            gap: 6px;
        }

        .tab-content canvas {
            margin: 10px 0;
            max-width: 100%;
            max-height: 300px;
            /* Đảm bảo không vượt quá container */
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .edit-btn {
            background: var(--warning-color);
            color: var(--dark-color);
        }

        .edit-btn:hover {
            background: #ffb300;
            transform: translateY(-2px);
        }

        .save-btn {
            background: var(--success-color);
            color: white;
        }

        .save-btn:hover {
            background: #00a843;
            transform: translateY(-2px);
        }

        .cancel-btn {
            background: #ccc;
            color: var(--dark-color);
        }

        .cancel-btn:hover {
            background: #b0b0b0;
            transform: translateY(-2px);
        }

        .delete-btn {
            background: var(--error-color);
            color: white;
        }

        .delete-btn:hover {
            background: #d32f2f;
            transform: translateY(-2px);
        }

        .reject-btn {
            background: #ff5722;
            color: white;
        }

        .reject-btn:hover {
            background: #e64a19;
            transform: translateY(-2px);
        }

        .status {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .status.pending {
            background: var(--warning-color);
            color: white;
        }

        .status.approved {
            background: var(--success-color);
            color: white;
        }

        .status.rejected {
            background: var(--error-color);
            color: white;
        }

        .logout-btn {
            background: var(--gradient);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            display: block;
            margin: 20px auto;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .success-message {
            color: var(--success-color);
            background: #d4edda;
            padding: 8px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
        }

        .user-name {
            cursor: pointer;
            color: var(--primary-color);
            text-decoration: underline;
        }

        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            width: 1000px;
            height: 700px;
            overflow-y: auto;
            max-width: 90%;
        }

        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .popup h3 {
            font-size: 1.4rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .popup .rating-stars {
            font-size: 1.5rem;
            color: var(--warning-color);
            margin-bottom: 1rem;
        }

        .popup p {
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .popup textarea,
        .popup input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid var(--accent-color);
            border-radius: 4px;
        }

        .popup button {
            background: var(--gradient);
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 1rem;
        }

        #revenueChart {
            max-width: 100%;
            margin: 0;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Định dạng bảng trong tab Quản lý thư viện đồ gốm */
        #ceramics table {
            table-layout: fixed;
            /* Đảm bảo các cột có độ rộng cố định */
            width: 100%;
        }

        /* Đặt độ rộng cố định cho các cột */
        #ceramics th,
        #ceramics td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--accent-color);
            word-wrap: break-word;
            /* Đảm bảo nội dung dài không tràn */
            overflow: hidden;
            text-overflow: ellipsis;
            /* Thêm dấu ... khi nội dung bị cắt */
        }

        /* Đặt độ rộng cụ thể cho từng cột */
        #ceramics th:nth-child(1),
        #ceramics td:nth-child(1) {
            width: 5%;
        }

        /* ID */
        #ceramics th:nth-child(2),
        #ceramics td:nth-child(2) {
            width: 15%;
        }

        /* Tên */
        #ceramics th:nth-child(3),
        #ceramics td:nth-child(3) {
            width: 25%;
        }

        /* Mô tả */
        #ceramics th:nth-child(4),
        #ceramics td:nth-child(4) {
            width: 15%;
        }

        /* Hình ảnh */
        #ceramics th:nth-child(5),
        #ceramics td:nth-child(5) {
            width: 15%;
        }

        /* Danh mục */
        #ceramics th:nth-child(6),
        #ceramics td:nth-child(6) {
            width: 15%;
        }

        /* Nguồn gốc */
        #ceramics th:nth-child(7),
        #ceramics td:nth-child(7) {
            width: 10%;
        }

        /* Hành động */
        /* Giới hạn chiều cao và ẩn nội dung dài trong cột Mô tả */
        #ceramics .description-cell {
            max-height: 3em;
            /* Giới hạn chiều cao (khoảng 3 dòng) */
            overflow: hidden;
            position: relative;
            line-height: 1.5em;
            /* Đảm bảo chiều cao dòng phù hợp */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            /* Giới hạn số dòng hiển thị */
            -webkit-box-orient: vertical;
        }

        /* Hiển thị toàn bộ nội dung khi có class expanded */
        #ceramics .description-cell.expanded {
            max-height: none;
            -webkit-line-clamp: unset;
        }

        /* Định dạng nút Xem thêm */
        #ceramics .toggle-description {
            color: var(--secondary-color);
            cursor: pointer;
            font-size: 0.9rem;
            margin-top: 5px;
            display: inline-block;
            text-decoration: underline;
        }

        #ceramics .toggle-description:hover {
            color: var(--dark-color);
        }

        /* Đảm bảo hình ảnh không vượt quá kích thước cột */
        #ceramics .image-cell img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        /* Ẩn nút Xem thêm khi đang chỉnh sửa */
        #ceramics .editable.editing .toggle-description {
            display: none;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
            }

            .sidebar a {
                font-size: 0;
            }

            .sidebar a i {
                margin-right: 0;
                font-size: 1.2rem;
            }

            .content {
                margin-left: 80px;
            }

            .stats {
                flex-direction: column;
            }

            table {
                font-size: 0.75rem;
            }
        }

        /* Thêm vào phần style */
        .action-btn.save-btn i {
            font-size: 0.9rem;
        }

        /* Lich Sử nhán diện .popup */
        /* Cải tiến Popup Lịch Sử Nhận Diện */
        #classificationPopup {
            width: 1000px;
            max-width: 95%;
            height: 700px;
            overflow-y: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        #classificationPopup h3 {
            font-size: 1.6rem;
            color: var(--primary-color);
            margin-bottom: 20px;
            text-align: center;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        #classificationPopup table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        #classificationPopup th,
        #classificationPopup td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--accent-color);
        }

        #classificationPopup th {
            background: var(--primary-color);
            color: white;
            font-weight: 500;
        }

        #classificationPopup tr:hover {
            background: var(--light-color);
        }

        #classificationPopup td img {
            max-width: 80px;
            height: auto;
            border-radius: 5px;
        }

        /* Giới hạn chiều cao và ẩn nội dung dài trong cột Thông Tin */
        #classificationPopup .info-cell {
            max-height: 3em;
            /* Giới hạn chiều cao (khoảng 3 dòng) */
            overflow: hidden;
            position: relative;
            line-height: 1.5em;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            /* Giới hạn số dòng hiển thị */
            -webkit-box-orient: vertical;
        }

        /* Hiển thị toàn bộ nội dung khi có class expanded */
        #classificationPopup .info-cell.expanded {
            max-height: none;
            -webkit-line-clamp: unset;
        }

        /* Định dạng nút Xem thêm */
        #classificationPopup .toggle-info {
            color: var(--secondary-color);
            cursor: pointer;
            font-size: 0.9rem;
            margin-top: 5px;
            display: inline-block;
            text-decoration: underline;
        }

        #classificationPopup .toggle-info:hover {
            color: var(--dark-color);
        }

        #classificationPopup button {
            background: var(--gradient);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
            transition: all 0.3s;
        }

        #classificationPopup button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        /* Dấu chấm xanh */
        .notification-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            background-color: var(--success-color);
            /* Màu xanh lá cây */
            border-radius: 50%;
            margin-left: 8px;
            vertical-align: middle;
        }

        .menu .logout {
            margin-top: auto;
            /* đẩy nút xuống cuối */
        }

        /* Tab Tin tức */
        /* Định dạng bảng trong tab Quản lý tin tức */
        #news table {
            table-layout: auto;
            margin: 5px;
            ;
            width: 100%;
        }

        #news th:nth-child(1),
        #news td:nth-child(1) {
            width: 5%;
        }

        /* ID */
        #news th:nth-child(2),
        #news td:nth-child(2) {
            width: 20%;
        }

        /* Tiêu đề */
        #news th:nth-child(3),
        #news td:nth-child(3) {
            width: 20%;
        }

        /* Mô tả ngắn */
        #news th:nth-child(4),
        #news td:nth-child(4) {
            width: 15%;
        }

        /* Hình ảnh */
        #news th:nth-child(5),
        #news td:nth-child(5) {
            width: 25%;
        }

        /* Nội dung */
        #news th:nth-child(6),
        #news td:nth-child(6) {
            width: 15%;
        }

        /* Hành động */
        #news .description-cell {
            max-height: 3em;
            overflow: hidden;
            line-height: 1.5em;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        #news .description-cell.expanded {
            max-height: none;
            -webkit-line-clamp: unset;
        }

        #news .toggle-description {
            color: var(--secondary-color);
            cursor: pointer;
            font-size: 0.9rem;
            margin-top: 5px;
            display: inline-block;
            text-decoration: underline;
        }

        #news .toggle-description:hover {
            color: var(--dark-color);
        }

        #news .editable.editing .toggle-description {
            display: none;
        }

        div.sidebar {
            overflow-y: scroll;
        }

        .status.active {
            background: var(--success-color);
            color: white;
        }

        .status.inactive {
            background: var(--error-color);
            color: white;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pagination a {
            padding: 8px 14px;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .pagination a:hover {
            background: var(--secondary-color);
        }

        .pagination a.active {
            background: var(--success-color);
            font-weight: bold;
        }

        .pagination a.disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        #classificationHistoryContent {
            max-height: 526px;
            overflow-y: auto;
        }

        div#contacts.container.tab-content table tbody tr td span {
            background-color: greenyellow;
        }

        /* Thông tin hệ thống */
        /* Định dạng menu con */
        .system-info-menu {
            position: relative;
        }

        .submenu {
            position: absolute;
            left: 100%;
            top: 0;
            width: 150px;
            background: var(--dark-color);
            list-style: none;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .submenu li {
            margin-bottom: 10px;
        }

        .submenu a {
            padding: 8px;
            font-size: 0.9rem;
        }

        .submenu a:hover {
            background: var(--gradient);
        }

        /* Hiển thị submenu khi hover */
        .system-info-menu:hover .submenu {
            display: block;
        }

        /* Định dạng tab nội dung */
        #system-info .tab-container {
            margin-top: 20px;
        }

        #system-info .tab-button {
            padding: 10px 20px;
            cursor: pointer;
            background: var(--accent-color);
            border-radius: 5px;
            margin-right: 10px;
        }

        #system-info .tab-button.active {
            background: var(--gradient);
            color: white;
        }

        #system-info .tab-content {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        /* Cảnh báo khẩn cấp */
        .alert-emergency {
            background-color: #ff3333;
            color: white;
            padding: 10px;
            margin-bottom: 20px;
            font-weight: bold;
            border-radius: 5px;
        }

        .stats-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        /* LLM Settings */
        #llm_api_key {
            width: 100%;
            max-width: 400px;
            padding: 10px 15px;
            border: 1px solid #d1d5db;
            /* Màu xám nhạt */
            border-radius: 8px;
            /* Góc bo tròn */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            /* Bóng mờ */
            font-size: 16px;
            color: #374151;
            /* Màu xám đậm */
            background-color: #ffffff;
            /* Màu nền trắng */
            transition: border-color 0.3s, box-shadow 0.3s;
            /* Hiệu ứng chuyển đổi mượt */
        }

        #llm_api_key:focus {
            border-color: #6366f1;
            /* Màu xanh Indigo */
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            /* Hiệu ứng focus */
            outline: none;
            /* Bỏ viền mặc định */
        }

        #llm_api_key::placeholder {
            color: #9ca3af;
            /* Màu placeholder xám nhạt */
            font-style: italic;
        }

        .apk-update-section {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            text-align: center;
            animation: fadeIn 1s ease-out;
        }

        .apk-update-section h2 {
            font-size: 2rem;
            color: var(--dark-color);
            margin-bottom: 1.5rem;
        }

        .apk-info {
            margin-bottom: 2rem;
            text-align: left;
            padding: 1rem;
            background-color: var(--accent-color);
            border-radius: 8px;
        }

        .apk-info h3 {
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .apk-info p {
            color: #555;
            margin-bottom: 0.5rem;
        }

        .apk-info a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .apk-info a:hover {
            color: var(--dark-color);
        }

        @media (max-width: 768px) {
            .apk-update-section {
                padding: 1.5rem;
            }

            .apk-update-section h2 {
                font-size: 1.5rem;
            }

            .apk-info {
                padding: 0.5rem;
            }
        }

        /* apk upload */
        /* CSS cho phần tử apk-upload-form */
        .apk-upload-form {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 500px;
            margin: 20px auto;
        }

        /* Tiêu đề trong apk-upload-form */
        .apk-upload-form h3 {
            font-size: 1.5rem;
            color: #333;
            margin: 0 0 20px;
            text-align: center;
            font-weight: 600;
        }

        /* Form bên trong apk-upload-form */
        .apk-upload-form form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        /* Nhóm form-group trong apk-upload-form */
        .apk-upload-form .form-group {
            display: flex;
            flex-direction: column;
        }

        /* Nhãn trong form-group */
        .apk-upload-form .form-group label {
            font-size: 0.9rem;
            color: #555;
            margin-bottom: 5px;
            font-weight: 500;
        }

        /* Input text và file trong form-group */
        .apk-upload-form .form-group input[type="text"],
        .apk-upload-form .form-group input[type="file"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
            color: #333;
            background-color: #f9f9f9;
            transition: border-color 0.3s ease;
        }

        /* Hiệu ứng focus và hover cho input */
        .apk-upload-form .form-group input[type="text"]:focus,
        .apk-upload-form .form-group input[type="file"]:focus {
            outline: none;
            border-color: #007bff;
            background-color: #fff;
        }

        /* Tùy chỉnh input file để đẹp hơn */
        .apk-upload-form .form-group input[type="file"] {
            padding: 5px;
        }

        /* Nút submit trong apk-upload-form */
        .apk-upload-form .action-btn.save-btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background-color 0.3s ease;
        }

        /* Hiệu ứng hover cho nút */
        .apk-upload-form .action-btn.save-btn:hover {
            background-color: #0056b3;
        }

        /* Icon trong nút */
        .apk-upload-form .action-btn.save-btn i {
            font-size: 1.1rem;
        }

        /* Responsive cho màn hình nhỏ */
        @media (max-width: 600px) {
            .apk-upload-form {
                padding: 15px;
                margin: 10px;
            }

            .apk-upload-form h3 {
                font-size: 1.3rem;
            }

            .apk-upload-form .action-btn.save-btn {
                padding: 10px;
                font-size: 0.9rem;
            }
        }

        /* Định dạng tab con trong Quản lý người dùng */
        .sub-tab-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .sub-tab-button {
            padding: 10px 20px;
            cursor: pointer;
            background: var(--accent-color);
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            color: var(--dark-color);
            transition: all 0.3s;
        }

        .sub-tab-button.active {
            background: var(--gradient);
            color: white;
        }

        .sub-tab-button:hover {
            background: var(--secondary-color);
            color: white;
        }

        .sub-tab-content {
            display: none;
        }

        .sub-tab-content.active {
            display: block;
        }

        .rating-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 15px;
        }

        .rating-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            max-width: 400px;
            margin-top: 10px;
        }

        .rating-left,
        .rating-right {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 80px;
        }

        .average-rating,
        .total-ratings {
            font-size: 2rem;
            font-weight: 600;
            color: var(--dark-color);
        }

        .rating-left p,
        .rating-right p {
            font-size: 0.9rem;
            color: var(--dark-color);
            margin-top: 5px;
        }

        .rating-middle {
            flex: 1;
            margin: 0 15px;
        }

        .rating-bar {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }

        .star-label {
            width: 40px;
            font-size: 0.9rem;
            color: var(--dark-color);
        }

        .bar-container {
            flex: 1;
            height: 10px;
            background-color: #e0e0e0;
            border-radius: 5px;
            margin: 0 10px;
            overflow: hidden;
        }

        .bar {
            height: 100%;
            border-radius: 5px;
            transition: width 0.3s;
        }

        .rating-count {
            width: 60px;
            font-size: 0.9rem;
            color: var(--dark-color);
            text-align: right;
        }
        #revenueChartMonth {
            width: 100% !important;
            height: 400px !important;
            display: block !important; /* Đảm bảo canvas hiển thị */
            visibility: visible !important;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h2>Admin</h2>
        <ul>
            <li><a href="#" data-tab="overview" class="active"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
            <li>
                <a href="#" data-tab="recharge">
                    <i class="fas fa-money-bill"></i> Deposit Request
                    @if ($rechargeRequests->isNotEmpty())
                        <span class="notification-dot"></span>
                    @endif
                </a>
            </li>
            <li><a href="#" data-tab="users"><i class="fas fa-users"></i> User Management</a></li>
            <li><a href="#" data-tab="metadata"><i class="fas fa-cogs"></i> Metadata Management</a></li>
            <li><a href="#" data-tab="apk-update"><i class="fas fa-upload"></i> APK Management</a></li>
            <li>
                <a href="#" data-tab="contacts">
                    <i class="fas fa-envelope"></i> Contact
                    @if ($contacts->where('is_read', false)->isNotEmpty())
                        <span class="notification-dot"></span>
                    @endif
                </a>
            </li>

            <li><a href="#" data-tab="recharge-packages"><i class="fas fa-box"></i> Manage packages</a></li>
            <li><a href="#" data-tab="revenue"><i class="fas fa-chart-line"></i> Revenue</a></li>
            <li><a href="#" data-tab="ceramics"><i class="fa-solid fa-layer-group"></i> Ceramic library management</a>
            </li>
            <li><a href="#" data-tab="news"><i class="fas fa-newspaper"></i>News management</a></li>
            <li><a href="#" data-tab="classifications"><i class="fas fa-history"></i>History of Identification</a></li>
            <li class="system-info-menu">
                <a href="#" data-tab="system-info"><i class="fas fa-server"></i>System information</a>
            </li>
            <li><a href="#" data-tab="terms"><i class="fas fa-file-alt"></i>Terms and Policies</a></li>
            <li><a href="#" data-tab="settings"><i class="fas fa-cog"></i> Setting</a></li>
            <li><a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                        class="fas fa-sign-out-alt"></i>Log out</a></li>
        </ul>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
    <div class="content">
        <!-- Tab Tổng quan -->
        <div class="container tab-content" id="overview">
            <h1>Overview</h1>
            <!-- Hàng 1 -->
            <div class="stats-row">
                <div class="stat-card">
                    <h3>Total users</h3>
                    <p>{{ $users->count() }}</p>
                    <canvas id="userStatusPieChart" style="max-width: 500px; max-height: 500px;"></canvas>
                </div>
                <div class="stat-card">
                    <h3>Request pending approval</h3>
                    <p>{{ $rechargeRequests->count() }}</p>
                    <canvas id="rechargeTrendChart"></canvas>
                </div>
                <div class="stat-card">
                    <h3>Total revenue</h3>
                    <p>{{ number_format($totalRevenue) }} VNĐ</p>
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>
            <!-- Hàng 2 -->
            <div class="stats-row">
                <div class="stat-card">
                    <h3>Request status</h3>
                    <div style="display: flex; flex-direction: column; align-items: center;">
                        <p style="margin-top: 10px;">
                            Resolved: {{ $approvedRequests }} | Escalated: {{ $rejectedRequests }} | Pending:
                            {{ $pendingRequests }}
                        </p>
                        <canvas id="requestStatusPieChart" style="max-width: 300px; max-height: 300px;"></canvas>

                    </div>
                </div>
                <div class="stat-card rating-card">
                    <h3>Average rating</h3>
                    <div class="rating-container">
                        <div class="rating-left">
                            <span class="average-rating">{{ number_format($averageRating, 1) }}</span>
                            <p>Average Rating</p>
                        </div>
                        <div class="rating-middle">
                            @php
                                $ratingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
                                $totalRatings = $users->sum(function ($user) {
                                    return $user->rating ? 1 : 0;
                                });
                                foreach ($users as $user) {
                                    if ($user->rating) {
                                        $ratingCounts[$user->rating]++;
                                    }
                                }
                            @endphp
                            @for ($i = 5; $i >= 1; $i--)
                                <div class="rating-bar">
                                    <span class="star-label">{{ $i }} ★</span>
                                    <div class="bar-container">
                                        <div class="bar"
                                            style="width: {{ $totalRatings > 0 ? ($ratingCounts[$i] / $totalRatings * 100) : 0 }}%; background-color: {{ $i == 5 ? '#00c853' : ($i == 4 ? '#66bb6a' : ($i == 3 ? '#ffca28' : ($i == 2 ? '#ff9800' : '#f44336'))) }};">
                                        </div>
                                    </div>
                                    <span class="rating-count">{{ $ratingCounts[$i] }}
                                        ({{ $totalRatings > 0 ? number_format($ratingCounts[$i] / $totalRatings * 100) : 0 }}%)</span>
                                </div>
                            @endfor
                        </div>
                        <div class="rating-right">
                            <span class="total-ratings">{{ $totalRatings }}</span>
                            <p>Total Ratings</p>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <h3>Total recognition</h3>
                    <p>{{ number_format($totalTokenUsed) }}</p>
                    <canvas id="tokenTrendChart"></canvas>
                </div>
            </div>
            <!-- Nút tab con -->
            <div class="sub-tab-container" style="margin-bottom: 20px;">
                <button class="sub-tab-button active"
                    onclick="openOverviewSubTab('transaction-history-tab')">Transaction History</button>
                <button class="sub-tab-button" onclick="openOverviewSubTab('revenue-by-user-tab')">Revenue Per
                    User</button>
            </div>
            <!-- Bảng lịch sử giao dịch -->
            <div class="sub-tab-content active" id="transaction-history-tab">
                <h3>Transaction History</h3>
                <form action="{{ route('admin.export.transaction.history') }}" method="GET"
                    style="margin-bottom: 20px;">
                    <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                        <div>
                            <label for="start_date">From date:</label>
                            <input type="date" id="start_date" name="start_date" required>
                        </div>
                        <div>
                            <label for="end_date">By date:</label>
                            <input type="date" id="end_date" name="end_date" required>
                        </div>
                        <button type="submit" class="action-btn save-btn">
                            <i class="fas fa-file-excel"></i> Excel Export
                        </button>
                    </div>
                </form>
                @if ($transactionHistory->isEmpty())
                    <p>No transactions yet.</p>
                @else
                    <table id="transactionTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User name</th>
                                <th>Amount</th>
                                <th>Corresponding Tokens</th>
                                <th>Status</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody id="transactionBody">
                            <!-- Nội dung sẽ được xử lý bằng JavaScript -->
                        </tbody>
                    </table>
                    <div class="pagination" id="transactionPagination" style="text-align: center; margin-top: 20px;">
                        <!-- Phân trang sẽ được thêm bằng JavaScript -->
                    </div>
                @endif
            </div>
            <!-- Bảng Doanh Thu Theo Người Dùng -->
            <div class="sub-tab-content" id="revenue-by-user-tab" style="display: none;">
                <h3>Revenue Per User</h3>
                @if ($revenueByUser->isEmpty())
                    <p>No revenue data available.</p>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>User name</th>
                                <th>Revenue (VNĐ)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($revenueByUser as $userId => $data)
                                <tr>
                                    <td>{{ $data['name'] }}</td>
                                    <td>{{ number_format($data['total_revenue']) }} VNĐ</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
        <div class="container tab-content" id="apk-update" style="display: none;">
            <h1>Quản Lý APK</h1>
            @if (session('success'))
                <div class="success-message">{{ session('success') }}</div>
            @endif
            <div class="apk-update-section">
                <h2>Cập nhật APK</h2>
                <div class="apk-info">
                    <h3>Thông tin APK hiện tại</h3>
                    @if ($latestApk)
                        <p><strong>Phiên bản:</strong> {{ $latestApk->version }}</p>
                        <p><strong>Tên tệp:</strong> {{ $latestApk->file_name }}</p>
                        <p><strong>Ngày cập nhật:</strong> {{ $latestApk->updated_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Liên kết tải:</strong> <a href="{{ Storage::url($latestApk->file_path) }}"
                                target="_blank">Tải xuống</a></p>
                    @else
                        <p>Chưa có APK nào được tải lên.</p>
                    @endif
                </div>
                <div class="apk-upload-form">
                    <h3>Tải lên APK mới</h3>
                    <form action="{{ route('admin.apk.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="version">Phiên bản</label>
                            <input type="text" name="version" placeholder="Nhập phiên bản (VD: 1.0.0)" required>
                        </div>
                        <div class="form-group">
                            <label for="apkFile">Tệp APK</label>
                            <input type="file" name="apkFile" accept=".apk" required>
                        </div>
                        <button type="submit" class="action-btn save-btn"><i class="fas fa-upload"></i> Tải lên</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Tab Liên hệ -->
        <div class="container tab-content" id="contacts" style="display: none;">
            <h2>Danh sách liên hệ từ người dùng</h2>
            @if (session('success'))
                <div class="success-message">{{ session('success') }}</div>
            @endif
            @if (!isset($contacts) || $contacts->isEmpty())
                <p>Chưa có liên hệ nào từ người dùng.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Họ tên</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contacts as $contact)
                            <tr
                                style="background-color: {{ $contact->is_read ? 'var(--card-bg)' : 'rgba(42, 92, 139, 0.1)' }}; 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            color: {{ $contact->is_read ? 'var(--text)' : 'var(--primary)' }};
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            border-left: 4px solid {{ $contact->is_read ? 'transparent' : 'var(--secondary)' }}">
                                <td>{{ $contact->name }}</td>
                                <td>
                                    <span
                                        style="display: inline-block; 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    padding: 0.25rem 0.5rem;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    border-radius: 12px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    background-color: {{ $contact->is_read ? 'var(--border)' : 'var(--secondary)' }};
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    color: {{ $contact->is_read ? 'var(--text)' : 'var(--text-light)' }};
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    font-size: 0.85rem;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    font-weight: 500;">
                                        {{ $contact->is_read ? 'Đã đọc' : 'Chưa đọc' }}
                                    </span>
                                </td>
                                <td>
                                    <button
                                        onclick="showContactPopup('{{ $contact->id }}', '{{ $contact->name }}', '{{ $contact->phone }}', '{{ $contact->email }}', '{{ $contact->message }}', '{{ $contact->is_read ? 'Đã đọc' : 'Chưa đọc' }}', {{ $contact->is_read ? 'true' : 'false' }})"
                                        class="action-btn view-btn"
                                        style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background-color: var(--primary); color: var(--text-light); border-radius: 6px; border: none; cursor: pointer; transition: var(--transition);">
                                        <i class="fas fa-eye"></i> Xem
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <!-- <div class="container tab-content" id="apk-update" style="display: none;">
            <h1>Quản Lý APK</h1>
            @if (session('success'))
                <div class="success-message">{{ session('success') }}</div>
            @endif
            <div class="apk-update-section">
                <h2>Cập nhật APK</h2>
                <div class="apk-info">
                    <h3>Thông tin APK hiện tại</h3>
                    @if ($latestApk)
                        <p><strong>Phiên bản:</strong> {{ $latestApk->version }}</p>
                        <p><strong>Tên tệp:</strong> {{ $latestApk->file_name }}</p>
                        <p><strong>Ngày cập nhật:</strong> {{ $latestApk->updated_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Liên kết tải:</strong> <a href="{{ url('/storage/apks/' . $latestApk->file_name) }}"
                                target="_blank">Tải xuống</a></p>
                    @else
                        <p>Chưa có APK nào được tải lên.</p>
                    @endif
                </div>
                <div class="sub-tab-content active" id="upload-apk">
                    <h3>Tải Lên APK</h3>
                    @if (session('success'))
                        <div class="success-message">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div
                            style="color: var(--error-color); background: #f8d7da; padding: 8px; border-radius: 4px; margin-bottom: 15px; text-align: center;">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('admin.apk.upload') }}" method="POST" enctype="multipart/form-data"
                        id="apkUploadForm">
                        @csrf
                        <div class="form-group">
                            <label for="apkFile">Chọn file APK</label>
                            <input type="file" name="apkFile" id="apkFile" accept=".apk" required>
                            @error('apkFile')
                                <span style="color: var(--error-color); font-size: 0.9rem;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="version">Version</label>
                            <input type="number" name="version" id="version" min="1" required>
                            @error('version')
                                <span style="color: var(--error-color); font-size: 0.9rem;">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="action-btn save-btn">Tải Lên</button>
                    </form>
                </div>

            </div>
        </div> -->
        <!-- Tab Quản lý gói nạp tiền -->
        <div class="container tab-content" id="recharge-packages" style="display: none;">
            <h1>Quản Lý Gói Nạp Tiền</h1>
            <!-- Nút thêm gói nạp tiền mới -->
            <button type="button" class="action-btn save-btn" onclick="showAddPackagePopup()"
                style="margin-bottom: 20px;">
                <i class="fas fa-plus"></i> Thêm gói nạp tiền mới
            </button>
            <!-- Thông báo thành công (nếu có) -->
            @if (session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif
            <!-- Bảng danh sách gói nạp tiền -->
            @if ($packages->isEmpty())
                <p>Không có gói nạp tiền nào.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Số Tiền (VNĐ)</th>
                            <th>Số Token</th>
                            <th>Mô Tả</th>
                            <th>Trạng Thái</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($packages as $package)
                            <tr id="package-row-{{ $package->id }}">
                                <form action="{{ route('recharge-packages.update', $package->id) }}" method="POST"
                                    class="edit-form" id="package-form-{{ $package->id }}">
                                    @csrf
                                    @method('PUT')
                                    <td>{{ $package->id }}</td>
                                    <td class="editable" data-field="amount">
                                        <span class="display">{{ number_format($package->amount) }}</span>
                                        <input type="number" name="amount" value="{{ $package->amount }}" style="display:none;"
                                            min="1000">
                                    </td>
                                    <td class="editable" data-field="tokens">
                                        <span class="display">{{ $package->tokens }}</span>
                                        <input type="number" name="tokens" value="{{ $package->tokens }}" style="display:none;"
                                            min="1">
                                    </td>
                                    <td class="editable" data-field="description">
                                        <span class="display description-cell"
                                            id="description-{{ $package->id }}">{{ $package->description ?? 'Không có' }}</span>
                                        <span class="toggle-description"
                                            onclick="togglePackageDescription('{{ $package->id }}')"
                                            id="toggle-{{ $package->id }}">Xem thêm</span>
                                        <textarea name="description"
                                            style="display:none;">{{ $package->description }}</textarea>
                                    </td>
                                    <td class="editable" data-field="is_active">
                                        <span
                                            class="display status {{ $package->is_active ? 'active' : 'inactive' }}">{{ $package->is_active ? 'Hoạt động' : 'Không hoạt động' }}</span>
                                        <select name="is_active" style="display:none;">
                                            <option value="1" {{ $package->is_active ? 'selected' : '' }}>Hoạt động</option>
                                            <option value="0" {{ !$package->is_active ? 'selected' : '' }}>Không hoạt động
                                            </option>
                                        </select>
                                    </td>
                                    <td class="actions">
                                        <button type="button" class="action-btn edit-btn"
                                            onclick="editPackageRow({{ $package->id }})"><i class="fas fa-edit"></i>
                                            Sửa</button>
                                        <form action="{{ route('recharge-packages.update', $package->id) }}" method="POST"
                                            style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa gói này?');">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="action-btn save-btn" style="display:none;"><i
                                                    class="fas fa-save"></i> Lưu</button>
                                        </form>

                                        <button type="button" class="action-btn cancel-btn" style="display:none;"
                                            onclick="cancelPackageEdit({{ $package->id }})"><i class="fas fa-times"></i>
                                            Hủy</button>
                                        <form action="{{ route('recharge-packages.destroy', $package->id) }}" method="POST"
                                            style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa gói này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn delete-btn"><i class="fas fa-trash"></i>
                                                Xóa</button>
                                        </form>
                                    </td>
                                </form>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <div class="container tab-content" id="users" style="display: none;">
            <h1>User Management</h1>
            @if (session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div
                    style="color: var(--error-color); background: #f8d7da; padding: 8px; border-radius: 4px; margin-bottom: 15px; text-align: center;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="filter-search" style="margin-bottom: 20px;">
                <input type="text" id="userSearch" placeholder="Search by name or email..." onkeyup="filterUsers()">
            </div>

            <!-- Nút tab con -->
            <div class="sub-tab-container" style="margin-bottom: 20px;">
                <button class="sub-tab-button active" onclick="openSubTab('active-users')">Active</button>
                <button class="sub-tab-button" onclick="openSubTab('inactive-users')">Inactive</button>
            </div>

            <!-- Nội dung tab con: Người dùng Hoạt động -->
            <div class="sub-tab-content" id="active-users">
                <h3>Active Users</h3>
                @if ($users->where('status', 'active')->isEmpty())
                    <p>There are no active users.</p>
                @else
                    <table id="active-users-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Tokens</th>
                                <th>Tokens used</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Action</th>
                                <th>Login History</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users->where('status', 'active') as $user)
                                <tr id="row-{{ $user->id }}">
                                    <td>{{ $user->id }}</td>
                                    <td class="editable" data-field="name">
                                        <span class="display user-name"
                                            onclick="showPopup('{{ $user->id }}', '{{ $user->name }}', '{{ $user->rating ?? 0 }}', '{{ $user->feedback ?? 'Chưa có phản hồi' }}')">{{ $user->name }}</span>
                                        <input type="text" name="name" value="{{ $user->name }}" style="display:none;">
                                    </td>
                                    <td class="editable" data-field="email">
                                        <span class="display">{{ $user->email }}</span>
                                        <input type="email" name="email" value="{{ $user->email }}" style="display:none;">
                                    </td>
                                    <td class="editable" data-field="role">
                                        <span class="display">{{ $user->role }}</span>
                                        <select name="role" style="display:none;">
                                            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Người dùng
                                            </option>
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </td>
                                    <td class="editable" data-field="tokens">
                                        <span class="display">{{ $user->tokens }}</span>
                                        <input type="number" name="tokens" value="{{ $user->tokens }}" style="display:none;"
                                            min="0">
                                    </td>
                                    <td>{{ $user->tokens_used }}</td>
                                    <td class="editable" data-field="phone">
                                        <span class="display">{{ $user->phone ?? 'Chưa có số điện thoại' }}</span>
                                        <input type="text" name="phone" value="{{ $user->phone }}" style="display:none;">
                                    </td>
                                    <td class="editable" data-field="status">
                                        <span
                                            class="display status {{ $user->status }}">{{ ucfirst($user->status === 'active' ? 'Hoạt động' : 'Không hoạt động') }}</span>
                                        <select name="status" style="display:none;">
                                            <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Hoạt động
                                            </option>
                                            <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Không
                                                hoạt động</option>
                                        </select>
                                    </td>
                                    <td class="actions">
                                        <form action="{{ route('admin.update', $user->id) }}" method="POST" class="edit-form"
                                            id="form-{{ $user->id }}" style="display:inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="button" class="action-btn edit-btn"
                                                onclick="editRow({{ $user->id }})"><i class="fas fa-edit"></i> Sửa</button>
                                            <button type="submit" class="action-btn save-btn" style="display:none;"><i
                                                    class="fas fa-save"></i> Lưu</button>
                                            <button type="button" class="action-btn cancel-btn" style="display:none;"
                                                onclick="cancelEdit({{ $user->id }})"><i class="fas fa-times"></i> Hủy</button>
                                        </form>
                                    </td>
                                    <td>
                                        <button class="action-btn save-btn"
                                            onclick="showLoginHistory('{{ $user->id }}', '{{ $user->name }}')">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <!-- Nội dung tab con: Người dùng Không hoạt động -->
            <div class="sub-tab-content" id="inactive-users" style="display: none;">
                <h3>Inactive Users</h3>
                @if ($users->where('status', 'inactive')->isEmpty())
                    <p>There are no inactive users.</p>
                @else
                    <table id="inactive-users-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Tokens</th>
                                <th>Tokens used</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Actiton</th>
                                <th>Login History</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users->where('status', 'inactive') as $user)
                                <tr id="row-{{ $user->id }}">
                                    <td>{{ $user->id }}</td>
                                    <td class="editable" data-field="name">
                                        <span class="display user-name"
                                            onclick="showPopup('{{ $user->id }}', '{{ $user->name }}', '{{ $user->rating ?? 0 }}', '{{ $user->feedback ?? 'Chưa có phản hồi' }}')">{{ $user->name }}</span>
                                        <input type="text" name="name" value="{{ $user->name }}" style="display:none;">
                                    </td>
                                    <td class="editable" data-field="email">
                                        <span class="display">{{ $user->email }}</span>
                                        <input type="email" name="email" value="{{ $user->email }}" style="display:none;">
                                    </td>

                                    <td class="editable" data-field="role">
                                        <span class="display">{{ $user->role }}</span>
                                        <select name="role" style="display:none;">
                                            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Người dùng
                                            </option>
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </td>
                                    <td class="editable" data-field="tokens">
                                        <span class="display">{{ $user->tokens }}</span>
                                        <input type="number" name="tokens" value="{{ $user->tokens }}" style="display:none;"
                                            min="0">
                                    </td>
                                    <td>{{ $user->tokens_used }}</td>
                                    <td class="editable" data-field="phone">
                                        <span class="display">{{ $user->phone ?? 'Chưa có số điện thoại' }}</span>
                                        <input type="text" name="phone" value="{{ $user->phone }}" style="display:none;">
                                    </td>
                                    <td class="editable" data-field="status">
                                        <span
                                            class="display status {{ $user->status }}">{{ ucfirst($user->status === 'active' ? 'Hoạt động' : 'Không hoạt động') }}</span>
                                        <select name="status" style="display:none;">
                                            <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Hoạt động
                                            </option>
                                            <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Không
                                                hoạt động</option>
                                        </select>
                                    </td>
                                    <td class="actions">
                                        <form action="{{ route('admin.update', $user->id) }}" method="POST" class="edit-form"
                                            id="form-{{ $user->id }}" style="display:inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="button" class="action-btn edit-btn"
                                                onclick="editRow({{ $user->id }})"><i class="fas fa-edit"></i> Sửa</button>
                                            <button type="submit" class="action-btn save-btn" style="display:none;"><i
                                                    class="fas fa-save"></i> Lưu</button>
                                            <button type="button" class="action-btn cancel-btn" style="display:none;"
                                                onclick="cancelEdit({{ $user->id }})"><i class="fas fa-times"></i> Hủy</button>
                                        </form>
                                    </td>
                                    <td>
                                        <button class="action-btn save-btn"
                                            onclick="showLoginHistory('{{ $user->id }}', '{{ $user->name }}')">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
        <!-- Popup thêm gói nạp tiền mới -->
        <div class="popup-overlay" id="addPackageOverlay" onclick="hideAddPackagePopup()"></div>
        <div class="popup" id="addPackagePopup">
            <h3>Thêm Gói Nạp Tiền Mới</h3>
            <form id="addPackageForm" method="POST" action="{{ route('recharge-packages.store') }}">
                @csrf
                <p><strong>Số Tiền (VNĐ):</strong></p>
                <input type="number" name="amount" required placeholder="Nhập số tiền" min="1000">
                <p><strong>Số Token:</strong></p>
                <input type="number" name="tokens" required placeholder="Nhập số token" min="1">
                <p><strong>Mô Tả:</strong></p>
                <textarea name="description" rows="4" placeholder="Nhập mô tả (tùy chọn)"></textarea>
                <p><strong>Trạng Thái:</strong></p>
                <select name="is_active">
                    <option value="1">Hoạt động</option>
                    <option value="0">Không hoạt động</option>
                </select>
                <button type="submit">Thêm</button>
            </form>
        </div>
        <!-- Popup Lịch Sử Đăng Nhập -->
        <div class="popup-overlay" id="loginHistoryOverlay" onclick="hideLoginHistory()"></div>
        <div class="popup" id="loginHistoryPopup">
            <h3>Lịch Sử Đăng Nhập của <span id="loginHistoryUserName"></span></h3>
            <div id="loginHistoryContent">
                <table>
                    <thead>
                        <tr>
                            <th>Thời Gian</th>
                            <th>Địa Chỉ IP</th>
                            <th>Thiết Bị</th>
                        </tr>
                    </thead>
                    <tbody id="loginHistoryTable">
                        <!-- Nội dung sẽ được thêm bằng JavaScript -->
                    </tbody>
                </table>
            </div>
            <button onclick="hideLoginHistory()">Đóng</button>
        </div>
        <!-- Tab Yêu cầu nạp tiền -->
        <div class="container tab-content" id="recharge" style="display: none;">
            <h1>Deposit Request</h1>
            @if ($rechargeRequests->isEmpty())
                <p>There are no pending requests.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User name</th>
                            <th>Amount</th>
                            <th>Corresponding Tokens</th>
                            <th>Photo evidence</th>
                            <th>Function</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rechargeRequests as $request)
                            <tr>
                                <td>{{ $request->id }}</td>
                                <td>{{ $request->user->name }}</td>
                                <td>{{ number_format($request->amount) }} VNĐ</td>
                                <td>{{ $request->requested_tokens }}</td>
                                <td>
                                    @if ($request->proof_image)
                                        <a href="{{ url('/storage/' . $request->proof_image) }}" target="_blank">
                                            <img src="{{ url('/storage/' . $request->proof_image) }}" alt="Proof"
                                                style="max-width: 100px; border-radius: 5px;">
                                        </a>
                                    @else
                                        <p>No photo evidence</p>
                                    @endif
                                </td>
                                <td class="actions">
                                    <form action="{{ route('admin.recharge.approve', $request->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        <button type="submit" class="action-btn save-btn"><i class="fas fa-check"></i>
                                            Confirm</button>
                                    </form>
                                    <button type="button" class="action-btn reject-btn"
                                        onclick="showRejectPopup('{{ $request->id }}', '{{ $request->user->name }}')"><i
                                            class="fas fa-times"></i>Not confirmed</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <!-- Tab Doanh thu -->
        <div class="container tab-content" id="revenue"  style="display: none;">
            <h1>Doanh Thu Theo Thời Gian</h1>
            <h6>Theo Ngày</h6>
            <canvas id="revenueChart" width="100%" height="400"></canvas>
            <h6>Theo tháng</h6>
            <canvas id="revenueChartMonth" width="100%" height="400" ></canvas>
        </div>
        <!-- Tab Quản lý thư viện đồ gốm -->
        <div class="container tab-content" id="ceramics" style="display: none;">
            <h1>Quản Lý Thư Viện Đồ Gốm</h1>
            <!-- Nút thêm món đồ gốm mới -->
            <button type="button" class="action-btn save-btn" onclick="showAddCeramicPopup()"
                style="margin-bottom: 20px;">
                <i class="fas fa-plus"></i> Thêm món đồ gốm mới
            </button>
            <!-- Thông báo thành công (nếu có) -->
            @if (session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif
            <!-- Bảng danh sách đồ gốm -->
            @if ($ceramics->isEmpty())
                <p>Không có món đồ gốm nào trong thư viện.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Mô tả</th>
                            <th>Hình ảnh</th>
                            <th>Danh mục</th>
                            <th>Nguồn gốc</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ceramics as $ceramic)
                            <tr id="ceramic-row-{{ $ceramic->id }}">
                                <form action="{{ route('admin.ceramics.update', $ceramic->id) }}" method="POST"
                                    class="edit-form" id="ceramic-form-{{ $ceramic->id }}">
                                    @csrf
                                    @method('PUT')
                                    <td>{{ $ceramic->id }}</td>
                                    <td class="editable" data-field="name">
                                        <span class="display">{{ $ceramic->name }}</span>
                                        <input type="text" name="name" value="{{ $ceramic->name }}" style="display:none;">
                                    </td>
                                    <td class="editable" data-field="description">
                                        <span class="display description-cell"
                                            id="description-{{ $ceramic->id }}">{{ $ceramic->description ?? 'Không có' }}</span>
                                        <span class="toggle-description" onclick="toggleDescription('{{ $ceramic->id }}')"
                                            id="toggle-{{ $ceramic->id }}">Xem thêm</span>
                                        <textarea name="description"
                                            style="display:none;">{{ $ceramic->description }}</textarea>
                                    </td>
                                    <td class="editable image-cell" data-field="image">
                                        <span class="display">
                                            @if ($ceramic->image)
                                                <img src="{{ url('/storage/' . $ceramic->image) }}" alt="{{ $ceramic->name }}"
                                                    style="max-width: 100px; border-radius: 5px;">
                                            @else
                                                Không có ảnh
                                            @endif
                                        </span>
                                        <input type="text" name="image" value="{{ $ceramic->image }}" style="display:none;"
                                            placeholder="Đường dẫn hình ảnh (ceramics/ten_hinh.jpg)">
                                    </td>
                                    <td class="editable" data-field="category">
                                        <span class="display">{{ $ceramic->category ?? 'Không có' }}</span>
                                        <input type="text" name="category" value="{{ $ceramic->category }}"
                                            style="display:none;">
                                    </td>
                                    <td class="editable" data-field="origin">
                                        <span class="display">{{ $ceramic->origin ?? 'Không có' }}</span>
                                        <input type="text" name="origin" value="{{ $ceramic->origin }}" style="display:none;">
                                    </td>
                                    <td class="actions">
                                        <button type="button" class="action-btn edit-btn"
                                            onclick="editCeramicRow({{ $ceramic->id }})"><i class="fas fa-edit"></i>
                                            Sửa</button>
                                        <form action="{{ route('admin.ceramics.update', $user->id) }}" method="POST"
                                            class="edit-form" id="form-{{ $user->id }}">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="action-btn save-btn" style="display:none;"><i
                                                    class="fas fa-save"></i> Lưu</button>
                                        </form>
                                        <button type="button" class="action-btn cancel-btn" style="display:none;"
                                            onclick="cancelCeramicEdit({{ $ceramic->id }})"><i class="fas fa-times"></i>
                                            Hủy</button>
                                        <form action="{{ route('admin.ceramics.delete', $ceramic->id) }}" method="POST"
                                            style="display:inline;"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa món đồ gốm này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn delete-btn"><i class="fas fa-trash"></i>
                                                Xóa</button>
                                        </form>
                                    </td>
                                </form>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <!-- Tab Yêu Cầu Settings -->
        <div class="container tab-content" id="settings" style="display: none;">
            
            <h1>Cài Đặt</h1>
            @include('llm_settings')

            <!-- Phần thông báo -->
            @if (session('recognition_model_success'))
                <div class="success-message">{{ session('recognition_model_success') }}</div>
            @endif
            @if (session('recognition_model_error'))
                <div
                    style="color: var(--error-color); background: #f8d7da; padding: 8px; border-radius: 4px; margin-bottom: 15px; text-align: center;">
                    {{ session('recognition_model_error') }}
                </div>
            @endif
            @if (session('upload_model_success'))
                <div class="success-message">{{ session('upload_model_success') }}</div>
            @endif
            @if (session('upload_model_error'))
                <div
                    style="color: var(--error-color); background: #f8d7da; padding: 8px; border-radius: 4px; margin-bottom: 15px; text-align: center;">
                    {{ session('upload_model_error') }}
                </div>
            @endif

            <!-- Form chuyển đổi mô hình nhận diện (chỉ 66 và 67) -->
            <h3>Switch Recognition Model</h3>
            <form action="{{ route('admin.recognition-model.update') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 15px; max-width: 500px;">
                    <div>
                        <label for="recognition_model">Select Recognition Model:</label>
                        <select name="recognition_model" id="recognition_model" onchange="toggleSwitchButton()">
                            <option value="default" {{ $recognitionModel && $recognitionModel->value === 'default' ? 'selected' : '' }}>Default (66)</option>
                            <option value="xception" {{ $recognitionModel && $recognitionModel->value === 'xception' ? 'selected' : '' }}>Xception (67)</option>
                        </select>
                    </div>
                    <button type="submit" class="action-btn save-btn" id="switchButton" disabled>
                        <i class="fas fa-sync-alt"></i> Switch Model
                    </button>
                </div>
            </form>

            <h3 style="margin-top: 30px;">Upload Custom Model</h3>
            <form action="{{ route('admin.model.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 15px; max-width: 500px;">
                    <div>
                        <label for="model_file">Upload Model File (Max 500MB):</label>
                        <input type="file" name="model_file" id="model_file" accept=".h5,.pth,.pt,.onnx"
                            onchange="toggleUploadButton()">
                        @error('model_file')
                            <span style="color: var(--error-color); font-size: 0.9rem;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="model_class">Model Classes (e.g., Class1, Class2, Class3):</label>
                        <input type="text" name="model_class" id="model_class"
                            placeholder="Enter classes separated by commas">
                        <small style="color: var(--dark-color); font-size: 0.8rem;">Nhập danh sách class, phân tách bằng
                            dấu phẩy (ví dụ: Class1, Class2, Class3).</small>
                        @error('model_class')
                            <span style="color: var(--error-color); font-size: 0.9rem;">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="action-btn save-btn" id="uploadButton" disabled>
                        <i class="fas fa-upload"></i> Upload Model
                    </button>
                </div>
            </form>

            <h3>Sao Lưu Dữ Liệu</h3>
            @if (session('backup_success'))
                <div class="success-message">
                    <!-- Ngay dưới phần success message -->
                    @if (session('error'))
                        <div
                            style="color: var(--error-color); background: #f8d7da; padding: 8px; border-radius: 4px; margin-bottom: 15px; text-align: center;">
                            {{ session('error') }}
                        </div>
                    @endif
                    {{ session('backup_success') }}
                </div>
            @endif
            <form method="POST" action="{{ route('admin.backup') }}">
                @csrf
                <p>Sao lưu toàn bộ cơ sở dữ liệu thành tệp SQL.</p>
                <button type="submit" class="action-btn save-btn">
                    <i class="fas fa-download"></i> Sao Lưu Dữ Liệu
                </button>
            </form>
            <h3>Thay Đổi Múi Giờ</h3>
            @if (session('timezone_success'))
                <div class="success-message">
                    {{ session('timezone_success') }}
                </div>
            @endif
            <form method="POST" action="{{ route('admin.settings.timezone') }}">
                @csrf
                <div>
                    <label for="timezone">Chọn múi giờ:</label>
                    <select name="timezone" id="timezone" required>
                        <option value="" disabled {{ !isset($currentTimezone) ? 'selected' : '' }}>Chọn múi giờ</option>
                        <option value="UTC" {{ isset($currentTimezone) && $currentTimezone === 'UTC' ? 'selected' : '' }}>
                            UTC</option>
                        <option value="Asia/Ho_Chi_Minh" {{ isset($currentTimezone) && $currentTimezone === 'Asia/Ho_Chi_Minh' ? 'selected' : '' }}>Asia/Ho_Chi_Minh (Việt Nam,
                            GMT+7)</option>
                        <option value="Asia/Bangkok" {{ isset($currentTimezone) && $currentTimezone === 'Asia/Bangkok' ? 'selected' : '' }}>Asia/Bangkok (Thái Lan, GMT+7)</option>
                        <option value="Asia/Tokyo" {{ isset($currentTimezone) && $currentTimezone === 'Asia/Tokyo' ? 'selected' : '' }}>Asia/Tokyo (Nhật Bản, GMT+9)</option>
                        <option value="America/New_York" {{ isset($currentTimezone) && $currentTimezone === 'America/New_York' ? 'selected' : '' }}>America/New_York (Mỹ, GMT-5)
                        </option>
                        <option value="Europe/London" {{ isset($currentTimezone) && $currentTimezone === 'Europe/London' ? 'selected' : '' }}>Europe/London (Anh, GMT+0)</option>
                    </select>
                </div>
                <button type="submit" class="action-btn save-btn"><i class="fas fa-save"></i> Lưu Múi Giờ</button>
            </form>
            <!-- Thêm vào dưới phần CAPTCHA hoặc bất kỳ đâu trong tab settings -->
            <h3>Chọn Giao diện Trang Chủ</h3>
            @if (session('theme_success'))
                <div class="success-message">
                    {{ session('theme_success') }}
                </div>
            @endif
            <form method="POST" action="{{ route('admin.settings.theme') }}">
                @csrf
                <div>
                    <label>
                        <input type="radio" name="theme" value="index" {{ $currentTheme === 'index' ? 'checked' : '' }}>
                        Giao diện 1 (Mặc định)
                    </label>
                    <label>
                        <input type="radio" name="theme" value="index2" {{ $currentTheme === 'index2' ? 'checked' : '' }}>
                        Giao diện 2 (Hiện đại)
                    </label>
                </div>
                <button type="submit" class="action-btn save-btn"><i class="fas fa-save"></i> Lưu Giao Diện</button>
            </form>
            <!-- Bật/Tắt CAPTCHA -->
            <h3>Bật/Tắt CAPTCHA cho Trang Đăng Nhập</h3>
            @if (session('captcha_success'))
                <div class="success-message">
                    {{ session('captcha_success') }}
                </div>
            @endif
            <form method="POST" action="{{ route('admin.settings.captcha') }}">
                @csrf
                <div>
                    <label for="recaptcha_enabled">
                        <input type="checkbox" id="recaptcha_enabled" name="recaptcha_enabled" value="1" {{ $recaptchaEnabled ? 'checked' : '' }}>
                        Bật CAPTCHA (reCAPTCHA) cho trang đăng nhập
                    </label>
                </div>
                <button type="submit" class="action-btn save-btn"><i class="fas fa-save"></i> Lưu Cài Đặt</button>
            </form>
        </div>
        <!-- Tab Lịch Sử Nhận Diện -->
        <div class="container tab-content" id="classifications" style="display: none;">
            <h1>Lịch Sử Nhận Diện</h1>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Người Dùng</th>
                        <th>Email</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td class="user-name"
                                onclick="showClassificationHistory('{{ $user->id }}', '{{ $user->name }}')">
                                {{ $user->name }}
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <button class="action-btn save-btn"
                                    onclick="showClassificationHistory('{{ $user->id }}', '{{ $user->name }}')">
                                    <i class="fas fa-eye"></i> Xem Lịch Sử
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Tab Quản lý Metadata -->
        <div class="container tab-content" id="metadata" style="display: none;">
            <h1>Quản lý Metadata</h1>
            <button type="button" class="action-btn save-btn" onclick="showAddMetadataPopup()"
                style="margin-bottom: 20px;">
                <i class="fas fa-plus"></i> Thêm Metadata mới
            </button>
            @if (session('success'))
                <div class="success-message">{{ session('success') }}</div>
            @endif
            @include('index-metadata', ['metadata' => $metadata])
        </div>
        <!-- Tab Chính sách và điều khoản -->
        <div class="container tab-content" id="terms" style="display: none;">
            <h1>Chính sách và điều khoản</h1>
            @if (session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif
            <form method="POST" action="{{ route('admin.terms.update') }}">
                @csrf
                <div>
                    <label for="terms_content">Nội dung chính sách và điều khoản:</label>
                    <textarea name="content" id="terms_content" rows="10"
                        required>{{ $terms ? $terms->content : '' }}</textarea>
                </div>
                <button type="submit" class="action-btn save-btn"><i class="fas fa-save"></i> Lưu</button>
            </form>
        </div>
        <!-- Tab Quản lý tin tức -->
        <!-- Tab Quản lý tin tức -->
        <div class="container tab-content" id="news" style="display: none;">
            <h1>Quản lý tin tức</h1>
            <!-- Nút Cập nhật tin tức -->
            <button type="button" class="action-btn save-btn" onclick="fetchNews()" style="margin-bottom: 20px;">
                <i class="fas fa-sync-alt"></i> Cập nhật tin tức
            </button>
            <!-- Thông báo thành công (nếu có) -->
            @if (session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif
            <!-- Bảng danh sách tin tức -->
            @if ($news->isEmpty())
                <p>Không có tin tức nào.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tiêu đề</th>
                            <th>Mô tả ngắn</th>
                            <th>Hình ảnh</th>
                            <th>Nội dung</th>
                            <th>Nguồn</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($news as $article)
                            <tr id="news-row-{{ $article->id }}">
                                <form action="{{ route('news.update', $article->id) }}" method="POST" class="edit-form"
                                    id="news-form-{{ $article->id }}">
                                    @csrf
                                    @method('PUT')
                                    <td>{{ $article->id }}</td>
                                    <td class="editable" data-field="title">
                                        <span class="display">{{ $article->title }}</span>
                                        <input type="text" name="title" value="{{ $article->title }}" style="display:none;">
                                    </td>
                                    <td class="editable" data-field="excerpt">
                                        <span class="display description-cell"
                                            id="excerpt-{{ $article->id }}">{{ $article->excerpt ?? 'Không có' }}</span>
                                        <span class="toggle-description" onclick="toggleNewsExcerpt('{{ $article->id }}')"
                                            id="toggle-excerpt-{{ $article->id }}">Xem thêm</span>
                                        <textarea name="excerpt" style="display:none;">{{ $article->excerpt }}</textarea>
                                    </td>
                                    <td class="editable" data-field="image">

                                        <img src="{{ $article->image }}" alt="Article Image" style="width:100%; height:auto;" />

                                    </td>
                                    <td class="editable" data-field="content">
                                        <span class="display description-cell"
                                            id="content-{{ $article->id }}">{{ $article->content ?? 'Không có' }}</span>
                                        <span class="toggle-description" onclick="toggleNewsContent('{{ $article->id }}')"
                                            id="toggle-content-{{ $article->id }}">Xem thêm</span>
                                        <textarea name="content" style="display:none;">{{ $article->content }}</textarea>
                                    </td>
                                    <td>
                                        @if ($article->source_url)
                                            <a href="{{ $article->source_url }}" target="_blank"
                                                style="color: var(--secondary-color); text-decoration: underline;">
                                                Xem nguồn
                                            </a>
                                        @else
                                            Không có
                                        @endif
                                    </td>
                                    <td class="actions">
                                        <button type="button" class="action-btn edit-btn"
                                            onclick="editNewsRow({{ $article->id }})"><i class="fas fa-edit"></i> Sửa</button>
                                        <button type="submit" class="action-btn save-btn" style="display:none;"><i
                                                class="fas fa-save"></i> Lưu</button>
                                        <button type="button" class="action-btn cancel-btn" style="display:none;"
                                            onclick="cancelNewsEdit({{ $article->id }})"><i class="fas fa-times"></i>
                                            Hủy</button>
                                        <form action="{{ route('news.delete', $article->id) }}" method="POST"
                                            style="display:inline;"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa tin tức này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn delete-btn"><i class="fas fa-trash"></i>
                                                Xóa</button>
                                        </form>
                                    </td>
                                </form>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <!-- Tab Thông tin hệ thống -->
        <div class="container tab-content" id="system-info" style="display: none;">
            <h1>Thông Tin Hệ Thống</h1>
            @if($isSystemInfoEnabled)
                <button onclick="updateSystemCharts()" class="action-btn save-btn">Làm mới dữ liệu</button>
                <div id="system-error" style="color: var(--error-color); display: none; margin-bottom: 20px;"></div>
                <div class="tab-container">
                    <button class="tab-button active" data-tab="fastapi" onclick="openSubTab('fastapi')">FastAPI
                        Stats</button>
                </div>
                <div id="fastapi" class="tab-content" style="display: block;">
                    <h3>FastAPI System Stats</h3>
                    <ul id="fastapi-stats">
                        <li>CPU Usage: <span id="cpu-usage">Đang tải...</span></li>
                        <li>RAM Total: <span id="ram-total">Đang tải...</span></li>
                        <li>RAM Used: <span id="ram-used">Đang tải...</span></li>
                        <li>RAM Usage: <span id="ram-usage">Đang tải...</span></li>
                        <li>GPU Usage: <span id="gpu-usage">Đang tải...</span></li>
                        <li>GPU Total: <span id="gpu-total">Đang tải...</span></li>
                        <li>GPU Used: <span id="gpu-used">Đang tải...</span></li>
                    </ul>
                    <div class="stats-row">
                        <div style="flex: 1;"><canvas id="fastApiRamChart"></canvas></div>
                        <div style="flex: 1;"><canvas id="fastApiCpuChart"></canvas></div>
                        <div style="flex: 1;"><canvas id="fastApiGpuChart"></canvas></div>
                    </div>
                </div>
            @else
                <p>Thông tin hệ thống hiện đang bị tắt.</p>
            @endif
        </div>
    </div>
    <!-- Popup thêm metadata mới -->
    <div class="popup-overlay" id="addMetadataOverlay" onclick="hideAddMetadataPopup()"></div>
    <div class="popup" id="addMetadataPopup">
        <h3>Thêm Metadata Mới</h3>
        <form id="addMetadataForm" method="POST" action="{{ route('admin.metadata.store') }}"
            enctype="multipart/form-data">
            @csrf
            <p><strong>Trang:</strong></p>
            <input type="text" name="page" required placeholder="Nhập tên trang (ví dụ: index)">
            <p><strong>Tiêu đề:</strong></p>
            <input type="text" name="title" required placeholder="Nhập tiêu đề">
            <p><strong>Mô tả:</strong></p>
            <textarea name="description" rows="4" placeholder="Nhập mô tả (tùy chọn)"></textarea>
            <p><strong>Từ khóa:</strong></p>
            <input type="text" name="keywords" placeholder="Nhập từ khóa, cách nhau bằng dấu phẩy (tùy chọn)">
            <p><strong>Favicon:</strong></p>
            <input type="file" name="favicon" placeholder="Chọn favicon (tùy chọn)">
            <button type="submit">Thêm</button>
        </form>
    </div>
    <!-- Popup Chi tiết Liên hệ -->
    <div class="popup-overlay" id="contactOverlay" onclick="hideContactPopup()"></div>
    <div class="popup" id="contactPopup">
        <h3>Chi tiết liên hệ từ <span id="contactName"></span></h3>
        <div id="contactDetails">
            <p><strong>Số điện thoại:</strong> <span id="contactPhone"></span></p>
            <p><strong>Email:</strong> <span id="contactEmail"></span></p>
            <p><strong>Nội dung:</strong> <span id="contactMessage"></span></p>
            <p><strong>Trạng thái:</strong> <span id="contactStatus"></span></p>
        </div>
        <button onclick="hideContactPopup()">Đóng</button>
    </div>
    <!-- Popup Lịch Sử Nhận Diện -->
    <div class="popup-overlay" id="classificationOverlay" onclick="hideClassificationHistory()"></div>
    <div class="popup" id="classificationPopup">
        <h3>Lịch Sử Nhận Diện của <span id="classificationUserName"></span></h3>
        <div id="classificationHistoryContent">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Kết Quả</th>
                        <th>Thông Tin</th>
                        <th>Thời Gian</th>
                    </tr>
                </thead>
                <tbody id="classificationHistoryTable">
                    <!-- Nội dung sẽ được thêm bằng JavaScript -->
                </tbody>
            </table>
        </div>
        <button onclick="hideClassificationHistory()">Đóng</button>
    </div>
    <!-- Popup "Xem thêm" cho Thông Tin -->
    <div class="popup-overlay" id="llmResponseOverlay" onclick="hideLlmResponsePopup()"></div>
    <div class="popup" id="llmResponsePopup">
        <h3>Thông Tin Chi Tiết</h3>
        <div id="llmResponseContent" class="llm-response-content"></div>
        <button onclick="hideLlmResponsePopup()">Đóng</button>
    </div>
    <!-- Popup thông tin đánh giá -->
    <div class="popup-overlay" onclick="hidePopup()"></div>
    <div class="popup" id="userPopup">
        <h3>Thông tin đánh giá của <span id="popupName"></span></h3>
        <div class="rating-stars" id="popupRating"></div>
        <p><strong>Phản hồi:</strong> <span id="popupFeedback"></span></p>
        <button onclick="hidePopup()">Đóng</button>
    </div>
    <!-- Popup từ chối yêu cầu nạp tiền -->
    <div class="popup-overlay" id="rejectOverlay" onclick="hideRejectPopup()"></div>
    <div class="popup" id="rejectPopup">
        <h3>Từ chối yêu cầu của <span id="rejectUserName"></span></h3>
        <form id="rejectForm" method="POST" action="">
            @csrf
            <input type="hidden" name="request_id" id="rejectRequestId">
            <p><strong>Lý do từ chối:</strong></p>
            <textarea name="reason" id="rejectReason" rows="4" placeholder="Nhập lý do từ chối..." required></textarea>
            <button type="submit">Gửi</button>
        </form>
    </div>
    <!-- Popup thêm bài viết tin tức mới -->
    <div class="popup-overlay" id="addNewsOverlay" onclick="hideAddNewsPopup()"></div>
    <div class="popup" id="addNewsPopup">
        <h3>Thêm Bài Viết Tin Tức Mới</h3>
        <form id="addNewsForm" method="POST" action="{{ route('news.store') }}">
            @csrf
            <p><strong>Tiêu đề:</strong></p>
            <input type="text" name="title" required placeholder="Nhập tiêu đề bài viết">
            <p><strong>Mô tả ngắn:</strong></p>
            <input type="text" name="excerpt" placeholder="Nhập mô tả ngắn (tùy chọn)">
            <p><strong>Hình ảnh:</strong></p>
            <input type="file" name="image" placeholder="Đường dẫn hình ảnh (ceramics/ten_hinh.jpg)">
            <p><strong>Nội dung:</strong></p>
            <textarea name="content" rows="6" placeholder="Nhập nội dung bài viết" required></textarea>
            <button type="submit">Thêm</button>
        </form>
    </div>
    <!-- Popup thêm món đồ gốm mới -->
    <div class="popup-overlay" id="addCeramicOverlay" onclick="hideAddCeramicPopup()"></div>
    <div class="popup" id="addCeramicPopup">
        <h3>Thêm Món Đồ Gốm Mới</h3>
        <form id="addCeramicForm" method="POST" action="{{ route('admin.ceramics.store') }}"
            enctype="multipart/form-data">
            @csrf
            <p><strong>Tên:</strong></p>
            <input type="text" name="name" required placeholder="Nhập tên món đồ gốm">
            <p><strong>Mô tả:</strong></p>
            <textarea name="description" rows="4" placeholder="Nhập mô tả (tùy chọn)"></textarea>
            <p><strong>Hình ảnh:</strong></p>
            <input type="file" name="image" placeholder="Đường dẫn hình ảnh (ceramics/ten_hinh.jpg)">
            <p><strong>Danh mục:</strong></p>
            <input type="text" name="category" placeholder="Nhập danh mục (tùy chọn)">
            <p><strong>Nguồn gốc:</strong></p>
            <input type="text" name="origin" placeholder="Nhập nguồn gốc (tùy chọn)">
            <button type="submit">Thêm</button>
        </form>

    </div>
    <script>
        // Tab switching
        document.querySelectorAll('.sidebar a').forEach(link => {
            link.addEventListener('click', function (e) {
                if (!this.href.includes('logout')) {
                    e.preventDefault();
                    document.querySelectorAll('.sidebar a').forEach(a => a.classList.remove('active'));
                    this.classList.add('active');
                    document.querySelectorAll('.tab-content').forEach(tab => tab.style.display = 'none');
                    const tabContent = document.getElementById(this.dataset.tab);
                    if (tabContent) {
                        tabContent.style.display = 'block';
                        if (this.dataset.tab === 'revenue') {
                            renderRevenueChart();
                        }
                    }
                }
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                document.getElementById(this.getAttribute('href').substring(1)).classList.add('active');
            });
        });
        // Edit user row
        // Biến để lưu giá trị ban đầu của các trường
        let initialValues = {};
        // Edit user row
        function editRow(userId) {
            const row = document.getElementById(`row-${userId}`);
            const editables = row.querySelectorAll('.editable');
            const editBtn = row.querySelector('.edit-btn');
            const saveBtn = row.querySelector('.save-btn');
            const cancelBtn = row.querySelector('.cancel-btn');
            // Lưu giá trị ban đầu của các trường
            initialValues[userId] = {};
            editables.forEach(cell => {
                const field = cell.dataset.field;
                const input = cell.querySelector('input, select');
                initialValues[userId][field] = input.value;
            });
            editables.forEach(cell => {
                const display = cell.querySelector('.display');
                const input = cell.querySelector('input, select');
                display.style.display = 'none';
                input.style.display = 'block';
            });
            editBtn.style.display = 'none';
            saveBtn.style.display = 'inline-flex';
            cancelBtn.style.display = 'inline-flex';
        }
        function cancelEdit(userId) {
            const row = document.getElementById(`row-${userId}`);
            const editables = row.querySelectorAll('.editable');
            const editBtn = row.querySelector('.edit-btn');
            const saveBtn = row.querySelector('.save-btn');
            const cancelBtn = row.querySelector('.cancel-btn');
            editables.forEach(cell => {
                const display = cell.querySelector('.display');
                const input = cell.querySelector('input, select');
                input.style.display = 'none';
                display.style.display = 'block';
                // Khôi phục giá trị ban đầu
                input.value = initialValues[userId][cell.dataset.field];
            });
            editBtn.style.display = 'inline-flex';
            saveBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
            // Xóa giá trị ban đầu khi hủy
            delete initialValues[userId];
        }
        // Kiểm tra thay đổi trước khi gửi form
        document.querySelectorAll('.edit-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Ngăn gửi form mặc định để kiểm tra
                const userId = this.id.replace('form-', '');
                const editables = document.getElementById(`row-${userId}`).querySelectorAll('.editable');
                let hasChanges = false;
                // Xóa các hidden input cũ (nếu có) để tránh trùng lặp
                const existingHiddenInputs = form.querySelectorAll('input[type="hidden"]:not([name="_token"]):not([name="_method"])');
                existingHiddenInputs.forEach(input => input.remove());
                // Thêm các trường ẩn vào form để gửi dữ liệu
                editables.forEach(cell => {
                    const field = cell.dataset.field;
                    const input = cell.querySelector('input, select');
                    const currentValue = input.value;
                    const initialValue = initialValues[userId][field];
                    // Tạo input ẩn để gửi dữ liệu
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = field;
                    hiddenInput.value = currentValue;
                    form.appendChild(hiddenInput);
                    // So sánh giá trị hiện tại với giá trị ban đầu
                    if (currentValue !== initialValue) {
                        hasChanges = true;
                    }
                });
                // Nếu không có thay đổi, hiển thị thông báo và dừng
                if (!hasChanges) {
                    alert('Không có thay đổi để lưu!');
                    return;
                }
                // Kiểm tra dữ liệu gửi đi
                const formData = new FormData(this);
                console.log('Dữ liệu gửi đi:', Object.fromEntries(formData));
                // Gửi form
                form.submit();
            });
        });
        // Biến để lưu giá trị ban đầu của các trường trong tab Quản lý thư viện đồ gốm
        let initialCeramicValues = {};
        // Edit ceramic row
        function editCeramicRow(ceramicId) {
            const row = document.getElementById(`ceramic-row-${ceramicId}`);
            const editables = row.querySelectorAll('.editable');
            const editBtn = row.querySelector('.edit-btn');
            const saveBtn = row.querySelector('.save-btn');
            const cancelBtn = row.querySelector('.cancel-btn');
            // Lưu giá trị ban đầu của các trường
            initialCeramicValues[ceramicId] = {};
            editables.forEach(cell => {
                const field = cell.dataset.field;
                const input = cell.querySelector('input, textarea');
                initialCeramicValues[ceramicId][field] = input.value;
                cell.classList.add('editing');
            });
            editables.forEach(cell => {
                const display = cell.querySelector('.display');
                const input = cell.querySelector('input, textarea');
                display.style.display = 'none';
                input.style.display = 'block';
            });
            editBtn.style.display = 'none';
            saveBtn.style.display = 'inline-flex';
            cancelBtn.style.display = 'inline-flex';
        }
        function cancelCeramicEdit(ceramicId) {
            const row = document.getElementById(`ceramic-row-${ceramicId}`);
            const editables = row.querySelectorAll('.editable');
            const editBtn = row.querySelector('.edit-btn');
            const saveBtn = row.querySelector('.save-btn');
            const cancelBtn = row.querySelector('.cancel-btn');
            editables.forEach(cell => {
                const display = cell.querySelector('.display');
                const input = cell.querySelector('input, textarea');
                input.style.display = 'none';
                display.style.display = 'block';
                // Khôi phục giá trị ban đầu
                input.value = initialCeramicValues[ceramicId][cell.dataset.field];
                cell.classList.remove('editing');
                const descriptionCell = cell.querySelector('.description-cell');
                if (descriptionCell) {
                    descriptionCell.classList.remove('expanded');
                    const toggleLink = cell.querySelector('.toggle-description');
                    toggleLink.textContent = 'Xem thêm';
                }
            });
            editBtn.style.display = 'inline-flex';
            saveBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
            // Xóa giá trị ban đầu khi hủy
            delete initialCeramicValues[ceramicId];
        }
        // Xử lý gửi form trong tab Quản lý thư viện đồ gốm
        document.querySelectorAll('#ceramics .edit-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Ngăn gửi form mặc định để kiểm tra
                const ceramicId = this.id.replace('ceramic-form-', '');
                const editables = document.getElementById(`ceramic-row-${ceramicId}`).querySelectorAll('.editable');
                let hasChanges = false;
                // Xóa các hidden input cũ (nếu có) để tránh trùng lặp
                const existingHiddenInputs = form.querySelectorAll('input[type="hidden"]:not([name="_token"]):not([name="_method"])');
                existingHiddenInputs.forEach(input => input.remove());
                // Thêm các trường ẩn vào form để gửi dữ liệu
                editables.forEach(cell => {
                    const field = cell.dataset.field;
                    const input = cell.querySelector('input, textarea');
                    const currentValue = input.value;
                    const initialValue = initialCeramicValues[ceramicId][field];
                    // Tạo input ẩn để gửi dữ liệu
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = field;
                    hiddenInput.value = currentValue;
                    form.appendChild(hiddenInput);
                    // So sánh giá trị hiện tại với giá trị ban đầu
                    if (currentValue !== initialValue) {
                        hasChanges = true;
                    }
                });
                // Nếu không có thay đổi, hiển thị thông báo và dừng
                if (!hasChanges) {
                    alert('Không có thay đổi để lưu!');
                    return;
                }
                // Kiểm tra dữ liệu gửi đi
                const formData = new FormData(this);
                console.log('Dữ liệu gửi đi:', Object.fromEntries(formData));
                // Gửi form
                form.submit();
            });
            // Ngăn chặn gửi form khi nhấn Enter trong khi chỉnh sửa
            form.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Ngăn chặn hành vi mặc định của Enter
                }
            });
        });
        // Show/Hide Add Ceramic Popup
        function showAddCeramicPopup() {
            const popup = document.getElementById('addCeramicPopup');
            const overlay = document.getElementById('addCeramicOverlay');
            popup.style.display = 'block';
            overlay.style.display = 'block';
        }
        function hideAddCeramicPopup() {
            const popup = document.getElementById('addCeramicPopup');
            const overlay = document.getElementById('addCeramicOverlay');
            popup.style.display = 'none';
            overlay.style.display = 'none';
        }
        // Popup thông tin đánh giá
        function toggleDescription(ceramicId) {
            const descriptionCell = document.getElementById(`description-${ceramicId}`);
            const toggleLink = document.getElementById(`toggle-${ceramicId}`);
            if (descriptionCell.classList.contains('expanded')) {
                descriptionCell.classList.remove('expanded');
                toggleLink.textContent = 'Xem thêm';
            } else {
                descriptionCell.classList.add('expanded');
                toggleLink.textContent = 'Ẩn bớt';
            }
        }
        function showPopup(userId, name, rating, feedback) {
            const popup = document.getElementById('userPopup');
            const overlay = document.querySelector('.popup-overlay');
            const popupName = document.getElementById('popupName');
            const popupRating = document.getElementById('popupRating');
            const popupFeedback = document.getElementById('popupFeedback');
            popupName.textContent = name;
            popupFeedback.textContent = feedback;
            popupRating.innerHTML = '';
            for (let i = 1; i <= 5; i++) {
                const star = document.createElement('i');
                star.classList.add('fa-star', i <= rating ? 'fas' : 'far');
                popupRating.appendChild(star);
            }
            popup.style.display = 'block';
            overlay.style.display = 'block';
        }
        function hidePopup() {
            const popup = document.getElementById('userPopup');
            const overlay = document.querySelector('.popup-overlay');
            popup.style.display = 'none';
            overlay.style.display = 'none';
        }
        // Popup từ chối yêu cầu
        function showRejectPopup(requestId, userName) {
            const popup = document.getElementById('rejectPopup');
            const overlay = document.getElementById('rejectOverlay');
            const rejectUserName = document.getElementById('rejectUserName');
            const rejectRequestId = document.getElementById('rejectRequestId');
            const form = document.getElementById('rejectForm');
            rejectUserName.textContent = userName;
            rejectRequestId.value = requestId;
            form.action = '{{ route("admin.recharge.reject") }}';
            popup.style.display = 'block';
            overlay.style.display = 'block';
        }
        function hideRejectPopup() {
            const popup = document.getElementById('rejectPopup');
            const overlay = document.getElementById('rejectOverlay');
            popup.style.display = 'none';
            overlay.style.display = 'none';
        }
        // Revenue Chart
        function renderRevenueChart() {
            const ctx = document.getElementById('revenueChart');
            if (!ctx) {
                console.error('Không tìm thấy canvas #revenueChart');
                return;
            }
            const labels = {!! json_encode($revenueLabels) !!} || ['Chưa có dữ liệu'];
            const data = {!! json_encode($revenueData) !!} || [0];
            console.log('Revenue Labels:', labels);
            console.log('Revenue Data:', data);
            if (window.revenueChart && typeof window.revenueChart.destroy === 'function') {
                window.revenueChart.destroy();
            }
            window.revenueChart = new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: data,
                        borderColor: '#1e88e5',
                        backgroundColor: 'rgba(30, 136, 229, 0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: value => value.toLocaleString('vi-VN') + ' VNĐ' }
                        }
                    },
                    plugins: {
                        legend: { display: true },
                        tooltip: { callbacks: { label: context => context.parsed.y.toLocaleString('vi-VN') + ' VNĐ' } }
                    }
                }
            });
        }

        // Ngăn chặn gửi form khi nhấn Enter trong khi chỉnh sửa
        document.querySelectorAll('.edit-form').forEach(form => {
            form.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Ngăn chặn hành vi mặc định của Enter
                }
            });
        });
        // Gọi lần đầu khi trang tải
        document.addEventListener('DOMContentLoaded', function () {
            // Dữ liệu từ PHP
            
            const userTrend = @json($userTrend);
            const rechargeTrend = @json($rechargeTrend);
            const revenueTrend = @json($revenueTrend);
            const ratingTrend = @json($ratingTrend);
            const approvedRequests = @json($approvedRequests);
            const rejectedRequests = @json($rejectedRequests);
            const pendingRequests = @json($rechargeRequests->count());
            const activeUsers = @json($activeUsers);
            const inactiveUsers = @json($inactiveUsers);
            const revenueLabels = @json($revenueLabels); // Dữ liệu ngày
            const revenueData = @json($revenueData);
            const revenueLabelsM = @json($revenueLabelsM);
            const revenueDataM = @json($revenueDataM);
            const tokenTrend = @json($tokenTrend);
            // Hàm tạo biểu đồ
            function createChart(canvasId, labels, values, label, color, isCurrency = false) {
                const ctx = document.getElementById(canvasId);
                // Đặt màu fill là đen (nếu cần)
                if (!ctx) return;
                new Chart(ctx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: label,
                            data: values,
                            borderColor: color,
                            backgroundColor: `${color}70`, // Màu nền mờ
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => isCurrency ? value.toLocaleString('vi-VN') + ' VNĐ' : value
                                }
                            },
                            x: { ticks: { font: { size: 10 } } }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: context => `${context.dataset.label}: ${isCurrency ? context.parsed.y.toLocaleString('vi-VN') + ' VNĐ' : context.parsed.y}`
                                }
                            }
                        }
                    }
                });
            }
            // Hàm tạo biểu đồ tròn
            // Hàm tạo biểu đồ tròn (Donut Chart)
            function createPieChart(canvasId, data, labels, colors) {
                const ctx = document.getElementById(canvasId);
                if (!ctx) return;
                new Chart(ctx.getContext('2d'), {
                    type: 'doughnut', // Change to doughnut for donut chart
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: colors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%', // Creates the donut hole (adjust percentage as needed)
                        plugins: {
                            legend: {
                                position: 'right', // Position the legend to the right
                                labels: {
                                    font: { size: 12 },
                                    padding: 20
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: context => `${context.label}: ${context.raw}%`
                                }
                            }
                        }
                    }
                });
            }

            // Vẽ biểu đồ tròn cho Trạng thái yêu cầu
            createPieChart(
                'requestStatusPieChart',
                [approvedRequests, rejectedRequests, pendingRequests],
                ['Resolved', 'Escalated', 'Pending'],
                ['#1976d2', '#ff9800', '#f44336'] // Colors: blue, orange, red
            );
            // Vẽ các biểu đồ
            // createChart('userTrendChart', userTrend.labels, userTrend.values, 'Người dùng mới', '#42a5f5');
            createChart('tokenTrendChart', tokenTrend.labels, tokenTrend.values, 'Lượt nhận diện', '#ff5722');
            createChart('rechargeTrendChart', rechargeTrend.labels, rechargeTrend.values, 'Yêu cầu mới', '#ffca28');
            createChart('revenueTrendChart', revenueTrend.labels, revenueTrend.values, 'Doanh thu', '#00c853', true);
            // createChart('ratingTrendChart', ratingTrend.labels, ratingTrend.values, 'Đánh giá', '#f44336');
            // Vẽ biểu đồ tròn cho Trạng thái yêu cầu

            //Gọi renderTransactionHistory nếu có dữ liệu
            if (transactions.length > 0) {
                renderTransactionHistory(1);
            }
            // Vẽ biểu đồ tròn cho Tổng người dùng
            createPieChart(
                'userStatusPieChart',
                [activeUsers, inactiveUsers],
                ['Hoạt động', 'Không hoạt động'],
                ['#00c853', '#f44336']
            );
        });
        //Lịch sử nhận diện
        // Dữ liệu lịch sử nhận diện (giả lập từ PHP)
        const classifications = @json($classifications);
        // Hiển thị lịch sử nhận diện của người dùng
        function showClassificationHistory(userId, userName) {
            const popup = document.getElementById('classificationPopup');
            const overlay = document.getElementById('classificationOverlay');
            const userNameElement = document.getElementById('classificationUserName');
            const historyTable = document.getElementById('classificationHistoryTable');
            // Hiển thị tên người dùng
            userNameElement.textContent = userName;
            // Lọc lịch sử nhận diện của người dùng
            const userClassifications = classifications.filter(item => item.user_id == userId);
            // Xóa nội dung cũ
            historyTable.innerHTML = '';
            // Nếu không có lịch sử
            if (userClassifications.length === 0) {
                historyTable.innerHTML = '<tr><td colspan="5">Không có lịch sử nhận diện.</td></tr>';
            } else {
                // Thêm các dòng lịch sử
                userClassifications.forEach(item => {
                    const infoText = item.llm_response || 'Không có thông tin';
                    const isLongInfo = infoText.length > 100; // Giới hạn độ dài để hiển thị "Xem thêm"
                    const row = document.createElement('tr');
                    row.innerHTML = `
                <td>${item.id}</td>
                <td><img src="${item.image_path}" alt="Image"></td>
                <td>${item.result}</td>
                <td>
                    <span class="info-cell" id="info-${item.id}">${infoText}</span>
                    ${isLongInfo ? `<span class="toggle-info" onclick="toggleInfo('${item.id}')">Xem thêm</span>` : ''}
                </td>
                <td>${new Date(item.created_at).toLocaleString('vi-VN')}</td>
            `;
                    historyTable.appendChild(row);
                });
            }
            // Hiển thị popup
            popup.style.display = 'block';
            overlay.style.display = 'block';
        }
        // Hàm toggleInfo để mở rộng/thu gọn nội dung
        function toggleInfo(id) {
            const infoCell = document.getElementById(`info-${id}`);
            const toggleLink = infoCell.nextElementSibling;
            if (infoCell.classList.contains('expanded')) {
                infoCell.classList.remove('expanded');
                toggleLink.textContent = 'Xem thêm';
            } else {
                infoCell.classList.add('expanded');
                toggleLink.textContent = 'Ẩn bớt';
            }
        }
        // Ẩn popup lịch sử nhận diện
        function hideClassificationHistory() {
            const popup = document.getElementById('classificationPopup');
            const overlay = document.getElementById('classificationOverlay');
            popup.style.display = 'none';
            overlay.style.display = 'none';
        }
        // Ẩn popup lịch sử nhận diện
        function hideClassificationHistory() {
            const popup = document.getElementById('classificationPopup');
            const overlay = document.getElementById('classificationOverlay');
            popup.style.display = 'none';
            overlay.style.display = 'none';
        }
        //lịch sử đăng nhập
        // Dữ liệu lịch sử đăng nhập (giả lập từ PHP)
        const loginHistories = @json($users->mapWithKeys(function ($user) {
            return [$user->id => $user->loginHistories];
        })->toArray());
        // Hiển thị lịch sử đăng nhập của người dùng
        function showLoginHistory(userId, userName) {
            const popup = document.getElementById('loginHistoryPopup');
            const overlay = document.getElementById('loginHistoryOverlay');
            const userNameElement = document.getElementById('loginHistoryUserName');
            const historyTable = document.getElementById('loginHistoryTable');
            // Hiển thị tên người dùng
            userNameElement.textContent = userName;
            // Lấy lịch sử đăng nhập của người dùng
            const userLoginHistories = loginHistories[userId] || [];
            // Xóa nội dung cũ
            historyTable.innerHTML = '';
            // Nếu không có lịch sử
            if (userLoginHistories.length === 0) {
                historyTable.innerHTML = '<tr><td colspan="3">Không có lịch sử đăng nhập.</td></tr>';
            } else {
                // Thêm các dòng lịch sử
                userLoginHistories.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                <td>${new Date(item.login_time).toLocaleString('vi-VN')}</td>
                <td>${item.ip_address || 'Không có'}</td>
                <td>${item.device_info || 'Không có'}</td>
            `;
                    historyTable.appendChild(row);
                });
            }
            // Hiển thị popup
            popup.style.display = 'block';
            overlay.style.display = 'block';
        }
        // Ẩn popup lịch sử đăng nhập
        function hideLoginHistory() {
            const popup = document.getElementById('loginHistoryPopup');
            const overlay = document.getElementById('loginHistoryOverlay');
            popup.style.display = 'none';
            overlay.style.display = 'none';
        }
        //Bật tắt capcha
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('captchaForm');
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                // Hiển thị loading
                const btn = this.querySelector('button[type="submit"]');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
                btn.disabled = true;
                fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Cập nhật thành công!');
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        btn.innerHTML = '<i class="fas fa-save"></i> Lưu thay đổi';
                        btn.disabled = false;
                    });
            });
        });
        // popup chi tiết liên hệ
        // Hiển thị Popup Chi tiết Liên hệ
        function showContactPopup(id, name, phone, email, message, status, isRead) {
            const popup = document.getElementById('contactPopup');
            const overlay = document.getElementById('contactOverlay');
            document.getElementById('contactName').textContent = name;
            document.getElementById('contactPhone').textContent = phone;
            document.getElementById('contactEmail').textContent = email;
            document.getElementById('contactMessage').textContent = message;
            document.getElementById('contactStatus').textContent = status;
            // Hiển thị popup
            popup.style.display = 'block';
            overlay.style.display = 'block';
            // Nếu chưa đọc, gửi yêu cầu cập nhật trạng thái
            if (!isRead) {
                fetch(`/admin/contact/${id}/mark-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('contactStatus').textContent = 'Đã đọc';
                            // Cập nhật giao diện bảng nếu cần
                            const row = document.querySelector(`tr[style*="${id}"]`);
                            if (row) {
                                row.style.backgroundColor = 'var(--card-bg)';
                                row.style.color = 'var(--text)';
                                row.style.borderLeft = '4px solid transparent';
                                row.querySelector('span').style.backgroundColor = 'var(--border)';
                                row.querySelector('span').style.color = 'var(--text)';
                                row.querySelector('span').textContent = 'Đã đọc';
                            }
                            updateContactNotification();
                        }
                    })
                    .catch(error => console.error('Lỗi khi cập nhật trạng thái:', error));
            }
        }
        // Ẩn Popup Chi tiết Liên hệ
        function hideContactPopup() {
            const popup = document.getElementById('contactPopup');
            const overlay = document.getElementById('contactOverlay');
            popup.style.display = 'none';
            overlay.style.display = 'none';
        }
        // Hàm cập nhật dấu chấm xanh cho tab Liên hệ
        function updateContactNotification() {
            const contactTabLink = document.querySelector('.sidebar a[data-tab="contacts"]');
            const notificationDot = contactTabLink.querySelector('.notification-dot');
            const unreadCount = @json($contacts->where('is_read', false)->count()); // Số liên hệ chưa đọc ban đầu
            // Xóa dấu chấm cũ nếu có
            if (notificationDot) {
                notificationDot.remove();
            }
            // Thêm dấu chấm nếu còn liên hệ chưa đọc
            if (unreadCount > 0) {
                const dot = document.createElement('span');
                dot.className = 'notification-dot';
                contactTabLink.appendChild(dot);
            }
        }
        // Gọi hàm khi trang tải
        document.addEventListener('DOMContentLoaded', updateContactNotification);
        // Hàm cập nhật dấu chấm xanh
        function updateRechargeNotification() {
            const rechargeTabLink = document.querySelector('.sidebar a[data-tab="recharge"]');
            const notificationDot = rechargeTabLink.querySelector('.notification-dot');
            const rechargeCount = @json($rechargeRequests->count()); // Số lượng ban đầu từ PHP
            // Xóa dấu chấm cũ nếu có
            if (notificationDot) {
                notificationDot.remove();
            }
            // Thêm dấu chấm nếu còn yêu cầu
            if (rechargeCount > 0) {
                const dot = document.createElement('span');
                dot.className = 'notification-dot';
                rechargeTabLink.appendChild(dot);
            }
        }
        // Gọi hàm khi trang tải
        document.addEventListener('DOMContentLoaded', updateRechargeNotification);
        function filterUsers() {
            const search = document.getElementById('userSearch').value.toLowerCase();
            const role = document.getElementById('roleFilter').value;

            // Lọc trên bảng Người dùng Hoạt động
            const activeRows = document.querySelectorAll('#users tbody')[0].querySelectorAll('tr');
            activeRows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const email = row.cells[2].textContent.toLowerCase();
                const roleValue = row.cells[3].textContent.toLowerCase();
                const matchesSearch = name.includes(search) || email.includes(search);
                const matchesRole = !role || roleValue === role;
                row.style.display = matchesSearch && matchesRole ? '' : 'none';
            });

            // Lọc trên bảng Người dùng Không Hoạt động
            const inactiveRows = document.querySelectorAll('#users tbody')[1].querySelectorAll('tr');
            inactiveRows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const email = row.cells[2].textContent.toLowerCase();
                const roleValue = row.cells[3].textContent.toLowerCase();
                const matchesSearch = name.includes(search) || email.includes(search);
                const matchesRole = !role || roleValue === role;
                row.style.display = matchesSearch && matchesRole ? '' : 'none';
            });
        }
        //Quản lý tin tức trang Chủ
        // Biến để lưu giá trị ban đầu của tin tức
        let initialNewsValues = {};
        // Edit news row
        function editNewsRow(newsId) {
            const row = document.getElementById(`news-row-${newsId}`);
            const editables = row.querySelectorAll('.editable');
            const editBtn = row.querySelector('.edit-btn');
            const saveBtn = row.querySelector('.save-btn');
            const cancelBtn = row.querySelector('.cancel-btn');
            initialNewsValues[newsId] = {};
            editables.forEach(cell => {
                const field = cell.dataset.field;
                const input = cell.querySelector('input, textarea');
                initialNewsValues[newsId][field] = input.value;
                cell.classList.add('editing');
            });
            editables.forEach(cell => {
                const display = cell.querySelector('.display');
                const input = cell.querySelector('input, textarea');
                display.style.display = 'none';
                input.style.display = 'block';
            });
            editBtn.style.display = 'none';
            saveBtn.style.display = 'inline-flex';
            cancelBtn.style.display = 'inline-flex';
        }
        function cancelNewsEdit(newsId) {
            const row = document.getElementById(`news-row-${newsId}`);
            const editables = row.querySelectorAll('.editable');
            const editBtn = row.querySelector('.edit-btn');
            const saveBtn = row.querySelector('.save-btn');
            const cancelBtn = row.querySelector('.cancel-btn');
            editables.forEach(cell => {
                const display = cell.querySelector('.display');
                const input = cell.querySelector('input, textarea');
                input.style.display = 'none';
                display.style.display = 'block';
                input.value = initialNewsValues[newsId][cell.dataset.field];
                cell.classList.remove('editing');
                const contentCell = cell.querySelector('.description-cell');
                if (contentCell) {
                    contentCell.classList.remove('expanded');
                    const toggleLink = cell.querySelector('.toggle-description');
                    toggleLink.textContent = 'Xem thêm';
                }
            });
            editBtn.style.display = 'inline-flex';
            saveBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
            delete initialNewsValues[newsId];
        }
        // Toggle nội dung tin tức
        function toggleNewsContent(newsId) {
            const contentCell = document.getElementById(`content-${newsId}`);
            const toggleLink = document.getElementById(`toggle-content-${newsId}`);
            if (contentCell.classList.contains('expanded')) {
                contentCell.classList.remove('expanded');
                toggleLink.textContent = 'Xem thêm';
            } else {
                contentCell.classList.add('expanded');
                toggleLink.textContent = 'Ẩn bớt';
            }
        }
        // Show/Hide Add News Popup
        function showAddNewsPopup() {
            const popup = document.getElementById('addNewsPopup');
            const overlay = document.getElementById('addNewsOverlay');
            popup.style.display = 'block';
            overlay.style.display = 'block';
        }
        function hideAddNewsPopup() {
            const popup = document.getElementById('addNewsPopup');
            const overlay = document.getElementById('addNewsOverlay');
            popup.style.display = 'none';
            overlay.style.display = 'none';
        }
        // Xử lý gửi form trong tab Quản lý tin tức
        document.querySelectorAll('#news .edit-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const newsId = this.id.replace('news-form-', '');
                const editables = document.getElementById(`news-row-${newsId}`).querySelectorAll('.editable');
                let hasChanges = false;
                const existingHiddenInputs = form.querySelectorAll('input[type="hidden"]:not([name="_token"]):not([name="_method"])');
                existingHiddenInputs.forEach(input => input.remove());
                editables.forEach(cell => {
                    const field = cell.dataset.field;
                    const input = cell.querySelector('input, textarea');
                    const currentValue = input.value;
                    const initialValue = initialNewsValues[newsId][field];
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = field;
                    hiddenInput.value = currentValue;
                    form.appendChild(hiddenInput);
                    if (currentValue !== initialValue) {
                        hasChanges = true;
                    }
                });
                if (!hasChanges) {
                    alert('Không có thay đổi để lưu!');
                    return;
                }
                form.submit();
            });
        });
        //Mới
        // Dữ liệu lịch sử giao dịch từ PHP
        const transactions = @json($transactionHistory->toArray());
        // Hiển thị lịch sử giao dịch với phân trang
        function renderTransactionHistory(page = 1) {
            const tbody = document.getElementById('transactionBody');
            const pagination = document.getElementById('transactionPagination');
            const itemsPerPage = 10;
            const totalItems = transactions.length;
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            // Tính toán chỉ số bắt đầu và kết thúc
            const start = (page - 1) * itemsPerPage;
            const end = Math.min(start + itemsPerPage, totalItems);
            const paginatedTransactions = transactions.slice(start, end);
            // Xóa nội dung cũ
            tbody.innerHTML = '';
            // Thêm các hàng giao dịch
            paginatedTransactions.forEach(transaction => {
                const row = document.createElement('tr');
                row.innerHTML = `
            <td>${transaction.id}</td>
            <td>${transaction.user?.name ?? 'Người dùng không tồn tại'}</td>
            <td>${Number(transaction.amount).toLocaleString('vi-VN')} VNĐ</td>
            <td>${transaction.requested_tokens}</td>
            <td><span class="status ${transaction.status}">${transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1)}</span></td>
            <td>${new Date(transaction.created_at).toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</td>
        `;
                tbody.appendChild(row);
            });
            // Tạo phân trang
            pagination.innerHTML = '';
            if (totalPages > 1) {
                // Nút "Trước"
                const prevLink = document.createElement('a');
                prevLink.textContent = 'Trước';
                prevLink.href = '#';
                prevLink.className = page === 1 ? 'disabled' : '';
                prevLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (page > 1) renderTransactionHistory(page - 1);
                });
                pagination.appendChild(prevLink);
                // Các trang số
                for (let i = 1; i <= totalPages; i++) {
                    const pageLink = document.createElement('a');
                    pageLink.textContent = i;
                    pageLink.href = '#';
                    pageLink.className = i === page ? 'active' : '';
                    pageLink.addEventListener('click', (e) => {
                        e.preventDefault();
                        renderTransactionHistory(i);
                    });
                    pagination.appendChild(pageLink);
                }
                // Nút "Sau"
                const nextLink = document.createElement('a');
                nextLink.textContent = 'Sau';
                nextLink.href = '#';
                nextLink.className = page === totalPages ? 'disabled' : '';
                nextLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (page < totalPages) renderTransactionHistory(page + 1);
                });
                pagination.appendChild(nextLink);
            }
        }
        // Gọi hàm khi trang tải
        document.addEventListener('DOMContentLoaded', function () {
            if (transactions.length > 0) {
                renderTransactionHistory(1); // Hiển thị trang đầu tiên
            }
            // Các hàm khác như vẽ biểu đồ vẫn giữ nguyên
            const userTrend = @json($userTrend);
            const rechargeTrend = @json($rechargeTrend);
            const revenueTrend = @json($revenueTrend);
            const ratingTrend = @json($ratingTrend);
            function createChart(canvasId, labels, values, label, color, isCurrency = false) {
                const ctx = document.getElementById(canvasId);
                if (!ctx) return;
                new Chart(ctx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: label,
                            data: values,
                            borderColor: color,
                            backgroundColor: `${color}70`,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => isCurrency ? value.toLocaleString('vi-VN') + ' VNĐ' : value
                                }
                            },
                            x: { ticks: { font: { size: 10 } } }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: context => `${context.dataset.label}: ${isCurrency ? context.parsed.y.toLocaleString('vi-VN') + ' VNĐ' : context.parsed.y}`
                                }
                            }
                        }
                    }
                });
            }
            createChart('userTrendChart', userTrend.labels, userTrend.values, 'Người dùng mới', '#42a5f5');
            createChart('rechargeTrendChart', rechargeTrend.labels, rechargeTrend.values, 'Yêu cầu mới', '#ffca28');
            createChart('revenueTrendChart', revenueTrend.labels, revenueTrend.values, 'Doanh thu', '#00c853', true);
            createChart('ratingTrendChart', ratingTrend.labels, ratingTrend.values, 'Đánh giá', '#f44336');
        });
        //Thông tin hệ thống
        // Xử lý tab con trong "Thông tin hệ thống"
        function openSubTab(tabName) {
            document.querySelectorAll('#system-info .tab-content').forEach(tab => tab.style.display = 'none');
            document.querySelectorAll('#system-info .tab-button').forEach(btn => btn.classList.remove('active'));
            document.getElementById(tabName).style.display = 'block';
            document.querySelector(`#system-info .tab-button[onclick="openSubTab('${tabName}')"]`).classList.add('active');
        }
        function openTab(evt, tabName) {
            console.log('openTab called with tabName:', tabName);
            const tabcontent = document.getElementsByClassName('tab-content');
            for (let i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = 'none';
            }
            const tablinks = document.getElementsByClassName('tablinks');
            for (let i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove('active');
            }
            const targetTab = document.getElementById(tabName);
            if (!targetTab) {
                console.warn('Tab not found:', tabName);
                return;
            }
            targetTab.style.display = 'block';
            if (evt?.currentTarget) {
                evt.currentTarget.classList.add('active');
            } else {
                console.warn('Event currentTarget is null');
            }
            console.log('Opened tab:', tabName);
            if (tabName === 'system-info') {
                updateSystemCharts();
            }
        }

        @if($isSystemInfoEnabled)
            let isFetching = false;
            let fastApiRamChart = null, fastApiCpuChart = null, fastApiGpuChart = null;

            function getElement(id) {
                const element = document.getElementById(id);
                if (!element) console.warn(`Element with ID "${id}" not found`);
                return element;
            }

            function initCharts() {
                const canvases = [
                    { id: 'fastApiRamChart', chart: fastApiRamChart, label: 'RAM (MB)', color: '#49eb34' },
                    { id: 'fastApiCpuChart', chart: fastApiCpuChart, label: 'CPU (%)', color: '#e53935' },
                    { id: 'fastApiGpuChart', chart: fastApiGpuChart, label: 'GPU (%)', color: '#43a047' }
                ];

                canvases.forEach(item => {
                    const canvas = getElement(item.id);
                    if (canvas) {
                        if (item.chart) item.chart.destroy();
                        item.chart = new Chart(canvas.getContext('2d'), {
                            type: 'line',
                            data: {
                                labels: [],
                                datasets: [{
                                    label: item.label,
                                    data: [],
                                    borderColor: item.color,
                                    backgroundColor: item.color.replace('1', '0.2'),
                                    fill: true,
                                    tension: 0.4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: { beginAtZero: true, max: item.label.includes('%') ? 100 : undefined },
                                    x: { ticks: { font: { size: 10 } } }
                                }
                            }
                        });
                        if (item.id === 'fastApiRamChart') fastApiRamChart = item.chart;
                        if (item.id === 'fastApiCpuChart') fastApiCpuChart = item.chart;
                        if (item.id === 'fastApiGpuChart') fastApiGpuChart = item.chart;
                    }
                });
                console.log('Charts initialized');
            }

            function updateSystemCharts() {
                if (isFetching) {
                    console.log('Fetch already in progress, skipping');
                    return;
                }
                isFetching = true;
                console.log('Calling fetch for /admin/system/fastapi-stats');

                const errorDiv = getElement('system-error');
                if (!errorDiv) {
                    console.warn('Cannot proceed without system-error element');
                    isFetching = false;
                    return;
                }

                fetch('/admin/system/fastapi-stats', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                    .then(response => {
                        console.log('Fetch response status:', response.status, 'OK:', response.ok);
                        if (!response.ok) {
                            if (response.status === 401 || response.status === 403) {
                                throw new Error('Phiên đăng nhập hết hạn. Vui lòng đăng nhập lại.');
                            }
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Fetch data:', data);
                        if (data.error || data.message) {
                            errorDiv.textContent = data.error || data.message || 'Không thể lấy dữ liệu';
                            errorDiv.style.display = 'block';
                            return;
                        }
                        errorDiv.style.display = 'none';

                        const now = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                        if (fastApiRamChart) {
                            fastApiRamChart.data.labels.push(now);
                            fastApiRamChart.data.datasets[0].data.push(data.ram_used_mb || 0);
                            fastApiRamChart.update();
                        }
                        if (fastApiCpuChart) {
                            fastApiCpuChart.data.labels.push(now);
                            fastApiCpuChart.data.datasets[0].data.push(data.cpu_usage_percent || 0);
                            fastApiCpuChart.update();
                        }
                        if (fastApiGpuChart) {
                            fastApiGpuChart.data.labels.push(now);
                            fastApiGpuChart.data.datasets[0].data.push(data.gpu_usage_percent || 0);
                            fastApiGpuChart.update();
                        }

                        const statsList = getElement('fastapi-stats');
                        if (statsList) {
                            statsList.innerHTML = `
                                                                                                                                                                                                            <li>CPU Usage: ${data.cpu_usage_percent || 0}%</li>
                                                                                                                                                                                                            <li>RAM Total: ${data.ram_total_mb || 0} MB</li>
                                                                                                                                                                                                            <li>RAM Used: ${data.ram_used_mb || 0} MB</li>
                                                                                                                                                                                                            <li>RAM Usage: ${data.ram_usage_percent || 0}%</li>
                                                                                                                                                                                                            <li>GPU Usage: ${data.gpu_usage_percent || 0}%</li>
                                                                                                                                                                                                            <li>GPU Total: ${data.gpu_total_mb || 0} MB</li>
                                                                                                                                                                                                            <li>GPU Used: ${data.gpu_used_mb || 0} MB</li>
                                                                                                                                                                                                        `;
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi khi lấy dữ liệu FastAPI:', error);
                        errorDiv.textContent = 'Lỗi kết nối: ' + error.message;
                        errorDiv.style.display = 'block';
                        if (error.message.includes('đăng nhập')) {
                            alert('Phiên đăng nhập hết hạn. Chuyển hướng tới trang đăng nhập.');
                            window.location.href = '/login';
                        }
                    })
                    .finally(() => {
                        isFetching = false;
                        console.log('Fetch completed');
                    });
            }

            document.addEventListener('DOMContentLoaded', () => {
                console.log('DOM loaded, initializing system info');
                const systemInfo = getElement('system-info');
                if (!systemInfo) {
                    console.warn('System info tab not found');
                    return;
                }
                initCharts();
                if (systemInfo.style.display !== 'none') {
                    updateSystemCharts();
                }
                setInterval(() => {
                    console.log('Interval triggered for updateSystemCharts');
                    if (systemInfo.style.display !== 'none') {
                        updateSystemCharts();
                    }
                }, 5000);
            });
        @endif


        //Quản lý gói nạp tiền// Biến để lưu giá trị ban đầu của các gói nạp tiền
        let initialPackageValues = {};

        // Edit package row
        function editPackageRow(packageId) {
            const row = document.getElementById(`package-row-${packageId}`);
            const editables = row.querySelectorAll('.editable');
            const editBtn = row.querySelector('.edit-btn');
            const saveBtn = row.querySelector('.save-btn');
            const cancelBtn = row.querySelector('.cancel-btn');

            // Lưu giá trị ban đầu
            initialPackageValues[packageId] = {};
            editables.forEach(cell => {
                const field = cell.dataset.field;
                const input = cell.querySelector('input, select, textarea');
                initialPackageValues[packageId][field] = input.value;
                cell.classList.add('editing');
            });

            editables.forEach(cell => {
                const display = cell.querySelector('.display');
                const input = cell.querySelector('input, select, textarea');
                display.style.display = 'none';
                input.style.display = 'block';
            });

            editBtn.style.display = 'none';
            saveBtn.style.display = 'inline-flex';
            cancelBtn.style.display = 'inline-flex';
        }

        // Cancel edit package row
        function cancelPackageEdit(packageId) {
            const row = document.getElementById(`package-row-${packageId}`);
            const editables = row.querySelectorAll('.editable');
            const editBtn = row.querySelector('.edit-btn');
            const saveBtn = row.querySelector('.save-btn');
            const cancelBtn = row.querySelector('.cancel-btn');

            editables.forEach(cell => {
                const display = cell.querySelector('.display');
                const input = cell.querySelector('input, select, textarea');
                input.style.display = 'none';
                display.style.display = 'block';
                input.value = initialPackageValues[packageId][cell.dataset.field];
                cell.classList.remove('editing');

                const descriptionCell = cell.querySelector('.description-cell');
                if (descriptionCell) {
                    descriptionCell.classList.remove('expanded');
                    const toggleLink = cell.querySelector('.toggle-description');
                    toggleLink.textContent = 'Xem thêm';
                }
            });

            editBtn.style.display = 'inline-flex';
            saveBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
            delete initialPackageValues[packageId];
        }

        // Toggle package description
        function togglePackageDescription(packageId) {
            const descriptionCell = document.getElementById(`description-${packageId}`);
            const toggleLink = document.getElementById(`toggle-${packageId}`);
            if (descriptionCell.classList.contains('expanded')) {
                descriptionCell.classList.remove('expanded');
                toggleLink.textContent = 'Xem thêm';
            } else {
                descriptionCell.classList.add('expanded');
                toggleLink.textContent = 'Ẩn bớt';
            }
        }

        // Show/Hide Add Package Popup
        function showAddPackagePopup() {
            const popup = document.getElementById('addPackagePopup');
            const overlay = document.getElementById('addPackageOverlay');
            popup.style.display = 'block';
            overlay.style.display = 'block';
        }

        function hideAddPackagePopup() {
            const popup = document.getElementById('addPackagePopup');
            const overlay = document.getElementById('addPackageOverlay');
            popup.style.display = 'none';
            overlay.style.display = 'none';
        }

        // Xử lý gửi form trong tab Quản lý gói nạp tiền
        document.querySelectorAll('#recharge-packages .edit-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const packageId = this.id.replace('package-form-', '');
                const editables = document.getElementById(`package-row-${packageId}`).querySelectorAll('.editable');
                let hasChanges = false;

                const existingHiddenInputs = form.querySelectorAll('input[type="hidden"]:not([name="_token"]):not([name="_method"])');
                existingHiddenInputs.forEach(input => input.remove());

                editables.forEach(cell => {
                    const field = cell.dataset.field;
                    const input = cell.querySelector('input, select, textarea');
                    const currentValue = input.value;
                    const initialValue = initialPackageValues[packageId][field];

                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = field;
                    hiddenInput.value = currentValue;
                    form.appendChild(hiddenInput);

                    if (currentValue !== initialValue) {
                        hasChanges = true;
                    }
                });

                if (!hasChanges) {
                    alert('Không có thay đổi để lưu!');
                    return;
                }

                form.submit();
            });
        });
        // Show/Hide Add Metadata Popup
        function showAddMetadataPopup() {
            const popup = document.getElementById('addMetadataPopup');
            const overlay = document.getElementById('addMetadataOverlay');
            popup.style.display = 'block';
            overlay.style.display = 'block';
        }

        function hideAddMetadataPopup() {
            const popup = document.getElementById('addMetadataPopup');
            const overlay = document.getElementById('addMetadataOverlay');
            popup.style.display = 'none';
            overlay.style.display = 'none';
        }
        document.addEventListener('DOMContentLoaded', function () {
            const selectElement = document.getElementById('recognition_model');
            const saveButton = document.getElementById('saveButton');
            const currentModel = '{{ $recognitionModel ? $recognitionModel->value : '' }}';

            // Hàm kiểm tra và cập nhật trạng thái nút Lưu
            function updateSaveButton() {
                const selectedModel = selectElement.value;
                // Bật nút nếu mô hình được chọn khác với mô hình hiện tại
                saveButton.disabled = (selectedModel === currentModel);
            }

            // Gọi hàm khi tải trang để thiết lập trạng thái ban đầu
            updateSaveButton();

            // Lắng nghe sự kiện thay đổi trên dropdown
            selectElement.addEventListener('change', updateSaveButton);
        });
        // Xử lý tab con trong Quản lý người dùng
        function openSubTab(tabName) {
            // Ẩn tất cả nội dung tab con
            document.querySelectorAll('#users .sub-tab-content').forEach(tab => {
                tab.classList.remove('active');
                tab.style.display = 'none';
            });

            // Xóa lớp active từ tất cả các nút tab con
            document.querySelectorAll('#users .sub-tab-button').forEach(btn => {
                btn.classList.remove('active');
            });

            // Hiển thị tab con được chọn
            const targetTab = document.getElementById(tabName);
            if (targetTab) {
                targetTab.classList.add('active');
                targetTab.style.display = 'block';
            }

            // Thêm lớp active cho nút được nhấn
            const targetButton = document.querySelector(`#users .sub-tab-button[onclick="openSubTab('${tabName}')"]`);
            if (targetButton) {
                targetButton.classList.add('active');
            }

            // Reset trường tìm kiếm và hiển thị tất cả hàng
            const searchInput = document.getElementById('userSearch');
            searchInput.value = '';
            filterUsers();
        }

        // Hàm lọc người dùng
        function filterUsers() {
            const search = document.getElementById('userSearch').value.toLowerCase();
            const activeTab = document.querySelector('#users .sub-tab-content.active');

            if (!activeTab) return;

            const table = activeTab.querySelector('table');
            if (!table) return;

            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const email = row.cells[2].textContent.toLowerCase();
                const matchesSearch = name.includes(search) || email.includes(search);
                row.style.display = matchesSearch ? '' : 'none';
            });
        }

        // Gọi openSubTab khi trang tải để hiển thị tab Hoạt động mặc định
        document.addEventListener('DOMContentLoaded', function () {
            openSubTab('active-users');
        });

        const formData = new FormData();
        const fileInput = document.getElementById('apkFile'); // Đảm bảo ID đúng

        // Debug: Kiểm tra file đã được chọn
        console.log('Selected file:', fileInput.files[0]);

        formData.append('apkFile', fileInput.files[0]); // Tên trường PHẢI khớp với server
        formData.append('version', document.getElementById('version').value);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        // Debug: Kiểm tra FormData
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }

        fetch('/admin/apk/upload', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest' // Giúp Laravel nhận biết AJAX request
            },
            body: formData
        })
            .then(response => {
                if (!response.ok) throw response;
                return response.json();
            })
            .then(data => console.log('Success:', data))
            .catch(async (error) => {
                const errorData = await error.json();
                console.error('Error:', errorData);
            });
        // Xử lý tab con trong tab Tổng quan
        function openOverviewSubTab(tabName) {
            // Ẩn tất cả nội dung tab con
            document.querySelectorAll('#overview .sub-tab-content').forEach(tab => {
                tab.classList.remove('active');
                tab.style.display = 'none';
            });

            // Xóa lớp active từ tất cả các nút tab con
            document.querySelectorAll('#overview .sub-tab-button').forEach(btn => {
                btn.classList.remove('active');
            });

            // Hiển thị tab con được chọn
            const targetTab = document.getElementById(tabName);
            if (targetTab) {
                targetTab.classList.add('active');
                targetTab.style.display = 'block';
            }

            // Thêm lớp active cho nút được nhấn
            const targetButton = document.querySelector(`#overview .sub-tab-button[onclick="openOverviewSubTab('${tabName}')"]`);
            if (targetButton) {
                targetButton.classList.add('active');
            }
        }

        // Gọi openOverviewSubTab khi trang tải để hiển thị tab "Lịch Sử Giao Dịch" mặc định
        document.addEventListener('DOMContentLoaded', function () {
            openOverviewSubTab('transaction-history-tab');
        });
        function toggleCustomModelFields() {
            const recognitionModel = document.getElementById('recognition_model').value;
            const customModelFields = document.getElementById('custom-model-fields');
            const saveButton = document.getElementById('saveButton');
            const currentModel = '{{ $recognitionModel ? $recognitionModel->value : '' }}';

            // Hiển thị hoặc ẩn các trường tùy chỉnh
            if (recognitionModel === 'custom') {
                customModelFields.style.display = 'block';
            } else {
                customModelFields.style.display = 'none';
            }

            // Cập nhật trạng thái nút Lưu
            const selectedModel = recognitionModel;
            saveButton.disabled = (selectedModel === currentModel);
        }
        function toggleSwitchButton() {
            const recognitionModel = document.getElementById('recognition_model').value;
            const switchButton = document.getElementById('switchButton');
            const currentModel = '{{ $recognitionModel ? $recognitionModel->value : '' }}';
            switchButton.disabled = (recognitionModel === currentModel);
        }

        function toggleUploadButton() {
            const modelFile = document.getElementById('model_file').value;
            const modelClass = document.getElementById('model_class').value.trim();
            const uploadButton = document.getElementById('uploadButton');
            uploadButton.disabled = !(modelFile && modelClass);
        }
        function toggleNewsContent(id) {
            const content = document.getElementById(`content-${id}`);
            const toggle = document.getElementById(`toggle-content-${id}`);
            if (content.classList.contains('expanded')) {
                content.classList.remove('expanded');
                toggle.textContent = 'Xem thêm';
            } else {
                content.classList.add('expanded');
                toggle.textContent = 'Thu gọn';
            }
        }
        // Cập nhật tin tức
        function fetchNews() {
            const button = event.currentTarget;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang cập nhật...';
            button.disabled = true;

            fetch('{{ route("admin.news.fetch") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload(); // Tải lại trang để hiển thị tin tức mới
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi cập nhật tin tức:', error);
                    alert('Có lỗi xảy ra khi cập nhật tin tức.');
                })
                .finally(() => {
                    button.innerHTML = '<i class="fas fa-sync-alt"></i> Cập nhật tin tức';
                    button.disabled = false;
                });
        }
        // Toggle mô tả ngắn tin tức
        function toggleNewsExcerpt(newsId) {
            const excerptCell = document.getElementById(`excerpt-${newsId}`);
            const toggleLink = document.getElementById(`toggle-excerpt-${newsId}`);
            if (excerptCell.classList.contains('expanded')) {
                excerptCell.classList.remove('expanded');
                toggleLink.textContent = 'Xem thêm';
            } else {
                excerptCell.classList.add('expanded');
                toggleLink.textContent = 'Ẩn bớt';
            }
        }
        document.addEventListener('DOMContentLoaded', function () {
            // Lấy canvas
            const canvas = document.getElementById('revenueChartMonth');
            if (!canvas) {
                console.error("Không tìm thấy canvas với ID 'revenueChartMonth'");
                return;
            }

            // Kiểm tra canvas khác (rechargeTrendChart) để debug
            const otherCanvas = document.getElementById('rechargeTrendChart');
            if (otherCanvas) {
                console.warn("Tìm thấy canvas với ID 'rechargeTrendChart'. Có thể gây xung đột.");
            }

            // Hủy biểu đồ hiện có nếu tồn tại
            if (canvas.chart) {
                canvas.chart.destroy();
                console.log("Đã hủy biểu đồ cũ trên canvas 'revenueChartMonth'");
            }

            // Lấy dữ liệu từ PHP
            const revenueLabels = @json($revenueLabelsM);
            const revenueData = @json($revenueDataM);

            console.log('Revenue Labels:', revenueLabels);
            console.log('Revenue Data:', revenueData);

            // Kiểm tra dữ liệu
            const labels = revenueLabels && revenueLabels.length > 0 ? revenueLabels : ['Chưa có dữ liệu'];
            const data = revenueData && revenueData.length > 0 ? revenueData : [0];

            // Kiểm tra Chart.js
            if (typeof Chart === 'undefined') {
                console.error('Thư viện Chart.js không tải được');
                return;
            }

            // Tạo biểu đồ mới
            try {
                const ctx = canvas.getContext('2d');
                canvas.chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Doanh Thu (VNĐ)',
                            data: data,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Doanh Thu (VNĐ)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString('vi-VN') + ' VNĐ';
                                    }
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Tháng'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            title: {
                                display: true,
                                text: 'Doanh Thu Hàng Tháng'
                            }
                        }
                    }
                });
                console.log('Biểu đồ mới đã được khởi tạo trên canvas revenueChartMonth');
            } catch (error) {
                console.error('Lỗi khi tạo biểu đồ:', error);
            }
        });
    </script>
</body>

</html>