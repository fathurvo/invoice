<?php
include 'start.php'; 
include 'config.php';

// Menghapus pembayaran jika tombol delete ditekan
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM payments WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: hps_pembayaran.php?message=Data+berhasil+dihapus");
    exit();
}

// Ambil data pembayaran
$result = $conn->query("SELECT p.id, c.customer_name, p.amount, p.payment_date, p.payment_method FROM payments p JOIN customers c ON p.customer_id = c.id");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pembayaran</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .wrapper {
            display: flex;
            min-height: 100vh;
            flex-direction: row;
        }
        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            background: #343a40;
            color: white;
            padding: 15px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
        }
        .sidebar h4 {
            color: #fff;
            margin-bottom: 30px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background: #495057;
        }
        /* Main content */
        .content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
        }
        .footer {
            background: #f8f9fa;
            text-align: center;
            padding: 10px;
            margin-top: auto;
        }
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
        <h4 class="text-center">REGENT.NET</h4>
        <a href="index.php">DASHBOARD</a>
        <a href="tambah_pelanggan.php">Tambah Pelanggan</a>
        <a href="pembayaran.php">Tambah Pembayaran</a>
        <a href="grafik.php">Lihat Grafik Pembayaran</a>
        <a href="isi_daftar_paket.php">Tambah Daftar Paket</a>
        <a href="hps_pembayaran.php">Hapus Data Pembayaran</a>
        <a href="user_active.php">Pelanggan Online</a>
        <a href="view_logs.php">Aktivitas Login</a>
        <a href="logout.php" class="text-danger">Logout</a>
    </nav>

        <!-- Main Content -->
        <div class="content">
            <h2 class="text-center mb-4">Daftar Pembayaran</h2>
            <?php if (isset($_GET['message'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_GET['message']); ?>
                </div>
            <?php endif; ?>
            
            <div class="table-container">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Pelanggan</th>
                            <th>Jumlah Pembayaran</th>
                            <th>Tanggal Pembayaran</th>
                            <th>Metode Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['customer_name']; ?></td>
                                <td>Rp <?php echo number_format($row['amount'], 0, ',', '.'); ?></td>
                                <td><?php echo $row['payment_date']; ?></td>
                                <td><?php echo $row['payment_method']; ?></td>
                                <td>
                                    <a href="?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pembayaran ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
          </div>
    </div>
</body>
</html>
