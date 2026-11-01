<?php
/**
 * ====================================================================================
 * FILE: pengguna.php
 * MODUL: Pengelolaan Data Pengguna (User Management)
 * DESKRIPSI:
 * Menangani fungsi CRUD (Create, Read, Update, Delete) akun pengguna sistem
 * serta pencarian pengguna. Hak akses halaman ini dibatasi khusus role 'Admin'.
 * Sesuai Gambar 4.22 dan Tabel 4.10 dokumen skripsi.
 * ====================================================================================
 */

if (!defined('APP_INIT')) define('APP_INIT', true);
require_once __DIR__ . '/config/database.php';

// Proteksi hak akses: Hanya pengguna dengan kategori 'Admin' yang berhak mengelola data pengguna
check_login('Admin');

$page_title = 'Data Pengguna';
$page_heading = 'Kelola Data Pengguna';

$edit_data = null;
$search = trim($_GET['search'] ?? '');

// 1. MEMPROSES AKSI FORM POST (CREATE / UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id_pengguna = trim($_POST['id_pengguna'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $kategori = trim($_POST['kategori'] ?? ''); // 'Admin' atau 'Penguji'

    // AKSI TAMBAH PENGGUNA BARU (CREATE)
    if ($action === 'create') {
        // Otomatisasi generate ID Pengguna (Contoh: ID0004) jika tidak diisi manual
        if (empty($id_pengguna)) {
            $last = query("SELECT id_pengguna FROM pengguna ORDER BY id_pengguna DESC LIMIT 1");
            $next_num = 1;
            if (!empty($last)) {
                $num = (int) preg_replace('/[^0-9]/', '', $last[0]['id_pengguna']);
                $next_num = $num + 1;
            }
            $id_pengguna = 'ID' . str_pad($next_num, 4, '0', STR_PAD_LEFT);
        }

        // Cek apakah username sudah pernah digunakan
        $check = query("SELECT * FROM pengguna WHERE username = '$username'");
        if (!empty($check)) {
            set_flash('danger', 'Username sudah digunakan oleh akun lain! Gunakan username berbeda.');
        } else {
            $stmt = $conn->prepare("INSERT INTO pengguna (id_pengguna, username, password, nama, kategori) VALUES (?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sssss", $id_pengguna, $username, $password, $nama, $kategori);
                if ($stmt->execute()) {
                    set_flash('success', 'Data pengguna baru berhasil ditambahkan!');
                    header("Location: pengguna.php");
                    exit;
                } else {
                    set_flash('danger', 'Gagal menambahkan data: ' . $stmt->error);
                }
            }
        }
    } 
    // AKSI PERBARUI PENGGUNA (UPDATE)
    elseif ($action === 'update') {
        $stmt = $conn->prepare("UPDATE pengguna SET username = ?, password = ?, nama = ?, kategori = ? WHERE id_pengguna = ?");
        if ($stmt) {
            $stmt->bind_param("sssss", $username, $password, $nama, $kategori, $id_pengguna);
            if ($stmt->execute()) {
                set_flash('success', 'Data pengguna berhasil diperbarui!');
                header("Location: pengguna.php");
                exit;
            } else {
                set_flash('danger', 'Gagal memperbarui data: ' . $stmt->error);
            }
        }
    }
}

// Handle GET Delete
if (isset($_GET['delete'])) {
    $del_id = trim($_GET['delete']);
    if ($del_id === $_SESSION['user_id']) {
        set_flash('danger', 'Tidak dapat menghapus akun yang sedang Anda gunakan login!');
    } else {
        $stmt = $conn->prepare("DELETE FROM pengguna WHERE id_pengguna = ?");
        $stmt->bind_param("s", $del_id);
        if ($stmt->execute()) {
            set_flash('success', 'Data pengguna berhasil dihapus!');
        } else {
            set_flash('danger', 'Gagal menghapus data: ' . $stmt->error);
        }
    }
    header("Location: pengguna.php");
    exit;
}

