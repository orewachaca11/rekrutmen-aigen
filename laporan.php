<?php
/**
 * ====================================================================================
 * FILE: laporan.php
 * MODUL: Menu Navigasi Laporan Sistem (Admin & Penguji)
 * DESKRIPSI:
 * Menyediakan dashboard navigasi ke 5 dokumen cetak resmi sistem:
 *  1. Laporan Data Peserta
 *  2. Laporan Aspek Penilaian
 *  3. Laporan Penilaian Uji Teknis
 *  4. Laporan Hasil Seleksi
 *  5. Laporan Hasil Ranking
 * Sesuai Gambar 4.29 dokumen skripsi.
 * ====================================================================================
 */

if (!defined('APP_INIT')) define('APP_INIT', true);
require_once __DIR__ . '/config/database.php';

// Verifikasi sesi login (Bisa diakses baik oleh Admin maupun Penguji)
check_login();

$page_title   = 'Menu Laporan';
$page_heading = 'Pusat Penerbitan Laporan Resmi Rekrutmen';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<!-- Sesuai Gambar 4.29: Tampilan Menu Laporan Admin dan Penguji -->
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card-custom">
            <div class="card-header bg-light">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-file-invoice text-primary me-2"></i>Pilih Dokumen Laporan Untuk Dicetak
                </h5>
                <small class="text-muted">Format siap cetak (Print) & Ekspor PDF</small>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <!-- 1. Laporan Peserta -->
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-white h-100 d-flex flex-column justify-content-between shadow-sm">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <div class="bg-primary text-white rounded p-3">
                                    <i class="fa-solid fa-users fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">1. Laporan Data Peserta</h6>
                                    <p class="text-muted small mb-0">Daftar lengkap seluruh calon peserta bootcamp beserta biodata, alamat, jurusan, dan kontak.</p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="cetak_peserta.php" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fa-solid fa-print me-1"></i> Buka / Cetak Laporan
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Laporan Kriteria -->
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-white h-100 d-flex flex-column justify-content-between shadow-sm">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <div class="bg-info text-white rounded p-3">
                                    <i class="fa-solid fa-list-check fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">2. Laporan Aspek Penilaian</h6>
                                    <p class="text-muted small mb-0">Daftar 7 aspek uji kompetensi, bobot kontribusi nilai, dan indikator penilaian.</p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="cetak_kriteria.php" target="_blank" class="btn btn-outline-info btn-sm">
                                    <i class="fa-solid fa-print me-1"></i> Buka / Cetak Laporan
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Laporan Penilaian -->
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-white h-100 d-flex flex-column justify-content-between shadow-sm">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <div class="bg-warning text-dark rounded p-3">
                                    <i class="fa-solid fa-clipboard-check fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">3. Laporan Penilaian Uji Teknis</h6>
                                    <p class="text-muted small mb-0">Rekapitulasi lengkap skor uji kompetensi teknis 30 pelamar pada 7 aspek kompetensi.</p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="cetak_penilaian.php" target="_blank" class="btn btn-outline-warning btn-sm">
                                    <i class="fa-solid fa-print me-1"></i> Buka / Cetak Laporan
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Laporan Hasil Seleksi -->
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-white h-100 d-flex flex-column justify-content-between shadow-sm">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <div class="bg-secondary text-white rounded p-3">
                                    <i class="fa-solid fa-file-circle-check fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">4. Laporan Hasil Seleksi Rekrutmen</h6>
                                    <p class="text-muted small mb-0">Daftar rekapitulasi pelamar dengan skor akhir serta status kelulusan (Lolos / Tidak Lolos).</p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="cetak_seleksi.php" target="_blank" class="btn btn-outline-secondary btn-sm">
                                    <i class="fa-solid fa-print me-1"></i> Buka / Cetak Laporan
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Laporan Hasil Ranking -->
                    <div class="col-md-12">
                        <div class="p-3 border rounded-3 bg-white h-100 d-flex flex-column justify-content-between shadow-sm border-success">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <div class="bg-success text-white rounded p-3">
                                    <i class="fa-solid fa-trophy fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-success">5. Laporan Peringkat Kelulusan Pelamar</h6>
                                    <p class="text-muted small mb-0">Urutan peringkat 1 s/d N pelamar berdasarkan akumulasi nilai uji kompetensi terbobot.</p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="cetak_ranking.php" target="_blank" class="btn btn-success btn-sm px-4">
                                    <i class="fa-solid fa-print me-1"></i> Buka / Cetak Laporan Ranking
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
