<?php
/**
 * ====================================================================================
 * FILE: dashboard.php
 * MODUL: Dashboard Utama (Admin & Penguji)
 * DESKRIPSI:
 * Menampilkan ringkasan data statistik sistem (Total Peserta, Laki-Laki, Perempuan)
 * serta tabel 10 besar (Top Ranking) hasil seleksi bootcamp IT secara realtime.
 * Mengacu pada spesifikasi tampilan Gambar 4.20 dan Gambar 4.21 dokumen skripsi.
 * ====================================================================================
 */

if (!defined('APP_INIT')) define('APP_INIT', true);
require_once __DIR__ . '/config/database.php';

// Verifikasi sesi login (Semua role: Admin & Penguji dapat mengakses dashboard)
check_login();

$page_title = 'Dashboard';
$page_heading = 'Dashboard Utama';

// Inisialisasi variabel statistik counter
$total_peserta = 0;
$total_laki = 0;
$total_perempuan = 0;
$hasil_seleksi = [];

if ($db_connected) {
    // 1. Query agregasi untuk menghitung jumlah total calon peserta dan distribusinya berdasarkan jenis kelamin
    $res = $conn->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN jenis_kelamin = 'Laki-Laki' THEN 1 ELSE 0 END) as laki,
        SUM(CASE WHEN jenis_kelamin = 'Perempuan' THEN 1 ELSE 0 END) as perempuan
        FROM peserta");
    if ($row = $res->fetch_assoc()) {
        $total_peserta = (int)($row['total'] ?? 0);
        $total_laki = (int)($row['laki'] ?? 0);
        $total_perempuan = (int)($row['perempuan'] ?? 0);
    }

    // 2. Query mengambil 10 besar peserta peringkat teratas (Top 10 Ranking) dari tabel hasil_seleksi
    $hasil_seleksi = query("SELECT h.*, p.nama_peserta, p.jurusan, p.jenis_kelamin 
        FROM hasil_seleksi h
        JOIN peserta p ON h.id_peserta = p.id_peserta
        ORDER BY h.ranking ASC LIMIT 10");
}

// Memuat template bagian atas (Head, Sidebar, dan Top Navbar)
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<!-- Stat Cards (Sesuai Gambar 4.20 & 4.21 Dokumen) -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card blue">
            <div class="icon-box">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div class="stat-title">TOTAL PELAMAR</div>
                <h2 class="stat-value"><?= $total_peserta ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card green">
            <div class="icon-box">
                <i class="fa-solid fa-mars"></i>
            </div>
            <div>
                <div class="stat-title">PELAMAR LAKI-LAKI</div>
                <h2 class="stat-value"><?= $total_laki ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card purple">
            <div class="icon-box">
                <i class="fa-solid fa-venus"></i>
            </div>
            <div>
                <div class="stat-title">PELAMAR PEREMPUAN</div>
                <h2 class="stat-value"><?= $total_perempuan ?></h2>
            </div>
        </div>
    </div>
</div>

<!-- Informasi Sambutan / Panduan Cepat -->
<div class="card-custom mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h5 class="fw-bold text-dark mb-1">
                    Selamat Datang di Sistem Seleksi Bootcamp IT PT Aigen Global Teknologi!
                </h5>
                <p class="text-muted mb-0 small">
                    Sistem ini mengimplementasikan <strong>Evaluasi Nilai Uji Kompetensi Terbobot</strong> untuk menentukan peringkat kelulusan calon pelamar secara terkomputerisasi, objektif, dan akurat.
                </p>
            </div>
            <div class="d-flex gap-2">
                <?php if ($_SESSION['user_role'] === 'Admin'): ?>
                    <a href="perhitungan.php" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-calculator me-1"></i> Evaluasi Nilai
                    </a>
                <?php else: ?>
                    <a href="penilaian.php" class="btn btn-success btn-sm">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Input Nilai
                    </a>
                <?php endif; ?>
                <a href="laporan.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-print me-1"></i> Menu Laporan
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Hasil Seleksi Singkat (Sesuai Mockup Dashboard) -->
<div class="card-custom">
    <div class="card-header">
        <h5 class="mb-0"><i class="fa-solid fa-trophy text-warning me-2"></i>TABEL HASIL SELEKSI (Top Ranking)</h5>
        <a href="hasil.php" class="btn btn-sm btn-outline-primary">Lihat Semua Data <i class="fa-solid fa-arrow-right ms-1"></i></a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-custom mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="70">RANK</th>
                        <th>ID PELAMAR</th>
                        <th>NAMA PELAMAR</th>
                        <th>JURUSAN</th>
                        <th>JENIS KELAMIN</th>
                        <th class="text-center">TOTAL SKOR AKHIR</th>
                        <th class="text-center">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($hasil_seleksi)): ?>
                        <?php foreach ($hasil_seleksi as $h): ?>
                            <tr>
                                <td class="text-center fw-bold">
                                    <?php if ($h['ranking'] == 1): ?>
                                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-crown me-1"></i>#1</span>
                                    <?php elseif ($h['ranking'] == 2): ?>
                                        <span class="badge bg-secondary">#2</span>
                                    <?php elseif ($h['ranking'] == 3): ?>
                                        <span class="badge bg-info text-dark">#3</span>
                                    <?php else: ?>
                                        <span class="text-muted">#<?= $h['ranking'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><code><?= htmlspecialchars($h['id_peserta']) ?></code></td>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($h['nama_peserta']) ?></td>
                                <td><?= htmlspecialchars($h['jurusan']) ?></td>
                                <td>
                                    <span class="badge <?= $h['jenis_kelamin'] === 'Laki-Laki' ? 'bg-primary-subtle text-primary' : 'bg-danger-subtle text-danger' ?> border">
                                        <?= htmlspecialchars($h['jenis_kelamin']) ?>
                                    </span>
                                </td>
                                <td class="text-center fw-bold text-primary font-monospace"><?= number_format($h['total_nilai'], 4) ?></td>
                                <td class="text-center">
                                    <span class="badge-status <?= $h['status'] === 'Lolos' ? 'badge-lolos' : 'badge-tidak-lolos' ?>">
                                        <?= htmlspecialchars($h['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-circle-exclamation fa-2x mb-2 d-block"></i>
                                Belum ada data hasil perhitungan. Silakan lakukan <a href="perhitungan.php">Evaluasi Nilai</a> terlebih dahulu.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
