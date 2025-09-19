<?php
include 'config.php';
session_start();

// ======================= CEK LOGIN ADMIN =======================
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

// ======================= AMBIL DATA ADMIN =======================
$id_admin = $_SESSION['admin'];
$stmt = $koneksi->prepare("SELECT * FROM users WHERE id=? LIMIT 1");
$stmt->bind_param("i", $id_admin);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// ======================= HANDLE UPLOAD FOTO PROFIL =======================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_profil'])) {
    $foto = $_FILES['foto_profil'];

    if ($foto['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $filename = "uploads/admin_" . $id_admin . "." . $ext;

        if (!is_dir("uploads")) mkdir("uploads", 0777, true);

        move_uploaded_file($foto['tmp_name'], $filename);

        $stmt = $koneksi->prepare("UPDATE users SET foto_profil=? WHERE id=?");
        $stmt->bind_param("si", $filename, $id_admin);
        $stmt->execute();

        $foto_profil = $filename;
    }
}

// ======================= HANDLE PRODUK =======================
// Edit mode
$editMode = false;
if (isset($_GET['aksi']) && $_GET['aksi'] === 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $produk = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM produk WHERE id='$id'"));
    $nama = $produk['nama'];
    $harga = $produk['harga'];
    $stok = $produk['stok'];
    $id_kategori = $produk['id_kategori']; // ✅ pakai id_kategori
    $foto = $produk['foto'];
    $deskripsi = $produk['deskripsi'];
    $editMode = true;
}

// Hapus produk
if (isset($_GET['aksi']) && $_GET['aksi'] === 'hapus' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $produk = mysqli_fetch_array(mysqli_query($koneksi, "SELECT foto FROM produk WHERE id='$id'"));
    if ($produk['foto'] && file_exists("img/" . $produk['foto'])) {
        unlink("img/" . $produk['foto']); // hapus file foto lama
    }
    mysqli_query($koneksi, "DELETE FROM produk WHERE id='$id'");
    header("Location: dashboard_admin.php");
    exit();
}

// Tambah / Update Produk
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nama'])) {
    $id         = $_POST['id'] ?? null; // null kalau tambah
    $nama       = $_POST['nama'];       // ✅ konsisten
    $harga      = $_POST['harga'];
    $stok       = $_POST['stok'];
    $id_kategori= $_POST['id_kategori'];
    $deskripsi  = $_POST['deskripsi'];

    // Foto produk
    if (!empty($_FILES['foto']['name'])) {
        $foto = time() . '_' . $_FILES['foto']['name'];
        move_uploaded_file($_FILES['foto']['tmp_name'], "img/" . $foto);
    } else {
        $foto = $_POST['foto_lama'] ?? '';
    }

    if ($id) {
        // UPDATE
        $sql = "UPDATE produk SET 
                    nama=?, harga=?, stok=?, id_kategori=?, deskripsi=?, foto=? 
                WHERE id=?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("sdiissi", $nama, $harga, $stok, $id_kategori, $deskripsi, $foto, $id);
        $stmt->execute();
    } else {
        // INSERT
        $sql = "INSERT INTO produk (nama, harga, stok, id_kategori, deskripsi, foto) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("sdiiss", $nama, $harga, $stok, $id_kategori, $deskripsi, $foto);
        $stmt->execute();
    }

    header("Location: dashboard_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body { 
    font-family: 'Inter', sans-serif; 
    background-color: #dedef2ff; 
    scroll-behavior: smooth; 
    font-size: 14px;
}