// Handle GET Edit
if (isset($_GET['edit'])) {
    $edit_id = trim($_GET['edit']);
    $res = query("SELECT * FROM pengguna WHERE id_pengguna = '$edit_id'");
    if (!empty($res)) {
        $edit_data = $res[0];
    }
}

// Query Pengguna with Search
$where = "";
if (!empty($search)) {
    $s = $conn->real_escape_string($search);
    $where = "WHERE username LIKE '%$s%' OR nama LIKE '%$s%' OR kategori LIKE '%$s%'";
}
$list_pengguna = query("SELECT * FROM pengguna $where ORDER BY id_pengguna ASC");

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="row">
    <!-- Form Input / Edit (Kiri - Sesuai Gambar 4.22) -->
    <div class="col-lg-4 mb-4">
        <div class="card-custom">
            <div class="card-header bg-light">
                <h5 class="fw-bold">
                    <i class="fa-solid fa-<?= $edit_data ? 'pen-to-square text-warning' : 'user-plus text-primary' ?> me-2"></i>
                    <?= $edit_data ? 'Form Edit Pengguna' : 'Form Tambah Pengguna' ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="pengguna.php">
                    <input type="hidden" name="action" value="<?= $edit_data ? 'update' : 'create' ?>">
                    <input type="hidden" name="id_pengguna" value="<?= $edit_data['id_pengguna'] ?? '' ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Input username" required value="<?= htmlspecialchars($edit_data['username'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Input password" required value="<?= htmlspecialchars($edit_data['password'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" placeholder="Input nama lengkap" required value="<?= htmlspecialchars($edit_data['nama'] ?? '') ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Kategori (Role)</label>
                        <select name="kategori" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Admin" <?= (isset($edit_data['kategori']) && $edit_data['kategori'] === 'Admin') ? 'selected' : '' ?>>Admin</option>
                            <option value="Penguji" <?= (isset($edit_data['kategori']) && $edit_data['kategori'] === 'Penguji') ? 'selected' : '' ?>>Penguji</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fa-solid fa-floppy-disk me-1"></i> <?= $edit_data ? 'Ubah' : 'Simpan' ?>
                        </button>
                        <?php if ($edit_data): ?>
                            <a href="pengguna.php" class="btn btn-secondary">
                                <i class="fa-solid fa-xmark me-1"></i> Batal
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabel Data Pengguna (Kanan - Sesuai Gambar 4.22) -->
    <div class="col-lg-8">
        <div class="card-custom">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-users text-primary me-2"></i>Daftar Pengguna</h5>
                <!-- Form Pencarian (Sesuai Gambar 4.22) -->
                <form method="GET" class="d-flex gap-2" style="max-width: 320px;">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama / username..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
                    <?php if (!empty($search)): ?>
                        <a href="pengguna.php" class="btn btn-sm btn-outline-secondary" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table-custom mb-0">
                        <thead>
                            <tr>
                                <th width="100">ID</th>
                                <th>USERNAME</th>
                                <th>NAMA LENGKAP</th>
                                <th>KATEGORI</th>
                                <th class="text-center" width="120">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($list_pengguna)): ?>
                                <?php foreach ($list_pengguna as $p): ?>
                                    <tr>
                                        <td><code><?= htmlspecialchars($p['id_pengguna']) ?></code></td>
                                        <td class="fw-semibold text-dark"><?= htmlspecialchars($p['username']) ?></td>
                                        <td><?= htmlspecialchars($p['nama']) ?></td>
                                        <td>
                                            <span class="badge <?= $p['kategori'] === 'Admin' ? 'bg-primary' : 'bg-success' ?>">
                                                <?= htmlspecialchars($p['kategori']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="pengguna.php?edit=<?= urlencode($p['id_pengguna']) ?>" class="btn btn-outline-warning" title="Edit">
                                                    <i class="fa-solid fa-pencil"></i>
                                                </a>
                                                <a href="pengguna.php?delete=<?= urlencode($p['id_pengguna']) ?>" class="btn btn-outline-danger" onclick="return confirm('Hapus pengguna <?= htmlspecialchars($p['nama']) ?>?');" title="Hapus">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Data pengguna tidak ditemukan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
