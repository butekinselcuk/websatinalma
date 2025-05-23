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
$stmt = $pdo->query($sql); // Simple query without parameters
$row_teklifoku = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_teklifoku = count($row_teklifoku);

$sql = "SELECT * FROM siteconfig";
$stmt = $pdo->query($sql);
$row_ayar = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_ayar = $stmt->rowCount();

$sql = "SELECT uyeID, sirketAdi, Ulke, sehir, HesapYoneticisiTitle, HesapYoneticisiTel, HesapYoneticisiMail, paylas FROM uyeler WHERE paylas = 1";
$stmt = $pdo->query($sql);
$row_Recordset1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_Recordset1 = count($row_Recordset1);

$uyeID = isset($row_uyebilgileri['uyeID']) ? $row_uyebilgileri['uyeID'] : 0;
$sql = "SELECT * FROM sohbet WHERE kime = :uyeID AND durum = 0";
$stmt = $pdo->prepare($sql);
$stmt->execute(['uyeID' => $uyeID]);
$row_mesaj = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_mesaj = count($row_mesaj);

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
include '../dil/' . $_SESSION['dil'] . '.php';
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
          <h3 class="card-title"><?php echo $dil['firmalar']; ?></h3>


        </div>
        <div class="card-body">
          
  <!-- KODLARRRRRRRRRR -->


            <div class="card card-primary card-outline">   
  
       <div class="row-fluid">
         <div class="span12">



                                   <form method="POST" action="<?php echo $editFormAction; ?>" name="form1"   id="form1" onSubmit="return check_frmm()" enctype="multipart/form-data" >

<table id="example2" class="display table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th width="900" style="width:8px;"><?php echo $dil['alma_firma']; ?></th>
            <th width="100"><?php echo $dil['ulke']; ?></th>
            <th width="100"><?php echo $dil['sehir']; ?></th>
            <th width="100"><?php echo $dil['kayit_personel']; ?></th>
            <th width="100" class="hidden-phone"><?php echo $dil['detay']; ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($row_Recordset1 as $record) { ?>
            <tr class="odd gradeX">
                <td><?php echo $record['sirketAdi']; ?></td>
                <td><?php echo $record['Ulke']; ?></td>
                <td><?php echo $record['sehir']; ?></td>
                <td><?php echo $record['HesapYoneticisiTitle']; ?></td>
                <td>
                    <center>
                        <a href="detay.php?uyeID=<?php echo $record['uyeID']; ?>">
                            <span class="label label-info"><?php echo $dil['hesap_bilgileri']; ?></span>
                        </a>
                    </center>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

                 </div>
          
             </div>

      </div>
          
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