/* Sidebar */
.sidebar { 
    transition: all 0.3s; 
    background: linear-gradient(180deg, #1e3c72, #2a5298); /* biru elegan */
    color: #fff;
}
.sidebar.active { 
    width: 60px; 
}

/* Card */
.card { 
    transition: transform 0.3s, box-shadow 0.3s; 
    border-radius: 12px; 
}
.card:hover { 
    transform: translateY(-6px); 
    box-shadow: 0 12px 24px rgba(0,0,0,0.12); 
}

/* Table */
table th {
    background-color: #4facfe;   /* biru gradasi muda */
    color: #fff;
}
table tr:nth-child(even) { 
    background-color: #e6f2ff;   /* biru sangat muda */
}

/* Tombol */
.btn-edit { 
    background-color: #4facfe;   /* biru utama */
    color: #fff; 
}
.btn-edit:hover { 
    background-color: #2196f3;   /* biru lebih tua */
}
.btn-delete { 
    background-color: #00c6ff;   /* biru cerah */
    color: #fff; 
}
.btn-delete:hover { 
    background-color: #0072ff;   /* biru gelap */
}

</style>
</head>
<body class="flex h-screen">

<!-- Sidebar -->
<div id="sidebar" class="sidebar w-64 flex flex-col p-5 text-white bg-green-700 transition-all duration-300">
    <div class="flex items-center justify-between mb-6">
        <h2 id="sidebarTitle" class="text-2xl font-bold">Admin</h2>
        <button id="toggleSidebar" class="text-white text-lg">
            <i id="sidebarIcon" class="fas fa-angle-left transition-transform duration-300"></i>
        </button>
    </div>

    <!-- Profile Section -->
    <div class="mb-6 flex items-center gap-3 relative">
        <!-- Foto Profil -->
        <a href="profil_admin.php">
            <img id="profileImg" 
                 src="<?= $admin['profile_picture'] ?? 'img/default_profile.png' ?>" 
                 alt="Profile" 
                 class="rounded-full w-12 h-12 border-2 border-white shadow object-cover cursor-pointer">
        </a>
        <!-- Tombol Kamera -->
        <label for="photoUpload" 
               class="absolute bottom-0 left-8 bg-gray-800 p-1 rounded-full cursor-pointer border border-white">
            <svg xmlns="http://www.w3.org/2000/svg" 
                 class="h-4 w-4 text-white" 
                 fill="none" 
                 viewBox="0 0 24 24" 
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M3 7h2l3-4h8l3 4h2a1 1 0 011 1v12a1 1 0 01-1 1H3a1 1 0 01-1-1V8a1 1 0 011-1z" />
            </svg>
        </label>
        <input type="file" id="photoUpload" name="photo" class="hidden" onchange="previewPhoto(event)">

        <!-- Username -->
        <div id="profileText" class="flex flex-col">
            <h3 class="font-semibold text-white text-lg">
                <?= htmlspecialchars($admin['nama'] ?? 'Admin') ?>
            </h3>
            <!-- Role -->
            <p class="text-sm text-gray-200">
                <?= htmlspecialchars($admin['role'] ?? 'Super Admin') ?>
            </p>
        </div>  
    </div>

    <!-- Menu -->
    <nav class="flex-1">
        <a href="dashboard_admin.php" class="menu-item flex items-center gap-3 p-3 rounded-lg hover:bg-green-500 mb-2 transition">
            <i class="fas fa-home"></i><span class="menu-text">Dashboard</span>
        </a>
        <a href="#produk" class="menu-item flex items-center gap-3 p-3 rounded-lg hover:bg-green-500 mb-2 transition">
            <i class="fas fa-box"></i><span class="menu-text">Tambah Produk</span>
        </a>
        <a href="#dataBarang" class="menu-item flex items-center gap-3 p-3 rounded-lg hover:bg-green-500 mb-2 transition">
            <i class="fas fa-layer-group"></i><span class="menu-text">Data Barang</span>
        </a>
        <a href="riwayat_transaksi_admin.php" class="menu-item flex items-center gap-3 p-3 rounded-lg hover:bg-green-500 mb-2 transition">
            <i class="fas fa-receipt"></i><span class="menu-text">Riwayat Transaksi</span>
        </a>
        <a href="data_pelanggan.php" class="menu-item flex items-center gap-3 p-3 rounded-lg hover:bg-green-500 mb-2 transition">
            <i class="fas fa-users"></i><span class="menu-text">Data Pelanggan</span>
        </a>
        <a href="index.php" class="menu-item flex items-center gap-3 p-3 rounded-lg hover:bg-green-500 mb-2 transition">
            <i class="fas fa-arrow-left"></i><span class="menu-text">Kembali</span>
        </a>
        <a href="logout.php" class="menu-item flex items-center gap-3 p-3 rounded-lg hover:bg-red-600 mt-6 transition">
            <i class="fas fa-sign-out-alt"></i><span class="menu-text">Logout</span>
        </a>
    </nav>
</div>

<script>
const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('toggleSidebar');
const sidebarIcon = document.getElementById('sidebarIcon');
const menuTexts = document.querySelectorAll('.menu-text');
const profileText = document.getElementById('profileText');
const sidebarTitle = document.getElementById('sidebarTitle');

toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('w-64');
    sidebar.classList.toggle('w-20'); // kecilkan sidebar
    sidebarIcon.classList.toggle('rotate-180'); // panah putar

    menuTexts.forEach(text => {
        text.classList.toggle('hidden'); // sembunyikan teks menu
    });

    profileText.classList.toggle('hidden'); // sembunyikan nama profil
    sidebarTitle.classList.toggle('hidden'); // sembunyikan judul Admin
});
</script>
<!-- end sidebbar! -->

