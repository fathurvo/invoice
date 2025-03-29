<?php
include 'start.php'; 
// Memuat autoloader Composer
require 'vendor/autoload.php';

// Memuat kredensial dari koneksi_mikrotik.php
require 'koneksi_mikrotik.php'; // Memuat kredensial MikroTik

use RouterOS\Client;
use RouterOS\Query;

// Menggunakan kredensial dari koneksi_mikrotik.php
$client = new Client([
    'host' => MIKROTIK_IP,  // Menggunakan konstanta dari koneksi_mikrotik.php
    'user' => MIKROTIK_USER, // Menggunakan konstanta dari koneksi_mikrotik.php
    'pass' => MIKROTIK_PASS, // Menggunakan konstanta dari koneksi_mikrotik.php
]);

try {
    // Ambil data pengguna yang ada di secret (ppp/secret)
    $secretQuery = new Query('/ppp/secret/print');
    $secrets = $client->query($secretQuery)->read();

    // Ambil data pengguna yang aktif (ppp/active)
    $activeQuery = new Query('/ppp/active/print');
    $activeUsers = $client->query($activeQuery)->read();

    echo "<h1>Dashboard - Pengguna Aktif dan Tidak Aktif di MikroTik</h1>";

    // Tampilan CSS yang lebih baik
    echo '
    <style>
        body {
            background-image: url("images/mikrotik-background.jpg");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: white;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        input[type="text"], select {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        th {
            background-color: black;
            color: white;
        }
        .active {
            color: green;
            font-weight: bold;
        }
        .inactive {
            color: red;
            font-weight: bold;
        }
        .status {
            padding: 5px 10px;
            border-radius: 5px;
            text-align: center;
        }
        .status.active {
            background-color: #4CAF50;
            color: white;
        }
        .status.inactive {
            background-color: #f44336;
            color: white;
        }
        .logout-btn {
            display: block;
            width: 100px;
            margin: 20px auto;
            padding: 10px;
            text-align: center;
            background-color: #f44336;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .logout-btn:hover {
            background-color: #d32f2f;
        }
        .back-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-btn:hover {
            background-color: #0b7dda;
        }
    </style>
    ';

    // Tombol kembali
    echo '<a href="index.php" class="back-btn">Kembali</a>';

    echo '<div class="container">';
    echo '<input type="text" id="searchInput" placeholder="Cari username...">';  // Filter pencarian
    echo '<select id="statusFilter">
            <option value="">Filter Status</option>
            <option value="active">Aktif</option>
            <option value="inactive">Tidak Aktif</option>
          </select>';  // Filter status

    // Tampilkan tabel
    echo '<table id="userTable">';
    echo '<tr><th>No.</th><th>Username</th><th>IP Address</th><th>Status</th><th>Uptime</th><th>Profile</th><th>Service</th></tr>';
    
    // Membandingkan data pengguna dari ppp/secret dan ppp/active
    $counter = 1;  // Inisialisasi penghitung untuk nomor urut
    foreach ($secrets as $secret) {
        $isActive = false;
        $statusText = 'Tidak Aktif';
        $statusClass = 'inactive';
        $userIp = '';  // Menyimpan IP yang ditemukan

        // Cek apakah username di ppp/secret ada di ppp/active
        foreach ($activeUsers as $user) {
            if ($user['name'] == $secret['name']) {
                $isActive = true;
                $statusText = 'Aktif';
                $statusClass = 'active';
                $userIp = $user['address'];  // Menyimpan IP address
                break;
            }
        }

        echo '<tr class="' . $statusClass . '">';
        echo '<td>' . $counter . '</td>';
        echo '<td>' . $secret['name'] . '</td>';
        echo '<td>' . $userIp . '</td>';  // Menampilkan IP address
        echo '<td><span class="status ' . $statusClass . '">' . $statusText . '</span></td>';
        echo '<td>' . (isset($user['uptime']) ? $user['uptime'] : 'N/A') . '</td>';
        echo '<td>' . (isset($user['profile']) ? $user['profile'] : 'N/A') . '</td>';
        echo '<td>' . (isset($user['service']) ? $user['service'] : 'N/A') . '</td>';
        echo '</tr>';
        
        $counter++;  // Menambahkan 1 ke penghitung setelah setiap baris
    }

    echo '</table>';
    echo '<a href="logout.php" class="logout-btn">Logout</a>';
    echo '</div>';

} catch (\RouterOS\Exception $e) {
    echo 'Gagal menghubungkan ke MikroTik: ' . $e->getMessage();
}

// Menambahkan meta refresh setiap 30 detik
echo '<meta http-equiv="refresh" content="30">';
?>

<script>
// Menambahkan filter pencarian untuk tabel
document.getElementById("searchInput").addEventListener("input", function() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("userTable");
    tr = table.getElementsByTagName("tr");

    // Loop untuk menyaring baris tabel
    for (i = 1; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[1]; // Mencari berdasarkan kolom kedua (username)
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
});

// Menambahkan filter status untuk tabel
document.getElementById("statusFilter").addEventListener("change", function() {
    var statusFilter = this.value;
    var table = document.getElementById("userTable");
    var tr = table.getElementsByTagName("tr");

    // Loop untuk menyaring baris tabel berdasarkan kelas status (active/inactive)
    for (var i = 1; i < tr.length; i++) {
        var statusClass = tr[i].classList.contains("active") ? "active" : "inactive"; // Cek kelas status
        if (statusFilter === "" || statusClass === statusFilter) {
            tr[i].style.display = "";
        } else {
            tr[i].style.display = "none";
        }
    }
});
</script>
