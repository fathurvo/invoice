<?php
include 'start.php'; 
include 'config.php';

// Ambil data jumlah pembayaran per tanggal
$result = $conn->query("SELECT payment_date, COUNT(*) as total FROM payments GROUP BY payment_date");

// Ambil hasil query dan simpan dalam array
$dates = [];
$counts = [];
while ($row = $result->fetch_assoc()) {
    $dates[] = $row['payment_date'];
    $counts[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grafik Pembayaran Pelanggan</title>
    <!-- Link ke Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link ke Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
            display: flex;
            justify-content: center; /* Untuk menempatkan grafik di tengah */
        }
        .card {
            border-radius: 15px;
            width: 50%; /* Ukuran grafik setengah dari lebar halaman */
        }
        canvas {
            width: 100% !important; /* Grafik menyesuaikan ukuran container */
            height: 300px !important; /* Ukuran tetap untuk grafik */
        }
        .btn-back-container {
            display: flex;
            justify-content: center; /* Untuk memusatkan tombol */
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                <h3>Grafik Pembayaran Pelanggan per Tanggal</h3>
            </div>
            <div class="card-body">
                <canvas id="paymentChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tombol Kembali yang dipusatkan -->
    <div class="btn-back-container">
        <a href="index.php" class="btn btn-secondary btn-back">Kembali</a>
          <!-- Place this where you want to display the logout button -->
     <a href="logout.php" class="btn btn-danger btn-lg">Logout</a>
    </div>
    

    <script>
        var ctx = document.getElementById('paymentChart').getContext('2d');

        // Membuat gradien untuk grafik
        var gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(75, 192, 192, 1)');
        gradient.addColorStop(1, 'rgba(153, 102, 255, 0.2)');

        // Membuat chart
        var paymentChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates); ?>, // Tanggal Pembayaran
                datasets: [{
                    label: 'Jumlah Pelanggan yang Membayar',
                    data: <?php echo json_encode($counts); ?>, // Jumlah Pembayaran per Tanggal
                    borderColor: gradient, // Warna border garis
                    backgroundColor: gradient, // Warna area bawah garis
                    borderWidth: 2,
                    fill: true, // Isi area grafik dengan warna
                    tension: 0.4 // Membuat garis lebih smooth
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            font: {
                                size: 14,
                                family: 'Arial',
                                weight: 'bold'
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            font: {
                                size: 14,
                                family: 'Arial',
                                weight: 'bold'
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top', // Menempatkan legend di atas grafik
                        labels: {
                            font: {
                                size: 16,
                                family: 'Arial',
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)', // Warna latar belakang tooltip
                        titleFont: {
                            size: 16,
                            family: 'Arial',
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 14,
                            family: 'Arial',
                            weight: 'normal'
                        },
                        callbacks: {
                            label: function(tooltipItem) {
                                return 'Pembayaran: Rp ' + tooltipItem.raw.toLocaleString(); // Format tooltip
                            }
                        }
                    }
                },
                animation: {
                    duration: 1500, // Durasi animasi (dalam milidetik)
                    easing: 'easeOutBounce' // Jenis easing animasi
                }
            }
        });
    </script>
    <!-- Bootstrap JS dan Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>


    <?php
include 'config.php';







// Ambil total pembayaran yang sudah masuk
$queryPaid = $conn->query("SELECT SUM(amount) AS total_paid FROM payments");
$rowPaid = $queryPaid->fetch_assoc();
$totalPaid = $rowPaid['total_paid'] ? $rowPaid['total_paid'] : 0;

// Ambil total pembayaran yang belum dibayar
$queryUnpaid = $conn->query("SELECT SUM(price) AS total_unpaid FROM customers WHERE status = 'Aktif'")
                ->fetch_assoc();
$totalUnpaid = $queryUnpaid['total_unpaid'] - $totalPaid;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keterangan Uang Masuk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header text-center">
                <h3>Keterangan Uang Masuk</h3>
            </div>
            <div class="card-body text-center">
                <p><strong>Sudah Dibayar:</strong> Rp <?php echo number_format($totalPaid, 0, ',', '.'); ?></p>
                <p><strong>Belum Dibayar:</strong> Rp <?php echo number_format($totalUnpaid, 0, ',', '.'); ?></p>
            </div>
        </div>
        
    </div>
   
</body>
</html>



</body>
</html>
