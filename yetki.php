<?php require_once('Connections/baglan.php'); ?>
<?php require_once('fonksiyon.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "1,2";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "login.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
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


$colname_uyebilgileri = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_uyebilgileri = $_SESSION['MM_Username'];
}
mysql_select_db($database_baglan, $baglan);
$query_uyebilgileri = sprintf("SELECT uyeID, seviyeID, uyeAdi, Parola, sirketAdi, VergiDairesi, VergiNumarasi, TicarisicilNo, BusinessRegNo, ToplamuretimAlani, Calisansayi, Ulke, sehir, PostaKodu, Adres, WebAdres, Tel, Fax, saticiBilgiDosyasi, imzasirkusu, sertifika, GecenYilciro, EkipmanYatirim, ihracatoran, HesapYoneticisiTitle, SalesMarketingTitle, RDTitle, LogisticsTitle, qualityTitle, ReferansMusteri, Kategori, HesapYoneticisiTel, HesapYoneticisiMail, PurchasingTitle, salesMarketingTel, salesMarketingMail, RDTel, RDMail, LogisticsTel, LogisticsMail, PurchasingTel, PurchasingMail, qualityTel, qualityMail, HukukiYapi FROM uyeler WHERE uyeAdi = %s", GetSQLValueString($colname_uyebilgileri, "text"));
$uyebilgileri = mysql_query($query_uyebilgileri, $baglan) or die(mysql_error());
$row_uyebilgileri = mysql_fetch_assoc($uyebilgileri);
$totalRows_uyebilgileri = mysql_num_rows($uyebilgileri);

mysql_select_db($database_baglan, $baglan);
$query_teklifoku = "SELECT teklifiste.TalepID, teklifiste.aciklama, teklifiste.gondermetarih, teklifiste.teklifaktif FROM teklifiste WHERE teklifiste.teklifaktif = 0 ORDER BY teklifiste.teklifaktif ASC";
$teklifoku = mysql_query($query_teklifoku, $baglan) or die(mysql_error());
$row_teklifoku = mysql_fetch_assoc($teklifoku);
$totalRows_teklifoku = mysql_num_rows($teklifoku);

mysql_select_db($database_baglan, $baglan);
$query_ayar = "SELECT * FROM siteconfig";
$ayar = mysql_query($query_ayar, $baglan) or die(mysql_error());
$row_ayar = mysql_fetch_assoc($ayar);
$totalRows_ayar = mysql_num_rows($ayar);
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"><!-- InstanceBegin template="/Templates/sablon.dwt.php" codeOutsideHTMLIsLocked="false" --> <!--<![endif]-->
<head>
<meta charset="utf-8">
<!-- InstanceBeginEditable name="doctitle" -->
   <title><?php echo $row_ayar['SiteTitle']; ?></title>
   <!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="css" -->
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<meta content="<?php echo $row_ayar['Metadesc']; ?>" name="description">
<meta content="<?php echo $row_ayar['MetaName']; ?>" name="keywords">
<meta content="pixel-industry" name="author">
<link href="admin/assets/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="admin/assets/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
<link href="admin/assets/bootstrap/css/bootstrap-fileupload.css" rel="stylesheet">
<link href="admin/assets/font-awesome/css/font-awesome.css" rel="stylesheet">
<link href="admin/assets/fancybox/source/jquery.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="admin/assets/gritter/css/jquery.css">
<link rel="stylesheet" type="text/css" href="admin/assets/uniform/css/uniform.css">
<link rel="stylesheet" type="text/css" href="admin/assets/chosen-bootstrap/chosen/chosen.css">
<link rel="stylesheet" type="text/css" href="admin/assets/jquery-tags-input/jquery.css">
<link rel="stylesheet" type="text/css" href="admin/assets/clockface/css/clockface.css">
<link rel="stylesheet" type="text/css" href="admin/assets/bootstrap-wysihtml5/bootstrap-wysihtml5.css">
<link rel="stylesheet" type="text/css" href="admin/assets/bootstrap-datepicker/css/datepicker.css">
<link rel="stylesheet" type="text/css" href="admin/assets/bootstrap-timepicker/compiled/timepicker.css">
<link rel="stylesheet" type="text/css" href="admin/assets/bootstrap-colorpicker/css/colorpicker.css">
<link rel="stylesheet" href="admin/assets/bootstrap-toggle-buttons/static/stylesheets/bootstrap-toggle-buttons.css">
<link rel="stylesheet" href="admin/assets/data-tables/DT_bootstrap.css">
<link rel="stylesheet" type="text/css" href="admin/assets/bootstrap-daterangepicker/daterangepicker.css">
<link href="admin/css/style.css" rel="stylesheet">
<link href="admin/css/style_responsive.css" rel="stylesheet">
<link href="admin/css/style_default.css" rel="stylesheet" id="style_color">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.7.5/css/bootstrap-select.min.css">
<link href="admin/assets/fancybox/source/jquery.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="admin/assets/uniform/css/uniform.css">
<link href="admin/assets/fullcalendar/fullcalendar/bootstrap-fullcalendar.css" rel="stylesheet">
<link href="admin/assets/jqvmap/jqvmap/jqvmap.css" media="screen" rel="stylesheet" type="text/css">
<link href="admin/css/inbox.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="admin/css/example-styles.css">
<link rel="stylesheet" type="text/css" href="admin/cssdemo-styles.css">
<link rel="stylesheet" type="text/css" href="admin/assets/jquery-ui/jquery-ui-1.10.1.custom.css">
<script type= "text/javascript" src = "admin/country_dropdown/countries3.js"></script>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
   <!-- InstanceEndEditable -->
 
