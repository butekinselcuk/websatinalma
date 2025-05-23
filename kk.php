<?php
require_once('Connections/baglan.php');
require_once('fonksiyon.php');

$maxRows_duyuru = 2;
$pageNum_duyuru = 0;

if (isset($_GET['pageNum_duyuru'])) {
    $pageNum_duyuru = $_GET['pageNum_duyuru'];
}

$startRow_duyuru = $pageNum_duyuru * $maxRows_duyuru;

try {
    $db = new PDO("mysql:host={$hostname};dbname={$database}", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query_duyuru = "SELECT * FROM duyuru WHERE duyuru.durum = '1' ORDER BY duyuruID DESC LIMIT :startRow, :maxRows";
    $stmt = $db->prepare($query_duyuru);
    $stmt->bindParam(':startRow', $startRow_duyuru, PDO::PARAM_INT);
    $stmt->bindParam(':maxRows', $maxRows_duyuru, PDO::PARAM_INT);
    $stmt->execute();
    $duyuru = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $query_totalRows_duyuru = "SELECT COUNT(*) FROM duyuru WHERE duyuru.durum = '1'";
    $stmt_totalRows = $db->query($query_totalRows_duyuru);
    $totalRows_duyuru = $stmt_totalRows->fetchColumn();
    $totalPages_duyuru = ceil($totalRows_duyuru / $maxRows_duyuru) - 1;

    $query_siteayar = "SELECT * FROM siteconfig";
    $stmt_siteayar = $db->query($query_siteayar);
    $row_siteayar = $stmt_siteayar->fetch(PDO::FETCH_ASSOC);
    $totalRows_siteayar = $stmt_siteayar->rowCount();
} catch (PDOException $e) {
    echo "Bağlantı hatası: " . $e->getMessage();
}
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
            <a class="navbar-brand" href="index.php#anasayfa"> 						<img src="admin/resim/logo/<?php echo $row_siteayar['Sitelogo']; ?>" width="245" height="90 alt="qhubi_banner" > </a> </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse main-navigation mainmenu " id="main-nav-bar">
                    <ul class="nav navbar-nav navigation-box fr">
                        
                        <li> <a href="index.php#ozellikler"><?php echo $dil['avantaj']; ?></a> </li>
                        <li> <a href="kk.php"><?php echo $dil['sss']; ?></a></li>
                        <li> <a href="index.php#kimler-kullanmali"><?php echo $dil['kp']; ?></a> </li>
                        <li> <a href="index.php#iletisim"><?php echo $dil['iletisim']; ?></a> </li>
						<li> <a href="hakkimizda.php"><?php echo $dil['hakkimizda']; ?></a> </li>
                        <li class="btn-demo"> <a href="kayit.php" class="btn-demo"><?php echo $dil['kayit_ol']; ?></a> </li>
                        <li class="btn-login"> <a href="http://www.worldpurnet.com/admin"  class="btn-login"><?php echo $dil['giris']; ?></a> </li>
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
                <h3><b>worldpurnet</b> <?php echo $dil['sss']; ?></h3>
            </div>
        </div>
    </div>

    <section class="faq-style-one padding-bottom-120 padding-top-60 gray-bg-1 faq-page">
        <div class="thm-container">
            
            <div class="row">
                <div class="col-md-6">
                    <ul>
                        <li><a class="faq-link" href="#demo-ucretli-mi"><?php echo $dil['35']; ?></a></li>
                        <li><a class="faq-link" href="#guzergah-olusturmaya-baslamak-icin-hangi-tanimlamalari-yapmaliyim"><?php echo $dil['36']; ?></a></li>
                        <li><a class="faq-link" href="#planlama-nedir"><?php echo $dil['37']; ?></a></li>
                        <li><a class="faq-link" href="#personeller-nasil-tanimlanir"><?php echo $dil['1']; ?><?php echo $dil['38']; ?></a></li>
                    </ul>
                </div><!-- /.col-md-6 -->
                <div class="col-md-6">
                    <ul>
                        <li><a class="faq-link" href="#planlamaya-nasil-personel-ekleyebilirim"><?php echo $dil['39']; ?></a></li>
                        <li><a class="faq-link" href="#guzergahlari-nasil-olusturabilirim"><?php echo $dil['40']; ?></a></li>
                        <li><a class="faq-link" href="#aktivite-takibi-nedir"><?php echo $dil['41']; ?></a></li>
                        <li><a class="faq-link" href="#fazla-mesai-yonetimi-nasil-calisir"><?php echo $dil['42']; ?><b>worldpurnet</b>?</a></li>
                    </ul>
                </div><!-- /.col-md-4 -->
            </div>
            <div class="row">
                <div id="demo-ucretli-mi" class="col-md-12">
                    <div class="single-faq-style-one">
                        <div class="icon-box">
                            <i class="fa fa-question"></i>
                        </div><!-- /.icon-box -->
                        <div class="title-box">
                            <h3><?php echo $dil['35']; ?></h3>
                        </div><!-- /.title-box -->
                        <p><?php echo $dil['43']; ?></p>
                    </div><!-- /.single-faq-style-one -->
                </div><!-- /.col-md-4 -->
                <div id="guzergah-olusturmaya-baslamak-icin-hangi-tanimlamalari-yapmaliyim" class="col-md-12">
                    <div class="single-faq-style-one">
                        <div class="icon-box">
                            <i class="fa fa-question"></i>
                        </div><!-- /.icon-box -->
                        <div class="title-box">
                            <h3><?php echo $dil['36']; ?></h3>
                        </div><!-- /.title-box -->
                        <p><?php echo $dil['44']; ?></p>
                    </div><!-- /.single-faq-style-one -->
                </div><!-- /.col-md-4 -->
                <div id="planlama-nedir" class="col-md-12">
                    <div class="single-faq-style-one">
                        <div class="icon-box">
                            <i class="fa fa-question"></i>
                        </div><!-- /.icon-box -->
                        <div class="title-box">
                            <h3><?php echo $dil['37']; ?></h3>
                        </div><!-- /.title-box -->
                        <p><?php echo $dil['45']; ?>

<b><?php echo $dil['46']; ?></b>
<?php echo $dil['47']; ?></p>
                    </div><!-- /.single-faq-style-one -->
                </div><!-- /.col-md-4 -->
                <div id="personeller-nasil-tanimlanir" class="col-md-12">
                    <div class="single-faq-style-one">
                        <div class="icon-box">
                            <i class="fa fa-question"></i>
                        </div><!-- /.icon-box -->
                        <div class="title-box">
                            <h3><?php echo $dil['38']; ?></h3>
                        </div><!-- /.title-box -->
                        <p><?php echo $dil['48']; ?></p>
                    </div><!-- /.single-faq-style-one -->
                </div><!-- /.col-md-4 -->
                <div id="planlamaya-nasil-personel-ekleyebilirim" class="col-md-12">
                    <div class="single-faq-style-one">
                        <div class="icon-box">
                            <i class="fa fa-question"></i>
                        </div><!-- /.icon-box -->
                        <div class="title-box">
                            <h3><?php echo $dil['39']; ?></h3>
                        </div><!-- /.title-box -->
                        <p><?php echo $dil['49']; ?></p>
                    </div><!-- /.single-faq-style-one -->
                </div><!-- /.col-md-4 -->
                <div id="guzergahlari-nasil-olusturabilirim" class="col-md-12">
                    <div class="single-faq-style-one">
                        <div class="icon-box">
                            <i class="fa fa-question"></i>
                        </div><!-- /.icon-box -->
                        <div class="title-box">
                            <h3><?php echo $dil['40']; ?></h3>
                        </div><!-- /.title-box -->
                        <p><?php echo $dil['50']; ?><b>worldpurnet</b> <?php echo $dil['51']; ?>(<b>worldpurnet</b>  <?php echo $dil['52']; ?> <b>worldpurnet</b> <?php echo $dil['53']; ?>) </p>
                    </div><!-- /.single-faq-style-one -->
                </div><!-- /.col-md-4 -->
                <div id="aktivite-takibi-nedir" class="col-md-12">
                    <div class="single-faq-style-one">
                        <div class="icon-box">
                            <i class="fa fa-question"></i>
                        </div><!-- /.icon-box -->
                        <div class="title-box">
                            <h3><?php echo $dil['41']; ?></h3>
                        </div><!-- /.title-box -->
                        <p><?php echo $dil['54']; ?> <b>worldpurnet</b> <?php echo $dil['55']; ?></p>
                    </div><!-- /.single-faq-style-one -->
                </div><!-- /.col-md-4 -->
                <div id="fazla-mesai-yonetimi-nasil-calisir" class="col-md-12">
                    <div class="single-faq-style-one">
                        <div class="icon-box">
                            <i class="fa fa-question"></i>
                        </div><!-- /.icon-box -->
                        <div class="title-box">
                            <h3><?php echo $dil['42']; ?> <b>worldpurnet</b>?</h3>
                        </div><!-- /.title-box -->
                        <p><b>worldpurnet</b> <?php echo $dil['56']; ?> </p>
                    </div><!-- /.single-faq-style-one -->
                </div><!-- /.col-md-4 -->
            </div><!-- /.row -->
        </div><!-- /.thm-container -->
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
                <p>&copy; <a href="https://www.worldpurnet.com/" target="_blank">worldpurnet.com <?php echo $dil['b1']; ?><?php echo $dil['34']; ?></p></a> 

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