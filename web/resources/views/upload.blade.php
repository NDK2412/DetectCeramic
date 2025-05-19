<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Ảnh</title>
</head>

<body>
    <h1>Upload Ảnh</h1>
    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
        <img src="{{ asset('images/' . session('image')) }}" alt="Uploaded Image" style="max-width: 300px;">
    @endif

    <form action="/upload" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="image" required>
        <button type="submit">Upload</button>
    </form>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</body>

</html>