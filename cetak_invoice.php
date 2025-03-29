<?php
include 'start.php'; 
include 'config.php';

// Ambil ID pelanggan dari parameter URL
$id = $_GET['id'];

// Ambil data pelanggan dari database
$result = $conn->query("SELECT * FROM customers WHERE id = $id");
$customer = $result->fetch_assoc();

// Jika data pelanggan tidak ditemukan, redirect ke halaman utama
if (!$customer) {
    header("Location: index.php?message=Data%20pelanggan%20tidak%20ditemukan");
    exit();
}

// Mendapatkan tanggal saat invoice dibuat
$invoice_date = date("d-m-Y"); // Format tanggal: dd-mm-yyyy

// Ambil status pembayaran untuk pelanggan ini
$payment_check = $conn->query("SELECT * FROM payments WHERE customer_id = $id ORDER BY payment_date DESC LIMIT 1");
$payment = $payment_check->fetch_assoc();

// Tentukan status pembayaran
if ($payment) {
    $payment_status = "Sudah Bayar (Tanggal: " . $payment['payment_date'] . ")";
} else {
    $payment_status = "Belum Bayar";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?php echo $customer['customer_name']; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .invoice-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        .invoice-header {
            text-align: center;
        }
        .invoice-header h2 {
            margin: 0;
        }
        .invoice-details {
            margin-top: 20px;
        }
        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-details th, .invoice-details td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .invoice-footer {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="invoice-container">
    <div class="invoice-header">
        <h2>INVOICE PELANGGAN REGENT.NET</h2>
        <p>Nomor Invoice: <?php echo $customer['id']; ?></p>
        <h3><?php echo $customer['customer_name']; ?></h3>
    </div>

    <div class="invoice-details">
        <table>
            <tr>
                <th>Paket</th>
                <td><?php echo $customer['package']; ?></td>
            </tr>
            <tr>
                <th>Harga</th>
                <td>Rp <?php echo number_format($customer['price'], 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <th>Modem</th>
                <td><?php echo $customer['modem']; ?></td>
            </tr>
            <tr>
                <th>Tanggal Pemasangan</th>
                <td><?php echo $customer['installation_date']; ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td><?php echo $customer['status']; ?></td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td><?php echo $payment_status; ?></td>
            </tr>
            <tr>
                <th>Tanggal Pencetakan Invoice</th>
                <td><?php echo $invoice_date; ?></td>
            </tr>
        </table>
    </div>

    <div class="invoice-footer">
        <p>Terima kasih telah menggunakan layanan kami.</p>
    </div>
</div>

</body>
</html>
