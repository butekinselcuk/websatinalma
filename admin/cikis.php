<?php
// *** Logout the current user.
$logoutGoTo = "../login.php";

// Güvenliği artırmak için HTTP only ve secure cookie ayarları
if (!isset($_SESSION)) {
    session_start([
        'cookie_lifetime' => 86400,
        'cookie_secure' => true,
        'cookie_httponly' => true,
        'use_only_cookies' => true
    ]);
}

// Tüm session değişkenlerini temizle
$_SESSION = array();

// Oturumu sonlandır
session_destroy();

// Güvenli bir şekilde çıkış yap ve login sayfasına yönlendir
if ($logoutGoTo != "") {
    header("Location: $logoutGoTo");
    exit;
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Çıkış Yapıldı</title>
</head>
<body>
    <h1>Çıkış İşlemi Başarılı</h1>
    <p><a href="../login.php">Tekrar giriş yapmak için tıklayın.</a></p>
</body>
</html>
