<?php
/**
 * ====================================================================================
 * FILE: hasil.php
 * MODUL: Hasil Seleksi & Perankingan Keputusan Bootcamp IT
 * DESKRIPSI:
 * Menyajikan hasil akhir kelulusan (Lolos / Tidak Lolos) dan urutan ranking
 * pelamar yang telah diproses melalui evaluasi nilai uji kompetensi. Dilengkapi ringkasan kuota lolos
 * dan tombol pintas cetak laporan. Sesuai Gambar 4.28 dokumen skripsi.
 * ====================================================================================
 */

if (!defined('APP_INIT')) define('APP_INIT', true);
require_once __DIR__ . '/config/database.php';

// Hak akses khusus Admin
check_login('Admin');

$page_title   = 'Hasil Seleksi & Ranking';
$page_heading = 'Hasil Seleksi & Perankingan Peserta';

// Mengambil seluruh data hasil perhitungan yang tersimpan dari tabel `hasil_seleksi`
$hasil_list = query("SELECT h.*, p.nama_peserta, p.jurusan, p.jenis_kelamin 
    FROM hasil_seleksi h
    JOIN peserta p ON h.id_peserta = p.id_peserta
    ORDER BY h.ranking ASC");

// Menghitung ringkasan jumlah peserta yang Lolos vs Tidak Lolos
$total_lolos = 0;
$total_tidak = 0;
foreach ($hasil_list as $item) {
    if ($item['status'] === 'Lolos') $total_lolos++;
    else $total_tidak++;
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<!-- Quick Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card blue">
            <div class="icon-box"><i class="fa-solid fa-list-ol"></i></div>
            <div>
                <div class="stat-title">TOTAL PELAMAR</div>
                <h3 class="stat-value"><?= count($hasil_list) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card green">
            <div class="icon-box"><i class="fa-solid fa-circle-check"></i></div>
            <div>
                <div class="stat-title">STATUS LOLOS</div>
                <h3 class="stat-value"><?= $total_lolos ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card purple">
            <div class="icon-box"><i class="fa-solid fa-circle-xmark"></i></div>
            <div>
                <div class="stat-title">STATUS TIDAK LOLOS</div>
                <h3 class="stat-value"><?= $total_tidak ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- 1. TABEL HASIL SELEKSI (Sesuai Gambar 4.28 Dokumen Bagian Atas) -->
<div class="card-custom mb-4">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h5 class="fw-bold mb-0">
            <i class="fa-solid fa-check-double text-primary me-2"></i>TABEL HASIL SELEKSI
        </h5>
        <div class="d-flex gap-2">
            <a href="perhitungan.php" class="btn btn-sm btn-primary">
                <i class="fa-solid fa-calculator me-1"></i> Tentukan Hasil Seleksi (Hitung Ulang)
            </a>
            <a href="cetak_seleksi.php" target="_blank" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-print me-1"></i> Cetak Laporan Seleksi
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table-custom mb-0">
                <thead>
                    <tr>
                        <th width="100">ID PELAMAR</th>
                        <th>NAMA PELAMAR</th>
                        <th>JURUSAN</th>
                        <th>JENIS KELAMIN</th>
                        <th class="text-center">TOTAL SKOR AKHIR</th>
                        <th class="text-center">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($hasil_list)): ?>
                        <?php foreach ($hasil_list as $h): ?>
                            <tr>
                                <td><code><?= htmlspecialchars($h['id_peserta']) ?></code></td>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($h['nama_peserta']) ?></td>
                                <td><?= htmlspecialchars($h['jurusan']) ?></td>
                                <td>
                                    <span class="badge <?= $h['jenis_kelamin'] === 'Laki-Laki' ? 'bg-primary-subtle text-primary' : 'bg-danger-subtle text-danger' ?> border">
                                        <?= htmlspecialchars($h['jenis_kelamin']) ?>
                                    </span>
                                </td>
                                <td class="text-center font-monospace fw-bold text-primary"><?= number_format($h['total_nilai'], 4) ?></td>
                                <td class="text-center">
                                    <span class="badge-status <?= $h['status'] === 'Lolos' ? 'badge-lolos' : 'badge-tidak-lolos' ?>">
                                        <?= htmlspecialchars($h['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data hasil seleksi. Silakan lakukan evaluasi nilai terlebih dahulu.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 2. TABEL HASIL RANKING (Sesuai Gambar 4.28 Dokumen Bagian Bawah) -->
<div class="card-custom">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h5 class="fw-bold mb-0">
            <i class="fa-solid fa-ranking-star text-warning me-2"></i>TABEL HASIL RANKING
        </h5>
        <div class="d-flex gap-2">
            <a href="cetak_ranking.php" target="_blank" class="btn btn-sm btn-outline-success">
                <i class="fa-solid fa-print me-1"></i> Cetak Laporan Ranking
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
            <table class="table-custom mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="80">RANKING</th>
                        <th width="100">ID PELAMAR</th>
                        <th>NAMA PELAMAR</th>
                        <th>JURUSAN</th>
                        <th>JENIS KELAMIN</th>
                        <th class="text-center">TOTAL SKOR AKHIR</th>
                        <th class="text-center">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($hasil_list)): ?>
                        <?php foreach ($hasil_list as $h): ?>
                            <tr class="<?= $h['ranking'] === 1 ? 'table-warning fw-bold' : '' ?>">
                                <td class="text-center fw-bold">
                                    <?php if ($h['ranking'] == 1): ?>
                                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-crown me-1"></i>#1</span>
                                    <?php elseif ($h['ranking'] == 2): ?>
                                        <span class="badge bg-secondary">#2</span>
                                    <?php elseif ($h['ranking'] == 3): ?>
                                        <span class="badge bg-info text-dark">#3</span>
                                    <?php else: ?>
                                        #<?= $h['ranking'] ?>
                                    <?php endif; ?>
                                </td>
                                <td><code><?= htmlspecialchars($h['id_peserta']) ?></code></td>
                                <td class="fw-semibold text-dark">
                                    <?= htmlspecialchars($h['nama_peserta']) ?>
                                    <?php if ($h['ranking'] === 1): ?>
                                        <span class="badge bg-success ms-1">Ranking 1</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($h['jurusan']) ?></td>
                                <td><?= htmlspecialchars($h['jenis_kelamin']) ?></td>
                                <td class="text-center font-monospace fw-bold text-primary"><?= number_format($h['total_nilai'], 4) ?></td>
                                <td class="text-center">
                                    <span class="badge-status <?= $h['status'] === 'Lolos' ? 'badge-lolos' : 'badge-tidak-lolos' ?>">
                                        <?= htmlspecialchars($h['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data perankingan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