<!-- Main Content -->
<div class="flex-1 p-8 overflow-auto">

    <!-- Dashboard -->
    <!-- <section id="dashboard" class="mb-8">
        <h1 class="text-3xl font-bold mb-6">Dashboard</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card bg-green-200 p-6 shadow">
                <h2 class="font-semibold text-gray-700">Total Produk</h2>
                <p class="text-2xl font-bold mt-2">120</p>
            </div>
            <div class="card bg-green-200 p-6 shadow">
                <h2 class="font-semibold text-gray-700">Transaksi Hari Ini</h2>
                <p class="text-2xl font-bold mt-2">30</p>
            </div>
            <div class="card bg-green-200 p-6 shadow">
                <h2 class="font-semibold text-gray-700">Pendapatan</h2>
                <p class="text-2xl font-bold mt-2">$1,500</p>
            </div>
        </div>
    </section> -->

<!-- ======================= FORM TAMBAH / EDIT PRODUK ======================= -->
<section id="produk" class="mb-8">
    <h1 class="text-2xl font-bold mb-4"><?= $editMode ? 'Edit Produk' : 'Tambah Produk' ?></h1>
    <form id="productForm" action="" method="POST" enctype="multipart/form-data" 
          class="bg-white p-6 rounded-xl shadow-md space-y-5">
        <?php if($editMode): ?>
            <input type="hidden" name="id" value="<?= $id ?>">
        <?php endif; ?>

        <div>
            <label class="block font-semibold mb-2">Nama Produk</label>
            <input type="text" id="nama" name="nama" value="<?= @$nama ?>" 
                class="w-full border rounded-lg p-3" placeholder="Masukkan nama produk" required>
        </div>

        <div>
            <label class="block font-semibold mb-2">Harga</label>
            <input type="number" id="harga" name="harga" value="<?= @$harga ?>" 
                class="w-full border rounded-lg p-3" placeholder="Masukkan harga" required>
        </div>

        <div>
            <label class="block font-semibold mb-2">Stok</label>
            <input type="number" id="stok" name="stok" value="<?= @$stok ?>" 
                class="w-full border rounded-lg p-3" placeholder="Jumlah stok" required>
        </div>

        <div>
            <label class="block font-semibold mb-2">Kategori</label>
            <select id="id_kategori" name="id_kategori" class="w-full border rounded-lg p-3" required>
                <option value="">-- Pilih Kategori --</option>
                <?php
                $kategori_q = mysqli_query($koneksi, "SELECT * FROM kategori");
                while ($row = mysqli_fetch_assoc($kategori_q)) {
                    $selected = ($editMode && $row['id_kategori'] == @$id_kategori) ? "selected" : "";
                    echo "<option value='{$row['id_kategori']}' $selected>{$row['nama_kategori']}</option>";
                }
                ?>
            </select>
        </div>

        <div>
            <label class="block font-semibold mb-2">Foto Produk</label>
            <input type="file" id="productImage" name="foto" class="w-full border rounded-lg p-2" accept="image/*">
            <input type="hidden" name="foto_lama" value="<?= @$foto ?>">

            <?php if (!empty($foto)): ?>
                <div class="mt-2">
                    <img id="preview" src="img/<?= htmlspecialchars($foto) ?>" 
                         class="max-h-40 rounded-lg border" alt="Preview Foto Lama">
                </div>
            <?php else: ?>
                <img id="preview" class="max-h-40 rounded-lg border hidden" alt="Preview Foto">
            <?php endif; ?>
        </div>

        <div>
            <label class="block font-semibold mb-2">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" class="w-full border rounded-lg p-3" rows="4" 
                placeholder="Masukkan deskripsi produk" required><?= @$deskripsi ?></textarea>
        </div>

        <div class="text-center">
            <button type="submit" id="submitBtn" name="save"
                class="bg-green-300 text-gray-800 p-3 rounded-lg hover:bg-green-400 font-semibold transition">
                <?= $editMode ? 'Update Produk' : 'Tambah Produk' ?>
            </button>
        </div>
    </form>
