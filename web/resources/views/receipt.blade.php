<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn Nạp Tiền</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #ffffff;
            color: #333;
            line-height: 1.4;
            height: 267mm;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .container {
            width: 180mm;
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            box-sizing: border-box;
            max-height: 267mm;
            overflow: hidden;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #1e88e5;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .header h1 {
            color: #1e88e5;
            font-size: 20px;
            margin: 0;
            text-transform: uppercase;
        }

        .header p {
            font-size: 12px;
            color: #666;
            margin: 3px 0;
        }

        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 12px;
        }

        .invoice-details div {
            flex: 1;
        }

        .invoice-details .right {
            text-align: right;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .invoice-table th,
        .invoice-table td {
            border: 1px solid #e0e0e0;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }

        .invoice-table th {
            background: #1e88e5;
            color: #ffffff;
            font-weight: 600;
        }

        .invoice-table td {
            background: #f9f9f9;
        }

        .proof-image {
            margin-top: 10px;
            text-align: center;
        }

        .proof-image p {
            font-size: 12px;
            margin-bottom: 5px;
        }

        .proof-image img {
            max-width: 150px;
            max-height: 100px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        }

        .footer {
            text-align: center;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #e0e0e0;
            font-size: 10px;
            color: #666;
        }

        .footer p {
            margin: 2px 0;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
                height: auto;
            }

            .container {
                box-shadow: none;
                border: none;
                max-height: none;
            }

            .proof-image img {
                max-width: 120px;
                max-height: 80px;
            }
        }

        @media (max-width: 600px) {
            .container {
                width: 100%;
                padding: 10px;
            }

            .header h1 {
                font-size: 18px;
            }

            .header p {
                font-size: 11px;
            }

            .invoice-details {
                flex-direction: column;
                gap: 8px;
            }

            .invoice-details .right {
                text-align: left;
            }

            .invoice-table th,
            .invoice-table td {
                font-size: 11px;
                padding: 6px;
            }

            .proof-image img {
                max-width: 120px;
                max-height: 80px;
            }

            .footer {
                font-size: 9px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Hóa Đơn Nạp Tiền</h1>
            <p>Ceramic Recognition System</p>
            <p>Email: khangkhang1111777@gmail.com | Hotline: 0982638519</p>
        </div>

        <div class="invoice-details">
            <div class="left">
                <p><strong>Khách hàng:</strong> {{ $record->user->name ?? 'Khách hàng' }}</p>
                <p><strong>Email:</strong> {{ $record->user->email ?? 'N/A' }}</p>
            </div>
            <div class="right">
                <p><strong>Mã hóa đơn:</strong> #{{ $record->id }}</p>
                <p><strong>Ngày xuất:</strong> {{ now()->format('d/m/Y') }}</p>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Nội dung</th>
                    <th>Thông tin</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Số tiền</td>
                    <td>{{ number_format($record->amount) }} VNĐ</td>
                </tr>
                <tr>
                    <td>Tokens nhận</td>
                    <td>{{ $record->tokens_added }}</td>
                </tr>
                <tr>
                    <td>Ngày duyệt</td>
                    <td>{{ \Carbon\Carbon::parse($record->approved_at)->format('d/m/Y H:i') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <p>Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!</p>
            <p>Website: www.ceramicapp.com | Địa chỉ: 123 Đường Gốm, TP.HCM</p>
        </div>
    </div>
</body>

</html>