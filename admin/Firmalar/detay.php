<?php 
require_once('../../Connections/baglan.php'); // Make sure this file sets up the PDO connection
require_once('../../fonksiyon.php');

session_start();
$MM_authorizedUsers = "1,2";
$MM_donotCheckaccess = "false";

function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
    $arrUsers = explode(",", $strUsers); 
    $arrGroups = explode(",", $strGroups); 
    if (!empty($UserName)) { 
        if (in_array($UserName, $arrUsers) || in_array($UserGroup, $arrGroups)) { 
            return true; 
        } 
    } 
    return false; 
}

$MM_restrictGoTo = "../../login.php";
if (!isset($_SESSION['MM_Username']) || !isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])) {   
    $MM_qsChar = strpos($MM_restrictGoTo, "?") ? "&" : "?";
    $MM_referrer = $_SERVER['PHP_SELF'] . (empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING']);
    header("Location: " . $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer));
    exit;
}

$colname_uyebilgileri = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";
$sql = "SELECT * FROM uyeler WHERE uyeAdi = :uyeAdi";
$stmt = $pdo->prepare($sql);
$stmt->execute(['uyeAdi' => $colname_uyebilgileri]);
$row_uyebilgileri = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_uyebilgileri = $stmt->rowCount();

$sql = "SELECT TalepID, aciklama, gondermetarih, teklifaktif FROM teklifiste WHERE teklifaktif = 0 ORDER BY teklifaktif ASC";
$stmt = $pdo->query($sql);
$rows_teklifoku = $stmt->fetchAll();


$sql = "SELECT * FROM siteconfig";
$stmt = $pdo->query($sql);
$rows_ayar = $stmt->fetchAll();


$uyeID = isset($_GET['uyeID']) ? $_GET['uyeID'] : "-1"; // Sanitize and validate this input
$sql = "SELECT * FROM uyeler WHERE paylas = '1' AND uyeID = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$uyeID]);
$row_hesaplar = $stmt->fetch();


$sql = "SELECT * FROM sohbet WHERE kime = ? AND durum = 0";
$stmt = $pdo->prepare($sql);
$stmt->execute([$uyeID]); // assuming $uyeID is already set and sanitized
$rows_mesaj = $stmt->fetchAll();
$totalRows_mesaj = $stmt->rowCount();  // Count rows

$sql = "SELECT * FROM uyeler INNER JOIN resim ON uyeler.uyeID = resim.uyeID WHERE resim.yer = 'sirket' AND uyeler.uyeID = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$uyeID]);
$rows_sirketresim = $stmt->fetchAll();


$sql = "SELECT * FROM uyeler INNER JOIN resim ON uyeler.uyeID = resim.uyeID WHERE resim.yer = 'sertifika' AND uyeler.uyeID = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$uyeID]);
$rows_sertifikaresim = $stmt->fetchAll();

?>

<?php
# Session başlat

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
include '../dil/'.$dil.'.php';
?>

<!DOCTYPE html>
<html>
<head>
   <title><?php echo $row_ayar['SiteTitle']; ?></title>
   <!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="css" -->
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<meta content="<?php echo $row_ayar['Metadesc']; ?>" name="description">
<meta content="<?php echo $row_ayar['MetaName']; ?>" name="keywords">
<meta content="pixel-industry" name="author">
<!--<meta http-equiv="refresh" content="0; url=Dashboard/index.php" />    -->

  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <!-- DataTables -->
  <link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap4.css">
  <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
</head>

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.0/css/rowReorder.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css">

<body class="hold-transition sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">
  <!-- Navbar -->
         <?php

include __DIR__  . "/../pages/kisayollar/ust.php";

          ?>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">

        <?php

include __DIR__  . "/../pages/kisayollar/logo.php";
        ?>


        <?php
include __DIR__  . "/../pages/kisayollar/solmenu.php";
        ?> 

  </aside>


  <div class="content-wrapper">

    <section class="content-header">

    </section>

  <!-- ORTA ALAN -->



