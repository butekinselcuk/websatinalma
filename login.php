<?php
require_once('Connections/baglan.php');
require_once('fonksiyon.php');

// PDO bağlantısını oluştur
try {
    $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Veritabanı bağlantısı başarısız: " . $e->getMessage());
}

// Veritabanı bağlantısı başarılı ise işlemleri gerçekleştir
if (!function_exists("GetSQLValueString")) {
    function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
    {
        switch ($theType) {
            case "text":
                $theValue = ($theValue != "") ? $theValue : "NULL";
                break;
            case "long":
            case "int":
                $theValue = ($theValue != "") ? intval($theValue) : "NULL";
                break;
            case "double":
                $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
                break;
            case "date":
                $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
                break;
            case "defined":
                $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
                break;
        }
        return $theValue;
    }
}

$colname_hatirlat = "-1";
if (isset($_POST['HesapYoneticisiMail'])) {
    $colname_hatirlat = filter_input(INPUT_POST, 'HesapYoneticisiMail', FILTER_SANITIZE_STRING);
}
$query_hatirlat = "SELECT * FROM uyeler WHERE HesapYoneticisiMail = :HesapYoneticisiMail";
$stmt_hatirlat = $pdo->prepare($query_hatirlat);
$stmt_hatirlat->bindParam(':HesapYoneticisiMail', $colname_hatirlat, PDO::PARAM_STR);
$stmt_hatirlat->execute();
$row_hatirlat = $stmt_hatirlat->fetch(PDO::FETCH_ASSOC);
$totalRows_hatirlat = $stmt_hatirlat->rowCount();

$query_ayar = "SELECT * FROM siteconfig";
$stmt_ayar = $pdo->prepare($query_ayar);
$stmt_ayar->execute();
$row_ayar = $stmt_ayar->fetch(PDO::FETCH_ASSOC);
$totalRows_ayar = $stmt_ayar->rowCount();

function guvenlik($q) {
    $q = str_replace(",","",$q);
    $q = str_replace(";","",$q);
    $q = str_replace("'","",$q);
    $q = str_replace("`","",$q);
    $q = str_replace('"',"",$q);
    $q = str_replace("<","",$q);
    $q = str_replace(">","",$q);
    $q = str_replace("´","",$q);
    $q = str_replace("|","",$q);
    $q = str_replace("=","",$q);
    $q = str_replace(" ","",$q);
    $q = str_replace("'","",$q);
    $q = str_replace("!","",$q);
    $q=trim($q);
    return $q;
}

session_start();

$dil = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
if(isset($_GET['dil'])) {
    $_SESSION['dil'] = filter_input(INPUT_GET, 'dil', FILTER_SANITIZE_STRING);
}
if (isset($_SESSION['dil']) && ($_SESSION['dil'] == 'tr' || $_SESSION['dil'] == 'en' || $_SESSION['dil'] == 'chn')) {
    $dil = $_SESSION['dil'];
} else {
    $_SESSION['dil'] = $dil;
}

include 'admin/dil/'.$dil.'.php';

if (!isset($_SESSION)) {
    session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
    $_SESSION['PrevUrl'] = filter_input(INPUT_GET, 'accesscheck', FILTER_SANITIZE_STRING);
}

