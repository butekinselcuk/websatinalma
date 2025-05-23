<?php require_once('Connections/baglan.php'); ?>
<?php require_once('fonksiyon.php'); ?>
<?php include_once("analyticstracking.php") ?>
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

$maxRows_teklifoku = 10;
$pageNum_teklifoku = 0;
if (isset($_GET['pageNum_teklifoku'])) {
  $pageNum_teklifoku = mysql_real_escape_string($_GET['pageNum_teklifoku']);
}
$startRow_teklifoku = $pageNum_teklifoku * $maxRows_teklifoku;

mysql_select_db($database_baglan, $baglan);
$query_teklifoku = "SELECT teklifiste.Teklifaktif, teklifiste.TalepID, teklifiste.Kategori, teklifiste.Aciklama, teklifiste.Gondermetarih FROM teklifiste ORDER BY teklifiste.TalepID ASC";
$query_limit_teklifoku = sprintf("%s LIMIT %d, %d", $query_teklifoku, $startRow_teklifoku, $maxRows_teklifoku);
$teklifoku = mysql_query($query_limit_teklifoku, $baglan) or die(mysql_error());
$row_teklifoku = mysql_fetch_assoc($teklifoku);

if (isset($_GET['totalRows_teklifoku'])) {
  $totalRows_teklifoku = mysql_real_escape_string($_GET['totalRows_teklifoku']);
} else {
  $all_teklifoku = mysql_query($query_teklifoku);
  $totalRows_teklifoku = mysql_num_rows($all_teklifoku);
}
$totalPages_teklifoku = ceil($totalRows_teklifoku/$maxRows_teklifoku)-1;

