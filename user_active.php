<?php
// Memuat autoloader Composer
require 'vendor/autoload.php'; // Pastikan autoloader dimuat dengan benar

// Memuat kredensial MikroTik
require 'koneksi_mikrotik.php'; // Memuat kredensial MikroTik
include('config.php'); // Memuat koneksi ke database MySQL
include('auto_number.php'); // Memuat fungsi auto number

use RouterOS\Client;
use RouterOS\Query;

// Membuat koneksi ke MikroTik
$client = new Client([
    'host' => MIKROTIK_IP,  // Menggunakan konstanta dari koneksi_mikrotik.php
    'user' => MIKROTIK_USER, // Menggunakan konstanta dari koneksi_mikrotik.php
    'pass' => MIKROTIK_PASS, // Menggunakan konstanta dari koneksi_mikrotik.php
]);

// Ambil data pengguna aktif dari MikroTik (ppp/secret)
function getActiveUsersFromMikrotik($client) {
    $secretQuery = new Query('/ppp/active/print');
    return $client->query($secretQuery)->read();
}

// Menampilkan data dari database dan menambahkan status aktif/tidak aktif
function displayPPPData($conn, $activeUsers, $orderBy, $orderDir) {
    // Mengubah query untuk menyertakan pengurutan
    $sql = "SELECT username, created_at, profile, service, remote_address FROM ppp_data 
            ORDER BY $orderBy $orderDir";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $index = 0; // Inisialisasi index untuk penomoran
        while ($row = $result->fetch_assoc()) {
            $username = $row['username'];
            $createdAt = $row['created_at'];
            $profile = $row['profile'];
            $service = $row['service'];
            $remoteAddress = $row['remote_address'];

            // Cek apakah username ada di user aktif MikroTik
            $status = 'Tidak Aktif';
            $statusClass = 'danger'; // Warna merah untuk status tidak aktif
            foreach ($activeUsers as $activeUser) {
                if ($activeUser['name'] == $username) {
                    $status = 'Aktif';
                    $remoteAddress = $activeUser['address']; // Ambil IP remote dari MikroTik
                    $statusClass = 'success'; // Warna hijau untuk status aktif
                    break;
                }
            }

            // Tampilkan data pengguna dengan penomoran otomatis
            $autoNumber = getAutoNumber($index, 1, 0); // Menggunakan halaman 1 dan 0 untuk menampilkan semua
            echo "<tr>
                    <td>$autoNumber</td>
                    <td>$username</td>
                    <td>$createdAt</td>
                    <td>$profile</td>
                    <td>$service</td>
                    <td>$remoteAddress</td>
                    <td>
                        <span class='badge bg-$statusClass'>$status</span>
                    </td>
                </tr>";
            $index++; // Increment index
        }
    } else {
        echo "<tr><td colspan='7' class='no-data'>Tidak ada data</td></tr>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data PPP</title>
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
        <a href="tambah_pengguna.php">Tambah Pelanggan</a>
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
        <h2 class="mb-4">Data PPP</h2>

        <!-- Tabel Data PPP -->
        <div class="table-container">
            <table class="table table-striped table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th><a href="?orderBy=username&orderDir=<?php echo (isset($_GET['orderDir']) && $_GET['orderDir'] == 'ASC') ? 'DESC' : 'ASC'; ?>">Nama</a></th>
                        <th><a href="?orderBy=created_at&orderDir=<?php echo (isset($_GET['orderDir']) && $_GET['orderDir'] == 'ASC') ? 'DESC' : 'ASC'; ?>">Created At</a></th>
                        <th><a href="?orderBy=profile&orderDir=<?php echo (isset($_GET['orderDir']) && $_GET['orderDir'] == 'ASC') ? 'DESC' : 'ASC'; ?>">Profile</a></th>
                        <th><a href="?orderBy=service&orderDir=<?php echo (isset($_GET['orderDir']) && $_GET['orderDir'] == 'ASC') ? 'DESC' : 'ASC'; ?>">Service</a></th>
                        <th><a href="?orderBy=remote_address&orderDir=<?php echo (isset($_GET['orderDir']) && $_GET['orderDir'] == 'ASC') ? 'DESC' : 'ASC'; ?>">IP Remote</a></th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Mengambil data pengguna aktif dari MikroTik
                    try {
                        $activeUsers = getActiveUsersFromMikrotik($client);

                        // Mendapatkan parameter pengurutan dari URL
                        $orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'username'; // Default pengurutan
                        $orderDir = isset($_GET['orderDir']) && $_GET['orderDir'] == 'DESC' ? 'DESC' : 'ASC'; // Default arah pengurutan

                        displayPPPData($conn, $activeUsers, $orderBy, $orderDir);
                    } catch (Exception $e) {
                        echo "<tr><td colspan='7' class='no-data'>Gagal menghubungkan ke MikroTik: " . $e->getMessage() . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>