<section class="content">

      <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"></i><?php echo $dil['hesap_bilgileri']; ?></h3>


        </div>
        <div class="card-body">
          
  <!-- KODLARRRRRRRRRR -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">



                  <!-- Profile Image -->
                  <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                      <div class="text-center">
                              <?php if ( empty($row_hesaplar['firmalogo']) ){ echo '<img src="../resim/yok.png" width="50" height="50">'; } else { ?>
                                      
                                  <img src="../resim/firmalar/logo/<?php echo $row_hesaplar['firmalogo']; ?>" width="135" height="50">

                              <?php }?>
                      </div>

                                <h3 class="profile-username text-center"><?php echo $row_hesaplar['sirketAdi']; ?></h3>

                                <p class="text-muted text-center"><?php echo $row_hesaplar['HesapYoneticisiTitle']; ?></p>

                                      <ul class="list-group list-group-unbordered mb-3">
                                          <li><a href="../resim/firmalar/saticiBilgiDosyasi/<?php echo $row_uyebilgileri['saticiBilgiDosyasi']; ?>"><i class="icon-coffee"></i><?php echo $dil['sbdosyasi']; ?></a></li>
                                          <li><a href="../resim/firmalar/imzasirkusu/<?php echo $row_uyebilgileri['imzasirkusu']; ?>"><i class="icon-paper-clip"></i><?php echo $dil['isirku']; ?></a></li>
                                          <li><a href="../resim/firmalar/osema/<?php echo $row_uyebilgileri['osema']; ?>"><i class="icon-picture"></i><?php echo $dil['osemasi']; ?></a></li>
                                      </ul>
                                      <ul class="list-group list-group-unbordered mb-3">
                                          <li><a href="../sohbet/omesaj.php?kime=<?php echo $colname_almagoruntu_uyebilgileri; ?>"><i class="icon-comments-alt"></i><?php echo $dil['omesaj']; ?></a></li>
                                      </ul>

                    </div>
                    <!-- /.card-body -->
                  </div>


            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title"><?php echo $dil['kayit_adres']; ?></h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <p class="text-muted"><?php echo $row_hesaplar['Ulke']; ?>/</strong><?php echo $row_hesaplar['sehir']; ?><br>
                                        <?php echo $row_hesaplar['Adres']; ?></p>
                                               <strong><?php echo $row_hesaplar['LogisticsTitle']; ?></strong><br>
                                        <a href="mailto:#"><?php echo $row_hesaplar['LogisticsMail']; ?></a>

              </div>
              <!-- /.card-body -->
            </div>

            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title"><?php echo $dil['sirketresimleri']; ?></h3>
              </div>
              <!-- /.card-header -->
                          <div class="card-body">
                              <p class="text-muted"><?php if ( empty($row_sirketresim['resim']) ){ echo '<img src="../resim/yok.png" width="70" height="70">'; } else { ?>                                
                                                <?php do { ?>
                                      </p>
                              <div class="item"> <a class="fancybox-button" data-rel="fancybox-button" title="Photo" href="../resim/sirket/<?php echo $row_sirketresim['resim']; ?>" width="70" height="70">
                                  <div class="zoom"> <img src="../resim/sirket/<?php echo $row_sirketresim['resim']; ?>" width="70" height="70" alt="Photo">
                                                          <?php } while ($row_sirketresim = mysql_fetch_assoc($sirketresim));  ?>  
                      
                                  </div>
                                                </a> 
                              </div>
                          </div>
              </div>


            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title"><?php echo $dil['sertifikaresimleri']; ?></h3>
              </div>
              <!-- /.card-header -->
                          <div class="card-body">
                              <p class="text-muted"><?php if ( empty($row_sertifikaresim['resim']) ){ echo '<img src="../resim/yok.png" width="50" height="50">'; } else { ?>
                                                <?php do { ?>
                                      </p>                                             
                              <div class="item"> <a class="fancybox-button" data-rel="fancybox-button" title="Photo" href="../resim/sertifika/<?php echo $row_sertifikaresim['resim']; ?>" width="70" height="70">
                                  <div class="zoom"> <img src="../resim/sertifika/<?php echo $row_sertifikaresim['resim']; ?>" width="70" height="70" alt="Photo">
                                    <?php } while ($row_sertifikaresim = mysql_fetch_assoc($sertifikaresim)); ?>
                      
                                  </div>
                                                </a> 
                              </div>
                          </div>
              </div><?php }?><?php }?>

 </div>
  </div>




   </div>
  <div class="col-md-9">
    <div class="card">
      <div class="card-header p-2">
                                <table class="table m-0">
                                    <tbody>
                                    <tr>
                                        <td class="span4"><?php echo $dil['ulke']; ?>/<?php echo $dil['sehir']; ?>:</td>
                                        <td><?php echo $row_hesaplar['Ulke']; ?>/<?php echo $row_hesaplar['sehir']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="span4"><?php echo $dil['kayit_web_adres']; ?></td>
                                        <td><?php echo $row_hesaplar['WebAdres']; ?></td>
                                    </tr>
                                    <tr>
                                      <td class="span4"><?php echo $dil['kayit_tel_no']; ?></td>
                                      <td><?php echo $row_hesaplar['HesapYoneticisiTel']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="span4"><?php echo $dil['kayit_topla_uretim']; ?></td>
                                        <td><?php echo $row_hesaplar['ToplamuretimAlani']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="span4"><?php echo $dil['kayit_calisan_sayi']; ?></td>
                                        <td><?php echo $row_hesaplar['Calisansayi']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="span4"><?php echo $dil['kayit_gecen_yil_ciro']; ?></td>
                                        <td><?php echo $row_hesaplar['GecenYilciro']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="span4"><?php echo $dil['kayit_ekipman_yatirim']; ?></td>
                                        <td><?php echo $row_hesaplar['EkipmanYatirim']; ?></td>
                                    </tr>
                                    <tr>
                                      <td class="span4"><?php echo $dil['kayit_i_orani']; ?></td>
                                      <td><?php echo $row_hesaplar['ihracatoran']; ?></td>
                                    </tr>
                                    <tr>
                                      <td class="span4"><?php echo $dil['kayit_hukukiyapi']; ?></td>
                                      <td><?php echo $row_hesaplar['HukukiYapi']; ?></td>
                                    </tr>
                                    <tr>
                                      <td class="span4"><?php echo $dil['kayit_ref_musteri']; ?></td>
                                      <td><?php echo $row_hesaplar['ReferansMusteri']; ?></td>
                                    </tr>
                                    <tr>
                                      <td class="span4"><?php echo $dil['kayit_sales']; ?></td>
                                      <td><?php echo $row_hesaplar['SalesMarketingTitle']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $row_hesaplar['salesMarketingTel']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $row_hesaplar['salesMarketingMail']; ?></td>
                                    </tr>
                                    <tr>
                                      <td class="span4"><?php echo $dil['kayit_rd']; ?></td>
                                      <td><?php echo $row_hesaplar['RDTitle']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $row_hesaplar['RDTel']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $row_hesaplar['RDMail']; ?></td>
                                    </tr>
                                    <tr>
                                      <td class="span4"><?php echo $dil['kayit_purc']; ?></td>
                                      <td><?php echo $row_hesaplar['PurchasingTitle']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $row_hesaplar['PurchasingTel']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $row_hesaplar['PurchasingMail']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="span4"><?php echo $dil['kayit_kalite']; ?></td>
                                        <td><?php echo $row_hesaplar['qualityTitle']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $row_hesaplar['qualityTel']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $row_hesaplar['qualityMail']; ?></td>
                                    </tr>
                                  </tbody>
                              </table>
    
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
     



  <!-- KODLARRRRRRRRRR -->




  <!-- ORTA ALAN -->
  </div>


     <?php
