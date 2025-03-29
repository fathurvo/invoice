<?php 
include 'start.php'; 
include 'config.php';

// Menyiapkan header untuk file CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="daftar_pelanggan.csv"');

// Membuka file output (ke browser)
$output = fopen('php://output', 'w');

// Menulis header kolom
fputcsv($output, ['ID', 'Nama Pelanggan', 'Paket', 'Harga', 'Modem', 'Tanggal Pemasangan', 'Status']);

// Ambil data pelanggan dari database
$result = $conn->query("SELECT * FROM customers");

// Menulis data pelanggan ke file CSV
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['customer_name'],
        $row['package'],
        number_format($row['price'], 0, ',', '.'),
        $row['modem'],
        $row['installation_date'],
        $row['status']
    ]);
}

// Menutup file output
fclose($output);
exit();
?>
