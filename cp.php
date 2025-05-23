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

$maxRows_duyuru = 2;
$pageNum_duyuru = 0;
if (isset($_GET['pageNum_duyuru'])) {
  $pageNum_duyuru = $_GET['pageNum_duyuru'];
}
$startRow_duyuru = $pageNum_duyuru * $maxRows_duyuru;

mysql_select_db($database_baglan, $baglan);
$query_duyuru = "SELECT * FROM duyuru WHERE duyuru.durum = '1' ORDER BY duyuruID DESC";
$query_limit_duyuru = sprintf("%s LIMIT %d, %d", $query_duyuru, $startRow_duyuru, $maxRows_duyuru);
$duyuru = mysql_query($query_limit_duyuru, $baglan) or die(mysql_error());
$row_duyuru = mysql_fetch_assoc($duyuru);

if (isset($_GET['totalRows_duyuru'])) {
  $totalRows_duyuru = $_GET['totalRows_duyuru'];
} else {
  $all_duyuru = mysql_query($query_duyuru);
  $totalRows_duyuru = mysql_num_rows($all_duyuru);
}
$totalPages_duyuru = ceil($totalRows_duyuru/$maxRows_duyuru)-1;

mysql_select_db($database_baglan, $baglan);
$query_siteayar = "SELECT * FROM siteconfig";
$siteayar = mysql_query($query_siteayar, $baglan) or die(mysql_error());
$row_siteayar = mysql_fetch_assoc($siteayar);
$totalRows_siteayar = mysql_num_rows($siteayar);
?>




<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>    
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-130647668-1"></script>
    <script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-130647668-1');
    </script>

    <meta charset="UTF-8" />
<title><?php echo $row_siteayar['SiteTitle']; ?></title>
<meta content="<?php echo $row_siteayar['Metadesc']; ?>" name="description">
  <meta content="<?php echo $row_siteayar['MetaName']; ?>" name="keywords">

    <!-- mobile responsive meta -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" sizes="57x57" href="asset/img/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="asset/img/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="asset/img/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="asset/img/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="asset/img/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="asset/img/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="asset/img/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="asset/img/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="asset/img/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="asset/img/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="asset/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="asset/img/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="asset/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="asset/img/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" href="asset/css/styled134.css?v=3.4">
    <link rel="stylesheet" href="asset/css/responsived134.css?v=3.4">
    <link rel="stylesheet" href="asset/css/qhubi134.css?v=3.4">
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

<body class="active-preloader-ovh home-page-two">



    <div class="preloader thm-gradient-two"><div class="spinner"></div></div> <!-- /.preloader -->

    <header class="header header-home-two header-home-two-inner">
      <nav class="navbar navbar-default header-navigation stricky">
        <div class="thm-container clearfix">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed mixup-icon-menu" data-toggle="collapse" data-target=".main-navigation" aria-expanded="false"> </button>
            <a class="navbar-brand" href="index.php#anasayfa"> 						<img src="admin/resim/logo/<?php echo $row_siteayar['Sitelogo']; ?>" alt="qhubi_banner"> </a> </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse main-navigation mainmenu " id="main-nav-bar">
                    <ul class="nav navbar-nav navigation-box fr">
                        
                        <li> <a href="index.php#ozellikler"><?php echo $dil['avantaj']; ?></a> </li>
                        <li> <a href="kk.php"><?php echo $dil['sss']; ?></a></li>
                        <li> <a href="index.php#kimler-kullanmali"><?php echo $dil['kp']; ?></a> </li>
                        <li> <a href="index.php#iletisim"><?php echo $dil['iletisim']; ?></a> </li>
						<li> <a href="hakkimizda.php"><?php echo $dil['hakkimizda']; ?></a> </li>
                        <li class="btn-demo"> <a href="kayit.php" class="btn-demo"><?php echo $dil['kayit_ol']; ?></a> </li>
                        <li class="btn-login"> <a href="http://www.qhubi.com/admin"  class="btn-login"><?php echo $dil['giris']; ?></a> </li>
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
          </div>
          <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
      </nav>
    </header>
    <!-- /.header -->

    <div class="inner-banner">
        <div class="thm-container clearfix">
            <div class="pull-left">
                <h3><b>Qhubi</b>--<?php echo $dil['cp']; ?> </h3>
            </div>
        </div>
    </div>

    <section class="contact-page-content grow-style-two padding-bottom-120 gray-bg-1">
        <div class="thm-container">
            <div class="row">

                <div class="col-md-12">
       
                
                            <div>
