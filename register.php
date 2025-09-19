<?php
include "config.php";
session_start();

if (isset($_POST['daftar'])) {
    $nama     = $_POST['nama'];
    $email    = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hp       = $_POST['hp'];
    $alamat   = $_POST['alamat'];

    if ($nama && $email && $username && $password) {
        $daftar = mysqli_query(
            $koneksi,
            "INSERT INTO users (nama, email, username, password, hp, alamat, role)
             VALUES ('$nama', '$email', '$username', '$password', '$hp', '$alamat', 'pelanggan')"
        );

        if ($daftar) {
            header('Location: login.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Registrasi</title>
    <style>
        body {
            background: linear-gradient(to right, #e3f2fd, #bbdefb);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .register-container {
            background-color: white;
            padding: 25px 20px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .register-container h2 {
            text-align: center;
            margin-bottom: 18px;
            color: #0d47a1;
            font-size: 22px;
        }
        label {
            display: block;
            margin-top: 10px;
            margin-bottom: 4px;
            font-weight: 600;
            font-size: 14px;
            color: #333;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        textarea {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
        }
        input:focus,
        textarea:focus {
            border-color: #007bff;
            outline: none;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            margin-top: 20px;
            padding: 10px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .register-container p {
            text-align: center;
            margin-top: 15px;
            font-size: 13px;
            color: #333;
        }
        .register-container a {
            color: #007bff;
            text-decoration: none;
        }
        .register-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="register-container">
    <h2>Register</h2>
    <form method="POST" action="" autocomplete="off">
        <label>Nama:</label>
        <input type="text" name="nama" required autocomplete="off">

        <label>Email:</label>
        <input type="email" name="email" required autocomplete="off">

        <label>Username:</label>
        <input type="text" name="username" required autocomplete="off">

        <label>Password:</label>
        <input type="password" name="password" required autocomplete="new-password">

        <label>Nomor HP:</label>
        <input type="text" name="hp" required autocomplete="off">

        <label>Alamat:</label>
        <textarea name="alamat" rows="3" required autocomplete="off"></textarea>

        <input type="submit" name="daftar" value="Daftar">
    </form>

    <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
</div>
</body>
</html>