</head>
<?php
require_once('fonksiyon.php');
?>

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

<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="fixed-top">
   <!-- BEGIN HEADER -->
   <div id="header" class="navbar navbar-inverse navbar-fixed-top">
       <!-- BEGIN TOP NAVIGATION BAR -->
       <div class="navbar-inner">
           <div class="container-fluid">
               <!-- BEGIN LOGO -->
 
                   <a class="nav top-men" href="admin/index.php">           
                   <img src="admin/resim/logo/<?php echo $row_ayar['Sitelogo']; ?>" alt="QHubi.com">
               </a>
                               <a class="btn btn-navbar collapsed" id="main_menu_trigger" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="arrow"></span>
                </a>
               <!-- END RESPONSIVE MENU TOGGLER -->
               

                    <div class="top-nav ">
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
                       <!-- BEGIN SUPPORT -->
                       <li class="dropdown mtop5">

                           <a class="dropdown-toggle element" data-placement="bottom" data-toggle="tooltip" href="admin/sohbet/index.php" data-original-title="Chat">
                               <i class="icon-comments-alt"></i>
                               <span class="badge badge-important"><?php echo $totalRows_mesaj ?></span>
                           </a>
                       </li>
                       <li class="dropdown mtop5">
                           <a class="dropdown-toggle element" data-placement="bottom" data-toggle="tooltip" href="#" data-original-title="Help">
                               <i class="icon-headphones"></i>
                           </a>
                       </li>
                       <!-- END SUPPORT -->
                       <!-- BEGIN USER LOGIN DROPDOWN -->
                       <li class="dropdown">
                           <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                               <span class="username"><?php echo $_SESSION['MM_Username']; ?> </span>
                               <b class="caret"></b>
                           </a>
                           <ul class="dropdown-menu">
                               <li><a href="admin/Hesap/index.php"><i class="icon-user"></i> <?php echo $dil['profilim']; ?></a></li>
                               <li class="divider"></li>
                               <li><a href="admin/cikis.php"><i class="icon-key"></i> <?php echo $dil['cikisyap']; ?></a></li>
                           </ul>
                       </li>
                       <!-- END USER LOGIN DROPDOWN -->
                   </ul>
                   <!-- END TOP NAVIGATION MENU -->
             </div>


               <!-- END LOGO -->
               <!-- BEGIN RESPONSIVE MENU TOGGLER -->
              
               
               <!-- END  NOTIFICATION -->
              
         </div>
       </div>
       <!-- END TOP NAVIGATION BAR -->
   </div>
   <!-- END HEADER -->
   <!-- BEGIN CONTAINER -->
