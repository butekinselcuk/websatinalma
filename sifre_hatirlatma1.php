<?php require_once('Connections/baglan.php'); ?>
<?php require_once('fonksiyon.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
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
  $colname_hatirlat = filter($_POST['HesapYoneticisiMail']);
}
mysql_select_db($database_baglan, $baglan);
$query_hatirlat = sprintf("SELECT * FROM uyeler WHERE HesapYoneticisiMail = %s", GetSQLValueString($colname_hatirlat, "text"));
$hatirlat = mysql_query($query_hatirlat, $baglan) or die(mysql_error());
$row_hatirlat = mysql_fetch_assoc($hatirlat);
$totalRows_hatirlat = mysql_num_rows($hatirlat);

mysql_select_db($database_baglan, $baglan);
$query_ayar = "SELECT * FROM siteconfig";
$ayar = mysql_query($query_ayar, $baglan) or die(mysql_error());
$row_ayar = mysql_fetch_assoc($ayar);
$totalRows_ayar = mysql_num_rows($ayar);
?>
<?php

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

?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = mysql_real_escape_string($_GET['accesscheck']);
}

if (isset($_POST['UyeAdi'])) {
  $loginUsername=guvenlik($_POST['UyeAdi']);
  $password=guvenlik(sha1(md5(sha1($_POST['Parola']))));
  $MM_fldUserAuthorization = "SeviyeID";
  $MM_redirectLoginSuccess = "admin/index.php";
  $MM_redirectLoginFailed = "login.php";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_baglan, $baglan);
  	
  $LoginRS__query=sprintf("SELECT UyeAdi, Parola, SeviyeID FROM uyeler WHERE UyeAdi=%s AND Parola=%s",
  GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $baglan) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    
    $loginStrGroup  = mysql_result($LoginRS,0,'SeviyeID');
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	    

	 

    $bilgi = '	 <div class="alert alert-success">
										<strong> BAŞARILI! </strong> Giriş yapılmıştır. yönlendiriliyorsunuz.
									</div>
		 
		 ' ;
		 header("Refresh: 1; url= admin/index.php"); 
		 
  }
  else {
	  
	  		 $bilgi = '	<div class="alert alert-error">
										<strong> HATA! </strong> Kullanıcı Adı veya Şifreniz Yanlış.
									</div>'
		 
		 ;
    
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
if(@$_GET['dil']) {
   # Dil seçimini session'a ata.
   $_SESSION['dil'] = mysql_real_escape_string($_GET['dil']);
   # Anasayfa'ya yönlendir.

}
# Seçili dili kontrol ediyoruz
if (@$_SESSION['dil'] == 'en') {
   $dil = 'en';
}
elseif (@$_SESSION['dil'] == 'tr') {
   $dil = 'tr';
}
else {
   # Eğer dil seçilmemişse tarayıcı dilini varsayılan dil olarak seçiyoruz
   $dil = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
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
           <img src="admin/resim/logo/<?php echo $row_ayar['Sitelogo']; ?>" alt="QHubi.com">
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

        <h2 class="form-signin-heading">Şifre Hatırlatma</h2>

 <div class="form-group">

        <span class="add-on"><i class="icon-envelope"></i></span><input id="HesapYoneticisiMail" name="HesapYoneticisiMail" type="text" placeholder="email">

 </div> <br>

 <button class="btn btn-lg btn-primary btn-block" type="submit">Parolayı sıfırla</button>

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
