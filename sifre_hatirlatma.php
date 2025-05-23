<?php 
require_once('Connections/baglan.php'); // PDO bağlantısı
require_once('fonksiyon.php'); 

if (!function_exists("GetSQLValueString")) {
    function GetSQLValueString($pdo, $theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
        switch ($theType) {
            case "text":
                return $theValue != "" ? $pdo->quote($theValue) : "NULL";
            case "long":
            case "int":
                return $theValue != "" ? intval($theValue) : "NULL";
            case "double":
                return $theValue != "" ? doubleval($theValue) : "NULL";
            case "date":
                return $theValue != "" ? $pdo->quote($theValue) : "NULL";
            case "defined":
                return $theValue != "" ? $theDefinedValue : $theNotDefinedValue;
        }
    }
}

session_start();

// Cleaning input to prevent SQL Injection
function guvenlik($input) {
    $search = array(",", ";", "'", "`", '"', "<", ">", "´", "|", "=", " ", "!", "'");
    return str_replace($search, '', trim($input));
}

if (isset($_POST['HesapYoneticisiMail'])) {
    $colname_hatirlat = guvenlik($_POST['HesapYoneticisiMail']);
    $stmt = $pdo->prepare("SELECT * FROM uyeler WHERE HesapYoneticisiMail = :email");
    $stmt->execute(['email' => $colname_hatirlat]);
    $row_hatirlat = $stmt->fetch();
    $totalRows_hatirlat = $stmt->rowCount();
}

$stmt = $pdo->query("SELECT * FROM siteconfig");
$row_ayar = $stmt->fetch();
$totalRows_ayar = $stmt->rowCount();

if (isset($_POST['UyeAdi'])) {
    $loginUsername = guvenlik($_POST['UyeAdi']);
    $password = guvenlik(sha1(md5(sha1($_POST['Parola']))));
    $stmt = $pdo->prepare("SELECT UyeAdi, Parola, SeviyeID FROM uyeler WHERE UyeAdi = :username AND Parola = :password");
    $stmt->execute(['username' => $loginUsername, 'password' => $password]);
    if ($stmt->rowCount() > 0) {
        $loginStrGroup = $stmt->fetch()['SeviyeID'];
        $_SESSION['MM_Username'] = $loginUsername;
        $_SESSION['MM_UserGroup'] = $loginStrGroup;
        header("Location: admin/index.php");
        exit;
    } else {
        header("Location: login.php");
        exit;
    }
}
?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['accesscheck'])) {
    $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['UyeAdi'])) {
    $loginUsername = guvenlik($_POST['UyeAdi']);
    $password = guvenlik(sha1(md5(sha1($_POST['Parola']))));
    $MM_fldUserAuthorization = "SeviyeID";
    $MM_redirectLoginSuccess = "admin/index.php";
    $MM_redirectLoginFailed = "login.php";
    $MM_redirecttoReferrer = false;

    // Hazırlanmış sorgu ile güvenli bir şekilde veritabanı sorgusu yap
    $stmt = $pdo->prepare("SELECT UyeAdi, Parola, SeviyeID FROM uyeler WHERE UyeAdi = :username AND Parola = :password");
    $stmt->execute(['username' => $loginUsername, 'password' => $password]);
    $user = $stmt->fetch();

    if ($user) {
        if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
        $_SESSION['MM_Username'] = $loginUsername;
        $_SESSION['MM_UserGroup'] = $user['SeviyeID'];

        echo '<div class="alert alert-success"><strong>BAŞARILI!</strong> Giriş yapılmıştır. yönlendiriliyorsunuz.</div>';
        header("Refresh: 1; url=$MM_redirectLoginSuccess");
        exit;
    } else {
        echo '<div class="alert alert-error"><strong>HATA!</strong> Kullanıcı Adı veya Şifreniz Yanlış.</div>';
        header("Refresh: 1; url=$MM_redirectLoginFailed");
        exit;
    }
}


?>



<!DOCTYPE html>


<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!-- BEGIN HEAD -->
<head>
  <meta charset="utf-8">
  <title><?php echo $row_ayar['SiteTitle']; ?></title>
  <meta content="<?php echo $row_ayar['Metadesc']; ?>" name="description">
  <meta content="<?php echo $row_ayar['MetaName']; ?>" name="keywords">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="" name="author">
  <link href="admin/assets/bootstrap/css/bootstrap.css" rel="stylesheet">
  <link href="admin/assets/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="admin/css/style.css" rel="stylesheet">
  <link href="admin/css/style_responsive.css" rel="stylesheet">
  <link href="admin/css/style_default.css" rel="stylesheet" id="style_color">
</head>
<?php

# Session başlat

# Dil seçimi yapılmışsa

$dil = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
if(@$_GET['dil']) {
   # Dil seçimini session'a ata.
   $_SESSION['dil'] = mysql_real_escape_string($_GET['dil']);
   # Anasayfa'ya yönlendir.
}
# Seçili dili kontrol ediyoruz
if (@$_SESSION['dil'] == 'tr') {
   $dil = 'tr';
}
elseif (@$_SESSION['dil'] == 'en') {
   $dil = 'en';
}

elseif (@$_SESSION['dil'] == 'chn') {
   $dil = 'chn';
}

else {
   # Eğer dil seçilmemişse tarayıcı dilini varsayılan dil olarak seçiyoruz
  $_SESSION['dil'] = $dil;
}
# Dil dosyamızı include ediyoruz
include 'admin/dil/'.$dil.'.php';
  	$basarili=$dil['basarili'];
	$hata=$dil['hata'];
	$giris_ok=$dil['giris_ok'];
	$giris_hata=$dil['giris_hata'];

?>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body id="login-body">
  <div class="login-header">
      <!-- BEGIN LOGO -->
      <div id="logo" class="center">
           <img src="admin/resim/logo/<?php echo $row_ayar['Sitelogo']; ?>" width="145" height="50" alt="worldpurnet.com">
      </div>
      <!-- END LOGO -->
  </div>

  <!-- BEGIN LOGIN -->
<div id="login">

    <!-- BEGIN LOGIN FORM -->
  
    <!-- END LOGIN FORM -->        
    <!-- BEGIN FORGOT PASSWORD FORM -->
 <div class="row text-center">

              <form class="form-signin col-md-6 col-md-offset-3" role="form" method="post" action="sifre_sifirlama.php">

        <h2 class="form-signin-heading"><?php echo $dil['sifrehatirlat']; ?></h2>

 <div class="form-group">

        <span class="add-on"><i class="icon-envelope"></i></span><input id="HesapYoneticisiMail" name="HesapYoneticisiMail" type="text" placeholder="email">

 </div> <br>

 <button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo $dil['sifrehsifirla']; ?></button>

      </form> 

          </div>

  
<?php
  if($_POST)
  {	echo  $bilgi;}
  ?>
    <!-- END FORGOT PASSWORD FORM -->
  </div>
  
  
  <!-- END LOGIN -->
  <!-- BEGIN COPYRIGHT -->
  <div id="login-copyright">
      <?php echo $row_ayar['GCode']; ?>
  </div>
  <!-- END COPYRIGHT -->
  <!-- BEGIN JAVASCRIPTS -->
<script src="admin/js/jquery-1.8.3.js"></script>
<script src="admin/assets/bootstrap/js/bootstrap.js"></script>
<script src="admin/js/jquery.js"></script>
<script src="admin/js/scripts.js"></script>
<script>
    jQuery(document).ready(function() {     
      App.initLogin();
    });
  </script>
  <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>
<?php
mysql_free_result($hatirlat);

mysql_free_result($ayar);
?>