</section>

<!-- ======================= DATA BARANG ======================= -->
<section id="dataBarang" class="mb-8">
    <h1 class="text-2xl font-bold mb-4">Data Barang</h1>
    <div class="overflow-x-auto bg-white rounded-xl shadow-md">
        <table class="min-w-full divide-y divide-gray-200" id="productTable">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left">No</th>
                    <th class="px-4 py-2 text-left">Nama Produk</th>
                    <th class="px-4 py-2 text-left">Harga</th>
                    <th class="px-4 py-2 text-left">Stok</th>
                    <th class="px-4 py-2 text-left">Kategori</th>
                    <th class="px-4 py-2 text-left">Foto</th>
                    <th class="px-4 py-2 text-left">Deskripsi</th>
                    <th class="px-4 py-2 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $hasil = mysqli_query($koneksi, "
                    SELECT p.*, k.nama_kategori 
                    FROM produk p
                    JOIN kategori k ON p.id_kategori = k.id_kategori
                ");
                while($data = mysqli_fetch_array($hasil)):
                ?>
                <tr class="border-b">
                    <td class="px-4 py-2"><?= $no++ ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($data['nama']) ?></td>
                    <td class="px-4 py-2">Rp <?= number_format($data['harga'],0,',','.') ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($data['stok']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($data['nama_kategori']) ?></td>
                    <td class="px-4 py-2">
                        <img src="img/<?= htmlspecialchars($data['foto']) ?>" class="h-12 w-auto rounded border" alt="Foto Produk">
                    </td>
                    <td class="px-4 py-2"><?= htmlspecialchars($data['deskripsi']) ?></td>
                    <td class="px-4 py-2">
                        <div class="flex gap-2">
                            <button onclick="editProduct(<?= $data['id'] ?>)" 
                                class="flex items-center justify-center w-10 h-10 bg-blue-500 text-white rounded hover:bg-blue-600 transition" 
                                title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteProduct(<?= $data['id'] ?>)" 
                                class="flex items-center justify-center w-10 h-10 bg-red-500 text-white rounded hover:bg-red-600 transition" 
                                title="Hapus">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- ======================= SWEETALERT ======================= -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Preview gambar
const productImage = document.getElementById('productImage');
const preview = document.getElementById('preview');
if (productImage) {
    productImage.addEventListener('change', function(){
        const file = this.files[0];
        if(file){
            const reader = new FileReader();
            reader.onload = function(e){
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });
}

// Submit produk (Tambah/Edit)
document.getElementById('productForm').addEventListener('submit', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Pastikan data sudah benar!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28A745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Data produk berhasil disimpan.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                willClose: () => {
                    e.target.submit(); // submit ke PHP
                }
            });
        }
    });
});

// Hapus produk
function deleteProduct(id) {
    Swal.fire({
        title: 'Hapus Produk?',
        text: "Produk akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#C0392B',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Terhapus!',
                text: 'Produk berhasil dihapus.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                willClose: () => {
                    window.location.href = "dashboard_admin.php?aksi=hapus&id=" + id;
                }
            });
        }
    });
}

// Edit produk
function editProduct(id) {
    Swal.fire({
        title: 'Edit Produk',
        text: 'Anda akan diarahkan ke halaman edit produk.',
        icon: 'info',
        confirmButtonText: 'OK'
    }).then(() => {
        window.location.href = "dashboard_admin.php?aksi=edit&id=" + id;
    });
}
</script>
</body>
</html>