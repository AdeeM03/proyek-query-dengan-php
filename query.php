<?php
include 'db.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';

$queries = [
    1 => "SELECT A.NomorPesanan, JenisProduk, JmlPesanan, Kelompok, SUM(Jumlah) AS JumlahBiaya
          FROM KartuPesanan A INNER JOIN RincianBiaya B ON A.NomorPesanan = B.NomorPesanan
          GROUP BY A.NomorPesanan, JenisProduk, JmlPesanan, Kelompok
          ORDER BY A.NomorPesanan, JenisProduk, JmlPesanan, Kelompok",
    2 => "SELECT YEAR(B.Tanggal) AS Tahun, MONTH(B.Tanggal) AS Bulan, Kelompok, SUM(Jumlah) AS JumlahBiaya
          FROM KartuPesanan A INNER JOIN RincianBiaya B ON A.NomorPesanan = B.NomorPesanan
          GROUP BY YEAR(B.Tanggal), MONTH(B.Tanggal), Kelompok
          ORDER BY 1,2,3",
    3 => "SELECT JenisProduk, Kelompok, SUM(Jumlah) AS JumlahBiaya
          FROM KartuPesanan A INNER JOIN RincianBiaya B ON A.NomorPesanan = B.NomorPesanan
          GROUP BY JenisProduk, Kelompok
          ORDER BY 1,2",
    4 => "SELECT A.NomorPesanan, JenisProduk, JmlPesanan, SUM(Jumlah) AS BiayaLangsung, 
                 SUM(Jumlah) * 30/100 AS BiayaOverHead, SUM(Jumlah) * 130/100 AS TotalBiaya,
                 (SUM(Jumlah) * 130/100) / JmlPesanan AS BiayaPerUnit
          FROM KartuPesanan A INNER JOIN RincianBiaya B ON A.NomorPesanan = B.NomorPesanan
          GROUP BY A.NomorPesanan, JenisProduk, JmlPesanan
          ORDER BY A.NomorPesanan",
    5 => "SELECT SubKelompok, SUM(Jumlah) AS JumlahBiaya, COUNT(Jumlah) AS JmlPesanan,
                 AVG(Jumlah) AS Rata_Rata, MAX(Jumlah) AS MaxBiaya, MIN(Jumlah) AS MinBiaya
          FROM KartuPesanan A INNER JOIN RincianBiaya B ON A.NomorPesanan = B.NomorPesanan
          GROUP BY SubKelompok
          ORDER BY SubKelompok",
    6 => "SELECT A.NomorPesanan, JenisProduk, JmlPesanan, Kelompok, SUM(Jumlah) AS JumlahBiaya
          FROM KartuPesanan A INNER JOIN RincianBiaya B ON A.NomorPesanan = B.NomorPesanan
          WHERE JenisProduk = 'Sepatu'
          GROUP BY A.NomorPesanan, JenisProduk, JmlPesanan, Kelompok
          ORDER BY A.NomorPesanan",
    7 => "SELECT A.NomorPesanan, JenisProduk, JmlPesanan, Kelompok, SUM(Jumlah) AS JumlahBiaya
          FROM KartuPesanan A INNER JOIN RincianBiaya B ON A.NomorPesanan = B.NomorPesanan
          GROUP BY A.NomorPesanan, JenisProduk, JmlPesanan, Kelompok
          HAVING SUM(Jumlah) > 20000000
          ORDER BY A.NomorPesanan",
    8 => "SELECT B.Kelompok AS `Kelompok Biaya`, A.JenisProduk, A.NomorPesanan,
                 SUM(B.Jumlah) AS JumlahBiaya
          FROM KartuPesanan A INNER JOIN RincianBiaya B ON A.NomorPesanan = B.NomorPesanan
          GROUP BY B.Kelompok, A.JenisProduk, A.NomorPesanan
          ORDER BY SUM(B.Jumlah) DESC
          LIMIT 3"
];

if (!isset($queries[$id])) {
    die("Query tidak ditemukan.");
}

$stmt = $pdo->query($queries[$id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hasil Query <?php echo $id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <a href="index.php" class="btn btn-secondary mb-3">⬅ Kembali</a>
    <div class="card shadow p-3">
        <h4 class="mb-3 text-center">Hasil Query <?php echo $id; ?></h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <?php foreach (array_keys($rows[0]) as $col): ?>
                            <th><?= htmlspecialchars($col) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <?php foreach ($row as $value): ?>
                                <td><?= htmlspecialchars($value) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
