<?php
/**
 * ====================================================================================
 * FILE: aspek.php
 * MODUL: Pengelolaan Aspek Kompetensi & Bobot Kontribusi Nilai
 * DESKRIPSI:
 * Mengelola 7 parameter aspek evaluasi (C1 s/d C7), bobot penilaian,
 * penentuan sifat aspek (Benefit vs Cost), serta menampilkan tabel subaspek
 * (standar skala penilaian 1 s/d 4) sesuai Tabel 4.1 & Tabel 4.2 dokumen skripsi.
 * ====================================================================================
 */

if (!defined('APP_INIT')) define('APP_INIT', true);
require_once __DIR__ . '/config/database.php';

// Proteksi hak akses khusus Admin
check_login('Admin');

$page_title = 'Data Aspek Kompetensi';
$page_heading = 'Kelola Aspek Kompetensi & Pembobotan';

$edit_data = null;

// 1. MEMPROSES PEMBARUAN BOBOT ATAU TIPE KRITERIA (UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action        = $_POST['action'] ?? '';
    $kode_kriteria = trim($_POST['kode_kriteria'] ?? '');
    $nama_kriteria = trim($_POST['nama_kriteria'] ?? '');
    $bobot         = (float)($_POST['bobot'] ?? 0);
    $tipe          = trim($_POST['tipe'] ?? 'Kompetensi Teknis'); // 'Kompetensi Teknis' atau 'Efisiensi'

    if ($action === 'update' && !empty($kode_kriteria)) {
        $stmt = $conn->prepare("UPDATE kriteria SET nama_kriteria = ?, bobot = ?, tipe = ? WHERE kode_kriteria = ?");
        if ($stmt) {
            $stmt->bind_param("sdss", $nama_kriteria, $bobot, $tipe, $kode_kriteria);
            if ($stmt->execute()) {
                set_flash('success', "Aspek Kompetensi <strong>$kode_kriteria</strong> berhasil diperbarui!");
            } else {
                set_flash('danger', 'Gagal memperbarui aspek: ' . $stmt->error);
            }
        }
        header("Location: aspek.php");
        exit;
    }
}

// 2. MENGAMBIL DATA UNTUK DITAMPILKAN KE FORM EDIT JIKA TOMBOL EDIT DIKLIK
if (isset($_GET['edit'])) {
    $edit_kode = trim($_GET['edit']);
    $res = query("SELECT * FROM kriteria WHERE kode_kriteria = '$edit_kode'");
    if (!empty($res)) {
        $edit_data = $res[0];
    }
}

// 3. MENGAMBIL SELURUH DATA KRITERIA DAN SUBKRITERIA DARI DATABASE
$list_aspek   = query("SELECT * FROM kriteria ORDER BY kode_kriteria ASC");
$subaspek_raw = query("SELECT * FROM subkriteria ORDER BY kode_kriteria ASC, nilai ASC");

// Mengelompokkan data subaspek berdasarkan kode aspek (C1..C7) untuk penyajian tabel bertingkat
$subaspek_map = [];
foreach ($subaspek_raw as $sub) {
    $subaspek_map[$sub['kode_kriteria']][] = $sub;
}

