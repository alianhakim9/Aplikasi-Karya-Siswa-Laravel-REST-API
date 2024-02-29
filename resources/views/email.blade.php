<!DOCTYPE html>
<html>

<head>
    <title>Akun Login</title>
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
        <h1>Akun Login</h1>
        <p>Halo {{ $akun['nama_lengkap'] }}, Berikut adalah akun login untuk aplikasi karya siswa SMK Islam Terpadu
            Nurul Imam:</p>
        <ul>
            <li>Email: {{ $akun['email'] }}</li>
            <li>Password: {{ $akun['password'] }}</li>
        </ul>
        <p class="warning">Penting: Mohon tidak menyebarkan akun ini kepada orang lain dan pastikan untuk mengubah
            password secara berkala.</p>
    </div>
</body>

</html>
