<?php
// Memuat autoloader Composer
require 'vendor/autoload.php'; // Pastikan autoloader dimuat dengan benar

// Memuat kredensial MikroTik
require 'koneksi_mikrotik.php'; // Memuat kredensial MikroTik
include('config.php'); // Memuat koneksi ke database MySQL

use RouterOS\Client;
use RouterOS\Query;

// Membuat koneksi ke MikroTik
$client = new Client([
    'host' => MIKROTIK_IP,  // Menggunakan konstanta dari koneksi_mikrotik.php
    'user' => MIKROTIK_USER, // Menggunakan konstanta dari koneksi_mikrotik.php
    'pass' => MIKROTIK_PASS, // Menggunakan konstanta dari koneksi_mikrotik.php
]);

// Fungsi untuk mengecek dan membuat database jika tidak ada
function createDatabaseIfNotExists($conn) {
    $sql = "CREATE DATABASE IF NOT EXISTS invoice";
    if ($conn->query($sql) === TRUE) {
        // Database sudah ada atau telah dibuat
    } else {
        die("Error membuat database: " . $conn->error);
    }

    // Menggunakan database yang telah dibuat
    if (!$conn->select_db('invoice')) {
        die("Gagal memilih database invoice: " . $conn->error);
    }
}

// Fungsi untuk mengecek dan membuat tabel jika tidak ada
function createTableIfNotExists($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS ppp_data (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        service VARCHAR(255) NOT NULL,
        profile VARCHAR(255) NOT NULL,
        local_address VARCHAR(255) NOT NULL,
        remote_address VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if ($conn->query($sql) === TRUE) {
        // Tabel sudah ada atau telah dibuat
    } else {
        die("Error membuat tabel: " . $conn->error);
    }
}

// Fungsi untuk menyimpan data ke database
function savePPPDataToDatabase($conn, $username, $password, $service, $profile, $localAddress, $remoteAddress) {
    $stmt = $conn->prepare("INSERT INTO ppp_data (username, password, service, profile, local_address, remote_address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $password, $service, $profile, $localAddress, $remoteAddress);

    if ($stmt->execute()) {
        // Data berhasil disimpan
    } else {
        echo "Error menyimpan data: " . $stmt->error . "\n";
    }

    $stmt->close();
}

// Fungsi untuk memperbarui data yang sudah ada
function updatePPPDataInDatabase($conn, $username, $password, $service, $profile, $localAddress, $remoteAddress) {
    $stmt = $conn->prepare("UPDATE ppp_data SET password=?, service=?, profile=?, local_address=?, remote_address=? WHERE username=?");
    $stmt->bind_param("ssssss", $password, $service, $profile, $localAddress, $remoteAddress, $username);

    if ($stmt->execute()) {
        // Data berhasil diperbarui
    } else {
        echo "Error memperbarui data: " . $stmt->error . "\n";
    }

    $stmt->close();
}

// Fungsi untuk menghapus data yang tidak ada di MikroTik
function deletePPPDataFromDatabase($conn, $username) {
    $stmt = $conn->prepare("DELETE FROM ppp_data WHERE username=?");
    $stmt->bind_param("s", $username);

    if ($stmt->execute()) {
        // Data berhasil dihapus
    } else {
        echo "Error menghapus data: " . $stmt->error . "\n";
    }

    $stmt->close();
}

// Fungsi untuk sinkronisasi data
function synchronizeData($conn, $secrets) {
    $existingUsers = [];
    $addedUsers = [];
    $updatedUsers = [];
    $deletedUsers = [];
    $notFoundUsers = []; // Menyimpan username yang tidak ditemukan di database

    // Ambil data yang sudah ada di database
    $result = $conn->query("SELECT username FROM ppp_data");
    while ($row = $result->fetch_assoc()) {
        $existingUsers[] = $row['username'];
    }

    // Sinkronisasi data pengguna dari PPP Secret
    foreach ($secrets as $secret) {
        $username = $secret['name'];
        $password = isset($secret['password']) ? $secret['password'] : 'N/A';
        $service = isset($secret['service']) ? $secret['service'] : 'N/A';
        $profile = isset($secret['profile']) ? $secret['profile'] : 'N/A';
        $localAddress = isset($secret['local-address']) ? $secret['local-address'] : 'N/A';
        $remoteAddress = isset($secret['remote-address']) ? $secret['remote-address'] : 'N/A';

        if (!in_array($username, $existingUsers)) {
            savePPPDataToDatabase($conn, $username, $password, $service, $profile, $localAddress, $remoteAddress);
            $addedUsers[] = $username; // Menambahkan yang baru
        } else {
            updatePPPDataInDatabase($conn, $username, $password, $service, $profile, $localAddress, $remoteAddress);
            $updatedUsers[] = $username; // Menambahkan yang diperbarui
        }

        unset($existingUsers[array_search($username, $existingUsers)]);
        
        // Cek jika username tidak ada di database
        if (!in_array($username, $existingUsers)) {
            $notFoundUsers[] = $username; // Menambahkan yang tidak ditemukan
        }
    }

    // Menghapus pengguna yang tidak ada di MikroTik
    foreach ($existingUsers as $deletedUsername) {
        deletePPPDataFromDatabase($conn, $deletedUsername);
        $deletedUsers[] = $deletedUsername; // Menambahkan yang dihapus
    }

    // Menghitung jumlah data yang tidak ditemukan
    $notFoundCount = count($notFoundUsers);

    // Menampilkan pesan jika ada username yang tidak ditemukan di database
    if ($notFoundCount > 0) {
        $message = "Ada $notFoundCount pengguna yang tidak ditemukan di database: " . implode(", ", $notFoundUsers);
        echo "<script>
            alert('$message');
            window.location.href = 'index.php';  // Arahkan ke index.php setelah pop-up
        </script>";
    } else {
        $message = "Semua pengguna sudah ada di database.";
        echo "<script>
            alert('$message');
            window.location.href = 'index.php';  // Arahkan ke index.php setelah pop-up
        </script>";
    }

    exit; // Menghentikan eksekusi script setelah pop-up dan pengalihan
}

// Mengambil data pengguna yang ada di secret (ppp/secret) 
try {
    // Query untuk mendapatkan data pengguna dari PPP Secret
    $secretQuery = new Query('/ppp/secret/print');
    $secrets = $client->query($secretQuery)->read();

    // Cek dan buat database dan tabel jika belum ada
    createDatabaseIfNotExists($conn);
    createTableIfNotExists($conn);

    // Sinkronisasi data antara MikroTik dan database
    synchronizeData($conn, $secrets);

} catch (Exception $e) {
    echo "<script>
        alert('Gagal menghubungkan ke MikroTik: " . $e->getMessage() . "');
        window.location.href = 'index.php';  // Arahkan ke index.php jika ada error
    </script>";
    exit; // Menghentikan eksekusi script jika terjadi error
}

// Menutup koneksi ke database
$conn->close();
?>
