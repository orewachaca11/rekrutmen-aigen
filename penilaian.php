<?php
/**
 * ====================================================================================
 * FILE: penilaian.php
 * MODUL: Penginputan & Evaluasi Nilai Peserta (Oleh Penguji)
 * DESKRIPSI:
 * Menangani penginputan skor evaluasi peserta bootcamp untuk 7 kriteria (C1 s/d C7).
 * Penilai (Penguji) memilih skala 1 s/d 4 yang otomatis terpetakan ke subkriteria dokumen.
 * Data yang diinputkan di sini menjadi Matriks Nilai Uji Kompetensi untuk evaluasi kelulusan.
 * Sesuai Gambar 4.25 (Admin View), Gambar 4.26 (Penguji View), & Tabel 4.4 Dokumen.
 * ====================================================================================
 */

if (!defined('APP_INIT')) define('APP_INIT', true);
require_once __DIR__ . '/config/database.php';

// Verifikasi sesi login (Admin dan Penguji memiliki akses ke halaman ini)
check_login();

$user_role    = $_SESSION['user_role'] ?? 'Admin';
$page_title   = 'Data Penilaian';
$page_heading = 'Penilaian Uji Kompetensi Pelamar';

$edit_data = null;
$search = trim($_GET['search'] ?? '');

// 1. MEMPROSES SIMPAN / UPDATE PENILAIAN DARI FORM INPUT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action     = $_POST['action'] ?? '';
    $id_peserta = trim($_POST['id_peserta'] ?? '');
    
    // Nilai skala 1..4 untuk masing-masing kriteria
    $c1 = (int)($_POST['c1'] ?? 1); // 1. Skor Tes Frontend
    $c2 = (int)($_POST['c2'] ?? 1); // 2. Skor Tes Backend
    $c3 = (int)($_POST['c3'] ?? 1); // 3. Usia & Kesiapan Kerja
    $c4 = (int)($_POST['c4'] ?? 1); // 4. Pendidikan Terakhir
    $c5 = (int)($_POST['c5'] ?? 1); // 5. Pengalaman Proyek / Portofolio
    $c6 = (int)($_POST['c6'] ?? 1); // C6: Waktu Tempuh ke Perusahaan
    $c7 = (int)($_POST['c7'] ?? 1); // 7. Tes Tertulis & Problem Solving

    if ($action === 'save' && !empty($id_peserta)) {
        // Cek apakah peserta tersebut sudah memiliki rekor penilaian di database
        $check = query("SELECT * FROM penilaian WHERE id_peserta = '$id_peserta'");
        if (!empty($check)) {
            // JIKA SUDAH ADA: Lakukan UPDATE data penilaian
            $stmt = $conn->prepare("UPDATE penilaian SET c1 = ?, c2 = ?, c3 = ?, c4 = ?, c5 = ?, c6 = ?, c7 = ? WHERE id_peserta = ?");
            if ($stmt) {
                $stmt->bind_param("iiiiiiis", $c1, $c2, $c3, $c4, $c5, $c6, $c7, $id_peserta);
                if ($stmt->execute()) {
                    set_flash('success', "Penilaian untuk peserta <strong>$id_peserta</strong> berhasil diperbarui!");
                } else {
                    set_flash('danger', 'Gagal memperbarui nilai: ' . $stmt->error);
                }
            } else {
                set_flash('danger', 'Query Error: ' . $conn->error);
            }
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO penilaian (id_peserta, c1, c2, c3, c4, c5, c6, c7) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("siiiiiii", $id_peserta, $c1, $c2, $c3, $c4, $c5, $c6, $c7);
                if ($stmt->execute()) {
                    set_flash('success', "Penilaian untuk peserta <strong>$id_peserta</strong> berhasil disimpan!");
                } else {
                    set_flash('danger', 'Gagal menyimpan nilai: ' . $stmt->error);
                }
            } else {
                set_flash('danger', 'Query Error: ' . $conn->error);
            }
        }
        header("Location: penilaian.php");
        exit;
    }
}

