<?php
session_start();
include 'config.php';

// Pastikan user login
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit();
}

// Ambil id user dari session user array
$user_id = $_SESSION['id_user'];
$success = "";
$error = "";

// Ambil data user dari database
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'"));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama    = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $alamat  = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $foto    = $user['profile_picture']; // default foto lama

    // Upload foto jika ada
    if (!empty($_FILES['foto']['name'])) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','gif'];

        if (in_array(strtolower($ext), $allowed)) {
            $nama_file = "profile_{$user_id}." . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], "img/" . $nama_file);
            $foto = $nama_file;
        } else {
            $error = "Format foto tidak valid.";
        }
    }

    // Update data (email & hp tidak diubah)
    if (empty($error)) {
        mysqli_query($koneksi, "UPDATE users 
            SET nama='$nama', alamat='$alamat', profile_picture='$foto' 
            WHERE id='$user_id'");

        // Update session user
        $_SESSION['user'] = [
            'id' => $user_id,
            'nama' => $nama,
            'alamat' => $alamat,
            'profile_picture' => $foto,
            'email' => $user['email'],
            'hp' => $user['hp']
        ];

        $success = "Profil berhasil diperbarui!";
        header("Location: profile.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <style>
    body {
        font-family: 'Segoe UI', Roboto, -apple-system, sans-serif;
        background: #f9fafb;
        margin: 0;
        padding: 20px;
    }
    .container {
        max-width: 620px;
        margin: 50px auto;
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 12px 35px rgba(59, 130, 246, 0.15); /* biru shadow */
        padding: 40px 35px;
        position: relative;
        overflow: hidden;
    }
    /* Strip kiri & kanan */
    .container::before,
    .container::after {
        content: "";
        position: absolute;
        top: 0;
        width: 12px;
        height: 100%;
        background: linear-gradient(180deg, #3b82f6, #60a5fa); /* biru */
    }
    .container::before { left: 0; border-top-left-radius: 18px; border-bottom-left-radius: 18px; }
    .container::after { right: 0; border-top-right-radius: 18px; border-bottom-right-radius: 18px; }

    h2 {
        text-align: center;
        color: #3b82f6; /* biru */
        font-weight: 700;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .alert {
        text-align: center;
        margin-bottom: 18px;
        font-weight: 500;
        border-radius: 8px;
        padding: 10px;
    }
    .error { background: #fee2e2; color: #b91c1c; }
    .success { background: #dbf4ff; color: #0369a1; } /* biru muda */

    .profile-img {
        width: 130px;
        height: 130px;
        display: block;
        margin: 0 auto 20px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #dbeafe; /* biru border */
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }
    label {
        display: block;
        margin: 14px 0 6px;
        font-weight: 600;
        color: #1e40af; /* biru gelap */
    }
    input, textarea {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #d1d5db;
        background-color: #f9fafb;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.2s ease;
    }
    input:focus, textarea:focus {
        outline: none;
        border-color: #3b82f6; /* biru */
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
        background: white;
    }
    input[readonly] {
        cursor: not-allowed;
        background-color: #f3f4f6;
        color: #6b7280;
    }
    button {
        width: 100%;
        padding: 14px;
        margin-top: 30px;
        background: linear-gradient(135deg, #3b82f6, #60a5fa); /* biru gradien */
        color: white;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        transition: 0.3s;
    }
    button:hover {
        background: linear-gradient(135deg, #2563eb, #3b82f6); /* biru hover */
        transform: translateY(-2px);
    }
</style>

</head>
<body>

<div class="container">
    <h2><i class="fas fa-user-edit"></i> Edit Profil</h2>

    <?php if (!empty($error)): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Nama Lengkap</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>

        <label>Email</label>
        <input type="email" value="<?= htmlspecialchars($user['email']) ?>" readonly id="emailField">

        <label>No. Telepon</label>
        <input type="text" value="<?= htmlspecialchars($user['hp'] ?? '') ?>" readonly id="hpField">

        <label>Alamat</label>
        <textarea name="alamat" rows="3" required><?= htmlspecialchars($user['alamat'] ?? '') ?></textarea>

        <label>Foto Profil Saat Ini</label>
        <?php if (!empty($user['profile_picture']) && file_exists('img/' . $user['profile_picture'])): ?>
            <img src="img/<?= htmlspecialchars($user['profile_picture']) ?>" alt="profile_picture" class="profile-img">
        <?php else: ?>
            <img src="img/" alt="No Foto" class="profile-img">
        <?php endif; ?>

        <label>Ganti Foto Profil</label>
        <input type="file" name="foto" accept="image/*">

        <button type="submit"><i class="fas fa-save"></i> Simpan Perubahan</button>
    </form>
</div>

<script>
    // SweetAlert untuk field readonly
    document.getElementById('emailField').addEventListener('click', function() {
        Swal.fire({
            icon: 'info',
            title: 'Tidak Bisa Diubah',
            text: 'Email tidak dapat diubah!',
            confirmButtonColor: '#10b981'
        });
    });

    document.getElementById('hpField').addEventListener('click', function() {
        Swal.fire({
            icon: 'info',
            title: 'Tidak Bisa Diubah',
            text: 'Nomor telepon tidak dapat diubah!',
            confirmButtonColor: '#10b981'
        });
    });
</script>

</body>
</html>