<?php
if ($_SESSION['dil'] == "en")  {

    echo'<p><strong><span style="font-size: 14.0pt; line-height: 115%; font-family: &#39;Arial&#39;,&#39;sans-serif&#39;;">Cookies Policy</span></strong></p>
<p>&nbsp;</p>
<p>Last updated: 18.03.2019</p>
<p style="line-height: normal;">&nbsp;</p>
<p style="line-height: normal;">Qhubi ("us", "we", or "our") uses cookies on www.qhubi.com (the "Service"). By using the Service, you consent to the use of cookies.</p>
<p style="line-height: normal;">&nbsp;</p>
<p style="line-height: normal;">Our Cookies Policy explains what cookies are, how we use cookies, how third-parties we may partner with may use cookies on the Service, your choices regarding cookies and further information about cookies.</p>
<p>&nbsp;</p>
<p><strong>What are cookies</strong></p>
<p>&nbsp;</p>
<p style="line-height: normal;">Cookies are small pieces of text sent by your web browser by a website you visit. A cookie file is stored in your web browser and allows the Service or a third-party to recognize you and make your next visit easier and the Service more useful to you.</p>
<p style="line-height: normal;">&nbsp;</p>
<p style="line-height: normal;">Cookies can be "persistent" or "session" cookies.</p>
<p>&nbsp;</p>
<p><strong>How Qhubi uses cookies</strong></p>
<p>&nbsp;</p>
<p style="line-height: normal;">When you use and access the Service, we may place a number of cookies files in your web browser.</p>
<p style="line-height: normal;">&nbsp;</p>
<p style="line-height: normal;">We use cookies for the following purposes: to enable certain functions of the Service, to provide analytics, to store your preferences, to enable advertisements delivery, including behavioral advertising.</p>
<p style="line-height: normal;">&nbsp;</p>
<p style="line-height: normal;">We use both session and persistent cookies on the Service and we use different types of cookies to run the Service:</p>
<p style="line-height: normal;">&nbsp;</p>
<p style="line-height: normal;">- Essential cookies. We may use essential cookies to authenticate users and prevent fraudulent use of user accounts.</p>
<p>&nbsp;</p>
<p style="line-height: normal;"><strong>Third-party cookies</strong></p>
<p>&nbsp;</p>
<p style="line-height: normal;">In addition to our own cookies, we may also use various third-parties cookies to report usage statistics of the Service, deliver advertisements on and through the Service, and so on.</p>
<p style="line-height: normal;"><strong>&nbsp;</strong></p>
<p style="line-height: normal;"><strong>What are your choices regarding cookies</strong></p>
<p>&nbsp;</p>
<p style="line-height: normal;">If you&#39;d like to delete cookies or instruct your web browser to delete or refuse cookies, please visit the help pages of your web browser.</p>
<p style="line-height: normal;">&nbsp;</p>
<p style="line-height: normal;">Please note, however, that if you delete cookies or refuse to accept them, you might not be able to use all of the features we offer, you may not be able to store your preferences, and some of our pages might not display properly.</p>
<p>&nbsp;</p>
<p style="line-height: normal;"><strong>Where can your find more information about cookies</strong></p>
<p>&nbsp;</p>
<p style="line-height: normal;">You can learn more about cookies and the following third-party websites:</p>
<p style="line-height: normal;">&nbsp;</p>
<ul>
<li>AllAboutCookies: <a href="http://www.allaboutcookies.org/"><span style="color: #1155cc;">http://www.allaboutcookies.org/</span></a></li>
<li>Network Advertising Initiative: <a href="http://www.networkadvertising.org/"><span style="color: #1155cc;">http://www.networkadvertising.org/</span></a></li>
</ul>';
	
	} elseif ($_SESSION['dil'] == "tr")  {
	
    echo '<p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
line-height:normal"><span style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,serif;
mso-fareast-font-family:&quot;Times New Roman&quot;;mso-fareast-language:TR">Web
sitemizden en verimli şekilde faydalanabilmeniz ve kullanıcı deneyiminizi
geliştirebilmek adına çerez kullanmaktayız. İşbu Çerez Politikası, Qhubi
tarafından yürütülen web sitelerinde hangi tür çerezlerin hangi koşullarda
kullanıldığını açıklamaktadır.<o:p></o:p></span></p>

<p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
line-height:normal"><span style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,serif;
mso-fareast-font-family:&quot;Times New Roman&quot;;mso-fareast-language:TR">Çerez,
ziyaret ettiğiniz bir web sitesinin, tarayıcınızdan, bilgisayarınızda veya
mobil cihazınızda saklanmasını istediği küçük bir veri dosyasıdır.<o:p></o:p></span></p>

<p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
line-height:normal"><span style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,serif;
mso-fareast-font-family:&quot;Times New Roman&quot;;mso-fareast-language:TR">Çerezler,
türüne bağlı olarak, siteyi ziyaret ettiğiniz cihazdaki tarama ve kullanım
tercihlerinize ilişkin veriler toplanmaktadır. Bu veriler, eriştiğiniz
sayfalar, incelediğiniz hizmet ve ürünler, tercih ettiğiniz dil seçeneği ve
diğer tercihlerinize dair bilgileri kapsamaktadır. Böylece, web sitemizi bir
sonraki ziyaretinizde daha iyi ve kişiselleştirilmiş bir kullanım deneyimi
yaşayabilirsiniz.<o:p></o:p></span></p>

<p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
line-height:normal"><span style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,serif;
mso-fareast-font-family:&quot;Times New Roman&quot;;mso-fareast-language:TR">Geçerlilik
sürelerine göre kalıcı çerez ve oturum çerezi olarak iki çerez tipi
bulunmaktadır. Oturum çerezleri web sitesini ziyaret ettiğiniz esnada
oluşturulur ve tarayıcınızı kapattığınızda süresi sona erer. Kalıcı çerezler
ise web sitesindeki tercihlerinizi hatırlamak için kullanılır ve siz silinceye
veya süreleri doluncaya kadar kalır.<o:p></o:p></span></p>

<p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
line-height:normal"><span style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,serif;
mso-fareast-font-family:&quot;Times New Roman&quot;;mso-fareast-language:TR">Web sitemiz
kalıcı ve oturum tipi çerezleri, ilgi alanlarınıza ve tercihlerinize göre
düzenleme yapmak için kullanabilmektedir. Qhubi tarafından tasarlanmış
çerezlerin yanı sıra üçüncü taraflardan alınan hizmetler kapsamında da çerez
kullanılabilmektedir.<o:p></o:p></span></p>

<p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
line-height:normal"><span style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,serif;
mso-fareast-font-family:&quot;Times New Roman&quot;;mso-fareast-language:TR">Hemen hemen
tüm internet tarayıcıları, çerezleri otomatik olarak kabul edecek şekilde ön
tanımlıdır. Ancak çerezleri dilediğiniz gibi kontrol edebilir veya
silebilirsiniz. Çerezleri yönetmek, tarayıcıdan tarayıcıya farklılık
gösterdiğinden ayrıntılı bilgi almak için tarayıcınızın yardım menüsünü
inceleyebilirsiniz.<o:p></o:p></span></p>

<p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
line-height:normal"><span style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,serif;
mso-fareast-font-family:&quot;Times New Roman&quot;;mso-fareast-language:TR">Çoğu
internet tarayıcısı aşağıdakileri yapmanıza olanak tanır:<o:p></o:p></span></p>

<ul type="disc">
 <li class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
     line-height:normal;mso-list:l0 level1 lfo1;tab-stops:list 36.0pt"><span style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:
     &quot;Times New Roman&quot;;mso-fareast-language:TR">Kaydedilmiş çerezleri
     görüntüleme ve dilediklerinizi silme<o:p></o:p></span></li>
 <li class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
     line-height:normal;mso-list:l0 level1 lfo1;tab-stops:list 36.0pt"><span style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:
     &quot;Times New Roman&quot;;mso-fareast-language:TR">Üçüncü taraf çerezleri
     engelleme<o:p></o:p></span></li>
 <li class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
     line-height:normal;mso-list:l0 level1 lfo1;tab-stops:list 36.0pt"><span style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:
     &quot;Times New Roman&quot;;mso-fareast-language:TR">Belli sitelerden çerezleri
     engelleme<o:p></o:p></span></li>
 <li class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
     line-height:normal;mso-list:l0 level1 lfo1;tab-stops:list 36.0pt"><span style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:
     &quot;Times New Roman&quot;;mso-fareast-language:TR">Tüm çerezleri engelleme<o:p></o:p></span></li>
 <li class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
     line-height:normal;mso-list:l0 level1 lfo1;tab-stops:list 36.0pt"><span style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:
     &quot;Times New Roman&quot;;mso-fareast-language:TR">İnternet tarayıcısını
     kapattığınızda tüm çerezleri silme<o:p></o:p></span></li>
</ul>

<p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
line-height:normal"><span style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,serif;
mso-fareast-font-family:&quot;Times New Roman&quot;;mso-fareast-language:TR">Çerezleri
silmeyi ya da tamamen engellemeyi tercih ederseniz ilgili web sitesindeki
tercihleriniz silinecektir. Ancak bu işlem sonucunda Qhubi’e ait web siteleri
dahil olmak üzere birçok web sitesinin düzgün çalışmayabileceğini hatırlatmak
isteriz.<o:p></o:p></span></p>

<p class="MsoNormal"><o:p>&nbsp;</o:p></p>';
	
} elseif ($_SESSION['dil'] == "chn")  {

    echo '<p><strong><span style="font-size: 14.0pt; line-height: 115%; font-family: &#39;Arial&#39;,&#39;sans-serif&#39;;">Cookies Policy</span></strong></p>
<p>&nbsp;</p>
<p>Last updated: 18.03.2019</p>
<p style="line-height: normal;">&nbsp;</p>
<p style="line-height: normal;">Qhubi ("us", "we", or "our") uses cookies on www.qhubi.com (the "Service"). By using the Service, you consent to the use of cookies.</p>
<p style="line-height: normal;">&nbsp;</p>
<p style="line-height: normal;">Our Cookies Policy explains what cookies are, how we use cookies, how third-parties we may partner with may use cookies on the Service, your choices regarding cookies and further information about cookies.</p>
<p>&nbsp;</p>
<p><strong>What are cookies</strong></p>
<p>&nbsp;</p>
<p style="line-height: normal;">Cookies are small pieces of text sent by your web browser by a website you visit. A cookie file is stored in your web browser and allows the Service or a third-party to recognize you and make your next visit easier and the Service more useful to you.</p>
<p style="line-height: normal;">&nbsp;</p>
<p style="line-height: normal;">Cookies can be "persistent" or "session" cookies.</p>
<p>&nbsp;</p>
<p><strong>How Qhubi uses cookies</strong></p>
<p>&nbsp;</p>
<p style="line-height: normal;">When you use and access the Service, we may place a number of cookies files in your web browser.</p>
<p style="line-height: normal;">&nbsp;</p>
<p style="line-height: normal;">We use cookies for the following purposes: to enable certain functions of the Service, to provide analytics, to store your preferences, to enable advertisements delivery, including behavioral advertising.</p>
<p style="line-height: normal;">&nbsp;</p>
<p style="line-height: normal;">We use both session and persistent cookies on the Service and we use different types of cookies to run the Service:</p>
<p style="line-height: normal;">&nbsp;</p>
<p style="line-height: normal;">- Essential cookies. We may use essential cookies to authenticate users and prevent fraudulent use of user accounts.</p>
<p>&nbsp;</p>
<p style="line-height: normal;"><strong>Third-party cookies</strong></p>
<p>&nbsp;</p>
<p style="line-height: normal;">In addition to our own cookies, we may also use various third-parties cookies to report usage statistics of the Service, deliver advertisements on and through the Service, and so on.</p>
<p style="line-height: normal;"><strong>&nbsp;</strong></p>
<p style="line-height: normal;"><strong>What are your choices regarding cookies</strong></p>
<p>&nbsp;</p>
<p style="line-height: normal;">If you&#39;d like to delete cookies or instruct your web browser to delete or refuse cookies, please visit the help pages of your web browser.</p>
<p style="line-height: normal;">&nbsp;</p>
<p style="line-height: normal;">Please note, however, that if you delete cookies or refuse to accept them, you might not be able to use all of the features we offer, you may not be able to store your preferences, and some of our pages might not display properly.</p>
<p>&nbsp;</p>
<p style="line-height: normal;"><strong>Where can your find more information about cookies</strong></p>
<p>&nbsp;</p>
<p style="line-height: normal;">You can learn more about cookies and the following third-party websites:</p>
<p style="line-height: normal;">&nbsp;</p>
<ul>
<li>AllAboutCookies: <a href="http://www.allaboutcookies.org/"><span style="color: #1155cc;">http://www.allaboutcookies.org/</span></a></li>
<li>Network Advertising Initiative: <a href="http://www.networkadvertising.org/"><span style="color: #1155cc;">http://www.networkadvertising.org/</span></a></li>
</ul>';
	
	} else {
	
	}
