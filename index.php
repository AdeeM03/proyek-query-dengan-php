<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugas Basis Data - Query Viewer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow p-4">
        <h3 class="mb-4 text-center">Aplikasi Web - Query Latihan Basis Data</h3>
        <form action="query.php" method="GET">
            <div class="mb-3">
                <label class="form-label">Pilih Query:</label>
                <select name="id" class="form-select" required>
                    <option value="">-- Pilih Query --</option>
                    <option value="1">Query 1</option>
                    <option value="2">Query 2</option>
                    <option value="3">Query 3</option>
                    <option value="4">Query 4</option>
                    <option value="5">Query 5</option>
                    <option value="6">Query 6</option>
                    <option value="7">Query 7</option>
                    <option value="8">Query 8</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Jalankan Query</button>
        </form>
    </div>
</div>
</body>
</html>