// Hitung total bobot preferensi (Harus bernilai 1.00 atau 100%)
$total_bobot = array_sum(array_column($list_aspek, 'bobot'));
$total_bobot = array_sum(array_column($list_aspek, 'bobot'));

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<!-- Alert Total Bobot -->
<?php if (abs($total_bobot - 1.0) > 0.001): ?>
    <div class="alert alert-warning d-flex align-items-center mb-4">
        <i class="fa-solid fa-triangle-exclamation fa-2x me-3"></i>
        <div>
            <strong>Perhatian:</strong> Total bobot saat ini adalah <strong><?= $total_bobot ?></strong>. 
            Dalam sistem seleksi ini, total bobot idealnya bernilai <strong>1.00 (100%)</strong> agar perangkingan optimal.
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Form Edit Bobot (Jika Mode Edit) -->
    <?php if ($edit_data): ?>
        <div class="col-lg-4 mb-4">
            <div class="card-custom">
                <div class="card-header bg-warning-subtle">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Aspek <?= htmlspecialchars($edit_data['kode_kriteria']) ?></h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="aspek.php">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="kode_kriteria" value="<?= htmlspecialchars($edit_data['kode_kriteria']) ?>">

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Kode Aspek Kompetensi</label>
                            <input type="text" class="form-control font-monospace bg-light" value="<?= htmlspecialchars($edit_data['kode_kriteria']) ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nama Aspek Kompetensi</label>
                            <input type="text" name="nama_kriteria" class="form-control" required value="<?= htmlspecialchars($edit_data['nama_kriteria']) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Bobot (0 - 1)</label>
                            <input type="number" step="0.01" min="0.01" max="1" name="bobot" class="form-control" required value="<?= htmlspecialchars($edit_data['bobot']) ?>">
                            <small class="text-muted">Misal: 0.15 = 15%</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold">Tipe Aspek Kompetensi</label>
                            <select name="tipe" class="form-select" required>
                                <option value="Benefit" <?= $edit_data['tipe'] === 'Kompetensi Teknis' ? 'selected' : '' ?>>Benefit (Semakin besar semakin baik)</option>
                                <option value="Cost" <?= $edit_data['tipe'] === 'Efisiensi' ? 'selected' : '' ?>>Cost (Semakin kecil semakin baik)</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">Simpan Perubahan</button>
                            <a href="aspek.php" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Tabel Aspek Kompetensi Utama (Tabel 4.1 Dokumen) -->
    <div class="<?= $edit_data ? 'col-lg-8' : 'col-lg-12' ?> mb-4">
        <div class="card-custom">
            <div class="card-header">
                <h5 class="fw-bold mb-0">
                    <i class="fa-solid fa-list-check text-primary me-2"></i>
                    Tabel 7 Aspek Uji Kompetensi & Pembobotan
                </h5>
                <span class="badge bg-primary">Total Bobot: <?= number_format($total_bobot, 2) ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table-custom mb-0">
                        <thead>
                            <tr>
                                <th width="100">KODE</th>
                                <th>NAMA ASPEK KOMPETENSI</th>
                                <th class="text-center" width="120">TIPE</th>
                                <th class="text-center" width="120">BOBOT</th>
                                <th class="text-center" width="120">PERSENTASE</th>
                                <th class="text-center" width="100">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($list_aspek as $k): ?>
                                <tr>
                                    <td><span class="badge bg-dark font-monospace"><?= htmlspecialchars($k['kode_kriteria']) ?></span></td>
                                    <td class="fw-semibold text-dark"><?= htmlspecialchars($k['nama_kriteria']) ?></td>
                                    <td class="text-center">
                                        <span class="badge <?= $k['tipe'] === 'Kompetensi Teknis' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                            <?= htmlspecialchars($k['tipe']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center fw-bold text-primary font-monospace"><?= number_format($k['bobot'], 2) ?></td>
                                    <td class="text-center text-muted"><?= ($k['bobot'] * 100) ?>%</td>
                                    <td class="text-center">
                                        <a href="aspek.php?edit=<?= urlencode($k['kode_kriteria']) ?>" class="btn btn-outline-warning btn-sm" title="Edit Bobot">
                                            <i class="fa-solid fa-pencil"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="3" class="text-end">TOTAL BOBOT:</td>
                                <td class="text-center text-primary font-monospace"><?= number_format($total_bobot, 2) ?></td>
                                <td class="text-center text-muted"><?= ($total_bobot * 100) ?>%</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Nilai Pilihan Aspek Kompetensi (Subaspek - Tabel 4.2 Dokumen) -->
<div class="card-custom">
    <div class="card-header bg-light">
        <h5 class="fw-bold mb-0">
            <i class="fa-solid fa-tags text-primary me-2"></i>
            Tabel Indikator Penilaian Uji Kompetensi (Skala Skor 1 - 4)
        </h5>
        <small class="text-muted">Standar Acuan Penilaian Peserta</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-custom mb-0">
                <thead>
                    <tr>
                        <th width="100">KODE</th>
                        <th width="300">NAMA ASPEK KOMPETENSI</th>
                        <th>RENTANG / INDIKATOR PENILAIAN</th>
                        <th class="text-center" width="150">NILAI SKALA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($list_aspek as $k): 
                        $subs = $subaspek_map[$k['kode_kriteria']] ?? [];
                        $rowspan = count($subs);
                    ?>
                        <?php if ($rowspan > 0): ?>
                            <?php foreach ($subs as $index => $sub): ?>
                                <tr>
                                    <?php if ($index === 0): ?>
                                        <td rowspan="<?= $rowspan ?>" class="align-top">
                                            <span class="badge bg-dark font-monospace"><?= htmlspecialchars($k['kode_kriteria']) ?></span>
                                        </td>
                                        <td rowspan="<?= $rowspan ?>" class="align-top fw-semibold text-dark">
                                            <?= htmlspecialchars($k['nama_kriteria']) ?>
                                            <div class="small text-muted">(<?= $k['tipe'] ?>)</div>
                                        </td>
                                    <?php endif; ?>
                                    <td><?= htmlspecialchars($sub['pilihan_aspek']) ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary border px-3 py-1 font-monospace fw-bold">
                                            <?= $sub['nilai'] ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td><span class="badge bg-dark font-monospace"><?= htmlspecialchars($k['kode_kriteria']) ?></span></td>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($k['nama_kriteria']) ?></td>
                                <td colspan="2" class="text-muted">Belum ada rentang pilihan aspek.</td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
