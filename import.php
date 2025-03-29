<?php
include 'start.php'; 
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file'])) {
    // Ambil file CSV yang diupload
    $file = $_FILES['csv_file']['tmp_name'];

    // Buka file CSV
    if (($handle = fopen($file, 'r')) !== FALSE) {
        // Abaikan baris pertama (header)
        fgetcsv($handle);

        // Mulai proses insert ke database
        while (($data = fgetcsv($handle)) !== FALSE) {
            // Ambil data dari CSV
            $customer_name = $data[1];
            $package = $data[2];
            $price = str_replace('.', '', $data[3]); // Hapus titik pada harga
            $modem = $data[4];
            $installation_date = $data[5];
            $status = $data[6];

            // Query untuk memasukkan data ke database
            $query = "INSERT INTO customers (customer_name, package, price, modem, installation_date, status) 
                      VALUES ('$customer_name', '$package', '$price', '$modem', '$installation_date', '$status')";
            
            // Eksekusi query
            if (!$conn->query($query)) {
                echo "Error: " . $conn->error;
            }
        }
        
        // Tutup file CSV
        fclose($handle);

        // Redirect kembali ke halaman utama dengan pesan sukses
        header("Location: index.php?message=Data Pelanggan Berhasil Diimport");
        exit();
    } else {
        echo "Gagal membuka file.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Import CSV</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Import Data Pelanggan dari CSV</h2>
        
        <!-- Form untuk upload file CSV -->
        <form action="import.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="csv_file" class="form-label">Pilih File CSV</label>
                <input type="file" class="form-control" name="csv_file" id="csv_file" required>
            </div>
            <button type="submit" class="btn btn-primary">Import</button>
        </form>
    </div>
</body>
</html>