if (isset($_POST['UyeAdi'])) {
    $loginUsername = guvenlik($_POST['UyeAdi']);
    $password = $_POST['Parola'];  // Kullanıcıdan alınan ham şifre

    $stmt = $pdo->prepare("SELECT UyeAdi, Parola, SeviyeID FROM uyeler WHERE UyeAdi = :UyeAdi");
    $stmt->bindParam(':UyeAdi', $loginUsername, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($password, $row['Parola'])) {  // password_verify ile şifre kontrolü
        $_SESSION['MM_Username'] = $loginUsername;
        $_SESSION['MM_UserGroup'] = $row['SeviyeID'];
        $bilgi = '<div class="alert alert-success"><strong>'.$dil['basarili'].'</strong> '.$dil['giris_ok'].'</div>';
        header("Refresh: 1; url=admin/index.php");
        exit;
    } else {
        $bilgi = '<div class="alert alert-error"><strong>'.$dil['hata'].'</strong> '.$dil['giris_hata'].'</div>';
    }               
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $row_ayar['SiteTitle']; ?></title>
  <meta content="<?php echo $row_ayar['Metadesc']; ?>" name="description">
  <meta content="<?php echo $row_ayar['MetaName']; ?>" name="keywords">

  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="admin/dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>

<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
       <img src="admin/resim/logo/<?php echo $row_ayar['Sitelogo']; ?>" width="180" height="70" alt="QHubi.com">
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg"><?php echo $dil['kgiris']; ?></p>

    <!-- BEGIN LOGIN FORM -->
    <form METHOD="POST" name="loginform" id="loginform" action="<?php echo $loginFormAction; ?>" onSubmit="return check_frmm()">
        <div class="input-group mb-3">
            <input name="UyeAdi" type="text" id="input-username" class="form-control" placeholder="<?php echo $dil['kullaniciadi']; ?>">
            <div class="input-group-append input-group-text">
                <span class="fas fa-envelope"></span>
            </div>
        </div>

        <div class="input-group mb-3">
            <input name="Parola" type="password" id="input-password" class="form-control" placeholder="<?php echo $dil['kullanicisifre']; ?>">
            <div class="input-group-append input-group-text">
                <span class="fas fa-lock"></span>
            </div>
        </div>

        <div class="row">
            <div class="col-8">
                <div class="icheck-primary">
                    <input type="checkbox" id="remember">
                    <label for="remember"><?php echo $dil['beni_hatirla']; ?></label>
                </div>
            </div>
            <div class="col-4">
                <button type="submit" class="btn btn-primary btn-block btn-flat"><?php echo $dil['giris_yap']; ?></button>
            </div>
        </div>
    </form>
    <!-- END LOGIN FORM -->

    <ul class="nav pull-right top-menu">
        <li class="dropdown" name="dropdown-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <span class="Dil"><?php echo $dil["dil_seciniz"]; ?></span>
                <b class="caret"></b>
            </a>
            <ul class="dropdown-menu">
                <li><a href="?dil=tr"><i class="icon-user"></i> Türkçe</a></li>
                <li><a href="?dil=en"><i class="icon-user"></i> English</a></li>
                <li><a href="?dil=chn"><i class="icon-user"></i> 中國語文</a></li>
            </ul>
        </li>
    </ul>

    <p class="mb-1">
        <a href="sifre_hatirlatma.php" id="forget-password"><?php echo $dil['sifremi_unuttum']; ?></a>
    </p>
    <p class="mb-0">
        <a href="kayit.php" class="text-center"><?php echo $dil['kayit_ol']; ?></a>
    </p>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>

<?php
  if($_POST) {
      echo $bilgi;
  }
?>

<?php echo $row_ayar['GCode']; ?>

<script language="JavaScript">
    function check_frmm() {
        if (document.loginform.UyeAdi.value == ""){
            alert ("<?php echo $dil['kayit_kadi']; ?>:<?php echo $dil['eksik']; ?>");
            document.form1.email.focus();
            return false; 
        }
        if (document.loginform.Parola.value == ""){
            alert ("<?php echo $dil['kayit_sifre']; ?>:<?php echo $dil['eksik']; ?>");
            document.form1.odemekosul.focus();
            return false; 
        }
        if (document.loginform.dropdown-menu.value == ""){
            alert ("<?php echo $dil['verme_teslimsekli']; ?>:<?php echo $dil['eksik']; ?>");
            document.form1.teslimsekli.focus();
            return false; 
        }
    }
</script>

<script>
    jQuery(document).ready(function() {     
      App.initLogin();
    });
</script>

<script src="admin/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="admin/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>
