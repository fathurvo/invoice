<?php
// Memuat autoloader Composer
require 'vendor/autoload.php';

// Memuat kredensial dari config.php
require 'koneksi_mikrotik.php';

session_start();

if (isset($_SESSION['mikrotik_client'])) {
    // Menghapus objek client dari sesi
    unset($_SESSION['mikrotik_client']);
    
    // Hapus data sesi
    session_destroy();
    
    echo "Anda telah berhasil logout dari MikroTik.";
} else {
    echo "Tidak ada koneksi yang aktif untuk logout.";
}
?>
