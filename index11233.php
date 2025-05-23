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
        <strong>Qhubi</strong> ile satınalma daha kolay
      </h3>
      <p>Qhubi üzerinde satınalma sürecini kendiniz yönetebilirsiniz,daha iyi fiyatlarla daha şeffaf satınalmalar yapabilirsiniz.</p>
      <p class="font-x1 uppercase bold">&nbsp;</p>
      <footer>
        <ul class="nospace inline pushright">
          <li><a class="btn inverse" href="kayit.php"><?php echo $dil['kayit_ol']; ?></a></li>
          <li><a class="btn inverse" href="login.php"><?php echo $dil['giris_yap']; ?></a></li>
        </ul>
      </footer>
      <!-- ################################################################################################ --> 
    </article>
  </div>
  <!-- ################################################################################################ --> 
</div>
<!-- End Top Background Image Wrapper --> 
<!-- ################################################################################################ --> 
<!-- ################################################################################################ --> 
<!-- ################################################################################################ -->
<div class="wrapper row3">
  <main class="hoc container clear"> 
    <!-- main body --> 
    <!-- ################################################################################################ -->

    <ul class="center">
      <li class="one_quarter">
        <article><a href="#"><i class="icon btmspace-40 fa fa-language"></i></a>
          <h4>E-İhale</h4>
          <p>Imperdiet interdum ut convallis eu sed tortor nunc auctor nec turpis quis dapibus&hellip;</p>
        </article>
      </li>
      <li class="one_quarter">
        <article><a href="#"><i class="icon btmspace-40 fa fa-adjust"></i></a>
          <h2>E-Satınalma</h2>
          <p>Integer mollis risus vel enim lobortis ornare phasellus imperdiet sapien tristique&hellip;</p>
        </article>
      </li>
      <li class="one_quarter">
        <article><a href="#"><i class="icon btmspace-40 fa fa-low-vision"></i></a>
          <h2>Tedarikçi bilgi ve performans yönetimi</h2>
          <p>Ullamcorper pulvinar lectus lacus porttitor eros non auctor orci felis in velit sed&hellip;</p>
        </article>
      </li>
    </ul>
    <!-- ################################################################################################ --> 
    <!-- / main body -->

  </main>
</div>
<!-- ################################################################################################ --> 
<!-- ################################################################################################ --> 
<!-- ################################################################################################ -->
<div class="wrapper coloured overlay bgded" style="background-image:url('images/demo/backgrounds/03.png');">
  <article class="hoc cta clear"> 
    <!-- ################################################################################################ -->
    <h6 class="three_quarter first">''Günün sözleri</h6>

    <!-- ################################################################################################ --> 
  </article>
</div>
<!-- ################################################################################################ --> 
<!-- ################################################################################################ --> 
<!-- ################################################################################################ -->
<div class="wrapper row3"></div>
<!-- ################################################################################################ --> 
<!-- ################################################################################################ --> 
<!-- ################################################################################################ -->
<div class="wrapper overlay bgded" style="background-image:url('images/demo/backgrounds/04.png');">
  <div id="ctdetails" class="hoc clear"> 
    <!-- ################################################################################################ -->
    <ul class="nospace group">
      <li class="one_third first"><i class="fa fa-map-marker"></i>
        <p>Our Location</p>
        <p><a href="#">Google Maps</a></p>
      </li>
      <li class="one_third"><i class="fa fa-phone"></i>
        <p>Call us</p>
        <p>+00 (123) 456 7890</p>
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
<div class="wrapper row4">
  <footer id="footer" class="hoc clear"> 
    <!-- ################################################################################################ -->
    <div class="one_quarter first">
      <h6 class="heading">Pcomspace</h6>
      <p>Finibus nibh sed hendrerit vivamus sit amet orci rhoncus dapibus nisl vitae dignissim.</p>
      <p>Augue sed vitae malesuada magna vivamus sit amet enim non odio eleifend ultricies velit elementum ac turpis.</p>
    </div>
    <div class="one_quarter">
      <h6 class="heading">Turpis egestas morbi</h6>
      <nav>
        <ul class="nospace">
          <li><a href="#"><i class="fa fa-lg fa-home"></i></a></li>
          <li><a href="#">About</a></li>
          <li><a href="#">Contact</a></li>
          <li><a href="#">Terms</a></li>
          <li><a href="#">Privacy</a></li>
          <li><a href="#">Cookies</a></li>
          <li><a href="#">Disclaimer</a></li>
        </ul>
      </nav>
      <ul class="faico clear">
        <li><a class="faicon-facebook" href="#"><i class="fa fa-facebook"></i></a></li>
        <li><a class="faicon-twitter" href="#"><i class="fa fa-twitter"></i></a></li>
        <li><a class="faicon-dribble" href="#"><i class="fa fa-dribbble"></i></a></li>
        <li><a class="faicon-linkedin" href="#"><i class="fa fa-linkedin"></i></a></li>
        <li><a class="faicon-google-plus" href="#"><i class="fa fa-google-plus"></i></a></li>
        <li><a class="faicon-vk" href="#"><i class="fa fa-vk"></i></a></li>
      </ul>
    </div>
    <div class="one_quarter">
      <h6 class="heading">Elit malesuada ut</h6>
      <article>
        <h2 class="nospace font-x1"><a href="#">Ac quis interdum</a></h2>
        <time class="font-xs" datetime="2045-04-06">Friday, 6<sup>th</sup> April 2045</time>
        <p>In eget nisi vestibulum eu magna rutrum convallis ex pretium varius neque ac dolor maecenas quis lectus porttitor leo sed lectus.</p>
      </article>
    </div>
    <div class="one_quarter">
      <h6 class="heading">Id tortor gravida</h6>
      <ul class="nospace linklist">
        <li><a href="#">Condimentum nullam tempor</a></li>
        <li><a href="#">Vestibulum vulputate iaculis</a></li>
        <li><a href="#">Sapien volutpat nec suscipit</a></li>
        <li><a href="#">Velit massa sed metus mauris</a></li>
      </ul>
    </div>
    <!-- ################################################################################################ --> 
  </footer>
</div>
<!-- ################################################################################################ --> 
<!-- ################################################################################################ --> 
<!-- ################################################################################################ -->
<div class="wrapper row5">
  <div id="copyright" class="hoc clear"> 
    <!-- ################################################################################################ -->
<?php echo $row_ayar['GCode']; ?>
    <!-- ################################################################################################ --> 
  </div>
</div>
<!-- ################################################################################################ --> 
<!-- ################################################################################################ --> 
<!-- ################################################################################################ --> 
<a id="backtotop" href="#top"><i class="fa fa-chevron-up"></i></a> 
<!-- JAVASCRIPTS --> 
<script src="layout/scripts/jquery.min.js"></script> 
<script src="layout/scripts/jquery.backtotop.js"></script> 
<script src="layout/scripts/jquery.mobilemenu.js"></script>
</body>
</html>
<?php
mysql_free_result($ayar);
?>