<?php
// Memasukkan koneksi dan sesi
include 'start.php';
include 'config.php';

// Ambil ID pelanggan dari URL
$id = $_GET['id'];

// Hapus data pelanggan berdasarkan ID
$sql = "DELETE FROM customers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    echo "Pelanggan berhasil dihapus!";
    header('Location: tambah_pelanggan.php');  // Mengarahkan kembali ke halaman tambah pelanggan setelah dihapus
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
?>
