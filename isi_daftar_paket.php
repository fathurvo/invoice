<?php
include 'start.php'; 
// Masukkan file config.php untuk koneksi database
include('config.php');

// Jika form disubmit, proses input data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama_paket = $_POST['nama_paket'];
    $harga_asli = $_POST['harga_asli'];
    $diskon_persen = $_POST['diskon_persen'];
    $ppn_option = $_POST['ppn_option'];
    
    // Hitung harga setelah diskon
    $harga_setelah_diskon = $harga_asli - ($harga_asli * $diskon_persen / 100);
    
    // Jika memilih PPN, hitung PPN
    if ($ppn_option == 'ppn') {
        $harga_setelah_ppn = $harga_setelah_diskon + ($harga_setelah_diskon * 11 / 100); // PPN 11%
    } else {
        // Jika memilih Non PPN, harga tetap tanpa PPN
        $harga_setelah_ppn = $harga_setelah_diskon;
    }
    
    // Keterangan (opsional)
    $keterangan = $_POST['keterangan'];

    // Query untuk menyisipkan data ke tabel paket
    $query = "INSERT INTO paket (nama_paket, harga, keterangan) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $nama_paket, $harga_setelah_ppn, $keterangan);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Data berhasil ditambahkan!</div>";
    } else {
        echo "<div class='alert alert-danger'>Terjadi kesalahan: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

// Jika ada permintaan untuk menghapus data
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    // Query untuk menghapus data berdasarkan ID
    $delete_query = "DELETE FROM paket WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $delete_id);

    if ($delete_stmt->execute()) {
        echo "<div class='alert alert-success'>Data berhasil dihapus!</div>";
    } else {
        echo "<div class='alert alert-danger'>Terjadi kesalahan saat menghapus data: " . $delete_stmt->error . "</div>";
    }

    $delete_stmt->close();
}

// Filter query
$filter_sql = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $filter_sql = "WHERE nama_paket LIKE '%$search%' OR harga LIKE '%$search%'";
}

// Query untuk menampilkan semua data dari tabel paket dengan filter
$query_tampil = "SELECT * FROM paket $filter_sql";
$result = $conn->query($query_tampil);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Isi Daftar Paket</title>

    <!-- Link ke Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .wrapper {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }
        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            background: #343a40;
            color: white;
            padding: 15px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
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
        /* Navbar Styling */
        .navbar {
            background-color: #343a40;
        }
        .navbar .navbar-brand {
            color: white;
        }
        .navbar-nav .nav-item .nav-link {
            color: white;
        }
        .navbar-nav .nav-item .nav-link:hover {
            color: #ccc;
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

    <script>
        function hitungHarga() {
            var hargaAsli = parseFloat(document.getElementById('harga_asli').value);
            var diskonPersen = parseFloat(document.getElementById('diskon_persen').value);
            var ppnOption = document.getElementById('ppn_option').value;

            if (!isNaN(hargaAsli) && !isNaN(diskonPersen)) {
                var hargaSetelahDiskon = hargaAsli - (hargaAsli * diskonPersen / 100);

                if (ppnOption == 'ppn') {
                    var hargaSetelahPpn = hargaSetelahDiskon + (hargaSetelahDiskon * 11 / 100);
                    document.getElementById('harga').value = hargaSetelahPpn.toFixed(2);
                } else {
                    document.getElementById('harga').value = hargaSetelahDiskon.toFixed(2);
                }
            }
        }
    </script>
</head>
<body>
    <!-- Navbar -->
   

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
            <div class="container mt-5">
                <h1 class="text-center">Form Input Daftar Paket</h1>
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <form action="isi_daftar_paket.php" method="POST" onsubmit="return hitungHarga()">
                            <div class="mb-3">
                                <label for="nama_paket" class="form-label">Nama Paket:</label>
                                <input type="text" id="nama_paket" name="nama_paket" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="harga_asli" class="form-label">Harga Asli:</label>
                                <input type="number" id="harga_asli" name="harga_asli" class="form-control" step="0.01" required oninput="hitungHarga()">
                            </div>

                            <div class="mb-3">
                                <label for="diskon_persen" class="form-label">Diskon (%)</label>
                                <input type="number" id="diskon_persen" name="diskon_persen" class="form-control" value="0" step="0.01" required oninput="hitungHarga()">
                            </div>

                            <div class="mb-3">
                                <label for="ppn_option" class="form-label">PPN:</label>
                                <select id="ppn_option" name="ppn_option" class="form-select" onchange="hitungHarga()">
                                    <option value="ppn">PPN (11%)</option>
                                    <option value="non_ppn">Non PPN</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="harga" class="form-label">Harga Setelah Diskon dan PPN:</label>
                                <input type="text" id="harga" name="harga" class="form-control" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan:</label>
                                <textarea id="keterangan" name="keterangan" class="form-control"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Tambah Paket</button>
                            <a href="index.php" class="btn btn-secondary btn-back">Kembali</a>
                        </form>
                    </div>
                </div>

                <h2 class="text-center mt-5">Daftar Paket</h2>

                <!-- Filter Form -->
                <form method="GET" class="mb-3">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control" placeholder="Cari Nama Paket atau Harga" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-info w-100">Cari</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Paket</th>
                                <th>Harga</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Tampilkan semua data paket dari database
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row['id'] . "</td>";
                                    echo "<td>" . $row['nama_paket'] . "</td>";
                                    echo "<td>" . number_format($row['harga'], 2, ',', '.') . "</td>";
                                    echo "<td>" . $row['keterangan'] . "</td>";
                                    echo "<td><a href='?delete_id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\")'>Hapus</a></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>Tidak ada data paket.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Link ke Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
