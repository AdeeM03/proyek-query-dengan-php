<?php
require_once "db.php";

$kartuPesanan = $pdo->query("SELECT * FROM kartupesanan")->fetchAll(PDO::FETCH_ASSOC);
$rincianBiaya = $pdo->query("SELECT * FROM rincianbiaya")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Web - Query Viewer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow p-4 mb-5">
        <h3 class="mb-4 text-center">Aplikasi Web - Query Latihan Basis Data</h3>
        <form action="query.php" method="GET">
            <div class="mb-3">
                <label class="form-label">Pilih Query:</label>
                <select name="id" class="form-select" required>
                    <option value="">-- Pilih Query --</option>
                    <?php for ($i = 1; $i <= 8; $i++): ?>
                        <option value="<?= $i ?>">Query <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Jalankan Query</button>
        </form>
    </div>

    <h4 class="mb-3 fw-bold text-success">Data from Table: kartupesanan</h4>
    <div class="card shadow-sm p-3 mb-4">
        <div class="text-end mb-3">
            <a href="query.php?action=add&table=kartupesanan" class="btn btn-success">➕ Add Data</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>NomorPesanan</th>
                        <th>JenisProduk</th>
                        <th>JmlPesanan</th>
                        <th>TglPesanan</th>
                        <th>TglSelesai</th>
                        <th>DipesanOleh</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kartuPesanan as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['NomorPesanan']) ?></td>
                        <td><?= htmlspecialchars($row['JenisProduk']) ?></td>
                        <td><?= htmlspecialchars($row['JmlPesanan']) ?></td>
                        <td><?= htmlspecialchars($row['TglPesanan']) ?></td>
                        <td><?= htmlspecialchars($row['TglSelesai']) ?></td>
                        <td><?= htmlspecialchars($row['DipesanOleh']) ?></td>
                        <td>
                            <a href="query.php?table=kartupesanan&action=edit&record_id=<?= $row['NomorPesanan'] ?>" class="btn btn-warning btn-sm">✏ Edit</a>
                            <a href="query.php?table=kartupesanan&action=delete&record_id=<?= $row['NomorPesanan'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this record?')">🗑 Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <h4 class="mb-3 fw-bold text-success">Data from Table: rincianbiaya</h4>
    <div class="card shadow-sm p-3">
        <div class="text-end mb-3">
            <a href="query.php?action=add&table=rincianbiaya" class="btn btn-success">➕ Add Data</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>NomorPesanan</th>
                        <th>Tanggal</th>
                        <th>Kelompok</th>
                        <th>SubKelompok</th>
                        <th>Jumlah</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rincianBiaya as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['NomorPesanan']) ?></td>
                        <td><?= htmlspecialchars($row['Tanggal']) ?></td>
                        <td><?= htmlspecialchars($row['Kelompok']) ?></td>
                        <td><?= htmlspecialchars($row['SubKelompok']) ?></td>
                        <td><?= htmlspecialchars($row['Jumlah']) ?></td>
                        <td>
                            <a href="query.php?table=kartupesanan&action=edit&record_id=<?= $row['NomorPesanan'] ?>" class="btn btn-warning btn-sm">✏ Edit</a>
                            <a href="query.php?table=kartupesanan&action=delete&record_id=<?= $row['NomorPesanan'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this record?')">🗑 Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
