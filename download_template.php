<?php
// Nama file CSV template
$filename = "template_pelanggan.csv";

// Header untuk file CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Membuka file untuk menulis
$output = fopen('php://output', 'w');

// Menulis header kolom CSV (struktur yang diinginkan)
fputcsv($output, array('ID', 'Nama Pelanggan', 'Paket', 'Harga', 'Modem', 'Tanggal Pemasangan', 'Status'));

// Menutup file CSV
fclose($output);
exit();
?>
