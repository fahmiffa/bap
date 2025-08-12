<!DOCTYPE html>
<html>
<head>
    <title>Upload PDF</title>
</head>
<body>
    <h1>Upload PDF</h1>
    <form action="{{ route('pdf.process') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="pdf" required>
        <button type="submit">Upload & Proses</button>
    </form>
</body>
</html>
