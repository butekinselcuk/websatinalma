<?php
$hostname = "localhost";
$database = "u1750718_kozmonotor";
$username = "root";
$password = "";
$prefix = "";
$charset = 'utf8mb4';

// PDO bağlantısını oluşturma
try {
    $pdo = new PDO("mysql:host=$hostname;dbname=$database;charset=$charset", $username, $password);
    // Hata raporlarını gösterme
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Bağlantı hatası oluşursa hatayı göster
    die("Veritabanına bağlanırken hata oluştu: " . $e->getMessage());
}
?>
