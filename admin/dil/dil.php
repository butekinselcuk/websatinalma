<?php
session_start();

// Gelen 'dil' parametresini filtreleyerek güvenlik sağla
$dil = filter_input(INPUT_GET, 'dil', FILTER_SANITIZE_STRING);

// Belirli diller arasında geçerli bir dil seçimi yapılmış mı kontrol et
if ($dil === "tr" || $dil === "en" || $dil === "chn") {
    $_SESSION["dil"] = $dil;
    header("Location: index.php");
    exit(); // Yönlendirme sonrası kodun daha fazla çalışmasını engelle
} else {
    // Geçersiz dil veya dil belirtilmemişse, ana sayfaya yönlendir
    header("Location: index.php");
    exit();
}
?>