$colname_uyebilgileri = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_uyebilgileri = $_SESSION['MM_Username'];
}
mysql_select_db($database_baglan, $baglan);
$query_uyebilgileri = sprintf("SELECT uyeler.UyeID, uyeler.SeviyeID, uyeler.UyeAdi, uyeler.Parola, uyeler.SirketAdi, uyeler.VergiDairesi, uyeler.VergiNumarasi, uyeler.TicariSicilNo, uyeler.BusinessRegNo, uyeler.ToplamUretimAlani, uyeler.CalisanSayi, uyeler.Ulke, uyeler.Sehir, uyeler.PostaKodu, uyeler.Adres, uyeler.WebAdres, uyeler.Tel, uyeler.Fax, uyeler.SaticiBilgiDosyasi, uyeler.ImzaSirkusu, uyeler.Sertifika, uyeler.GecenYilCiro, uyeler.EkipmanYatirim, uyeler.IhracatOran, uyeler.HesapYoneticisiTitle, uyeler.SalesMarketing, uyeler.RD, uyeler.Logistics, uyeler.Quality, uyeler.ReferansMusteri, uyeler.Kategori, uyeler.HesapYoneticisiTel, uyeler.HesapYoneticisiMail FROM uyeler WHERE uyeler.UyeAdi = %s", GetSQLValueString($colname_uyebilgileri, "text"));
$uyebilgileri = mysql_query($query_uyebilgileri, $baglan) or die(mysql_error());
$row_uyebilgileri = mysql_fetch_assoc($uyebilgileri);
$totalRows_uyebilgileri = mysql_num_rows($uyebilgileri);
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"><!-- InstanceBegin template="/Templates/adminpanel.dwt.php" codeOutsideHTMLIsLocked="false" --> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
   <meta charset="utf-8">
   <!-- InstanceBeginEditable name="doctitle" -->
   <title>Site Yönetim Paneli</title>
   <!-- InstanceEndEditable -->
   <meta content="width=device-width, initial-scale=1.0" name="viewport">
   <meta content="" name="description">
   <meta content="" name="author">
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

   <link href="admin/assets/fancybox/source/jquery.css" rel="stylesheet">
   <link rel="stylesheet" type="text/css" href="admin/assets/uniform/css/uniform.css">
   <!-- InstanceBeginEditable name="head" -->
   
         <div class="container-fluid">
           <!-- BEGIN PAGE HEADER-->
           <div class="row-fluid">
             <div class="span12">
               <!-- BEGIN PAGE TITLE & BREADCRUMB-->
               <h3 class="page-title"> Yönetim Paneline Hoş Geldiniz </h3>
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
               <div class="row-fluid">
               <div class="span12">
                  <!-- BEGIN SAMPLE FORM widget-->   
                  <div class="widget">
                     <div class="widget-title">
                        <h4><i class="icon-reorder"></i>Genel Şirket Bilgileri</h4>
                     </div>
                     <div class="widget-body form">
                        <!-- BEGIN FORM-->
                       <form action="#" class="form-horizontal">
                           <div class="control-group">
                              <label class="control-label">Şirket adı</label>
                              <div class="controls">
                                <input type="text" class="span3  popovers" data-trigger="hover" data-content="Şirket adı.">
                              </div>
                           </div>
                           <div class="control-group">
                              <label class="control-label">Vergi Dairesi</label>
                              <div class="controls">
                                <input type="text" class="span3  popovers" data-trigger="hover" data-content="Vergi Dairesi.">

                              </div>
                           </div>
                           <div class="control-group">
                              <label class="control-label">Vergi Numarası</label>
                              <div class="controls">
                                <input type="text" class="span3  popovers" data-trigger="hover" data-content="Vergi Numarası.">

                              </div>
                           </div>
                           <div class="control-group">
                              <label class="control-label">Ticaret Sicil No</label>
                              <div class="controls">
                                <input type="text" class="span3  popovers" data-trigger="hover" data-content="Ticaret Sicil No.">

                              </div>
                           </div>
                           <div class="control-group">
                              <label class="control-label">Bussines Reg. Number</label>
                              <div class="controls">
                                <input type="text" class="span3  popovers" data-trigger="hover" data-content="Bussines Reg. Number.">

                              </div>
                           </div>
                           <div class="control-group">
                              <label class="control-label">Hukuki Yapı</label>
                              <div class="controls">
                                <input type="text" class="span3  popovers" data-trigger="hover" data-content="Hukuki Yapı.">

                              </div>
                           </div>
                           <div class="control-group">
                              <label class="control-label">Toplam üretim alanı (m2)</label>
                              <div class="controls">
                                <input type="text" class="span3  popovers" data-trigger="hover" data-content="Toplam üretim alanı (m2).">

                              </div>
                           </div>
                         <div class="control-group">
                              <label class="control-label">Çalışan sayısı</label>
                              <div class="controls">
                                <input type="text" class="span3  popovers" data-trigger="hover" data-content="Çalışan sayısı.">

                              </div>
                          </div>

                        </form>
                        
                        
                        
                        <!-- END FORM-->           
                     </div>
                  </div>
                  <!-- END SAMPLE FORM widget-->
               </div>
            </div>
                        <div class="row-fluid">
                <div class="span6">