include __DIR__  . "/../pages/kisayollar/alt.php";
     ?> 

  <aside class="control-sidebar control-sidebar-dark">

  </aside>

</div>

<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>
<!-- DataTables -->
<script src="../plugins/datatables/jquery.dataTables.js"></script>
<script src="../plugins/datatables/dataTables.bootstrap4.js"></script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>

<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
   <script src="https://cdn.datatables.net/rowreorder/1.2.0/js/dataTables.rowReorder.min.js"></script>
   <script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script> 
   <script src="https://cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js"></script> 
   <script src="//cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js"></script>
 <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
 <script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
 <script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
 <script src="//cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js"></script>
 <script src="//cdn.datatables.net/buttons/1.2.4/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('#example').DataTable( {
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    "language": {
            "lengthMenu": "Display _MENU_ records per page",
            "zeroRecords": "Nothing found - sorry",
            "info": "Showing page _PAGE_ of _PAGES_",
            "infoEmpty": "No records available",
            "infoFiltered": "(filtered from _MAX_ total records)" },
    "order": [[ 0, "desc" ]],
        dom: 'Bfrtip',
       buttons: [
            'copy', 'excel', 'pdf', 'print'
        ]
    } );
} );
</script>
<script>
$(document).ready(function() {
    $('#example2').DataTable( {
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    "language": {
            "lengthMenu": "Display _MENU_ records per page",
            "zeroRecords": "Nothing found - sorry",
            "info": "Showing page _PAGE_ of _PAGES_",
            "infoEmpty": "No records available",
            "infoFiltered": "(filtered from _MAX_ total records)" },
    "order": [[ 0, "desc" ]],
        dom: 'Bfrtip',
       buttons: [
            'copy', 'excel', 'pdf', 'print'
        ]
    } );
} );
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
</html>