// Delete Penilaian
if (isset($_GET['delete'])) {
    $del_id = trim($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM penilaian WHERE id_peserta = ?");
    $stmt->bind_param("s", $del_id);
    if ($stmt->execute()) {
        set_flash('success', 'Data penilaian berhasil dihapus!');
    } else {
        set_flash('danger', 'Gagal menghapus penilaian: ' . $stmt->error);
    }
    header("Location: penilaian.php");
    exit;
}

// Edit Penilaian
if (isset($_GET['edit'])) {
    $edit_id = trim($_GET['edit']);
    $res = query("SELECT pn.*, ps.nama_peserta FROM penilaian pn JOIN peserta ps ON pn.id_peserta = ps.id_peserta WHERE pn.id_peserta = '$edit_id'");
    if (!empty($res)) {
        $edit_data = $res[0];
    }
}

// Ambil list peserta yang belum dinilai (untuk dropdown form tambah)
$peserta_list = query("SELECT id_peserta, nama_peserta FROM peserta ORDER BY nama_peserta ASC");

// Ambil list penilaian join peserta
$where = "";
if (!empty($search)) {
    $s = $conn->real_escape_string($search);
    $where = "WHERE ps.id_peserta LIKE '%$s%' OR ps.nama_peserta LIKE '%$s%'";
}

$list_penilaian = query("SELECT pn.*, ps.nama_peserta, ps.jurusan, ps.jenis_kelamin 
    FROM penilaian pn 
    JOIN peserta ps ON pn.id_peserta = ps.id_peserta 
    $where 
    ORDER BY pn.id_peserta ASC");

// Ambil subkriteria options untuk dropdown
$subkriteria_all = query("SELECT * FROM subkriteria ORDER BY kode_kriteria, nilai");
$sub_map = [];
foreach ($subkriteria_all as $sub) {
    $sub_map[$sub['kode_kriteria']][$sub['nilai']] = $sub['pilihan_kriteria'];
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<!-- Form Input / Edit Penilaian (Khusus Penguji atau jika Edit) -->
<div class="card-custom mb-4">
    <div class="card-header bg-light">
        <h5 class="fw-bold mb-0">
            <i class="fa-solid fa-clipboard-question text-primary me-2"></i>
            <?= $edit_data ? 'Form Edit Penilaian Peserta (' . htmlspecialchars($edit_data['id_peserta']) . ' - ' . htmlspecialchars($edit_data['nama_peserta']) . ')' : 'Form Input Penilaian Uji Teknis Pelamar' ?>
        </h5>
        <small class="text-muted">Skala Nilai 1 (Terendah) s/d 4 (Tertinggi)</small>
    </div>
    <div class="card-body">
        <form method="POST" action="penilaian.php">
            <input type="hidden" name="action" value="save">

            <!-- Pilih Peserta -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Pilih Pelamar Bootcamp</label>
                    <select name="id_peserta" class="form-select" required <?= $edit_data ? 'disabled' : '' ?>>
                        <option value="">-- Pilih Peserta --</option>
                        <?php foreach ($peserta_list as $p): ?>
                            <option value="<?= htmlspecialchars($p['id_peserta']) ?>" <?= (isset($edit_data['id_peserta']) && $edit_data['id_peserta'] === $p['id_peserta']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['id_peserta']) ?> - <?= htmlspecialchars($p['nama_peserta']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="id_peserta" value="<?= htmlspecialchars($edit_data['id_peserta']) ?>">
                    <?php endif; ?>
                </div>
            </div>

            <!-- 7 Aspek Kompetensi Input Options -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-truncate d-block" title="Skor Tes Frontend">
                        1. Skor Tes Frontend
                    </label>
                    <select name="c1" class="form-select form-select-sm" required>
                        <?php for ($i=1; $i<=4; $i++): ?>
                            <option value="<?= $i ?>" <?= (isset($edit_data['c1']) && $edit_data['c1'] == $i) ? 'selected' : '' ?>>
                                Nilai <?= $i ?> (<?= $sub_map['C1'][$i] ?? '' ?>)
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-truncate d-block" title="Skor Tes Backend">
                        2. Skor Tes Backend
                    </label>
                    <select name="c2" class="form-select form-select-sm" required>
                        <?php for ($i=1; $i<=4; $i++): ?>
                            <option value="<?= $i ?>" <?= (isset($edit_data['c2']) && $edit_data['c2'] == $i) ? 'selected' : '' ?>>
                                Nilai <?= $i ?> (<?= $sub_map['C2'][$i] ?? '' ?>)
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-truncate d-block" title="Usia & Kesiapan Kerja">
                        3. Usia & Kesiapan Kerja
                    </label>
                    <select name="c3" class="form-select form-select-sm" required>
                        <?php for ($i=1; $i<=4; $i++): ?>
                            <option value="<?= $i ?>" <?= (isset($edit_data['c3']) && $edit_data['c3'] == $i) ? 'selected' : '' ?>>
                                Nilai <?= $i ?> (<?= $sub_map['C3'][$i] ?? '' ?>)
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-truncate d-block" title="Pendidikan Terakhir">
                        4. Pendidikan Terakhir
                    </label>
                    <select name="c4" class="form-select form-select-sm" required>
                        <?php for ($i=1; $i<=4; $i++): ?>
                            <option value="<?= $i ?>" <?= (isset($edit_data['c4']) && $edit_data['c4'] == $i) ? 'selected' : '' ?>>
                                Nilai <?= $i ?> (<?= $sub_map['C4'][$i] ?? '' ?>)
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-truncate d-block" title="Pengalaman Proyek / Portofolio">
                        5. Pengalaman Proyek / Portofolio
                    </label>
                    <select name="c5" class="form-select form-select-sm" required>
                        <?php for ($i=1; $i<=4; $i++): ?>
                            <option value="<?= $i ?>" <?= (isset($edit_data['c5']) && $edit_data['c5'] == $i) ? 'selected' : '' ?>>
                                Nilai <?= $i ?> (<?= $sub_map['C5'][$i] ?? '' ?>)
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-truncate d-block" title="Efisiensi Waktu Tempuh">
                        6. Efisiensi Waktu Tempuh
                    </label>
                    <select name="c6" class="form-select form-select-sm" required>
                        <?php for ($i=1; $i<=4; $i++): ?>
                            <option value="<?= $i ?>" <?= (isset($edit_data['c6']) && $edit_data['c6'] == $i) ? 'selected' : '' ?>>
                                Nilai <?= $i ?> (<?= $sub_map['C6'][$i] ?? '' ?>)
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-truncate d-block" title="Tes Tertulis IT & Problem Solving">
                        7. Tes Tertulis & Problem Solving
                    </label>
                    <select name="c7" class="form-select form-select-sm" required>
                        <?php for ($i=1; $i<=4; $i++): ?>
                            <option value="<?= $i ?>" <?= (isset($edit_data['c7']) && $edit_data['c7'] == $i) ? 'selected' : '' ?>>
                                Nilai <?= $i ?> (<?= $sub_map['C7'][$i] ?? '' ?>)
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> <?= $edit_data ? 'Update Nilai' : 'Simpan Penilaian' ?>
                </button>
                <?php if ($edit_data): ?>
                    <a href="penilaian.php" class="btn btn-secondary">
                        <i class="fa-solid fa-xmark me-1"></i> Batal
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Rekapitulasi Skor Uji Kompetensi Pelamar -->
<div class="card-custom">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h5 class="fw-bold mb-0">
            <i class="fa-solid fa-table-cells text-primary me-2"></i>
            Tabel Rekapitulasi Penilaian Pelamar
            <span class="badge bg-secondary ms-2"><?= count($list_penilaian) ?> Data</span>
        </h5>
        <form method="GET" class="d-flex gap-2" style="max-width: 300px;">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama peserta..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
            <?php if (!empty($search)): ?>
                <a href="penilaian.php" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
            <?php endif; ?>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-custom mb-0 text-center">
                <thead>
                    <tr>
                        <th class="text-start" width="70">ID</th>
                        <th class="text-start">NAMA PELAMAR</th>
                        <th>Frontend</th>
                        <th>Backend</th>
                        <th>Usia</th>
                        <th>Pendidikan</th>
                        <th>Pengalaman</th>
                        <th>Waktu Tempuh</th>
                        <th>Tes Tertulis</th>
                        <th width="100">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($list_penilaian)): ?>
                        <?php foreach ($list_penilaian as $pn): ?>
                            <tr>
                                <td class="text-start"><code><?= htmlspecialchars($pn['id_peserta']) ?></code></td>
                                <td class="text-start fw-semibold text-dark"><?= htmlspecialchars($pn['nama_peserta']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= $pn['c1'] ?></span></td>
                                <td><span class="badge bg-light text-dark border"><?= $pn['c2'] ?></span></td>
                                <td><span class="badge bg-light text-dark border"><?= $pn['c3'] ?></span></td>
                                <td><span class="badge bg-light text-dark border"><?= $pn['c4'] ?></span></td>
                                <td><span class="badge bg-light text-dark border"><?= $pn['c5'] ?></span></td>
                                <td><span class="badge bg-light text-dark border"><?= $pn['c6'] ?></span></td>
                                <td><span class="badge bg-light text-dark border"><?= $pn['c7'] ?></span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="penilaian.php?edit=<?= urlencode($pn['id_peserta']) ?>" class="btn btn-outline-warning" title="Edit Nilai">
                                            <i class="fa-solid fa-pencil"></i>
                                        </a>
                                        <a href="penilaian.php?delete=<?= urlencode($pn['id_peserta']) ?>" class="btn btn-outline-danger" onclick="return confirm('Hapus penilaian <?= htmlspecialchars($pn['nama_peserta']) ?>?');" title="Hapus">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">Belum ada data penilaian peserta.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
