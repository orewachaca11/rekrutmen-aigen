<?php
/**
 * ====================================================================================
 * FILE: perhitungan.php
 * MODUL: Engine Evaluasi Skor Terbobot & Passing Grade
 * DESKRIPSI:
 * Mengimplementasikan seluruh tahapan rumus evaluasi nilai terbobot sesuai standar uji kompetensi:
 *  1. Rekapitulasi Skor Mentah Uji Kompetensi : Data skor tiap kandidat pada kriteria C1 s/d C7.
 *  2. Nilai Max & Min       : Mencari nilai tertinggi (benefit) & terendah (cost).
 *  3. Matriks Ternormalisasi:
 *       - Benefit (C1,C2,C3,C4,C5,C7) -> r_ij = x_ij / max(x_j)
 *       - Cost (C6 - Waktu Tempuh)    -> r_ij = min(x_j) / x_ij
 *  4. Akumulasi Skor Akhir Terbobot (S)  : V_i = SUM( w_j * r_ij )
 *  5. Perankingan           : Mengurutkan nilai V dari yang terbesar hingga terkecil.
 *  6. Penyimpanan Hasil     : Menyimpan hasil ke tabel database `hasil_seleksi`.
 * ====================================================================================
 */

if (!defined('APP_INIT')) define('APP_INIT', true);
require_once __DIR__ . '/config/database.php';

// Hak akses khusus Admin untuk kalkulasi sistem
check_login('Admin');

$page_title   = 'Evaluasi Nilai Uji Kompetensi';
$page_heading = 'Evaluasi & Agregasi Skor Terbobot Pelamar Bootcamp IT';

// -----------------------------------------------------------------------------
// TAHAP 1: MENGAMBIL BOBOT ASPEK DAN PERSENTASE KONTRIBUSI
// -----------------------------------------------------------------------------
$kriteria_db = query("SELECT * FROM kriteria ORDER BY kode_kriteria ASC");
$weights = [];
$types   = [];
foreach ($kriteria_db as $k) {
    $code           = strtolower($k['kode_kriteria']);
    $weights[$code] = (float)$k['bobot']; // Contoh: C1 => 0.10, C4 => 0.20
    $types[$code]   = $k['tipe'];         // 'Benefit' atau 'Cost'
}