?>							
							
								
								
    
</div>



    </section><!-- /.faq-style-one -->

<footer class="footer-style-two">
        <div class="footer-bottom text-center">
			 	    <div class="thm-container">
<p class="MsoNormal" align="center" style="text-align:center">
	<span style="font-size:10.0pt;line-height:107%">
		<a href="gs.php" target="_blank"><?php echo $dil['gs']; ?></a> 
		<span style="mso-spacerun:yes">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span>
		<a href="ks.php" target="_blank"><?php echo $dil['ks']; ?></a> 
		<span style="mso-spacerun:yes">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span>
		<a href="cp.php" target="_blank"><?php echo $dil['cp']; ?></a> </span></p>
						
	</div>   
            <div class="thm-container">
                <p>&copy; <a href="https://www.qhubi.com/" target="_blank">Qhubi.com <?php echo $dil['b1']; ?><?php echo $dil['34']; ?></p></a> 

            </div><!-- /.thm-container -->
        </div><!-- /.footer-bottom text-center -->

	
    </footer><!-- /.footer-style-one -->


    <div class="scroll-to-top scroll-to-target thm-gradient-two" data-target="html"><i class="fa fa-angle-up"></i></div>

    <script src="asset/js/jquery.js"></script>

    <script src="asset/js/bootstrap.min.js"></script>
    <script src="asset/js/bootstrap-select.min.js"></script>
    <script src="asset/js/jquery.validate.min.js"></script>
    <script src="asset/js/jqBootstrapValidation.js"></script>
    <script src="asset/js/owl.carousel.min.js"></script>
    <script src="asset/js/isotope.js"></script>
    <script src="asset/js/jquery.magnific-popup.min.js"></script>
    <script src="asset/js/waypoints.min.js"></script>
    <script src="asset/js/jquery.counterup.min.js"></script>
    <script src="asset/js/wow.min.js"></script>
    <script src="asset/js/jquery.easing.min.js"></script>
    <script src="asset/js/customd134.js?v=3.4"></script>
    <script src="asset/js/qhubi.js"></script>
    <script src="asset/js/demo.js"></script>
</body>


</html>