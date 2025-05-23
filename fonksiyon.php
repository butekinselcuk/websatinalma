<?php
# filtre 
require_once("Connections/baglan.php");

function filter($txt, $seviye=1, $pdo) {
    if (is_array($txt)) {
        foreach ($txt as $key => $value) {
            $txt[$key] = filter($value, $seviye, $pdo);
        }
        return $txt;
    }

    // Veriyi temizleme
    $txt = stripslashes($txt);
    $stmt = $pdo->prepare("SELECT :txt AS safe_txt");
    $stmt->bindParam(':txt', $txt, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $txt = isset($result['safe_txt']) ? $result['safe_txt'] : $txt;

    // Diğer temizlik işlemleri
    $allow = '<br><br /><i><u><sup><sub><h1><h2><h3><h4><h5><h6><div><p><img><a><table><center><font><span>';
    if ($seviye == 3) 
        $txt = strip_tags($txt, $allow); 
    else 
        $txt = strip_tags($txt);

    $txt = htmlspecialchars($txt); 
    $txt = trim($txt);
    return $txt; 
}

// Verileri filtreleme
foreach($_GET as $var => $val) { 
    $_GET[$var] = filter($val, 1, $pdo); 
} 

foreach($_REQUEST as $var => $val) { 
    $_REQUEST[$var] = filter($val, 1, $pdo); 
} 

foreach($_POST as $var => $val) { 
    $_POST[$var] = filter($val, 1, $pdo); 
}
?>