// -----------------------------------------------------------------------------
// TAHAP 2: MENGAMBIL REKAPITULASI NILAI UJI KOMPETENSI 30 PELAMAR
// -----------------------------------------------------------------------------
$matrix_x = query("SELECT pn.*, ps.nama_peserta, ps.jurusan, ps.jenis_kelamin 
    FROM penilaian pn 
    JOIN peserta ps ON pn.id_peserta = ps.id_peserta 
    ORDER BY pn.id_peserta ASC");

// -----------------------------------------------------------------------------
// TAHAP 3: MENCARI NILAI MAKSIMUM (MAX) DAN MINIMUM (MIN) SETIAP KOLOM KRITERIA
// -----------------------------------------------------------------------------
$max_min = [];
foreach (['c1', 'c2', 'c3', 'c4', 'c5', 'c6', 'c7'] as $c) {
    $vals = array_column($matrix_x, $c);
    $max_min[$c] = [
        'max' => !empty($vals) ? max($vals) : 1, // Nilai tertinggi kolom
        'min' => !empty($vals) ? min($vals) : 1  // Nilai terendah kolom
    ];
}

// -----------------------------------------------------------------------------
// TAHAP 4 & 5: KONVERSI SKOR TERSTANDARISASI DAN AKUMULASI SKOR AKHIR
// -----------------------------------------------------------------------------
$matrix_r = [];
$ranking_list = [];

foreach ($matrix_x as $row) {
    $id = $row['id_peserta'];
    $r_row = [
        'id_peserta'    => $id,
        'nama_peserta'  => $row['nama_peserta'],
        'jurusan'       => $row['jurusan'],
        'jenis_kelamin' => $row['jenis_kelamin']
    ];

    $v_total = 0; // Inisialisasi total akumulasi bobot * normalisasi

    // Melakukan loop pada setiap kriteria C1 s/d C7
    foreach (['c1', 'c2', 'c3', 'c4', 'c5', 'c6', 'c7'] as $c) {
        $val     = (float)$row[$c];
        $is_cost = ($types[$c] ?? 'Benefit') === 'Cost';

        if ($is_cost) {
            // RUMUS COST: r_ij = min(x_j) / x_ij (Semakin kecil nilai aktual, semakin besar hasil normalisasinya)
            $norm = $val > 0 ? ($max_min[$c]['min'] / $val) : 0;
        } else {
            // RUMUS BENEFIT: r_ij = x_ij / max(x_j) (Semakin besar nilai aktual, semakin besar hasil normalisasinya)
            $max_val = $max_min[$c]['max'];
            $norm = $max_val > 0 ? ($val / $max_val) : 0;
        }

        $r_row[$c] = $norm; // Simpan nilai ternormalisasi ke baris matriks R

        // Akumulasikan ke nilai preferensi V: V_i = SUM( w_j * r_ij )
        $v_total += ($norm * ($weights[$c] ?? 0));
    }

    $matrix_r[] = $r_row;

    // Simpan calon ranking sementara
    $ranking_list[] = [
        'id_peserta'    => $id,
        'nama_peserta'  => $row['nama_peserta'],
        'jurusan'       => $row['jurusan'],
        'jenis_kelamin' => $row['jenis_kelamin'],
        'v_total'       => round($v_total, 4) // Bulatkan 4 digit desimal sesuai standar skripsi
    ];
}

// -----------------------------------------------------------------------------
// TAHAP 6: PENGURUTAN (SORTING) NILAI PREFERENSI DARI TERTINGGI KE TERENDAH
// -----------------------------------------------------------------------------
usort($ranking_list, function($a, $b) {
    if ($a['v_total'] == $b['v_total']) return 0;
    return ($a['v_total'] > $b['v_total']) ? -1 : 1; // Descending
});

// Pemberian nomor urut peringkat (Ranking 1, 2, 3, ...)
$rank = 1;
foreach ($ranking_list as &$item) {
    $item['ranking'] = $rank++;
}
unset($item);

// Handle Simpan Hasil ke Database
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_results'])) {
    $passing_grade = (float)($_POST['passing_grade'] ?? 0.7000);
    
    // Clear old results
    $conn->query("TRUNCATE TABLE hasil_seleksi");

    $stmt = $conn->prepare("INSERT INTO hasil_seleksi (id_peserta, total_nilai, ranking, status) VALUES (?, ?, ?, ?)");
    foreach ($ranking_list as $res) {
        $status = ($res['v_total'] >= $passing_grade) ? 'Lolos' : 'Tidak Lolos';
        $stmt->bind_param("sdis", $res['id_peserta'], $res['v_total'], $res['ranking'], $status);
        $stmt->execute();
    }

    set_flash('success', 'Hasil seleksi dan perankingan uji kompetensi berhasil disimpan ke database!');
    header("Location: hasil.php");
    exit;
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<!-- Tombol Simpan & Terapkan Ranking -->
<div class="card-custom mb-4 bg-primary-subtle border-primary">
    <div class="card-body p-4">
        <form method="POST" action="perhitungan.php" class="row g-3 align-items-center">
            <input type="hidden" name="save_results" value="1">
            <div class="col-lg-6 col-md-12">
                <h5 class="fw-bold text-primary mb-1">
                    <i class="fa-solid fa-calculator me-2"></i>Perhitungan Otomatis Selesai
                </h5>
                <p class="mb-0 text-secondary small">
                    Skor akhir uji kompetensi telah dihitung. Tentukan ambang batas kelulusan (passing grade = 0.70) lalu simpan hasil seleksi untuk diarsipkan dan dicetak.
                </p>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="row g-2 align-items-end justify-content-lg-end">
                    <div class="col-sm-5 col-5">
                        <label class="form-label small fw-semibold text-dark mb-1">Batas Nilai Lolos:</label>
                        <input type="number" step="0.01" min="0" max="1" name="passing_grade" class="form-control font-monospace text-center fw-bold" value="0.70" style="height: 42px;">
                    </div>
                    <div class="col-sm-7 col-7">
                        <button type="submit" class="btn btn-primary w-100 fw-semibold d-flex align-items-center justify-content-center gap-2" style="height: 42px;">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Hasil Seleksi
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 1. Rekapitulasi Skor Mentah Uji Kompetensi -->
<div class="card-custom mb-4">
    <div class="card-header">
        <h5 class="fw-bold mb-0">
            <i class="fa-solid fa-table me-2 text-primary"></i>1. Rekapitulasi Skor Mentah Uji Kompetensi
        </h5>
        <small class="text-muted">Data skor pelamar pada setiap aspek uji kompetensi (skala 1 - 4)</small>
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matrix_x as $x): ?>
                        <tr>
                            <td class="text-start"><code><?= htmlspecialchars($x['id_peserta']) ?></code></td>
                            <td class="text-start fw-semibold"><?= htmlspecialchars($x['nama_peserta']) ?></td>
                            <td><?= $x['c1'] ?></td>
                            <td><?= $x['c2'] ?></td>
                            <td><?= $x['c3'] ?></td>
                            <td><?= $x['c4'] ?></td>
                            <td><?= $x['c5'] ?></td>
                            <td class="fw-bold text-danger"><?= $x['c6'] ?></td>
                            <td><?= $x['c7'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="2" class="text-start">NILAI MAKSIMUM (Max):</td>
                        <td><?= $max_min['c1']['max'] ?></td>
                        <td><?= $max_min['c2']['max'] ?></td>
                        <td><?= $max_min['c3']['max'] ?></td>
                        <td><?= $max_min['c4']['max'] ?></td>
                        <td><?= $max_min['c5']['max'] ?></td>
                        <td><?= $max_min['c6']['max'] ?></td>
                        <td><?= $max_min['c7']['max'] ?></td>
                    </tr>
                    <tr class="table-light fw-bold text-danger">
                        <td colspan="2" class="text-start">NILAI MINIMUM (Min) :</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td><?= $max_min['c6']['min'] ?></td>
                        <td>-</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-top small text-muted">
        * <em>C6 (Waktu Tempuh ke Perusahaan) bertipe <strong>Cost</strong>, rumus normalisasi: <code>min(C6) / x_ij</code>. Kriteria lainnya bertipe <strong>Benefit</strong>: <code>x_ij / max(C)</code>.</em>
    </div>
</div>

<!-- 2. Tabel Konversi Skor Terstandarisasi -->
<div class="card-custom mb-4">
    <div class="card-header">
        <h5 class="fw-bold mb-0">
            <i class="fa-solid fa-chart-column me-2 text-success"></i>2. Konversi Skor Terstandarisasi
        </h5>
        <small class="text-muted">Konversi nilai terstandarisasi berdasarkan acuan skor</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-custom mb-0 text-center font-monospace">
                <thead>
                    <tr>
                        <th class="text-start" width="70">ID</th>
                        <th class="text-start font-sans">NAMA PELAMAR</th>
                        <th>Frontend (10%)</th>
                        <th>Backend (10%)</th>
                        <th>Usia (15%)</th>
                        <th>Pendidikan (20%)</th>
                        <th>Pengalaman (20%)</th>
                        <th>Waktu (5%)</th>
                        <th>Tes (20%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matrix_r as $r): ?>
                        <tr>
                            <td class="text-start"><code><?= htmlspecialchars($r['id_peserta']) ?></code></td>
                            <td class="text-start font-sans fw-semibold"><?= htmlspecialchars($r['nama_peserta']) ?></td>
                            <td><?= rtrim(rtrim(number_format($r['c1'], 4), '0'), '.') ?></td>
                            <td><?= rtrim(rtrim(number_format($r['c2'], 4), '0'), '.') ?></td>
                            <td><?= rtrim(rtrim(number_format($r['c3'], 4), '0'), '.') ?></td>
                            <td><?= rtrim(rtrim(number_format($r['c4'], 4), '0'), '.') ?></td>
                            <td><?= rtrim(rtrim(number_format($r['c5'], 4), '0'), '.') ?></td>
                            <td><?= rtrim(rtrim(number_format($r['c6'], 4), '0'), '.') ?></td>
                            <td><?= rtrim(rtrim(number_format($r['c7'], 4), '0'), '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 3. Hasil Perankingan Akumulasi Skor Akhir Terbobot (S) - Tabel 4.7 Dokumen -->
<div class="card-custom">
    <div class="card-header">
        <h5 class="fw-bold mb-0">
            <i class="fa-solid fa-award me-2 text-warning"></i>3. Hasil Peringkat & Skor Akhir Pelamar
        </h5>
        <small class="text-muted">Akumulasi skor terbobot 7 aspek | Skor tertinggi = Peringkat 1</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-custom mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="70">RANK</th>
                        <th width="100">ID</th>
                        <th>NAMA PELAMAR</th>
                        <th>JURUSAN</th>
                        <th class="text-center">JENIS KELAMIN</th>
                        <th class="text-center">TOTAL SKOR AKHIR</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ranking_list as $rk): ?>
                        <tr class="<?= $rk['ranking'] === 1 ? 'table-warning fw-semibold' : '' ?>">
                            <td class="text-center fw-bold">
                                <?php if ($rk['ranking'] === 1): ?>
                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-crown me-1"></i>#1</span>
                                <?php elseif ($rk['ranking'] === 2): ?>
                                    <span class="badge bg-secondary">#2</span>
                                <?php elseif ($rk['ranking'] === 3): ?>
                                    <span class="badge bg-info text-dark">#3</span>
                                <?php else: ?>
                                    #<?= $rk['ranking'] ?>
                                <?php endif; ?>
                            </td>
                            <td><code><?= htmlspecialchars($rk['id_peserta']) ?></code></td>
                            <td class="fw-semibold text-dark">
                                <?= htmlspecialchars($rk['nama_peserta']) ?>
                                <?php if ($rk['ranking'] === 1): ?>
                                    <span class="badge bg-success ms-2">Peringkat 1 (Tertinggi)</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($rk['jurusan']) ?></td>
                            <td class="text-center">
                                <span class="badge <?= $rk['jenis_kelamin'] === 'Laki-Laki' ? 'bg-primary-subtle text-primary' : 'bg-danger-subtle text-danger' ?> border">
                                    <?= htmlspecialchars($rk['jenis_kelamin']) ?>
                                </span>
                            </td>
                            <td class="text-center fw-bold text-primary font-monospace fs-6">
                                <?= number_format($rk['v_total'], 4) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
