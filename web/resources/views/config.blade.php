<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cấu hình API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; }
        .container { max-width: 600px; margin: 50px auto; }
        .card { background-color: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 20px; }
        .form-group { margin-bottom: 15px; }
        .btn-primary { background-color: #007bff; border: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2 class="text-center">Cấu hình API</h2>
            <form action="{{ route('config.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="fastapi_url">API URL</label>
                    <input type="url" name="fastapi_url" id="fastapi_url" class="form-control" value="{{ $fastapiUrl }}" required>
                    @error('fastapi_url')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="fastapi_key">API Key</label>
                    <input type="text" name="fastapi_key" id="fastapi_key" class="form-control" value="{{ $fastapiKey }}" required>
                    @error('fastapi_key')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary w-100">Lưu cấu hình</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>