<div id="container" class="row-fluid">
      <!-- BEGIN SIDEBAR -->
      <div id="sidebar" class="nav-collapse collapse">

         <div class="sidebar-toggler hidden-phone"></div>   

         <!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
         <div class="navbar-inverse">
            <form class="navbar-search visible-phone">
               <input type="text" class="search-query" placeholder="Search">
            </form>
            
            <?php require_once ('fonksiyon.php');?>
         </div>
         <!-- END RESPONSIVE QUICK SEARCH FORM -->
         <!-- BEGIN SIDEBAR MENU -->
          <ul class="sidebar-menu">
              <li class="has-sub active">
                  <a href="javascript:;" class="active">
                  <?php if($row_uyebilgileri['seviyeID']== 1)
{
  ?>
                      <span class="icon-box"> <i class=" icon-leaf"></i></span> <?php echo $dil['yonetim']; ?>
                      <span class="arrow"></span>
                  </a>
                  

                  <ul class="sub">
                      <li><a class="" href="admin/index.php">Ana Sayfa</a></li>
                      <li><a class="" href="admin/Ayar/index.php">Site Ayarları</a></li>
                      <li><a class="" href="admin/Ayar/uyeler/index.php">Üyeler</a></li>
                      <li><a class="" href="admin/Ayar/seviyeler/index.php">Üye Seviyeleri</a></li>
                      <li><a class="" href="admin/Kategoriler/index.php">Kategoriler</a></li>

                  </ul>
				  
				
				  <?php }else{

}?>
				  
              </li>
              <li class="has-sub">
              
                  <a href="javascript:;" class="">
                      <span class="icon-box"> <i class="icon-book"></i></span> <?php echo $dil['hesap_bilgi']; ?>
                     </span>
                  </a>
 
 					<ul class="sub">
                    	<li><a class="" href="admin/Hesap/index.php"><?php echo $dil['ayarlar']; ?></a></li>
                        <li><a class="" href="sifredegis.php"><?php echo $dil['sifre_degis']; ?></a></li>
                        <li><a class="" href="admin/Hesap/yukle/index.php"><?php echo $dil['sirketresim']; ?></a></li>
                        <li><a class="" href="admin/Hesap/yukle/index1.php"><?php echo $dil['sertifikaresim']; ?></a></li>
                    </ul>
              </li>

              <li class="has-sub">
                  <a href="admin/Teklif/verme/index.php" class="">
                      <span class="icon-box"><i class="icon-signin"></i></span> <?php echo $dil['teklif_iste']; ?>
                      </span>
                  </a>

              </li>
              <li class="has-sub">
                  <a href="admin/Teklif/alma/index.php" class="">
                      <span class="icon-box"><i class="icon-signout"></i></span> <?php echo $dil['teklif_verme']; ?>
                      </span>
                  </a>
              </li>
              <li class="has-sub">
                  <a href="admin/Dashboard/index.php" class="">
                      <span class="icon-box"><i class="icon-dashboard"></i></span> <?php echo $dil['dashboard']; ?>
                      </span>
                  </a>
              </li>

              <li class="has-sub">
                  <a href="admin/Firmalar/index.php" class="">
                      <span class="icon-box"><i class="icon-globe"></i></span> <?php echo $dil['firmalar']; ?>
                      </span>
                  </a>
              </li>
              <li class="has-sub">
                  <a href="admin/Teklif/tekliflerim/index.php" class="">
                      <span class="icon-box"><i class="icon-briefcase"></i></span> <?php echo $dil['tekliflerim']; ?>
                     </span>

                  </a>
              </li>
              <li class="has-sub">
                  <a href="admin/Yardim/index.php" class="">
                      <span class="icon-box"><i class="icon-magic"></i></span> <?php echo $dil['yardim_merkezi']; ?>
                      </span>
                  </a>

              </li>
            
          </ul>
         <!-- END SIDEBAR MENU -->
      </div>
      <!-- END SIDEBAR -->
      <!-- BEGIN PAGE -->  
      <div id="main-content">
         <!-- BEGIN PAGE CONTAINER--><!-- InstanceBeginEditable name="orta" -->
         <div class="container-fluid">
           <!-- BEGIN PAGE HEADER-->
           <div class="row-fluid">
             <div class="span12">
               <!-- BEGIN PAGE TITLE & BREADCRUMB-->
               <h3 class="page-title"> Yönetim Paneline Hoş Geldiniz. </h3>
               <ul class="breadcrumb">
                 <li> <a href="admin/index.php"><i class="icon-home"></i></a><span class="divider">&nbsp;</span> </li>
                 <li> <a href="admin/index.php">Yönetim Ana Sayfa</a> <span class="divider">&nbsp;</span> </li>
                 <li><a href="#">Blank Page</a><span class="divider-last">&nbsp;</span></li>
               </ul>
               <!-- END PAGE TITLE & BREADCRUMB-->
             </div>
           </div>
           <!-- END PAGE HEADER-->
           <!-- BEGIN PAGE CONTENT-->
           <div class="row-fluid">
             <div class="span12">
               <div class="widget">
                 <div class="widget-title">
                   <h4><i class="icon-globe"></i>Blank Page</h4>
                 </div>
                 <div class="widget-body">
                   <p>Yetkiniz bulunmuyor!!</p>
                   <p></p>
                 </div>
               </div>
             </div>
           </div>
         </div>
         <!-- InstanceEndEditable --></div>
            <!-- END PAGE CONTENT-->         
