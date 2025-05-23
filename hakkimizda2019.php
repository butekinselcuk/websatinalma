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
<html><!-- InstanceBegin template="/Templates/index.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<!-- InstanceBeginEditable name="doctitle" -->
<title><?php echo $row_siteayar['SiteTitle']; ?></title>
<!-- InstanceEndEditable -->
<meta content="<?php echo $row_siteayar['Metadesc']; ?>" name="description">
  <meta content="<?php echo $row_siteayar['MetaName']; ?>" name="keywords">

	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"/>
	
	<link rel="shortcut icon" href="/favicon.ico"/>

	
	<link rel="stylesheet" type="text/css" href="3dParty/bootstrap/css/bootstrap.min.css"/>
	
	<link rel="stylesheet" type="text/css" href="css/global.css"/>
	
	<link rel="stylesheet" type="text/css" href="3dParty/rs-plugin/css/pi.settings.css"/>
	
	<link rel="stylesheet" type="text/css" href="css/typo.css"/>
	
	<link rel="stylesheet" type="text/css" href="3dParty/colorbox/colorbox.css"/>
	
	<link rel="stylesheet" type="text/css" href="css/portfolio.css"/>
	
	<link rel="stylesheet" type="text/css" href="css/slider.css"/>
	
	<link rel="stylesheet" type="text/css" href="css/counters.css"/>
	
	<link rel="stylesheet" type="text/css" href="css/social.css"/>
	

	<!--Fonts-->
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&amp;subset=latin,cyrillic'
		  rel='stylesheet' type='text/css'/>

	<!--Fonts with Icons-->
	<link rel="stylesheet" href="3dParty/fontello/css/fontello.css"/>
	<!-- InstanceBeginEditable name="head" -->
	<!-- InstanceEndEditable -->
</head>
<body>
<!-- InstanceBeginEditable name="sayfa" -->
<div id="pi-all">
  <!-- Header -->
  <div class="pi-header">
    <!-- Header row -->
    <div class="pi-section-w pi-section-dark">
      <div class="pi-section pi-row-sm">
        <!-- Phone -->
        <div class="pi-row-block pi-row-block-txt"> <i class="pi-row-block-icon icon-phone pi-icon-base pi-icon-square"></i>Call Us: <strong><?php echo $row_siteayar['tel']; ?> </strong> </div>
        <!-- End phone -->
        
        
        <!-- Email -->
        <div class="pi-row-block pi-row-block-txt pi-hidden-xs"><i
				class="pi-row-block-icon icon-mail pi-icon-base pi-icon-square"></i>Email: <a
				href="#"><?php echo $row_siteayar['mail']; ?></a> </div>
        <!-- End email -->
        <!-- Social icons -->
        <div class="pi-row-block pi-pull-right pi-hidden-2xs">
          <ul class="pi-social-icons pi-stacked pi-jump pi-full-height pi-bordered pi-small pi-colored-bg clearFix">
            <li><a href="https://twitter.com/<?php echo $row_siteayar['twitter']; ?>" class="pi-social-icon-twitter"><i class="icon-twitter"></i></a></li>
            <li><a href="https://www.facebook.com/<?php echo $row_siteayar['facebook']; ?>" class="pi-social-icon-facebook"><i class="icon-facebook"></i></a></li>
            <li><a href="https://dribbble.com/qhubi"" class="pi-social-icon-dribbble"><i class="icon-dribbble"></i></a></li>
          </ul>
        </div>
        <!-- End social icons -->
        <!-- Text -->
 
        <div class="pi-row-block pi-row-block-txt pi-pull-right pi-hidden-xs">Takip Et:</div>
