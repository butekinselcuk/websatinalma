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
$db->exec("SET NAMES 'utf8mb4'");
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

<?php


// Varsayılan dil ayarı, session'da dil ayarı yoksa yapılır.
if (!isset($_SESSION['dil'])) {
    // Kullanıcının tarayıcı dilini al
    $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    // Desteklenen diller
    $supportedLangs = ['tr', 'en', 'chn'];
    // Tarayıcı dili desteklenen diller arasında ise onu kullan, değilse varsayılan olarak Türkçe'yi seç
    $_SESSION['dil'] = in_array($browserLang, $supportedLangs) ? $browserLang : 'tr';
}

// Dilin GET parametresi üzerinden alınması ve session'a kaydedilmesi
if (isset($_GET['dil'])) {
    $izinli_diller = ['tr', 'en', 'chn']; // İzin verilen diller
    $secili_dil = $_GET['dil'];

    // Güvenli bir şekilde dil seçimi yapılıyor
    if (in_array($secili_dil, $izinli_diller)) {
        $_SESSION['dil'] = $secili_dil;
    }
}

// Seçilen dilin dosyasını include etme
include 'admin/dil/' . $_SESSION['dil'] . '.php';
$metaName = mb_convert_encoding($row_siteayar['MetaName'], 'UTF-8', mb_detect_encoding($row_siteayar['MetaName']));
?>


<!DOCTYPE html>
<html >



<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-138423389-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-138423389-1');
</script>


<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $row_siteayar['SiteTitle']; ?></title>
<meta content="<?php echo $row_siteayar['Metadesc']; ?>" name="description">

<meta name="keywords" content="<?php echo htmlspecialchars($metaName, ENT_QUOTES, 'UTF-8'); ?>">






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
    <link rel="stylesheet" type="text/css" href="asset/fonts/icomoon.css">
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-65XKEPTV30"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-65XKEPTV30');
</script>
</head>