</div>
         <!-- END PAGE CONTAINER-->
      </div>
      <!-- END PAGE -->  
   </div>
   <!-- END CONTAINER -->
   <!-- BEGIN FOOTER -->
<!-- InstanceBeginEditable name="degis" -->
   <div id="footer"> <?php echo $row_ayar['GCode']; ?>
     <div class="span pull-right"> <span class="go-top"><i class="icon-arrow-up"></i></span></div>
   </div>
   <!-- END FOOTER -->
   <!-- BEGIN JAVASCRIPTS -->
   <!-- Load javascripts at bottom, this will reduce page load time -->
   <script type="text/javascript" src="admin/js/jquery-2.2.4.min.js"></script>
   <script type="text/javascript" src="admin/js/jquery.multi-select.js"></script>
   <script type="text/javascript">
    $(function(){
        $('#people').multiSelect();
        $('#line-wrap-example').multiSelect({
            positionMenuWithin: $('.position-menu-within')
        });
        $('#categories').multiSelect({
            noneText: 'All categories',
            presets: [
                {
                    name: 'All categories',
                    options: []
                },
                {
                    name: 'My categories',
                    options: ['a', 'c']
                }
            ]
        })
    });
    </script>
   <script src="admin/js/jquery-1.8.3.js"></script>
   <script src="admin/assets/bootstrap/js/bootstrap.js"></script>
   <script type="text/javascript" src="https://cdn.ckeditor.com/4.5.7/full-all/ckeditor.js"></script>
   <script src="admin/js/jquery.js"></script>
   <script src="admin/assets/jquery-slimscroll/jquery-ui-1.9.2.custom.js"></script>
   <script src="admin/assets/jquery-slimscroll/jquery.slimscroll.js"></script>
   <script src="admin/assets/fullcalendar/fullcalendar/fullcalendar.js"></script>
   <script src="admin/js/excanvas.js"></script>
   <script src="admin/js/respond.js"></script>
   <script language="javascript">print_country("country");</script>
   <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-43091346-1', 'devzone.co.in');
  ga('send', 'pageview');

   </script>
   <!-- ie8 fixes -->
   <!--[if lt IE 9]>
  
   <![endif]-->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>
   <script type="text/javascript" src="admin/assets/chosen-bootstrap/chosen/chosen.jquery.js"></script>
   <script type="text/javascript" src="admin/assets/uniform/jquery.uniform.js"></script>
   <script type="text/javascript" src="admin/assets/bootstrap-wysihtml5/wysihtml5-0.3.js"></script>
   <script type="text/javascript" src="admin/assets/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>
   <script type="text/javascript" src="admin/assets/clockface/js/clockface.js"></script>
   <script type="text/javascript" src="admin/assets/jquery-tags-input/jquery.tagsinput.js"></script>
   <script type="text/javascript" src="admin/assets/bootstrap-toggle-buttons/static/js/jquery.toggle.js"></script>
   <script type="text/javascript" src="admin/assets/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
   <script type="text/javascript" src="admin/assets/bootstrap-daterangepicker/date.js"></script>
   <script type="text/javascript" src="admin/assets/bootstrap-daterangepicker/daterangepicker.js"></script>
   <script type="text/javascript" src="admin/assets/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
   <script type="text/javascript" src="admin/assets/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
   <script type="text/javascript" src="admin/assets/bootstrap-inputmask/bootstrap-inputmask.js"></script>
   <script type="text/javascript" src="admin/assets/data-tables/jquery.js"></script>
   <script type="text/javascript" src="admin/assets/data-tables/DT_bootstrap.js"></script>
   <script src="admin/assets/jquery-ui/jquery-ui-1.10.1.custom.js" type="text/javascript"></script>
   <script src="admin/assets/jqvmap/jqvmap/jquery.js" type="text/javascript"></script>
   <script src="admin/assets/jqvmap/jqvmap/maps/jquery.vmap.js" type="text/javascript"></script>
   <script src="admin/assets/jqvmap/jqvmap/maps/jquery.vmap%20(1).js" type="text/javascript"></script>
   <script src="admin/assets/jqvmap/jqvmap/maps/jquery.vmap%20(2).js" type="text/javascript"></script>
   <script src="admin/assets/jqvmap/jqvmap/maps/jquery.vmap%20(3).js" type="text/javascript"></script>
   <script src="admin/assets/jqvmap/jqvmap/maps/jquery.vmap%20(4).js" type="text/javascript"></script>
   <script src="admin/assets/jqvmap/jqvmap/data/jquery.vmap.js" type="text/javascript"></script>
   <script src="admin/assets/jquery-knob/js/jquery.js"></script>
   <script src="admin/assets/flot/jquery.js"></script>
   <script src="admin/assets/flot/jquery.flot.js"></script>
   <script src="admin/assets/flot/jquery.flot%20(1).js"></script>
   <script src="admin/assets/flot/jquery.flot%20(2).js"></script>
   <script src="admin/assets/flot/jquery.flot%20(3).js"></script>
   <script src="admin/js/jquery.peity.js"></script>
   <script type="text/javascript" src="admin/assets/uniform/jquery.uniform.js"></script>
   <script src="admin/js/scripts.js"></script>
   <script src="admin/js/ui-jqueryui.js"></script>
   <script>
      jQuery(document).ready(function() {       
         // initiate layout and plugins
         App.init();
		 UIJQueryUI.init();
      });
   </script>
<!-- ie8 fixes -->
<!--[if lt IE 9]>

	<![endif]-->
<!-- END JAVASCRIPTS -->
<!-- InstanceEndEditable -->
   <script>
		jQuery(document).ready(function() {
			// initiate layout and plugins
			App.setMainPage(true);
			App.init();
		});
	</script>
</body>
<!-- END BODY -->
<!-- InstanceEnd --></html>
<?php
mysql_free_result($uyebilgileri);

mysql_free_result($teklifoku);

mysql_free_result($ayar);
?>
