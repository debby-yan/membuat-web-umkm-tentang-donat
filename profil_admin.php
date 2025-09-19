<?php
include 'config.php';
session_start();

// ✅ Cek login admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin'];

// ✅ Ambil data admin dari database
function getAdminData($koneksi, $admin_id) {
    $stmt = $koneksi->prepare("SELECT id, username, nama, email, profile_picture FROM users WHERE id=? LIMIT 1");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

$admin = getAdminData($koneksi, $admin_id);

// ✅ Kalau data admin tidak ada, kasih default
if (!$admin) {
    $admin = [
        'id' => 0,
        'username' => 'Admin',
        'nama' => 'Administrator',
        'email' => 'admin@gmail.com',
        'profile_picture' => null
    ];
}

// ✅ Jika ada upload foto baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    $foto = $_FILES['foto'];

    if ($foto['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
        $filename = "img/" . $admin_id . "." . $ext; // nama file unik per admin

        // Pastikan folder img ada
        if (!is_dir("img")) {
            mkdir("img", 0777, true);
        }

        // Pindahkan file upload
        if (move_uploaded_file($foto['tmp_name'], $filename)) {
            // Update database
            $stmt = $koneksi->prepare("UPDATE users SET profile_picture=? WHERE id=?");
            $stmt->bind_param("si", $filename, $admin_id);
            $stmt->execute();

            // Simpan di session
            $_SESSION['admin_profile_picture'] = $filename;

            // Refresh halaman agar PP baru langsung muncul
            header("Location: profil_admin.php");
            exit();
        }
    }
}

// ✅ Tentukan foto profil (ambil dari DB terbaru atau default)
$foto = !empty($admin['profile_picture']) ? $admin['profile_picture'] : 'img/default_profile.png';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Admin</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f8f9fa;
    }
    .card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        transition: transform 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .profile-img {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .profile-wrapper {
        position: relative;
        display: inline-block;
    }
    .camera-icon {
        position: absolute;
        bottom: 8px;
        right: 8px;
        background: #fff;
        color: #007bff;
        border-radius: 50%;
        padding: 10px;
        cursor: pointer;
        border: 1px solid #ddd;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }
    .camera-icon:hover {
        background: #007bff;
        color: #fff;
    }
    h2 {
        font-weight: 700;
        margin-bottom: 30px;
    }
    .btn {
        border-radius: 30px;
        padding: 10px 25px;
    }
  </style>
</head>
<body>
  <div class="container mt-5">
      <h2 class="text-center">Profil Admin</h2>
      <div class="card text-center p-4">
          <div class="card-body">
              <div class="profile-wrapper">
                  <img src="<?= htmlspecialchars($foto) ?>" alt="Foto Profil" class="profile-img mb-3">
                  <label for="uploadFoto" class="camera-icon">
                      <i class="fas fa-camera"></i>
                  </label>
              </div>
              <h4 class="mt-3"><?= htmlspecialchars($admin['username']) ?></h4>
              <p class="mb-1"><i class="fas fa-user me-2"></i> <?= htmlspecialchars($admin['nama']) ?></p>
              <p><i class="fas fa-envelope me-2"></i> <?= htmlspecialchars($admin['email']) ?></p>

              <div class="d-flex justify-content-center gap-3 mt-3">
                  <a href="dashboard_admin.php" class="btn btn-outline-primary me-2">
                      <i class="fas fa-home"></i> Dashboard
                  </a>
                  <a href="logout.php" class="btn btn-outline-danger">
                      <i class="fas fa-sign-out-alt"></i> Logout
                  </a>
              </div>
          </div>
      </div>
  </div>

  <!-- Form Upload Foto -->
  <form method="POST" enctype="multipart/form-data" style="display:none;">
      <input type="file" id="uploadFoto" name="foto" onchange="this.form.submit()">
  </form>
</body>
</html>
