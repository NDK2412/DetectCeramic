<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết liên hệ</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            padding: 2rem;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2ecc71;
            margin-bottom: 1.5rem;
        }
        p {
            margin-bottom: 1rem;
        }
        .btn {
            display: inline-block;
            padding: 0.6rem 1.2rem;
            background-color: #2ecc71;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 1rem;
        }
        .btn:hover {
            background-color: #f1c40f;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Chi tiết liên hệ từ {{ $contact->name }}</h1>
        <p><strong>Số điện thoại:</strong> {{ $contact->phone }}</p>
        <p><strong>Email:</strong> {{ $contact->email }}</p>
        <p><strong>Nội dung:</strong> {{ $contact->message }}</p>
        <p><strong>Trạng thái:</strong> {{ $contact->is_read ? 'Đã đọc' : 'Chưa đọc' }}</p>
        <a href="{{ route('admin.index') }}" class="btn">Quay lại</a>
    </div>
</body>
</html>