<div class="pi-row-block pi-row-block-txt pi-pull-right pi-hidden-xs"><a href="admin/">Giriş</a></div>
        <!-- End text -->
      </div>
    </div>
    <!-- End header row -->
    <div class="pi-header-sticky">
      <!-- Header row -->
      <div class="pi-section-w pi-section-white pi-shadow-bottom pi-row-reducible">
        <div class="pi-section pi-row-lg">
          <!-- Logo -->
          <div class="pi-row-block pi-row-block-logo"> <a href="index.php"><img src="img/logo-base.png" alt=""></a> </div>
          <!-- End logo -->
          <!-- Text -->
          <div class="pi-row-block pi-row-block-txt pi-hidden-2xs">International Quotation Hub</div>
          <!-- End text -->
          <!-- Menu -->
          <div class="pi-row-block pi-pull-right">
            <ul class="pi-simple-menu pi-has-hover-border pi-full-height pi-hidden-sm">
              <li class="active"><a href="index.php"><span>Ana Sayfa</span></a> </li>
              <li><a href="referanslar.php"><span>Referanslar</span></a> </li>
              <li><a href="sss.php"><span>SSS</span></a> </li>
              <li><a href="hakkimizda.php"><span>Hakkımızda</span></a> </li>
              <li><a href="iletisim.php"><span>İletişim</span></a> </li>
            </ul>
          </div>
          <!-- End menu -->
          <!-- Mobile menu button -->
          <div class="pi-row-block pi-pull-right pi-hidden-lg-only pi-hidden-md-only">
            <button class="btn pi-btn pi-mobile-menu-toggler" data-target="#pi-main-mobile-menu"> <i class="icon-menu pi-text-center"></i> </button>
          </div>
          <!-- End mobile menu button -->
          <!-- Mobile menu -->
          <div id="pi-main-mobile-menu" class="pi-section-menu-mobile-w pi-section-dark">
            <div class="pi-section-menu-mobile">
              <!-- Search form -->
              <form class="form-inline pi-search-form-wide ng-pristine ng-valid" role="form">
                <div class="pi-input-with-icon">
                  <div class="pi-input-icon"><i class="icon-search-1"></i></div>
                  <input type="text" class="form-control pi-input-wide" placeholder="Search..">
                </div>
              </form>
              <!-- End search form -->
              <ul class="pi-menu-mobile pi-items-have-borders pi-menu-mobile-dark">
                <li class="active"><a href="index.php"><span>Ana Sayfa</span></a> </li>
                <li><a href="referanslar.php"><span>Referanslar</span></a> </li>
                <li><a href="sss.php"><span>SSS</span></a> </li>
                <li><a href="hakkimizda.php"><span>Hakkımızda</span></a> </li>
                <li><a href="iletisim.php"><span>İletişim</span></a> </li>
              </ul>
            </div>
          </div>
          <!-- End mobile menu -->
        </div>
      </div>
      <!-- End header row -->
    </div>
  </div>
  <!-- End header -->
<!-- - - - - - - - - - SECTION - - - - - - - - - -->

<div class="pi-section-w pi-section-white pi-slider-enabled">
	<div class="pi-section pi-padding-bottom-20">
		
		<p class="lead-20 pi-uppercase pi-weight-700 pi-text-dark">
			------------------------------------------------------------------------------------------------------------------------------------------
		</p>
		<p class="lead-24">
			<span class="pi-dropcap">Q</span>hubi.com  2016 yılında kendi sektöründe gelişmekte olan şirketlere satın alma çözümleri sunmak amacıyla oluşturulmuştur. Uluslararası ün kazanmış bir firmada uzun yıllar satın alma tecrübesi elde etmiş 3 girişimci tarafından proje olarak hayata geçirilmiş kısa sürede şeffaf ve dinamik bir satın alma aracı haline gelmiştir. Her geçen gün kullanıcılarının dinamik pazarlarda hızlı bir şekilde doğru fiyatlara ulaşması için hizmet vermektedir.
		</p>
		
		<hr class="pi-divider pi-divider-dashed pi-divider-bigger">
		

		
		<!-- Row -->
		<div class="pi-row">
			
		

			<div class="pi-clearfix pi-visible-xs"></div>

		

			<div class="pi-clearfix pi-visible-xs"></div>

	
		
	</div>
</div>

