<?php
include "config.php";

// kalau ada id di URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // ambil data user berdasarkan id
    $result = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$id'");
    $user = mysqli_fetch_assoc($result);

    // generate password random (8 karakter campuran)
    function generateRandomPassword($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($chars), 0, $length);
    }

    $password_baru = generateRandomPassword();

    // update password langsung (tanpa hash)
    $query = "UPDATE users SET password='$password_baru' WHERE id='$id'";
    $update = mysqli_query($koneksi, $query);

    // siapkan status notifikasi
    if ($update) {
        $status = "success";
        $pesan  = "Password untuk {$user['nama']} berhasil direset! Password baru: $password_baru";
    } else {
        $status = "error";
        $pesan  = "Gagal mereset password!";
    }
} else {
    header("Location: data_pelanggan.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php if (isset($status)) : ?>
<script>
    Swal.fire({
        icon: '<?= $status ?>',
        title: '<?= ($status == "success") ? "Berhasil" : "Gagal" ?>',
        html: '<?= $pesan ?>',
        confirmButtonColor: '#3085d6'
    }).then((result) => {
        window.location = "data_pelanggan.php";
    });
</script>
<?php endif; ?>
</body>
</html>