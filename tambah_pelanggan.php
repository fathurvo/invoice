<?php
include 'start.php';  
include 'config.php'; 

// Menambah data pelanggan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    // Debugging: Cek apakah data sudah diterima
    echo "<pre>";
    print_r($_POST); // Menampilkan semua data yang dikirimkan melalui form
    echo "</pre>";

    $customer_name = $_POST['customer_name'];
    $package = $_POST['package']; // Paket akan berisi ID paket dari tabel paket
    $price = $_POST['price'];
    $modem = $_POST['modem'];
    $installation_date = $_POST['installation_date'];
    $status = $_POST['status'];
    
    // Mengecek apakah nama pelanggan sudah ada
    $result = $conn->query("SELECT id FROM customers WHERE customer_name = '$customer_name'");
    if ($result->num_rows > 0) {
        // Jika sudah ada, tampilkan pesan popup
        echo "<script>alert('Nama pelanggan sudah ada!');</script>";
    } else {
        // Jika belum ada, lanjutkan dengan penambahan data pelanggan
        $stmt = $conn->prepare("INSERT INTO customers (customer_name, package, price, modem, installation_date, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisss", $customer_name, $package, $price, $modem, $installation_date, $status);
        $stmt->execute();
        $stmt->close();
        

        // Mengalihkan dengan pesan sukses
        header("Location: tambah_pelanggan.php?message=Data+berhasil+ditambahkan+untuk+" . urlencode($customer_name));
        exit(); 
    }
}

// Mengupdate data pelanggan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $customer_name = $_POST['customer_name'];
    $package = $_POST['package'];
    $price = $_POST['price'];
    $modem = $_POST['modem'];
    $installation_date = $_POST['installation_date'];
    $status = $_POST['status'];
    $customer_id = $_GET['edit']; // Ambil ID pelanggan dari parameter URL

    // Update data pelanggan
    $stmt = $conn->prepare("UPDATE customers SET customer_name = ?, package = ?, price = ?, modem = ?, installation_date = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssisssi", $customer_name, $package, $price, $modem, $installation_date, $status, $customer_id);
    $stmt->execute();
    $stmt->close();


    // Mengalihkan dengan pesan sukses
    header("Location: tambah_pelanggan.php?message=Data+pelanggan+berhasil+diupdate");
    exit(); 
}

// Menghapus data pelanggan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $customer_id = $_POST['customer_id'];

    // Menghapus pelanggan dari database
    $stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $stmt->close();

    // Mengalihkan dengan pesan sukses
    header("Location: tambah_pelanggan.php?message=Data+pelanggan+berhasil+dihapus");
    exit(); 
}
// Ambil data paket untuk dropdown
$packages_result = $conn->query("SELECT id, nama_paket, harga FROM paket");

// Ambil data pelanggan untuk ditampilkan
$customers_result = $conn->query("SELECT c.*, p.nama_paket FROM customers c LEFT JOIN paket p ON c.package = p.nama_paket");


// Ambil data pelanggan yang akan diedit jika ada
if (isset($_GET['edit'])) {
    $customer_id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM customers WHERE id = '$customer_id'");
    $customer = $result->fetch_assoc();
    // Pastikan data pelanggan ditemukan
    if ($customer) {
        $customer_name = $customer['customer_name'];
        $package = $customer['package'];
        $price = $customer['price'];
        $modem = $customer['modem'];
        $installation_date = $customer['installation_date'];
        $status = $customer['status'];
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Pelanggan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
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

        .container {
            max-width: 800px;
            margin-top: 50px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-back {
            margin-right: 10px;
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

    <div class="container text-center">
        <h2 class="mb-4"><?php echo isset($customer) ? "Edit" : "Tambah"; ?> Data Pelanggan</h2>

        <!-- Menampilkan pesan jika ada -->
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nama Pelanggan:</label>
                <input type="text" class="form-control" name="customer_name" value="<?php echo isset($customer_name) ? $customer_name : ''; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Paket Langganan:</label>
                <select class="form-control" name="package" id="package" onchange="updatePrice()" required>
                    <option value="">Pilih Paket</option>
                    <?php while ($row = $packages_result->fetch_assoc()): ?>
                        <option value="<?php echo $row['nama_paket']; ?>" data-harga="<?php echo $row['harga']; ?>" <?php echo (isset($package) && $package == $row['id']) ? 'selected' : ''; ?>>
                            <?php echo $row['nama_paket']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>

            </div>

            <div class="mb-3">
                <label class="form-label">Harga Paket:</label>
                <input type="number" class="form-control" name="price" id="price" value="<?php echo isset($price) ? $price : ''; ?>" required readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Jenis Modem:</label>
                <input type="text" class="form-control" name="modem" value="<?php echo isset($modem) ? $modem : ''; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Pemasangan:</label>
                <input type="date" class="form-control" name="installation_date" value="<?php echo isset($installation_date) ? $installation_date : ''; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Status:</label>
                <select class="form-control" name="status" required>
                    <option value="Aktif" <?php echo (isset($status) && $status == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                    <option value="Tidak Aktif" <?php echo (isset($status) && $status == 'Tidak Aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                </select>
            </div>

            <button type="submit" name="<?php echo isset($customer) ? 'update' : 'add'; ?>" class="btn btn-primary">
                <?php echo isset($customer) ? 'Update' : 'Simpan'; ?>
            </button>
            <a href="index.php" class="btn btn-secondary btn-back">Kembali</a>
            <a href="logout.php" class="btn btn-danger btn-lg">Logout</a>
        </form>
    </div>

    <div class="container mt-5 table-container">
        <h2 class="text-center">Daftar Pelanggan</h2>
        <table class="table table-striped table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nama Pelanggan</th>
                    <th>Paket</th>
                    <th>Harga</th>
                    <th>Modem</th>
                    <th>Tanggal Pemasangan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $customers_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['customer_name']; ?></td>
                        <td><?php echo $row['nama_paket']; ?></td>
                        <td><?php echo number_format($row['price'], 0, ',', '.'); ?></td>
                        <td><?php echo $row['modem']; ?></td>
                        <td><?php echo $row['installation_date']; ?></td>
                        <td>
                            <span class="badge bg-<?php echo ($row['status'] == 'Aktif') ? 'success' : 'danger'; ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="tambah_pelanggan.php?edit=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?');" style="display:inline;">
                                <input type="hidden" name="customer_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="delete" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>
        // Update harga berdasarkan pilihan paket
        function updatePrice() {
            var select = document.getElementById('package');
            var selectedOption = select.options[select.selectedIndex];
            var price = selectedOption.getAttribute('data-harga');
            document.getElementById('price').value = price;
        }
    </script>
</body>
</html>