<body class="active-preloader-ovh home-page-two">

    <div class="preloader thm-gradient-two"><div class="spinner"></div></div> <!-- /.preloader -->




    <header class="header header-home-two header-home-two-inner">
        <nav class="navbar navbar-default header-navigation stricky">
            <div class="thm-container clearfix">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed mixup-icon-menu" data-toggle="collapse" data-target=".main-navigation" aria-expanded="false"> </button>
                    <a class="navbar-brand" href="index.php#anasayfa">
						<img src="admin/resim/logo/<?php echo $row_siteayar['Sitelogo']; ?>" alt="qhubi_banner" width="245" height="90">
                    </a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse main-navigation mainmenu " id="main-nav-bar">

                    <ul class="nav navbar-nav navigation-box fr">
                        
                        <li> <a href="index.php#ozellikler"><?php echo $dil['avantaj']; ?></a> </li>
                        <li> <a href="kk.php"><?php echo $dil['sss']; ?></a></li>
                        <li> <a href="index.php#kimler-kullanmali"><?php echo $dil['kp']; ?></a> </li>
                        <li> <a href="index.php#iletisim"><?php echo $dil['iletisim']; ?></a> </li>
						<li> <a href="hakkimizda.php"><?php echo $dil['hakkimizda']; ?></a> </li>
                        <li class="btn-demo"> <a href="kayit.php" class="btn-demo"><?php echo $dil['kayit_ol']; ?></a> </li>
                        <li class="btn-login"> <a href="admin"  class="btn-login"><?php echo $dil['giris']; ?></a> </li>
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

                </div><!-- /.navbar-collapse -->
                
            </div><!-- /.container -->
        </nav>
    </header><!-- /.header -->

    <section id="anasayfa" class="banner-style-two">
        <div class="banner-style-two-bg"></div>
        <div class="thm-container max-width-100 ">

            <div class="top-banner-carousel owl-carousel owl-theme">
                <div class="item">

                    <div class="thm-container padding-none">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="banner-content">
                                    <h3><?php echo $dil['b1']; ?></h3>
                                    
                                    <p class="banner-slogan"> <?php echo $dil['azalt']; ?></p>

                                    <ul class="banner2">
                                        <li><?php echo $dil['1']; ?></li>
                                        <li><?php echo $dil['2']; ?></li>
                                        <li><?php echo $dil['3']; ?></li>
                                        <li><?php echo $dil['4']; ?></li>
                                        <li><?php echo $dil['5']; ?></li>
                                        <li><?php echo $dil['6']; ?></li>
                                    </ul>

                                  <a href="kayit.php" class="banner-btn"><?php echo $dil['kayit_ol']; ?></a>
                                </div><!-- /.banner-content -->
                            </div><!-- /.col-md-6 -->
                            <div class="col-md-6">
                                <img class="width-auto banner-img-1" src="asset/img/banner-moc-2-1.png" alt="worldpurnet.com" />
                            </div><!-- /.col-md-6 -->
                        </div><!-- /.row -->
                    </div><!-- /.thm-container -->
                </div>
                <div class="item">
                    <div class="cta-style-two cta-banner">
                        <div class="thm-container padding-none">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="cta-style-two-content">
                                        <h2><?php echo $dil['7']; ?>&#186; <br><?php echo $dil['8']; ?></h2>
                                        <p><?php echo $dil['9']; ?></p>
      
                                    </div><!-- /.cta-style-two-content -->
                                </div><!-- /.col-md-6 -->
                                <div class="col-md-6">
                                    <img src="asset/img/cta-moc-1-1.png" alt="mobil-uygulama" />
                                </div><!-- /.col-md-6 -->
                            </div><!-- /.row -->
                        </div><!-- /.thm-container -->
                    </div>
                </div>
            </div>

        </div>
    </section><!-- /.banner-style-one -->


    <section id="ozellikler" class="feature-style-two sec-pad gray-bg-2">
        <div class="thm-container">
            <div class="sec-title text-center style-two">
                <h3><?php echo $dil['10']; ?></h3>
            </div><!-- /.sec-title text-center -->
            <div class="row">
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="single-feature-style-two hvr-bounce-to-bottom">
                        <div class="icon-box">
                            <i class="icon-magic"></i>
                        </div><!-- /.icon-box -->
                        <h3><?php echo $dil['11']; ?></h3>
                        <p><?php echo $dil['12']; ?></p>
                    </div><!-- /.single-feature-style-two hvr-bounce-to-bottom -->
                </div><!-- /.col-md-4 -->
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="single-feature-style-two hvr-bounce-to-bottom">
                        <div class="icon-box">
                            <i class="icon-calendar-check-o"></i>
                        </div><!-- /.icon-box -->
                        <h3><?php echo $dil['13']; ?></h3>
                        <p><?php echo $dil['14']; ?></p>
                    </div><!-- /.single-feature-style-two hvr-bounce-to-bottom -->
                </div><!-- /.col-md-4 -->
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="single-feature-style-two hvr-bounce-to-bottom">
                        <div class="icon-box">
                            <i class="icon-calendar-plus-o"></i>
                        </div><!-- /.icon-box -->
                        <h3><?php echo $dil['15']; ?></h3>
                        <p><?php echo $dil['16']; ?></p>
                    </div><!-- /.single-feature-style-two -->
                </div><!-- /.col-md-4 -->
            </div><!-- /.row -->

            <div class="row">
                <div class="col-md-4 col-sm-6 col-xs-12 mt-1">
                    <div class="single-feature-style-two hvr-bounce-to-bottom">
                        <div class="icon-box">                            
                            <i class="icon-location2"></i>
                        </div><!-- /.icon-box -->
                        <h3><?php echo $dil['17']; ?></h3>
                        <p><?php echo $dil['18']; ?></p>
                    </div><!-- /.single-feature-style-two hvr-bounce-to-bottom -->
                </div><!-- /.col-md-4 -->
                <div class="col-md-4 col-sm-6 col-xs-12 mt-1">
                    <div class="single-feature-style-two hvr-bounce-to-bottom">
                        <div class="icon-box">
                            <i class="icon-timeline"></i>
                        </div><!-- /.icon-box -->
                        <h3><?php echo $dil['19']; ?></h3>
                        <p><?php echo $dil['20']; ?></p>
                    </div><!-- /.single-feature-style-two hvr-bounce-to-bottom -->
                </div><!-- /.col-md-4 -->
                <div class="col-md-4 col-sm-6 col-xs-12 mt-1">
                    <div class="single-feature-style-two hvr-bounce-to-bottom">
                        <div class="icon-box">
                            <i class="icon-binoculars2"></i>
                        </div><!-- /.icon-box -->
                        <h3><?php echo $dil['21']; ?></h3>
                        <p><?php echo $dil['22']; ?></p>
                    </div><!-- /.single-feature-style-two -->
                </div><!-- /.col-md-4 -->
            </div><!-- /.row -->
        </div><!-- /.thm-container -->
    </section>

    <!-- /.feature-style-two sec-pad gray-bg-2 -->

    <section class="grow-style-two sec-pad dark-gray-bg-2">
        <div class="thm-container">
            <div class="row">
                <div class="col-md-6">
                    <img src="asset/img/grow-2-1.png" alt="worldpurnet.com" />
                </div><!-- /.col-md-6 -->
                <div class="col-md-6">
                    <div class="grow-content-two">
                      <h3><?php echo $dil['23']; ?></h3>
                    </div><!-- /.grow-content-two -->
                </div><!-- /.col-md-6 -->
            </div><!-- /.row -->
        </div><!-- /.thm-container -->
    </section><!-- /.grow-style-two -->

    <section class="grow-style-two sec-pad gray-bg-2">
        <div class="thm-container">
            <div class="row">
                <div class="col-md-7 pull-right text-right">
                    <img src="asset/img/grow-2-2.png" alt="personel_servis_kullanim_orani_takibi" />
                </div><!-- /.col-md-6 -->
                <div class="col-md-5 pull-left">
                    <div class="grow-content-two">
                        <h3><?php echo $dil['24']; ?></h3>
                        <p><?php echo $dil['25']; ?></p>
                    </div><!-- /.grow-content-two -->
                </div><!-- /.col-md-6 -->
            </div><!-- /.row -->
        </div><!-- /.thm-container -->
    </section><!-- /.grow-style-two -->

    <section class="grow-style-two sec-pad dark-gray-bg-2">
        <div class="thm-container">
            <div class="row">
                <div class="col-md-8 pull-left text-left image-shadow">
                    <img src="asset/img/grow-3-1.png" alt="personel_servis_güzergah_karşilaştırma" />
                </div><!-- /.col-md-6 -->
                <div class="col-md-4 pull-right">
                    <div class="grow-content-two">

                      <h3><?php echo $dil['26']; ?></h3>

                    </div><!-- /.grow-content-two -->
                </div><!-- /.col-md-6 -->
            </div><!-- /.row -->
        </div><!-- /.thm-container -->
    </section><!-- /.grow-style-two -->

    <section class="grow-style-two sec-pad gray-bg-2">
        <div class="thm-container">
            <div class="row">
                <div class="col-md-7 pull-right text-right">
                    <img src="asset/img/grow-4-1.png" alt="personel_servis_fazla_mesai_güzergah_yönetimi" />
                </div><!-- /.col-md-6 -->
                <div class="col-md-5 pull-left">
                    <div class="grow-content-two">

                      <h3><?php echo $dil['27']; ?></h3>
                        <p><?php echo $dil['28']; ?></p>

                    </div><!-- /.grow-content-two -->
                </div><!-- /.col-md-6 -->
            </div><!-- /.row -->
        </div><!-- /.thm-container -->
    </section><!-- /.grow-style-two -->


    <section id="kimler-kullanmali" class="grow-style-two sec-pad dark-gray-bg-2">


        <div class="thm-container">
            <div class="sec-title text-center style-two">
                <h3><?php echo $dil['29']; ?></h3>
            </div><!-- /.sec-title text-center -->
            <div class="row">
                <div class="col-md-6 pull-left text-left image-shadow-2">
                    <img src="asset/img/grow-5-1.png" alt="personel_servis_güzergah_karşilaştırma" />
                </div><!-- /.col-md-6 -->
                <div class="col-md-6 pull-right">
                    <div class="grow-content-two padding-none">
                        <p><?php echo $dil['30']; ?></p>
                    </div><!-- /.grow-content-two -->
                </div><!-- /.col-md-6 -->
            </div><!-- /.row -->
            <div class="row">
                <div class="col-md-6 pull-right text-right"></div><!-- /.col-md-6 --><!-- /.col-md-6 -->
            </div><!-- /.row -->
        </div><!-- /.thm-container -->
    </section><!-- /.grow-style-two -->

    <section id="iletisim" class="feature-style-two sec-pad dark-gray-bg-2 padding-bottom-0">
        <div class="brand-section thm-gradient-two-bg">
            <div class="thm-container">
                <div class="brand-carousel owl-carousel owl-theme">
                    <div class="item">
                        <div class="referance-bayer" alt="bayer" title="Bayer İlaç"></div>
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="referance-sedeffuar" alt="sedef_fuar" title="Sedef Fuar"></div>
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="referance-vaillant" alt="vaillant" title="Vaillant Teknik Servis"></div>
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="referance-bifa" alt="bifa" title="Bifa Bisküvi Fabrikası"></div>
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="referance-avansas" alt="avansas" title="Avansas"></div>
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="referance-axasigorta" alt="avansas" title="Axa Sigorta"></div>
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="referance-basaktur" alt="basaktur" title="Başaktur"></div>
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="referance-k12" alt="k12" title="K12 Tur"></div>
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="referance-aykanlar" alt="aykanlar" title="Aykanlar"></div>
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="referance-bek" alt="bek" title="Bursa Eczacılar Kooperatifi"></div>
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="referance-reklam212" alt="reklam212" title="Reklam 212"></div>
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="referance-acikoleji" alt="aci_koleji" title="Açı Koleji Sarıyer"></div>
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="referance-berkoilac" alt="berko_ilaç" title="Berko İlaç"></div>
                    </div><!-- /.item -->
                    
                </div><!-- /.brand-carousel -->
            </div><!-- /.thm-container -->
        </div><!-- /.brand-section -->
    </section>

    
    
    <section id="basinda-biz" class="testimonials-style-two sec-pad dark-gray-bg-2">
        <div class="thm-container">
            <div class="sec-title text-center style-two">
                <h3><?php echo $dil['31']; ?></h3>
            </div><!-- /.sec-title text-center -->
            <div class="row">
              <div class="col-md-6">

                <div class="padding-top-60">
                  <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3124.5548319250593!2d27.1763036657361!3d38.45175037964182!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14b962a90d76ecff%3A0xb16364e47894b32f!2sFolkart+Towers!5e0!3m2!1str!2str!4v1546967604112" width="550" height="400" frameborder="0" style="border:0" allowfullscreen></iframe>
                </div>

              </div>
              <!-- /.col-md-6 -->
              <div class="col-md-6">
                <div class="contact-info">
                  <div class="grow-content-two padding-top-30">
                    <h4><?php echo $dil['32']; ?></h4>
	
		<style type='text/css'>     
			.e_name, .e_email, .e_email-1, .e_sub, .e_mes { display:none;}
		</style> 

		
	<body>
 		<div class="container">
	 		<form method="post" class="form-horizontal" action="send.php" id="contactForm" >
				<div class="form-group">
					<label for="name"> <?php echo $dil['isim']; ?> </label><br />
					<input type="text" id="name"   name="name" />
			 		<span class="alert alert-danger e_name">*<?php echo $dil['isim']; ?><?php echo $dil['eksik']; ?></span> 
				</div>
				<div class="form-group">
					<label for="email"><?php echo $dil['kayit_email']; ?></label><br />
					<input type="email"  id="email"  name="email" />
					<span class="alert alert-danger e_email">*<?php echo $dil['kayit_email']; ?><?php echo $dil['eksik']; ?></span>   
					<span class="alert alert-danger e_email-1">*<?php echo $dil['kayit_email']; ?><?php echo $dil['hatali']; ?></span>   
				</div>
				<div class="form-group">
					<label for="subject"><?php echo $dil['mkonu']; ?></label><br>
					<input type="text" id="subject" name="subject" />
		 			<span class="alert alert-danger e_sub">*<?php echo $dil['mkonu']; ?>&nbsp;&nbsp;<?php echo $dil['eksik']; ?>.</span>   
	 			</div>
				<div class="form-group">
					<textarea  rows="5" cols="30" id="message" name="message"></textarea>
					<span class="alert alert-danger e_mes">*<?php echo $dil['mesaj']; ?>&nbsp;&nbsp;<?php echo $dil['eksik']; ?>.</span> 
				</div>

				<div class="btn btn-info">
					<a href="javascript:gonder();"  id="btn" class="link"><center><?php echo $dil['gonder']; ?></center></a>
				</div>
			 
	   			<div id="info"></div>                             
			</form>
		</div>
											                    
		<script >
		function kapat() {
			$('#info').fadeOut(500);

		}
		function gonder() {
			
			$('.e_name').hide();
			$('.e_email').hide();
			$('.e_sub').hide();
			$('.e_mes').hide();
 		 	var name = $('#name').val();
			var email = $('#email').val();
			var subject = $('#subject').val();
			var message = $('#message').val();
 		 	name = jQuery.trim(name);
			email = jQuery.trim(email);
			subject = jQuery.trim(subject);
			message = jQuery.trim(message);
 

			if(name == "") {
				$('.e_name').fadeIn(100);
				$('#name').val(name);
			}
			if(email == "") {
				$('.e_email').fadeIn(100);
				$('#email').val(email);
			}
			if(subject == "") {
				$('.e_sub').fadeIn(100);
				$('#subject').val(subject);
			}
			if(message == "") {
				$('.e_mes').fadeIn(100);
				$('#message').val(message);
			}	
 		     function validateEmail(email) {
		          var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
		          return emailReg.test( email );
		        }
		  if( !validateEmail(email)) {
		      $('.e_email-1').fadeIn(100);
				$('#email').val(email);
		        }else{

			
			if( name == "" || email == "" || subject == "" || message == "" ) exit(); 
			
			$('#info').html('<span>Please Wait...</span>');
			$('#info').show(300);

			$.ajax( {
				type: "POST",
				url: "send.php",
				data:$('#contactForm').serialize(),
				success: function(cevap) {
					$('#info').show();
					if(cevap==''){
						$('#info').html('<span>message has been sent...</span><br /><input  value="Reset" type="reset" onClick="kapat()" />');
					}else{
						$('#info').html('<span style="color:#ff0000">There is an Error</span><br /><input  value="Close" type="reset" onClick="kapat()" />');
					}
				}
			});
		}
		}

		</script>
  		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
  	</body>
                    <!-- /.single-contact-info -->
                    <div class="single-contact-info">
                      <h4><?php echo $dil['33']; ?></h4>
<h3>info@worldpurnet.com</h3>
                      <div class="single-contact-info">
                        <h3><?php echo $dil['verme_adres']; ?></h3>
                        <h4>Sıraselviler Caddesi No 12, Taksim, Beyoğlu, 34433 - Istanbul</h4>
                        <h4>Bayraklı Folkart Tower - B Blok - Kat : 31 – İzmir</h4>
                      </div>
                      <!-- /.single-contact-info -->
                    </div>
                  </div>
                  <!-- /.contact-info -->
                </div>
                <!-- /.col-md-5 -->
              </div>
              <!-- /.row -->
            </div>
        </div>
    </section><!-- /.cta-style-two -->

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
    <script src="asset/js/owl.carousel.min.js"></script>
    <script src="asset/js/isotope.js"></script>
    <script src="asset/js/jquery.magnific-popup.min.js"></script>
    <script src="asset/js/waypoints.min.js"></script>
    <script src="asset/js/jquery.counterup.min.js"></script>
    <script src="asset/js/wow.min.js"></script>
    <script src="asset/js/jquery.easing.min.js"></script>
    <script src="asset/js/customd134.js?v=3.4"></script>

	



</body>


</html>