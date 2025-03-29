<?php  
include 'start.php'; 
include 'config.php'; 
include 'auto_number.php'; 

// Mengatur jumlah baris per halaman
$perPage = 5;

// Mengambil nomor halaman dari URL, default ke halaman 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $perPage;

// Pencarian berdasarkan nama dan status pembayaran
$search_name = isset($_GET['search_name']) ? $_GET['search_name'] : '';
$search_status = isset($_GET['search_status']) ? $_GET['search_status'] : '';

// Query untuk menghitung total data yang sesuai dengan pencarian
$whereClause = "WHERE 1";
if ($search_name) {
    $whereClause .= " AND c.customer_name LIKE '%" . $conn->real_escape_string($search_name) . "%'";
}
if ($search_status) {
    if ($search_status == 'paid') {
        $whereClause .= " AND EXISTS (SELECT 1 FROM payments p WHERE p.customer_id = c.id)";
    } else if ($search_status == 'unpaid') {
        $whereClause .= " AND NOT EXISTS (SELECT 1 FROM payments p WHERE p.customer_id = c.id)";
    }
}

// Mengambil data pelanggan dengan pagination dan filter pencarian
$sql = "SELECT c.*, 
               IFNULL((SELECT payment_date FROM payments p WHERE p.customer_id = c.id ORDER BY payment_date DESC LIMIT 1), '') AS last_payment_date
        FROM customers c
        $whereClause
        LIMIT $start, $perPage";
$result = $conn->query($sql);

// Mengambil jumlah total pelanggan berdasarkan filter pencarian
$total_sql = "SELECT COUNT(*) as total FROM customers c $whereClause";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $perPage);

// Menghitung jumlah pelanggan yang sudah bayar dan belum bayar
$paid_sql = "SELECT COUNT(DISTINCT c.id) as paid_count
             FROM customers c
             JOIN payments p ON c.id = p.customer_id";
$paid_result = $conn->query($paid_sql);
$paid_row = $paid_result->fetch_assoc();
$paid_count = $paid_row['paid_count'];

$unpaid_count = $total_records - $paid_count;

// Menyusun data pelanggan dengan status pembayaran
$customers_with_payments = [];
while ($row = $result->fetch_assoc()) {
    $payment_status = $row['last_payment_date'] ? "Sudah Bayar (Tanggal: " . $row['last_payment_date'] . ")" : "Belum Bayar";
    $row['payment_status'] = $payment_status;
    $customers_with_payments[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pelanggan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .wrapper {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }
        .sidebar {
            width: 250px;
            background: #343a40;
            color: white;
            padding: 15px;
            height: 100vh;
            position: fixed;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
        }
        .sidebar a:hover {
            background: #495057;
            border-radius: 5px;
        }
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
    </style>
</head>
<body>

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
        <h2 class="mb-4">Daftar Pelanggan</h2>

        <!-- Form Pencarian -->
        <form method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="search_name" class="form-control" placeholder="Cari Nama Pelanggan" value="<?php echo htmlspecialchars($search_name); ?>">
                </div>
                <div class="col-md-4">
                    <select name="search_status" class="form-control">
                        <option value="">Pilih Status Pembayaran</option>
                        <option value="paid" <?php echo ($search_status == 'paid') ? 'selected' : ''; ?>>Sudah Bayar</option>
                        <option value="unpaid" <?php echo ($search_status == 'unpaid') ? 'selected' : ''; ?>>Belum Bayar</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </div>
        </form>

        <!-- Tombol Tambah, Impor, dan Ekspor -->
        <div class="btn-group" role="group" aria-label="Button group">
            <a href="tambah_pelanggan.php" class="btn btn-primary">Tambah Pelanggan</a>
            <a href="download_template.php" class="btn btn-success">Unduh Template CSV</a>
            <a href="import.php" class="btn btn-warning">Import CSV</a>
            <a href="export.php" class="btn btn-info">Export ke CSV</a>
        </div>

        <!-- Tabel Daftar Pelanggan -->
            <div class="table-container">
                <table class="table table-striped table-bordered mt-3">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th> <!-- Ganti 'ID' dengan 'No' untuk nomor urut -->
                            <th>Nama Pelanggan</th>
                            <th>Paket</th>
                            <th>Harga</th>
                            <th>Modem</th>
                            <th>Status</th>
                            <th>Status Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = $start + 1; // Mulai nomor urut berdasarkan halaman
                        foreach ($customers_with_payments as $row) { ?>
                            <tr>
                                <td><?php echo $no++; ?></td> <!-- Menampilkan nomor urut -->
                                <td><?php echo $row['customer_name']; ?></td>
                                <td><?php echo $row['package']; ?></td>
                                <td><?php echo number_format($row['price'], 0, ',', '.'); ?></td>
                                <td><?php echo $row['modem']; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo ($row['status'] == 'Aktif') ? 'success' : 'danger'; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td><?php echo $row['payment_status']; ?></td>
                                <td>
                                    <a href="tambah_pelanggan.php?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="tambah_pelanggan.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?');">Hapus</a>
                                    <a href="cetak_invoice.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info" target="_blank">Cetak Invoice</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>


            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=1&search_name=<?php echo urlencode($search_name); ?>&search_status=<?php echo urlencode($search_status); ?>">First</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&search_name=<?php echo urlencode($search_name); ?>&search_status=<?php echo urlencode($search_status); ?>">Previous</a>
                        </li>
                    <?php endif; ?>

                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&search_name=<?php echo urlencode($search_name); ?>&search_status=<?php echo urlencode($search_status); ?>">Next</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $total_pages; ?>&search_name=<?php echo urlencode($search_name); ?>&search_status=<?php echo urlencode($search_status); ?>">Last</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Total Data: <?php echo $total_records; ?> | Sudah Bayar: <?php echo $paid_count; ?> | Belum Bayar: <?php echo $unpaid_count; ?></p>
        </div>
    </div>
</body>
</html>