<!-- - - - - - - - - - END SECTION - - - - - - - - - -->
  
  <!-- Footer -->
  <!-- Widget area -->
  <div class="pi-section-w pi-border-bottom pi-border-top-light pi-section-dark">
    <div class="pi-section pi-padding-bottom-10">
      <!-- Row -->
      <div class="pi-row">
        <!-- Col 4 -->
        <div class="pi-col-md-4 pi-padding-bottom-30">
          <h6 class="pi-margin-bottom-25 pi-weight-700 pi-uppercase pi-letter-spacing"> <a href="#" class="pi-link-no-style">SON Tweet</a> </h6>
          <!-- Twitter -->
          <div class="pi-footer-tweets">
              <a class="twitter-timeline" href="https://twitter.com/qhubicom?ref_src=twsrc%5Etfw">Tweets by qhubicom</a> <script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>

          </div>
          <!-- End twitter -->
        </div>
        <!-- End col 4 -->
        <div class="pi-clearfix pi-hidden-lg-only pi-hidden-md-only"></div>
        <!-- Col 4 -->
        <div class="pi-col-md-4 pi-col-sm-6 pi-padding-bottom-30" style="background-image: url('img/map-base.png'); background-position: 50% 55px; background-repeat: no-repeat;">
          <h6 class="pi-margin-bottom-25 pi-weight-700 pi-uppercase pi-letter-spacing"> Contact Us </h6>
          <!-- Contact info -->
          <ul class="pi-list-with-icons pi-list-big-margins">
            <li> <span class="pi-bullet-icon"><i class="icon-location"></i></span> <strong>Address:</strong><?php echo $row_siteayar['adres']; ?> </li>
            <li> <span class="pi-bullet-icon"><i class="icon-phone"></i></span> <strong>Phone:</strong><?php echo $row_siteayar['tel']; ?> </li>
            <li> <span class="pi-bullet-icon"><i class="icon-mail"></i></span> <strong>Email:</strong> <a href="<?php echo $row_siteayar['mail']; ?>"><?php echo $row_siteayar['mail']; ?></a> </li>
            <li> <span class="pi-bullet-icon"><i class="icon-clock"></i></span> Monday - Sunday: <strong>7/24</strong></li>
          </ul>
          <!-- End contact info -->
        </div>
        <!-- End col 4 -->
        <!-- Col 4 -->
        <div class="pi-col-md-4 pi-col-sm-6 pi-padding-bottom-30">
          <h6 class="pi-margin-bottom-25 pi-weight-700 pi-uppercase pi-letter-spacing"> Say Hey </h6>
          <!-- Contact form -->
          <form role="form" action="handlers/formContact.php" data-captcha="no" class="pi-contact-form">
            <div class="pi-error-container"></div>
            <div class="pi-row pi-grid-small-margins">
              <div class="pi-col-2xs-6">
                <div class="form-group">
                  <div class="pi-input-with-icon">
                    <div class="pi-input-icon"><i class="icon-user"></i></div>
                    <input class="form-control form-control-name" id="exampleInputName"
										   placeholder="Name">
                  </div>
                </div>
              </div>
              <div class="pi-col-2xs-6">
                <div class="form-group">
                  <div class="pi-input-with-icon">
                    <div class="pi-input-icon"><i class="icon-mail"></i></div>
                    <input type="email" class="form-control form-control-email" id="exampleInputEmail"
										   placeholder="Email">
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="pi-input-with-icon">
                <div class="pi-input-icon"><i class="icon-pencil"></i></div>
                <textarea class="form-control form-control-comments" id="exampleInputMessage"
									  placeholder="Message"
									  rows="3"></textarea>
              </div>
            </div>
            <p>
              <button type="submit" class="btn pi-btn-base pi-btn-no-border">Send</button>
            </p>
          </form>
          <!-- End contact form -->
        </div>
        <!-- End col 4 -->
      </div>
      <!-- End row -->
    </div>
  </div>
  <!-- End widget area -->
  <!-- Copyright area -->
  <div class="pi-section-w pi-section-dark pi-border-top-light pi-border-bottom-strong-base">
    <div class="pi-section pi-row-lg pi-center-text-2xs pi-clearfix">
      <!-- Social icons -->
      <div class="pi-row-block pi-pull-right pi-hidden-2xs">
        <ul class="pi-social-icons-simple pi-small clearFix">
          <li><a href="https://www.facebook.com/<?php echo $row_siteayar['facebook']; ?>" class="pi-social-icon-facebook"><i class="icon-facebook"></i></a></li>
          <li><a href="https://twitter.com/<?php echo $row_siteayar['twitter']; ?>" class="pi-social-icon-twitter"><i class="icon-twitter"></i></a></li>
          <li><a href="https://dribbble.com/qhubi" class="pi-social-icon-dribbble"><i class="icon-dribbble"></i></a></li>
          <li><a href="https://qhubi.tumblr.com/" class="pi-social-icon-tumblr"><i class="icon-tumblr"></i></a></li>
          <li><a href="https://vimeo.com/qhubi" class="pi-social-icon-vimeo"><i class="icon-vimeo"></i></a></li>
          <li><a href="#" class="pi-social-icon-rss"><i class="icon-rss"></i></a></li>
        </ul>
      </div>
      <!-- End social icons -->
      <!-- Footer logo -->
      <div class="pi-row-block pi-row-block-logo pi-row-block-bordered"><a href="#"><img src="img/logo-opacity-dark.png" alt=""></a></div>
      <!-- End footer logo -->
      <!-- Text -->
      <span class="pi-row-block pi-row-block-txt pi-hidden-xs"><a href="#"><?php echo $row_siteayar['GCode']; ?></a> </span>
      <!-- End text -->
    </div>
  </div>
  <!-- End copyright area -->
  <!-- End footer -->
</div>
<div class="pi-scroll-top-arrow" data-scroll-to="0"></div>
<script src="3dParty/jquery-1.11.0.min.js"></script>
<script src="3dParty/bootstrap/js/bootstrap.min.js"></script>
<script src="3dParty/jquery.touchSwipe.min.js"></script>
<script src="3dParty/gauge.min.js"></script>
<script src="3dParty/inview.js"></script>
<script src="3dParty/rs-plugin/js/jquery.themepunch.tools.min.js"></script>
<script src="3dParty/rs-plugin/js/jquery.themepunch.revolution.min.js"></script>
<script src="3dParty/requestAnimationFramePolyfill.min.js"></script>
<script src="3dParty/jquery.scrollTo.min.js"></script>
<script src="3dParty/colorbox/jquery.colorbox-min.js"></script>
<script src="scripts/pi.global.js"></script>
<script src="scripts/pi.slider.js"></script>
<script src="scripts/pi.init.slider.js"></script>
<script src="3dParty/jquery.easing.1.3.js"></script>
<script src="scripts/pi.counter.js"></script>
<script src="scripts/pi.init.counter.js"></script>
<script src="scripts/pi.parallax.js"></script>
<script src="scripts/pi.init.parallax.js"></script>
<script src="scripts/pi.init.revolutionSlider.js"></script>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($duyuru);

mysql_free_result($siteayar);
?>