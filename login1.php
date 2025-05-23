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
if (isSet($_POST['HesapYoneticisiMail'])) {
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
  <link href="../1 temmuz/admin/assets/bootstrap/css/bootstrap.css" rel="stylesheet">
  <link href="../1 temmuz/admin/assets/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="../1 temmuz/admin/css/style.css" rel="stylesheet">
  <link href="../1 temmuz/admin/css/style_responsive.css" rel="stylesheet">
  <link href="../1 temmuz/admin/css/style_default.css" rel="stylesheet" id="style_color">
</head>
<?php
# Session başlat
  session_start();
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
	$basarili=$dil['basarili'];
	$giris_ok=$dil['giris_ok'];

		 

    $bilgi = '	 <div class="alert alert-success">
										<strong> '.$basarili.' </strong> '.$giris_ok.'
									</div> ' ;
		 header("Refresh: 1; url= admin/index.php"); 
		 
  } else {
	$hata=$dil['hata'];
	$giris_hata=$dil['giris_hata'];
	
	  		 $bilgi = '	<div class="alert alert-error">
										<strong> '.$hata.' </strong> '.$giris_hata.'
									</div>';
									

}								
									
    }
 
?>
  <?php if(isset($_GET['Ekle'])) :?>

						<?php if($_GET['Ekle']=='EklemeBasarili') ?>	
									<div class="alert alert-success">
										<button class="close" data-dismiss="alert">×</button>
										<strong><?php echo $dil['basarili']; ?></strong> <?php echo $dil['basarieklendi']; ?>
									</div>
<?php endif 	;?>	

                        <?php if(isset($_GET['Duzenle'])) :?>

						<?php if($_GET['Duzenle']=='DuzenlemeBasarili') ?>	
									<div class="alert alert-info">
										<button class="close" data-dismiss="alert">×</button>
										<strong><?php echo $dil['basarili']; ?></strong> <?php echo $dil['basariduzenlendi']; ?>
									</div>
                                    
                                    
<?php endif 	;?>	
                        <?php if(isset($_GET['Sil'])) :?>

						<?php if($_GET['Sil']=='SilmeBasarili') ?>	

									<div class="alert alert-error">
										<button class="close" data-dismiss="alert">×</button>
										<strong><?php echo $dil['basarili']; ?></strong> <?php echo $dil['basarisilindi']; ?>
									</div>

<?php endif 	;?>	 
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
    <form METHOD="POST" name="loginform" id="loginform" class="form-vertical no-padding no-margin" action="<?php echo $loginFormAction; ?>" onSubmit="return check_frmm()">
      <div class="lock">
          <i class="icon-lock"></i>
      </div>
      <div class="control-wrap">
          <h4><?php echo $dil['kgiris']; ?></h4>
          <div class="control-group">
              <div class="controls">
                  <div class="input-prepend">
                      <span class="add-on"><i class="icon-user"></i></span><input name="UyeAdi" type="text" id="input-username" placeholder="<?php echo $dil['kullaniciadi']; ?>">
                  </div>
              </div>
          </div>
          <div class="control-group">
              <div class="controls">
                  <div class="input-prepend">
                      <span class="add-on"><i class="icon-key"></i></span><input name="Parola" type="password" id="input-password" placeholder="<?php echo $dil['kullanicisifre']; ?>">
                  </div>
                  <div class="mtop10">
                      <div class="block-hint pull-left small">
                          <input type="checkbox" id=""> <?php echo $dil['beni_hatirla']; ?>
                      </div>
                      <div class="block-hint pull-right">
                          <a href="sifre_hatirlatma.php" class="" id="forget-password"><?php echo $dil['sifremi_unuttum']; ?></a>
                      </div>
                  </div>

                  <div class="clearfix space5"></div>
              </div>

          </div>
      </div>
                         		                   <ul class="nav pull-right top-menu">
                   		<li class="dropdown" name="dropdown-menu">
                           <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                               <span class="Dil"><?php echo $dil["dil_seciniz"]; ?></span>
                               <b class="caret"></b>
                           </a>
                           <ul class="dropdown-menu" >
                               <li><a href="?dil=tr"><i class="icon-user"></i> Türkçe</a></li>
                               <li><a href="?dil=en"><i class="icon-user"></i> English</a></li>
                               <li><a href="?dil=chn"><i class="icon-user"></i> 中國語文</a></li>
                           </ul>
                       </li>
 </ul>
      <input type="submit" id="login-btn" class="btn btn-block login-btn" value="<?php echo $dil['giris_yap']; ?>">
    </form>
    <!-- END LOGIN FORM -->        
    <!-- BEGIN FORGOT PASSWORD FORM -->
  <form id="forgotform" class="form-vertical no-padding no-margin hide" action="admin/index.html">
      <p class="center">Enter your e-mail address below to reset your password.</p>
      <div class="control-group">
        <div class="controls">
          <div class="input-prepend">
            <span class="add-on"><i class="icon-envelope"></i></span><input id="HesapYoneticisiMail" name="HesapYoneticisiMail" type="text" placeholder="email">
          </div>
        </div>
        <div class="space20"></div>
      </div>
    <input type="button" id="forget-btn" class="btn btn-block login-btn" value="Submit">
  </form>

  
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
  
  <script language="JavaScript">
function check_frmm(){

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
