<?php
include "config.php";

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = "";
if ($search !== "") {
    $safe = mysqli_real_escape_string($koneksi, $search);
    $where = "AND (nama LIKE '%$safe%' OR email LIKE '%$safe%' OR username LIKE '%$safe%')";
}

// Ambil semua data user role pelanggan
$result = mysqli_query($koneksi, "SELECT * FROM users WHERE role = 'pelanggan' $where ORDER BY id DESC");
$totalRows = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Pelanggan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background: linear-gradient(135deg, #1e3c72, #2a5298);
      font-family: "Segoe UI", sans-serif;
      color: #2c3e50;
      min-height: 100vh;
    }
    h2 {
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 20px;
    }
    .card-custom {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.12);
      padding: 24px;
    }
    thead {
      background: linear-gradient(135deg, #3498db, #2980b9) !important;
      color: white;
    }
    tbody tr:hover {
      background: #f5f9ff;
    }
    .btn-reset {
      border-radius: 8px;
      padding: 6px 14px;
      font-size: 0.85rem;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: linear-gradient(135deg, #e74c3c, #c0392b);
      border: none;
      color: white;
      transition: all 0.25s ease;
      box-shadow: 0 2px 6px rgba(231,76,60,0.4);
    }
    .btn-reset:hover {
      background: linear-gradient(135deg, #c0392b, #962d22);
      transform: translateY(-2px);
    }
    .btn-back {
      border-radius: 50%;
      width: 44px;
      height: 44px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #3498db, #2980b9);
      color: white;
      font-size: 1.25rem;
      border: none;
      transition: all 0.25s ease;
      text-decoration: none;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .btn-back:hover {
      background: linear-gradient(135deg, #2980b9, #2471a3);
      transform: translateY(-2px) scale(1.05);
      color: white;
    }
    table {
      border-radius: 10px;
      overflow: hidden;
    }
    .total-info {
      font-weight: 500;
      color: #555;
    }
  </style>
</head>
<body>
<div class="container my-5">
  <div class="card-custom">

    <!-- Header -->
    <div class="d-flex align-items-center mb-4 gap-3">
      <a href="dashboard_admin.php" class="btn-back">
        <i class="bi bi-arrow-left"></i>
      </a>
      <div>
        <h2 class="mb-0">Data Pelanggan</h2>
        <p class="total-info">Total: <?= $totalRows ?></p>
      </div>
    </div>

    <!-- Search -->
    <form class="search-bar d-flex justify-content-center mb-4" method="get" action="">
      <div class="input-group" style="max-width:500px;">
        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
        <input 
          type="text" 
          name="search" 
          class="form-control" 
          placeholder="Cari nama, email, username" 
          value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-outline-primary" type="submit">Cari</button>
      </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
      <table class="table table-bordered align-middle text-center shadow-sm">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Username</th>
            <th>No. HP</th>
            <th>Alamat</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $no = 1; 
          if ($totalRows > 0):
            while($row = mysqli_fetch_assoc($result)) { ?>
              <tr>
                <td><?= $no++; ?></td>
                <td class="text-start"><?= htmlspecialchars($row['nama']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['username']); ?></td>
                <td><?= htmlspecialchars($row['hp']); ?></td>
                <td class="text-start"><?= htmlspecialchars($row['alamat']); ?></td>
                <td>
                  <button 
                    class="btn-reset" 
                    onclick="resetPassword(<?= $row['id']; ?>, '<?= htmlspecialchars($row['nama']); ?>')">
                    <i class="bi bi-key-fill"></i> Reset
                  </button>
                </td>
              </tr>
          <?php } else: ?>
            <tr>
              <td colspan="7" class="text-center text-muted">Tidak ada data pelanggan</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

<script>
function resetPassword(id, nama) {
  Swal.fire({
    title: 'Konfirmasi Reset Password',
    text: "Password untuk " + nama + " akan direset.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#e74c3c',
    cancelButtonColor: '#3498db',
    confirmButtonText: 'Ya, Reset',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = "reset_password.php?id=" + id;
    }
  });
}
</script>
</body>
</html>
