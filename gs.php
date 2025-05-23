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
                <h3><b>Qhubi</b>--<?php echo $dil['gs']; ?></h3>
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

    echo'<p style="line-height: normal;"><strong><span style="font-size: 24.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Privacy Policy of Qhubi</span></strong></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Qhubi operates the www.qhubi.com website, which provides the SERVICE.</span></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">This page is used to inform website visitors regarding our policies with the collection, use, and disclosure of Personal Information if anyone decided to use our Service, the Qhubi website.</span></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">If you choose to use our Service, then you agree to the collection and use of information in relation with this policy. The Personal Information that we collect are used for providing and improving the Service. We will not use or share your information with anyone except as described in this Privacy Policy.</span></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">The terms used in this Privacy Policy have the same meanings as in our Terms and Conditions, which is accessible at www.qhubi.com, unless otherwise defined in this Privacy Policy. </span></p>
<p style="line-height: normal;"><strong><span style="font-size: 18.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Information Collection and Use</span></strong></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">For a better experience while using our Service, we may require you to provide us with certain personally identifiable information, including but not limited to your name, phone number, and postal address. The information that we collect will be used to contact or identify you.</span></p>
<p style="line-height: normal;"><strong><span style="font-size: 18.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Log Data</span></strong></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">We want to inform you that whenever you visit our Service, we collect information that your browser sends to us that is called Log Data. This Log Data may include information such as your computer&rsquo;s Internet Protocol ("IP") address, browser version, pages of our Service that you visit, the time and date of your visit, the time spent on those pages, and other statistics.</span></p>
<p style="line-height: normal;"><strong><span style="font-size: 18.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Cookies</span></strong></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Cookies are files with small amount of data that is commonly used an anonymous unique identifier. These are sent to your browser from the website that you visit and are stored on your computer&rsquo;s hard drive.</span></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Our website uses these "cookies" to collection information and to improve our Service. You have the option to either accept or refuse these cookies, and know when a cookie is being sent to your computer. If you choose to refuse our cookies, you may not be able to use some portions of our Service.</span></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">&nbsp;</span></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">&nbsp;</span></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">&nbsp;</span></p>
<p style="line-height: normal;"><strong><span style="font-size: 18.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Service Providers</span></strong></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">We may employ third-party companies and individuals due to the following reasons:</span></p>
<ul>
<li style="line-height: normal; tab-stops: list 36.0pt;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">To facilitate our Service;</span></li>
<li style="line-height: normal; tab-stops: list 36.0pt;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">To provide the Service on our behalf;</span></li>
<li style="line-height: normal; tab-stops: list 36.0pt;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">To perform Service-related services; or</span></li>
<li style="line-height: normal; tab-stops: list 36.0pt;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">To assist us in analyzing how our Service is used.</span></li>
</ul>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">We want to inform our Service users that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</span></p>
<p style="line-height: normal;"><strong><span style="font-size: 18.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Security</span></strong></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and we cannot guarantee its absolute security.</span></p>
<p style="line-height: normal;"><strong><span style="font-size: 18.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Links to Other Sites</span></strong></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Our Service may contain links to other sites. If you click on a third-party link, you will be directed to that site. Note that these external sites are not operated by us. Therefore, we strongly advise you to review the Privacy Policy of these websites. We have no control over, and assume no responsibility for the content, privacy policies, or practices of any third-party sites or services.</span></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Children&rsquo;s Privacy</span></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Our Services do not address anyone under the age of 13. We do not knowingly collect personal identifiable information from children under 13. In the case we discover that a child under 13 has provided us with personal information, we immediately delete this from our servers. If you are a parent or guardian and you are aware that your child has provided us with personal information, please contact us so that we will be able to do necessary actions.</span></p>
<p style="line-height: normal;"><strong><span style="font-size: 18.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Changes to This Privacy Policy</span></strong></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">We may update our Privacy Policy from time to time. Thus, we advise you to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page. These changes are effective immediately, after they are posted on this page.</span></p>
<p style="line-height: normal;"><strong><span style="font-size: 18.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Contact Us</span></strong></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">If you have any questions or suggestions about our Privacy Policy, do not hesitate to contact us.</span></p>';
	
	} elseif ($_SESSION['dil'] == "tr")  {
	
    echo '<p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
line-height:normal"><span style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,serif;
mso-fareast-font-family:&quot;Times New Roman&quot;;mso-fareast-language:TR">Taraflar,
üçüncü taraflardan gizlenmesi gereken tüm bilgileri gizli tutacağını taahhüt
eder. Tarafların bu yükümlülüğü, işbu Sözleşme’nin sona ermesinden itibaren 5
yıl boyunca devam eder. Hukuken yanlarında bulundurmaları gereken belgeler
hariç olmak üzere, taraflar Sözleşme’nin sona ermesiyle beraber, talep üzerine
kendilerine verilmiş belgeleri gecikmeksizin iade veya imha etmeyi taahhüt
ederler. Ayrıca, taraflar işbu Sözleşme’nin konusuyla ilişkili olan
çalışanlarının ve bağlı şirketlerinin de işbu Sözleşme kapsamındaki gizlilik
yükümlülüğüne ve karşılıklı gizlilik hükmüne riayet etmelerini sağlar. Aksi
halde, taraflar karşılıklı gizliliğe dair bu hükmü ihlal eden çalışanları/bağlı
şirketleriyle beraber müştereken ve müteselsilen sorumlu olmayı kabul ederler.<o:p></o:p></span></p>

<ul type="disc">
 <li class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
     line-height:normal;mso-list:l0 level1 lfo1;tab-stops:list 36.0pt"><span style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:
     &quot;Times New Roman&quot;;mso-fareast-language:TR">Gizlilik yükümlülüğü ve gizli
     bilgilerin kullanımı hükmü aşağıda belirtilen bilgileri kapsamaz:<o:p></o:p></span></li>
 <li class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
     line-height:normal;mso-list:l0 level1 lfo1;tab-stops:list 36.0pt"><span style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:
     &quot;Times New Roman&quot;;mso-fareast-language:TR">Bilgiyi edinen tarafın ilgili
     bilgiyi karşı tarafın açıklamasından önce bildiğini ispatladığı bilgiler,<o:p></o:p></span></li>
 <li class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
     line-height:normal;mso-list:l0 level1 lfo1;tab-stops:list 36.0pt"><span style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:
     &quot;Times New Roman&quot;;mso-fareast-language:TR">Karşı tarafın hakları ihlal
     edilmeksizin, bilgiyi edinen tarafa üçüncü kişilerce sağlanan bilgiler,<o:p></o:p></span></li>
 <li class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
     line-height:normal;mso-list:l0 level1 lfo1;tab-stops:list 36.0pt"><span style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:
     &quot;Times New Roman&quot;;mso-fareast-language:TR">Bilgiyi edinen tarafın herhangi
     bir müdahalesi olmaksızın kamusal alana düşen bilgiler,<o:p></o:p></span></li>
 <li class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
     line-height:normal;mso-list:l0 level1 lfo1;tab-stops:list 36.0pt"><span style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:
     &quot;Times New Roman&quot;;mso-fareast-language:TR">Bilgiyi edinen tarafın yetkili
     hukuk gereği açıklamak zorunda olduğu bilgiler,<o:p></o:p></span></li>
 <li class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
     line-height:normal;mso-list:l0 level1 lfo1;tab-stops:list 36.0pt"><span style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:
     &quot;Times New Roman&quot;;mso-fareast-language:TR">Menkul Kıymetler Hukuku
     kuralları kapsamında kamuya sunulan bilgiler ve gerekli veya tavsiye
     niteliğindeki ürün bilgileri.<o:p></o:p></span></li>
</ul>

<p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
line-height:normal"><span style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,serif;
mso-fareast-font-family:&quot;Times New Roman&quot;;mso-fareast-language:TR">İşbu
Sözleşme kapsamında taraflar arasındaki bilgi alışverişi, işbu Sözleşme’nin
uygulanması için gerekli bilgi ile sınırlıdır. Taraflar, fiyat ve pazarlama
politikaları, kar marjı veya kullanım kapasitesi gibi rekabet açısından hassas
nitelikte olan bilgi alışverişinde bulunmamalıdırlar.<o:p></o:p></span></p>';
	
} elseif ($_SESSION['dil'] == "chn")  {

    echo '<p style="line-height: normal;"><strong><span style="font-size: 24.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Privacy Policy of Qhubi</span></strong></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Qhubi operates the www.qhubi.com website, which provides the SERVICE.</span></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">This page is used to inform website visitors regarding our policies with the collection, use, and disclosure of Personal Information if anyone decided to use our Service, the Qhubi website.</span></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">If you choose to use our Service, then you agree to the collection and use of information in relation with this policy. The Personal Information that we collect are used for providing and improving the Service. We will not use or share your information with anyone except as described in this Privacy Policy.</span></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">The terms used in this Privacy Policy have the same meanings as in our Terms and Conditions, which is accessible at www.qhubi.com, unless otherwise defined in this Privacy Policy. </span></p>
<p style="line-height: normal;"><strong><span style="font-size: 18.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Information Collection and Use</span></strong></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">For a better experience while using our Service, we may require you to provide us with certain personally identifiable information, including but not limited to your name, phone number, and postal address. The information that we collect will be used to contact or identify you.</span></p>
<p style="line-height: normal;"><strong><span style="font-size: 18.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Log Data</span></strong></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">We want to inform you that whenever you visit our Service, we collect information that your browser sends to us that is called Log Data. This Log Data may include information such as your computer&rsquo;s Internet Protocol ("IP") address, browser version, pages of our Service that you visit, the time and date of your visit, the time spent on those pages, and other statistics.</span></p>
<p style="line-height: normal;"><strong><span style="font-size: 18.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Cookies</span></strong></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Cookies are files with small amount of data that is commonly used an anonymous unique identifier. These are sent to your browser from the website that you visit and are stored on your computer&rsquo;s hard drive.</span></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Our website uses these "cookies" to collection information and to improve our Service. You have the option to either accept or refuse these cookies, and know when a cookie is being sent to your computer. If you choose to refuse our cookies, you may not be able to use some portions of our Service.</span></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">&nbsp;</span></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">&nbsp;</span></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">&nbsp;</span></p>
<p style="line-height: normal;"><strong><span style="font-size: 18.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Service Providers</span></strong></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">We may employ third-party companies and individuals due to the following reasons:</span></p>
<ul>
<li style="line-height: normal; tab-stops: list 36.0pt;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">To facilitate our Service;</span></li>
<li style="line-height: normal; tab-stops: list 36.0pt;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">To provide the Service on our behalf;</span></li>
<li style="line-height: normal; tab-stops: list 36.0pt;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">To perform Service-related services; or</span></li>
<li style="line-height: normal; tab-stops: list 36.0pt;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">To assist us in analyzing how our Service is used.</span></li>
</ul>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">We want to inform our Service users that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</span></p>
<p style="line-height: normal;"><strong><span style="font-size: 18.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Security</span></strong></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and we cannot guarantee its absolute security.</span></p>
<p style="line-height: normal;"><strong><span style="font-size: 18.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Links to Other Sites</span></strong></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Our Service may contain links to other sites. If you click on a third-party link, you will be directed to that site. Note that these external sites are not operated by us. Therefore, we strongly advise you to review the Privacy Policy of these websites. We have no control over, and assume no responsibility for the content, privacy policies, or practices of any third-party sites or services.</span></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Children&rsquo;s Privacy</span></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Our Services do not address anyone under the age of 13. We do not knowingly collect personal identifiable information from children under 13. In the case we discover that a child under 13 has provided us with personal information, we immediately delete this from our servers. If you are a parent or guardian and you are aware that your child has provided us with personal information, please contact us so that we will be able to do necessary actions.</span></p>
<p style="line-height: normal;"><strong><span style="font-size: 18.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Changes to This Privacy Policy</span></strong></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">We may update our Privacy Policy from time to time. Thus, we advise you to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page. These changes are effective immediately, after they are posted on this page.</span></p>
<p style="line-height: normal;"><strong><span style="font-size: 18.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">Contact Us</span></strong></p>
<p style="line-height: normal;"><span style="font-size: 12.0pt; font-family: &#39;Times New Roman&#39;,&#39;serif&#39;;">If you have any questions or suggestions about our Privacy Policy, do not hesitate to contact us.</span></p>';
	
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