<div class="widget">
                        <div class="widget-title">
                            <h4><i class="icon-reorder"></i>Adres</h4>
                        </div>
                        <div class="widget-body form">
                            <!-- BEGIN FORM-->
                            <form action="#" class="form-horizontal">
                                  <div class="control-group">
                              <label class="control-label">Ülke</label>
                              <div class="controls">
                                <input type="text" class="span3  popovers" data-trigger="hover" data-content="Ülke.">
                              </div>
                           </div>
                                  <div class="control-group">
                              <label class="control-label">Şehir</label>
                              <div class="controls">
                                <input type="text" class="span3  popovers" data-trigger="hover" data-content="Şehir.">
                              </div>
                           </div>
                                  <div class="control-group">
                              <label class="control-label">Posta Kodu</label>
                              <div class="controls">
                                <input type="text" class="span3  popovers" data-trigger="hover" data-content="Posta Kodu.">
                              </div>
                           </div>
       
                                                           <div class="control-group">
                              <label class="control-label">Adres</label>
                              <div class="controls">
                                <textarea type="text" class="span6  popovers" rows="3" data-trigger="hover" data-content="Adres."></textarea>
                              </div>
                           </div>
           
                                                           <div class="control-group">
                              <label class="control-label">Web Adresi</label>
                              <div class="controls">
                                <input type="text" class="span3  popovers" data-trigger="hover" data-content="Web Adresi.">
                              </div>
                           </div>
                                <div class="control-group">
                                    <label class="control-label">Tel</label>
                                    <div class="controls">
                                        <input class="span5" type="text" data-mask="(999) 999-999-99-99" placeholder="">
                                        <span class="help-inline">(999) 999-999-99-99</span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Fax</label>
                                    <div class="controls">
                                        <input class="span5" type="text" data-mask="(999) 999-999-99-99" placeholder="">
                                        <span class="help-inline">(999) 999-999-99-99</span>
                                    </div>
                                </div>
                            </form>
                            <!-- END FORM-->
                        </div>
                    </div>
                         </div>               

                    
                                    <div class="span6">
                    <!-- BEGIN widget-->
                    <div class="widget">
                        <div class="widget-title">
                            <h4><i class="icon-reorder"></i>Yüklenmesi zorunlu dosyalar</h4>

                        </div>
                        <div class="widget-body form">
                            <!-- BEGIN FORM-->
                          <form action="#" class="form-horizontal">
                            <div class="control-group">
                                    <label class="control-label">Satıcı Bilgi Dosyası</label>
                                    <div class="controls">
                                        <input type="file" class="default">
                                    </div>
                                </div>
                                                        <div class="control-group">
                                    <label class="control-label">İmza Sirküleri</label>
                                    <div class="controls">
                                        <input type="file" class="default">
                                    </div>
                                </div>
                                                        <div class="control-group">
                                    <label class="control-label">Organizasyon Şeması</label>
                                    <div class="controls">
                                        <input type="file" class="default">
                                    </div>
                                </div>
                                
                                
                          </form>
                            <!-- END FORM-->
                        </div>
                    </div>
                    
