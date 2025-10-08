<?php
try {
  $pdo = new PDO('mysql:host=localhost;dbname=kasus4', 'root', '');
  echo "Koneksi berhasil!";
} catch (PDOException $e) {
  echo "Koneksi gagal: " . $e->getMessage();
}
?>

