<?php
include 'config.php';
session_start();

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $login = mysqli_query(
        $koneksi,
        "SELECT * FROM users WHERE username='$username' AND password='$password'"
    );

    if (mysqli_num_rows($login) > 0) {
        $data = mysqli_fetch_assoc($login);

        if ($data['role'] == "admin") {
            $_SESSION['admin'] = $username;
            header("Location: dashboard_admin.php");
            exit;
        } elseif ($data['role'] == "pelanggan") {
            $_SESSION['user']   = $data['username'];
            $_SESSION['nama']   = $data['nama'];
            $_SESSION['id_user'] = $data['id'];
            header("Location: index.php");
            exit;
        }
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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
        .login-container {
            background-color: white;
            padding: 25px 20px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 350px;
        }
        .login-container h2 {
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
        input[type="password"] {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
        }
        input:focus {
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
        .login-container p {
            text-align: center;
            margin-top: 15px;
            font-size: 13px;
            color: #333;
        }
        .login-container a {
            color: #007bff;
            text-decoration: none;
        }
        .login-container a:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            font-size: 13px;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="login-container">
    <h2>Login</h2>
    <?php if (!empty($error)) { echo "<div class='error'>$error</div>"; } ?>
    <form method="POST" action="" autocomplete="off">
        <label>Username:</label>
        <input type="text" name="username" required autocomplete="off">

        <label>Password:</label>
        <input type="password" name="password" required autocomplete="new-password">

        <input type="submit" name="login" value="Login">
    </form>
    <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
</div>

</body>
</html>
