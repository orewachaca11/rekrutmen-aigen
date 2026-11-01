<?php
// includes/sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
$user_id = $_SESSION['user_id'] ?? 'ID0001';
$user_name = $_SESSION['user_name'] ?? 'User';
$user_role = $_SESSION['user_role'] ?? 'Admin';
?>
<aside class="sidebar">
    <div class="brand">
        <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
            <img src="assets/logo-aigen.jpg" alt="Logo" style="width: 28px; height: 28px; border-radius: 6px; background: #fff; padding: 2px;">
            <h5 class="mb-0">REKRUTMEN IT</h5>
        </div>
        <small>PT. Aigen Global Teknologi</small>
    </div>

    <!-- User Profile Box -->
    <div class="user-box">
        <div class="user-id"><i class="fa-solid fa-id-card me-1"></i><?= htmlspecialchars($user_id) ?></div>
        <div class="user-name text-truncate"><?= htmlspecialchars($user_name) ?></div>
        <span class="user-role"><?= htmlspecialchars($user_role) ?></span>
    </div>

    <!-- Navigation Menu -->
    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="<?= in_array($current_page, ['dashboard.php', 'index.php']) ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge-high"></i>
                <span>DASHBOARD</span>
            </a>
        </li>

        <?php if ($user_role === 'Admin'): ?>
            <li>
                <a href="pengguna.php" class="<?= $current_page === 'pengguna.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-users-gear"></i>
                    <span>DATA PENGGUNA</span>
                </a>
            </li>
            <li>
                <a href="peserta.php" class="<?= $current_page === 'peserta.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-user-graduate"></i>
                    <span>DATA PELAMAR</span>
                </a>
            </li>
            <li>
                <a href="kriteria.php" class="<?= $current_page === 'kriteria.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-list-check"></i>
                    <span>ASPEK KOMPETENSI</span>
                </a>
            </li>
        <?php endif; ?>

        <li>
            <a href="penilaian.php" class="<?= $current_page === 'penilaian.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-clipboard-check"></i>
                <span>UJI KOMPETENSI</span>
            </a>
        </li>

        <?php if ($user_role === 'Admin'): ?>
            <li>
                <a href="perhitungan.php" class="<?= $current_page === 'perhitungan.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-calculator"></i>
                    <span>EVALUASI NILAI</span>
                </a>
            </li>
            <li>
                <a href="hasil.php" class="<?= $current_page === 'hasil.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-square-poll-vertical"></i>
                    <span>HASIL SELEKSI</span>
                </a>
            </li>
        <?php endif; ?>

        <li>
            <a href="laporan.php" class="<?= in_array($current_page, ['laporan.php', 'cetak_peserta.php', 'cetak_kriteria.php', 'cetak_penilaian.php', 'cetak_seleksi.php', 'cetak_ranking.php']) ? 'active' : '' ?>">
                <i class="fa-solid fa-print"></i>
                <span>LAPORAN</span>
            </a>
        </li>

        <li class="mt-4">
            <a href="logout.php" class="logout-btn" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?');">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>LOGOUT</span>
            </a>
        </li>
    </ul>
</aside>
