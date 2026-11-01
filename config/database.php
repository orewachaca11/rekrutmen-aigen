<?php
/**
 * ====================================================================================
 * FILE: config/database.php
 * MODUL: Konfigurasi Database & Helper Global Sistem
 * DESKRIPSI:
 * Mengatur koneksi utama ke database MySQL, inisialisasi session aplikasi,
 * serta menyediakan fungsi-fungsi pembantu (helper) seperti query database,
 * flash message notifikasi, proteksi otentikasi & hak akses (role), dan format tanggal.
 * ====================================================================================
 */

// 1. Inisialisasi Session PHP untuk manajemen status login pengguna
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Kredensial Koneksi Database MySQL (Default XAMPP)
$db_host = 'localhost';        // Host server MySQL
$db_user = 'root';             // Username MySQL
$db_pass = '';                 // Password MySQL (kosong di XAMPP)
// Database utama sistem rekrutmen
$db_name = 'rekrutmen_aigen';

// 3. Membuka Koneksi Menggunakan MySQLi
try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        $db_connected = false;
        $db_error = $conn->connect_error;
    } else {
        $db_connected = true;
        $conn->set_charset("utf8mb4"); // Set encoding utf8mb4 agar mendukung karakter khusus
    }
} catch (Exception $e) {
    $db_connected = false;
    $db_error = $e->getMessage();
}

/**
 * FUNGSI: query($sql)
 * KEGUNAAN: Menjalankan perintah SQL SELECT dan mengembalikan seluruh baris data sebagai array asosiatif.
 * @param string $sql String query SQL SELECT
 * @return array Sekumpulan data hasil query dalam bentuk array asosiatif
 */
function query($sql) {
    global $conn, $db_connected;
    if (!$db_connected) return [];
    
    $result = $conn->query($sql);
    if (!$result) return [];
    
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

/**
 * FUNGSI: set_flash($type, $message)
 * KEGUNAAN: Menyimpan pesan notifikasi sementara ke session (Flash Message)
 *           yang akan langsung ditampilkan pada halaman tujuan setelah redirect.
 * @param string $type Jenis notifikasi: 'success', 'danger', 'warning', 'info'
 * @param string $message Isi pesan yang ingin ditampilkan
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * FUNGSI: get_flash()
 * KEGUNAAN: Mengambil pesan notifikasi dari session dan langsung menghapusnya 
 *           agar pesan hanya muncul 1 kali (flash message).
 * @return array|null Array data notifikasi atau null jika tidak ada pesan
 */
function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * FUNGSI: check_login($role_required)
 * KEGUNAAN: Memverifikasi apakah pengguna sudah login ke sistem.
 *           Jika belum login, pengguna otomatis dialihkan ke halaman login.php.
 *           Jika role_required diisi dan role pengguna tidak cocok, diarahkan ke dashboard.
 * @param string|null $role_required Role yang diwajibkan ('Admin' atau 'Penguji')
 */
function check_login($role_required = null) {
    // Cek apakah session user_id sudah ada (sudah login)
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
    
    // Cek hak akses role khusus (misal halaman yang hanya boleh diakses Admin)
    if ($role_required && $_SESSION['user_role'] !== $role_required) {
        set_flash('danger', 'Akses ditolak: Anda tidak memiliki wewenang untuk membuka halaman tersebut!');
        header("Location: dashboard.php");
        exit;
    }
}

/**
 * FUNGSI: tgl_indo($tanggal)
 * KEGUNAAN: Mengubah format tanggal standar database (YYYY-MM-DD) menjadi format bahasa Indonesia formal (Contoh: 14 Oktober 2026).
 * @param string $tanggal Tanggal format Y-m-d
 * @return string Tanggal format Indonesia
 */
function tgl_indo($tanggal) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $pecahkan = explode('-', $tanggal);
    if (count($pecahkan) === 3) {
        return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
    }
    return $tanggal;
}
?>
