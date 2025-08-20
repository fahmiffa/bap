<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Preview Word</title>
    <style>
        #viewer {
            width: 100%;
            height: 600px;
            border: 1px solid #ccc;
            overflow: auto;
        }
    </style>
</head>

<body>
    <iframe src="https://docs.google.com/gview?url={{ urlencode($fileUrl) }}&embedded=true"
            style="width:50%; height:600px;" frameborder="0">
    </iframe>
</body>
</html>
