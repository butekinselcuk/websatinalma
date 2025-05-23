<?php require_once('Connections/baglan.php'); ?>
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

mysql_select_db($database_baglan, $baglan);
$query_ayar = "SELECT * FROM siteconfig";
$ayar = mysql_query($query_ayar, $baglan) or die(mysql_error());
$row_ayar = mysql_fetch_assoc($ayar);
$totalRows_ayar = mysql_num_rows($ayar);
?>
<!DOCTYPE html>

<html>
<head>
  <meta charset="utf-8">
<title><?php echo $row_ayar['SiteTitle']; ?></title>
  <meta content="<?php echo $row_ayar['Metadesc']; ?>" name="description">
  <meta content="<?php echo $row_ayar['MetaName']; ?>" name="keywords">

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link href="layout/styles/layout.css" rel="stylesheet" type="text/css" media="all">
</head>

<?php
session_start();
# Session başlat
# Dil seçimi yapılmışsa
if(@$_GET['dil']) {
   # Dil seçimini session'a ata.
   $_SESSION['dil'] = $_GET['dil'];
   # Anasayfa'ya yönlendir.

}
# Seçili dili kontrol ediyoruz
if (@$_SESSION['dil'] == 'en') {
   $dil = 'en';
}
elseif (@$_SESSION['dil'] == 'tr') {
   $dil = 'tr';
}
elseif (@$_SESSION['dil'] == 'chn') {
   $dil = 'chn';
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

<body id="top">

<!-- Top Background Image Wrapper -->
<div class="bgded overlay" style="background-image:url('images/demo/1.jpg');"> 
  <!-- ################################################################################################ -->
  <div class="wrapper row1">
    <header id="header" class="hoc clear"> 
      <!-- ################################################################################################ -->
   </br> 

        <img src="admin/resim/logo/<?php echo $row_ayar['Sitelogo']; ?>" alt="QHubi.com" class="pull-left">
   
            <nav id="mainav" class="fl_right">
                         		                   <ul class="nav pull-right top-menu">
                   		<li class="dropdown">
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
      </nav>
      <!-- ################################################################################################ --> 
    </header>
  </div>
  <!-- ################################################################################################ --> 
  <!-- ################################################################################################ --> 
  <!-- ################################################################################################ -->
  <div id="pageintro" class="hoc clear">
    <article> 
      <h3>
        <!-- ################################################################################################ -->
        <strong>Qhubi</strong>
      </h3>
      <p><?php echo $dil['b1']; ?></p>
      <p><?php echo $dil['b2']; ?></p>
      <p class="font-x1 uppercase bold">&nbsp;</p>
      <footer>
        <ul class="nospace inline pushright">
          <li><a class="btn inverse" href="kayit.php"><?php echo $dil['kayit_ol']; ?></a></li>
          <li><a class="btn inverse" href="login.php"><?php echo $dil['giris_yap']; ?></a></li>
        </ul>
      </footer>

    </article>
  </div>
</div>

  <div id="ctdetails" class="hoc"> 
    <!-- ################################################################################################ -->
    <ul>
         <li class="one_third first"><i class="fa fa-phone"></i>
        <p>About Us</p>
        <p><a href="#">About Us</a></p>
      </li>
      <li class="one_third"><i class="fa fa-phone"></i>
        <p>Call us</p>
        <p>+00</p>
      </li>
      <li class="one_third"><i class="fa fa-envelope-o"></i>
        <p>Email us</p>
        <p>info@qhubi.com</p>
      </li>
    </ul>
    <!-- ################################################################################################ --> 
  </div>
</div>
<!-- ################################################################################################ --> 
<!-- ################################################################################################ --> 
<!-- ################################################################################################ -->
<!-- ################################################################################################ --> 
<!-- ################################################################################################ --> 
<!-- ################################################################################################ -->

  <div id="copyright" class="hoc clear"> 
    <!-- ################################################################################################ -->
<?php echo $row_ayar['GCode']; ?>
    <!-- ################################################################################################ --> 
</div>
<!-- ################################################################################################ --> 
<!-- ################################################################################################ --> 
<!-- ################################################################################################ --> 

<!-- JAVASCRIPTS --> 
<script src="layout/scripts/jquery.min.js"></script> 
<script src="layout/scripts/jquery.backtotop.js"></script> 
<script src="layout/scripts/jquery.mobilemenu.js"></script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-98783510-1', 'auto');
  ga('send', 'pageview');

</script>
</body>
</html>
<?php
mysql_free_result($ayar);
?>