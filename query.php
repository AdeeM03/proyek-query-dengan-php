<?php
require_once "db.php";

$id = $_GET['id'] ?? '';
$action = $_GET['action'] ?? '';
$table = $_GET['table'] ?? '';
$record_id = $_GET['record_id'] ?? '';

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

function inputType($type) {
    $type = strtolower($type);
    if (str_contains($type, 'int') || str_contains($type, 'decimal') || str_contains($type, 'float')) {
        return 'number';
    } elseif (str_contains($type, 'date')) {
        return 'date';
    } else {
        return 'text';
    }
}

if ($action && $table) {
    $columns = [];
    $stmt = $pdo->query("DESCRIBE $table");
    while ($col = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $columns[] = $col;
    }
    $pk = $columns[0]['Field'];

    if ($action === 'add') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fields = array_column($columns, 'Field'); // include all fields
            $placeholders = implode(",", array_fill(0, count($fields), "?"));
            $sql = "INSERT INTO $table (" . implode(",", $fields) . ") VALUES ($placeholders)";
            $stmt = $pdo->prepare($sql);
            $values = [];
            foreach ($fields as $f) {
                $values[] = $_POST[$f] ?? null;
            }
            $stmt->execute($values);
            header("Location: index.php?msg=added");
            exit;
        }

        echo "<!DOCTYPE html><html lang='en'><head>
        <meta charset='UTF-8'><title>Add Record</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
        </head><body class='bg-light'><div class='container mt-4'>
        <h3 class='mb-3 text-success'>Add New Record to $table</h3>
        <form method='POST' class='border p-4 bg-white rounded shadow-sm'>";
        foreach ($columns as $col) {
            $colName = $col['Field'];
            $type = inputType($col['Type']);
            echo "<div class='mb-3'>
                    <label class='form-label'>$colName</label>
                    <input type='$type' class='form-control' name='$colName' required>
                  </div>";
        }
        echo "<button type='submit' class='btn btn-success'>Save</button>
              <a href='index.php' class='btn btn-secondary ms-2'>Cancel</a></form></div></body></html>";
        exit;
    }

    elseif ($action === 'edit' && $record_id) {
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE $pk=?");
        $stmt->execute([$record_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            header("Location: index.php?msg=notfound");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fields = array_column(array_slice($columns, 1), 'Field');
            $set = implode(",", array_map(fn($f) => "$f=?", $fields));
            $sql = "UPDATE $table SET $set WHERE $pk=?";
            $stmt = $pdo->prepare($sql);
            $values = [];
            foreach ($fields as $f) {
                $values[] = $_POST[$f] ?? null;
            }
            $values[] = $_GET['record_id']; 
            $stmt->execute($values);
            header("Location: index.php?msg=updated");
            exit;
        }

        echo "<!DOCTYPE html><html lang='en'><head>
        <meta charset='UTF-8'><title>Edit Record</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
        </head><body class='bg-light'><div class='container mt-4'>
        <h3 class='mb-3 text-warning'>Edit Record in $table</h3>
        <form method='POST' class='border p-4 bg-white rounded shadow-sm'>";
        foreach ($columns as $col) {
            $colName = $col['Field'];
            $type = inputType($col['Type']);
            $value = htmlspecialchars($row[$colName] ?? '');
            echo "<div class='mb-3'>
                    <label class='form-label'>$colName</label>
                    <input type='$type' class='form-control' name='$colName' value='$value' required>
                  </div>";
        }
        echo "<button type='submit' class='btn btn-warning'>Update</button>
              <a href='index.php' class='btn btn-secondary ms-2'>Cancel</a></form></div></body></html>";
        exit;
    }

    elseif ($action === 'delete' && $record_id) {
        $stmt = $pdo->prepare("DELETE FROM $table WHERE $pk=?");
        $stmt->execute([$record_id]);
        header("Location: index.php?msg=deleted");
        exit;
    }
}

if ($id && isset($queries[$id])) {
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
    <?php
    exit;
}

echo "<div class='alert alert-warning text-center mt-5'>⚠ No valid query or table action provided.</div>";
echo "<div class='text-center'><a href='index.php' class='btn btn-secondary mt-3'>← Back</a></div>";
?>
