<!DOCTYPE html>
<html>

<head>
    <title>Reset password Login</title>
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            border: 1px solid #ccc;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        .warning {
            color: #ff0000;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Reset password</h1>
        <p>Berikut adalah token untuk melakukan reset password aplikasi karya siswa SMK Islam Terpadu
            Nurul Imam:</p>
        <center>
            <h1> {{ $token }}</h1>
            <p>Token ini aktif selama 60 menit</p>
            <p class="warning">Penting: Mohon tidak menyebarkan token ini kepada orang lain.</p>
        </center>
    </div>
</body>

</html>
