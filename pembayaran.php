<?php 
include 'start.php'; 
include 'config.php';

// Menyimpan data pembayaran ke dalam database
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_payment'])) {
    $customer_id = $_POST['customer_id'];
    $amount = $_POST['amount'];
    $payment_date = $_POST['payment_date'];
    $payment_method = $_POST['payment_method'];

    // Mengecek apakah pelanggan sudah melakukan pembayaran sebelumnya
    $check_payment_query = "SELECT * FROM payments WHERE customer_id = ?";
    $stmt_check = $conn->prepare($check_payment_query);
    $stmt_check->bind_param("i", $customer_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Jika pelanggan sudah melakukan pembayaran sebelumnya
        echo "<div class='alert alert-danger'>Pembayaran untuk pelanggan ini sudah ada!</div>";
        exit();
    }

    // Menggunakan prepared statements untuk menyimpan data pembayaran
    $stmt = $conn->prepare("INSERT INTO payments (customer_id, amount, payment_date, payment_method) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $customer_id, $amount, $payment_date, $payment_method);
    $stmt->execute();
    $stmt->close();

    // Mengalihkan dengan pesan sukses
    header("Location: hps_pembayaran.php?message=Pembayaran+berhasil+disimpan.");
    exit();
}

// Ambil data pembayaran
$result = $conn->query("SELECT p.id, c.customer_name, p.amount, p.payment_date, p.payment_method FROM payments p JOIN customers c ON p.customer_id = c.id");

// Ambil semua pelanggan untuk dropdown
$customers_result = $conn->query("SELECT id, customer_name, price FROM customers");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pembayaran</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

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
            <h2 class="text-center mb-4">Input Pembayaran Pelanggan</h2>
            
            <!-- Menampilkan pesan jika ada -->
            <?php if (isset($_GET['message'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_GET['message']); ?>
                </div>
            <?php endif; ?>

            <!-- Form untuk memilih pelanggan dan memasukkan pembayaran -->
            <form method="POST" id="payment_form">
                <div class="mb-3">
                    <label for="customer_id" class="form-label">Pilih Pelanggan</label>
                    <select name="customer_id" class="form-control" id="customer_id" onchange="updatePrice()" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        <?php
                        // Ambil data pelanggan dari database
                        while ($row = $customers_result->fetch_assoc()) {
                            echo "<option value='".$row['id']."' data-price='".$row['price']."'>".$row['customer_name']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="amount" class="form-label">Jumlah Pembayaran (Rp)</label>
                    <input type="number" class="form-control" name="amount" id="amount" required readonly>
                </div>
                <div class="mb-3">
                    <label for="payment_date" class="form-label">Tanggal Pembayaran</label>
                    <input type="date" class="form-control" name="payment_date" required>
                </div>
                <div class="mb-3">
                    <label for="payment_method" class="form-label">Metode Pembayaran</label>
                    <!-- Dropdown untuk memilih metode pembayaran -->
                    <select name="payment_method" class="form-control" required>
                        <option value="">-- Pilih Metode Pembayaran --</option>
                        <option value="Cash">Cash</option>
                        <option value="Transfer">Transfer</option>
                    </select>
                </div>
                <button type="submit" name="submit_payment" class="btn btn-primary">Simpan Pembayaran</button>
                <div><a href="index.php" class="btn btn-secondary btn-back">Kembali</a></div>
            </form>

            <hr>

            <h2 class="text-center mb-4">Daftar Pembayaran</h2>
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

    <script>
        // Fungsi untuk memperbarui harga berdasarkan pelanggan yang dipilih
        function updatePrice() {
            var selectedOption = document.getElementById('customer_id').selectedOptions[0];
            var price = selectedOption.getAttribute('data-price');
            document.getElementById('amount').value = price;
        }
    </script>
</body>
</html>