<div class="widget">
                        <div class="widget-title">
                           <h4><i class="icon-globe"></i>Sertifikalarınız</h4>
                   
                        </div>
                      <div class="widget-body form">
                          <form action="admin/assets/dropzone/upload.php" class="dropzone" id="my-awesome-dropzone">
                          </form>
                      </div>
                  </div>
                        </div>
                    </div>
                                            </div>

                    </div>
         
         <!-- InstanceEndEditable -->
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="fixed-top">
   <!-- BEGIN HEADER -->
   <div id="header" class="navbar navbar-inverse navbar-fixed-top">
       <!-- BEGIN TOP NAVIGATION BAR -->
       <div class="navbar-inner">
           <div class="container-fluid">
               <!-- BEGIN LOGO -->
               <a class="brand" href="admin/index.php">
                   <img src="admin/img/logo.png" alt="QHubi.com">
               </a>
               <!-- END LOGO -->
               <!-- BEGIN RESPONSIVE MENU TOGGLER -->
               <a class="btn btn-navbar collapsed" id="main_menu_trigger" data-toggle="collapse" data-target=".nav-collapse">
                   <span class="icon-bar"></span>
                   <span class="icon-bar"></span>
                   <span class="icon-bar"></span>
                   <span class="arrow"></span>
               </a>
               <!-- END RESPONSIVE MENU TOGGLER -->
               <div id="top_menu" class="nav notify-row">
                   <!-- BEGIN NOTIFICATION -->
                   <ul class="nav top-menu">
                       <!-- BEGIN SETTINGS -->
                       <li class="dropdown">
                           <a class="dropdown-toggle element" data-placement="bottom" data-toggle="tooltip" href="admin/Ayar/index.php" data-original-title="Site Ayarları">
                               <i class="icon-cog"></i>
                           </a>
                       </li>
                       <!-- END SETTINGS -->
                       <!-- BEGIN INBOX DROPDOWN -->
                       <li class="dropdown" id="header_inbox_bar">
                           <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                               <i class="icon-envelope-alt"></i>
                         <?php if($row_teklifoku['teklifaktif']=='0') :?>
                         
                           <span class="badge badge-important"><?php echo $totalRows_teklifoku; ?></span>
                           
                           <?php endif ;?>
                           
                           </a>
                            <?php if($row_teklifoku['teklifaktif']=='0') :?>
                           <ul class="dropdown-menu extended inbox">
                               <li>
                                   <p><?php echo $totalRows_teklifoku; ?> Mesajınız var</p>
                               </li>
                                  <li>
                                  <?php do { ?>
                                    <a href="admin/Teklif/tekliflerim/index.php">
									
								    <span class="subject">
								    <span class="from"><?php echo $row_teklifoku['TalepID']; ?></span>
								    <span class="time"><?php echo $row_teklifoku['gondermetarih']; ?></span>
								    </span>
									  
                                    </a>                             
                             <?php } while ($row_teklifoku = mysql_fetch_assoc($teklifoku)); ?>
                               
                            
                                   
                               </li>
                           </ul>
                           
                            <?php else :?>
                                                      <ul class="dropdown-menu extended inbox">
                               <li>
                                   <p>Mesajınız Yok</p>
                               </li>

                           </ul>	
                           <?php endif ;?>
                       </li>
                       <!-- END INBOX DROPDOWN -->


                   </ul>
               </div>
               <!-- END  NOTIFICATION -->
               <div class="top-nav ">
                   <ul class="nav pull-right top-menu">
                       <!-- BEGIN SUPPORT -->
                       <li class="dropdown mtop5">

                           <a class="dropdown-toggle element" data-placement="bottom" data-toggle="tooltip" href="#" data-original-title="Chat">
                               <i class="icon-comments-alt"></i>
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
                               <img src="admin/img/avatar1_small.jpg" alt="">
                               <span class="username"><?php echo $_SESSION['MM_Username']; ?> </span>
                               <b class="caret"></b>
                           </a>
                           <ul class="dropdown-menu">
                               <li><a href="admin/Ayar/uyeler/duzenle.php?uyeID=<?php echo $row_uyebilgileri['uyeID']; ?>"><i class="icon-user"></i> Profilim</a></li>
                               <li class="divider"></li>
                               <li><a href="admin/cikis.php"><i class="icon-key"></i> Çıkış Yap</a></li>
                           </ul>
                       </li>
                       <!-- END USER LOGIN DROPDOWN -->
                   </ul>
                   <!-- END TOP NAVIGATION MENU -->
               </div>
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
         </div>
         <!-- END RESPONSIVE QUICK SEARCH FORM -->
         <!-- BEGIN SIDEBAR MENU -->
          <ul class="sidebar-menu">
              <li class="has-sub active">
                  <a href="javascript:;" class="active">
                      <span class="icon-box"> <i class=" icon-leaf"></i></span> Yönetim
                      <span class="arrow"></span>
                  </a>
                  <ul class="sub">
                      <li><a class="" href="admin/index.php">Ana Sayfa</a></li>
                      <li><a class="" href="admin/Ayar/index.php">Site Ayarları</a></li>
                      <li><a class="" href="admin/Ayar/seviyeler/index.php">Üye Seviyeleri</a></li>

                  </ul>
              </li>
              <li class="has-sub">
                  <a href="admin/Hesap/index.php" class="" >
                      <span class="icon-box"> <i class="icon-book"></i></span> Hesap Bilgilerim
                     </span>
                  </a>
 
              </li>
              <li class="has-sub">
                  <a href="admin/Teklif/alma/index.php" class="">
                      <span class="icon-box"><i class="icon-signin"></i></span> Teklif İsteme Ekranı
                      </span>
                  </a>

              </li>
              <li class="has-sub">
                  <a href="admin/Teklif/verme/index.php" class="">
                      <span class="icon-box"><i class="icon-signout"></i></span> Teklif Verme Ekranı
                      </span>
                  </a>
              </li>
              <li class="has-sub">
                  <a href="admin/Dashboard/index.php" class="">
                      <span class="icon-box"><i class="icon-dashboard"></i></span> Dashboard
                      </span>
                  </a>
              </li>
              <li class="has-sub">
                  <a href="admin/Kategoriler/index.php" class="">
                      <span class="icon-box"><i class="icon-qrcode"></i></span> Kategoriler
                      </span>
                  </a>
              </li>

              <li class="has-sub">
                  <a href="admin/Firmalar/index.php" class="">
                      <span class="icon-box"><i class="icon-globe"></i></span> Firmalar
                      </span>
                  </a>
              </li>
              <li class="has-sub">
                  <a href="admin/Yardim/index.php" class="">
                      <span class="icon-box"><i class="icon-magic"></i></span> Yardım Merkezi
                      </span>
                  </a>
              </li>
              <li class="has-sub">
                  <a href="admin/Teklif/tekliflerim/index.php" class="">
                      <span class="icon-box"><i class="icon-briefcase"></i></span> Tekliflerim
                     </span>
                  </a>

              </li>
            
          </ul>
         <!-- END SIDEBAR MENU -->
      </div>
      <!-- END SIDEBAR -->
      <!-- BEGIN PAGE -->  
      <div id="main-content">
         <!-- BEGIN PAGE CONTAINER-->
         <div class="container-fluid">
            <!-- BEGIN PAGE HEADER-->   
            <div class="row-fluid">
               <div class="span12">

                  <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                   <h3 class="page-title">
                     Yönetim Paneline Hoş Geldiniz
                  </h3>
                   <ul class="breadcrumb">
                       <li>
                           <a href="admin/index.php"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                       </li>
                       <li>
                           <a href="admin/index.php">Yönetim Ana Sayfa</a> <span class="divider">&nbsp;</span>
                       </li>
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
                          <p>Blank page sample</p>
                        <p>





                                 </p>
                                 
                                 
                              </div>
                           </div>
                        </div>
                  </div>
               </div>
            </div>
            <!-- END PAGE CONTENT-->         
         </div>
         <!-- END PAGE CONTAINER-->
      </div>
      <!-- END PAGE -->  
   </div>
   <!-- END CONTAINER -->
   <!-- BEGIN FOOTER -->
   <div id="footer">
       2013 &copy; Admin Lab Dashboard.
      <div class="span pull-right">
         <span class="go-top"><i class="icon-arrow-up"></i></span>
      </div>
   </div>
   <!-- END FOOTER -->
   <!-- BEGIN JAVASCRIPTS -->    
   <!-- Load javascripts at bottom, this will reduce page load time -->
   <script src="admin/js/jquery-1.8.3.js"></script>
   <script src="admin/assets/bootstrap/js/bootstrap.js"></script>
   <script type="text/javascript" src="https://cdn.ckeditor.com/4.5.7/full-all/ckeditor.js"></script>
   <script src="admin/js/jquery.js"></script>
   <!-- ie8 fixes -->
   <!--[if lt IE 9]>
   <script src="js/excanvas.js"></script>
   <script src="js/respond.js"></script>
   <![endif]-->
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
   <script src="admin/js/scripts.js"></script>
   <script>
      jQuery(document).ready(function() {       
         // initiate layout and plugins
         App.init();
      });
   </script>
   <!-- END JAVASCRIPTS -->   
</body>
<!-- END BODY -->
<!-- InstanceEnd --></html>
<?php
mysql_free_result($teklifoku);

mysql_free_result($uyebilgileri);
?>