<?php
require_once('fonksiyon.php');

# Session başlat
session_start();

# Dil seçimi yapılmışsa
if(isset($_GET['dil'])) {
   # Dil seçimini session'a ata.
   $_SESSION['dil'] = $_GET['dil'];
   # Anasayfa'ya yönlendir.
   header("Location:kayit.php");
}

# Seçili dili kontrol ediyoruz
if(isset($_SESSION['dil'])) {
   $dil = $_SESSION['dil'];
} else {
   # Eğer dil seçilmemişse tarayıcı dilini varsayılan dil olarak seçiyoruz
   $dil = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
}

# Dil dosyamızı include ediyoruz
include 'admin/dil/'.$dil.'.php';

# Bu kod parçaçığı veritabanından kullanıcı kontrolü yapıyor.. tablo isimlerini kendinize göre düzenleyin lütfen
$hata3 = $dil['userhata3'];

if(isset($_POST['uyeAdi'])) {
    $uyeAdi = filter_input(INPUT_POST, 'uyeAdi', FILTER_SANITIZE_STRING);

    require_once("Connections/baglan.php");

    try {
        $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_check = "SELECT uyeAdi FROM {$prefix}uyeler WHERE uyeAdi=:uyeAdi";
        $stmt = $pdo->prepare($sql_check);
        $stmt->bindParam(':uyeAdi', $uyeAdi, PDO::PARAM_STR);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            echo '<span style="color: red;"><b>'.$uyeAdi.'</b> '.$hata3.'</span>';
        } else {
            echo 'OK';
        }
    } catch(PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }

    $pdo = null;
}
?>
