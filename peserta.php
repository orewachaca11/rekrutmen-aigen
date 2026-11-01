<?php
/**
 * ====================================================================================
 * FILE: peserta.php
 * MODUL: Pengelolaan Data Calon Pelamar Bootcamp IT IT
 * DESKRIPSI:
 * Menangani pendaftaran calon peserta bootcamp, pembaruan profil (Nama, Jurusan,
 * Alamat, Tanggal Lahir, Jenis Kelamin, Kontak WhatsApp/Telepon), serta penghapusan data.
 * Dilengkapi pencarian interaktif dan nomor ID otomatis (Contoh: P001, P002, dst).
 * Sesuai Gambar 4.23 dan Tabel 4.11 dokumen skripsi.
 * ====================================================================================
 */

if (!defined('APP_INIT')) define('APP_INIT', true);
require_once __DIR__ . '/config/database.php';

// Hak akses khusus Admin
check_login('Admin');

$page_title = 'Data Peserta';
$page_heading = 'Kelola Data Peserta Bootcamp';

$edit_data = null;
$search = trim($_GET['search'] ?? '');

// 1. MEMPROSES FORM POST (TAMBAH / UBAH DATA PESERTA)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action        = $_POST['action'] ?? '';
    $id_peserta    = trim($_POST['id_peserta'] ?? '');
    $nama_peserta  = trim($_POST['nama_peserta'] ?? '');
    $jurusan       = trim($_POST['jurusan'] ?? '');
    $alamat        = trim($_POST['alamat'] ?? '');
    $tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');
    $jenis_kelamin = trim($_POST['jenis_kelamin'] ?? '');
    $no_telepon    = trim($_POST['no_telepon'] ?? '');

    // TAMBAH PESERTA BARU
    if ($action === 'create') {
        // Pembuatan ID Peserta Otomatis jika kosong (Format: P001, P002, ...)
        if (empty($id_peserta)) {
            $last = query("SELECT id_peserta FROM peserta ORDER BY id_peserta DESC LIMIT 1");
            $next_num = 1;
            if (!empty($last)) {
                $num = (int) preg_replace('/[^0-9]/', '', $last[0]['id_peserta']);
                $next_num = $num + 1;
            }
            $id_peserta = 'P' . str_pad($next_num, 3, '0', STR_PAD_LEFT);
        }

        // Validasi keunikan ID Peserta
        $check = query("SELECT * FROM peserta WHERE id_peserta = '$id_peserta'");
        if (!empty($check)) {
            set_flash('danger', 'ID Peserta sudah terdaftar! Gunakan ID lain.');
        } else {
            $stmt = $conn->prepare("INSERT INTO peserta (id_peserta, nama_peserta, jurusan, alamat, tanggal_lahir, jenis_kelamin, no_telepon) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sssssss", $id_peserta, $nama_peserta, $jurusan, $alamat, $tanggal_lahir, $jenis_kelamin, $no_telepon);
                if ($stmt->execute()) {
                    set_flash('success', "Peserta <strong>$nama_peserta</strong> ($id_peserta) berhasil didaftarkan!");
                    header("Location: peserta.php");
                    exit;
                } else {
                    set_flash('danger', 'Gagal mendaftarkan peserta: ' . $stmt->error);
                }
            }
        }
    } 
    // UPDATE DATA PESERTA
    elseif ($action === 'update') {
        $stmt = $conn->prepare("UPDATE peserta SET nama_peserta = ?, jurusan = ?, alamat = ?, tanggal_lahir = ?, jenis_kelamin = ?, no_telepon = ? WHERE id_peserta = ?");
        if ($stmt) {
            $stmt->bind_param("sssssss", $nama_peserta, $jurusan, $alamat, $tanggal_lahir, $jenis_kelamin, $no_telepon, $id_peserta);
            if ($stmt->execute()) {
                set_flash('success', "Data peserta <strong>$nama_peserta</strong> berhasil diperbarui!");
                header("Location: peserta.php");
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
    $stmt = $conn->prepare("DELETE FROM peserta WHERE id_peserta = ?");
    $stmt->bind_param("s", $del_id);
    if ($stmt->execute()) {
        set_flash('success', 'Data peserta berhasil dihapus!');
    } else {
        set_flash('danger', 'Gagal menghapus data peserta: ' . $stmt->error);
    }
    header("Location: peserta.php");
    exit;
}

// Handle GET Edit
if (isset($_GET['edit'])) {
    $edit_id = trim($_GET['edit']);
    $res = query("SELECT * FROM peserta WHERE id_peserta = '$edit_id'");
    if (!empty($res)) {
        $edit_data = $res[0];
    }
}

// Auto generate ID untuk preview form tambah
if (!$edit_data) {
    $last = query("SELECT id_peserta FROM peserta ORDER BY id_peserta DESC LIMIT 1");
    $next_num = 1;
    if (!empty($last)) {
        $num = (int) preg_replace('/[^0-9]/', '', $last[0]['id_peserta']);
        $next_num = $num + 1;
    }
    $auto_id = 'P' . str_pad($next_num, 3, '0', STR_PAD_LEFT);
}

// Query List Peserta with Search
$where = "";
if (!empty($search)) {
    $s = $conn->real_escape_string($search);
    $where = "WHERE id_peserta LIKE '%$s%' OR nama_peserta LIKE '%$s%' OR jurusan LIKE '%$s%' OR alamat LIKE '%$s%'";
}
$list_peserta = query("SELECT * FROM peserta $where ORDER BY id_peserta ASC");

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="row">
    <!-- Form Input / Edit Pelamar (Kiri - Sesuai Gambar 4.23) -->
    <div class="col-lg-4 mb-4">
        <div class="card-custom">
            <div class="card-header bg-light">
                <h5 class="fw-bold">
                    <i class="fa-solid fa-<?= $edit_data ? 'pen-to-square text-warning' : 'user-plus text-primary' ?> me-2"></i>
                    <?= $edit_data ? 'Form Edit Pelamar' : 'Form Tambah Pelamar' ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="peserta.php">
                    <input type="hidden" name="action" value="<?= $edit_data ? 'update' : 'create' ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">ID Peserta</label>
                        <input type="text" name="id_peserta" class="form-control font-monospace bg-light" value="<?= htmlspecialchars($edit_data['id_peserta'] ?? $auto_id) ?>" required <?= $edit_data ? 'readonly' : '' ?>>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Peserta</label>
                        <input type="text" name="nama_peserta" class="form-control" placeholder="Nama lengkap peserta" required value="<?= htmlspecialchars($edit_data['nama_peserta'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Jurusan / Keahlian</label>
                        <input type="text" name="jurusan" class="form-control" placeholder="Contoh: Teknik Informatika" required value="<?= htmlspecialchars($edit_data['jurusan'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat tempat tinggal" required><?= htmlspecialchars($edit_data['alamat'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" required value="<?= htmlspecialchars($edit_data['tanggal_lahir'] ?? '2000-01-01') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold d-block">Jenis Kelamin</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="jenis_kelamin" id="jkL" value="Laki-Laki" <?= (!isset($edit_data) || $edit_data['jenis_kelamin'] === 'Laki-Laki') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="jkL">Laki-Laki</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="jenis_kelamin" id="jkP" value="Perempuan" <?= (isset($edit_data) && $edit_data['jenis_kelamin'] === 'Perempuan') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="jkP">Perempuan</label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold">No. Telepon / WhatsApp</label>
                        <input type="text" name="no_telepon" class="form-control" placeholder="08xxxxxxxxxx" required value="<?= htmlspecialchars($edit_data['no_telepon'] ?? '') ?>">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fa-solid fa-floppy-disk me-1"></i> <?= $edit_data ? 'Ubah' : 'Simpan' ?>
                        </button>
                        <?php if ($edit_data): ?>
                            <a href="peserta.php" class="btn btn-secondary">
                                <i class="fa-solid fa-xmark me-1"></i> Batal
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabel Data Peserta (Kanan - Sesuai Gambar 4.23) -->
    <div class="col-lg-8">
        <div class="card-custom">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h5 class="fw-bold mb-0">
                    <i class="fa-solid fa-user-graduate text-primary me-2"></i>
                    Daftar Calon Peserta Bootcamp
                    <span class="badge bg-secondary ms-2"><?= count($list_peserta) ?></span>
                </h5>
                <form method="GET" class="d-flex gap-2" style="max-width: 320px;">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama / ID..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
                    <?php if (!empty($search)): ?>
                        <a href="peserta.php" class="btn btn-sm btn-outline-secondary" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table-custom mb-0">
                        <thead>
                            <tr>
                                <th width="80">ID</th>
                                <th>NAMA PESERTA</th>
                                <th>JURUSAN</th>
                                <th>JK</th>
                                <th>NO TELEPON</th>
                                <th class="text-center" width="100">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($list_peserta)): ?>
                                <?php foreach ($list_peserta as $p): ?>
                                    <tr>
                                        <td><code><?= htmlspecialchars($p['id_peserta']) ?></code></td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars($p['nama_peserta']) ?></div>
                                            <small class="text-muted d-block text-truncate" style="max-width: 220px;" title="<?= htmlspecialchars($p['alamat']) ?>">
                                                <?= htmlspecialchars($p['alamat']) ?>
                                            </small>
                                        </td>
                                        <td><?= htmlspecialchars($p['jurusan']) ?></td>
                                        <td>
                                            <span class="badge <?= $p['jenis_kelamin'] === 'Laki-Laki' ? 'bg-primary-subtle text-primary' : 'bg-danger-subtle text-danger' ?> border">
                                                <?= $p['jenis_kelamin'] === 'Laki-Laki' ? 'L' : 'P' ?>
                                            </span>
                                        </td>
                                        <td><small class="text-secondary"><?= htmlspecialchars($p['no_telepon']) ?></small></td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="peserta.php?edit=<?= urlencode($p['id_peserta']) ?>" class="btn btn-outline-warning" title="Edit">
                                                    <i class="fa-solid fa-pencil"></i>
                                                </a>
                                                <a href="peserta.php?delete=<?= urlencode($p['id_peserta']) ?>" class="btn btn-outline-danger" onclick="return confirm('Hapus peserta <?= htmlspecialchars($p['nama_peserta']) ?>? Semua data penilaian peserta ini juga akan terhapus.');" title="Hapus">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Data peserta tidak ditemukan.</td>
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
