<?php 
require_once('../../../Connections/baglan.php'); 
require_once('../../../fonksiyon.php'); 

$tarih = date("Y/m/d");
if (!isset($_SESSION)) {
    session_start();
}

$MM_authorizedUsers = "1,2";
$MM_donotCheckaccess = "false";

// *** Sayfaya Erişim Kısıtlaması: Bu fonksiyon kullanıcı erişimini kontrol eder.
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
    $isValid = False; 

    if (!empty($UserName)) { 
        $arrUsers = explode(",", $strUsers); 
        $arrGroups = explode(",", $strGroups); 
        if (in_array($UserName, $arrUsers) || in_array($UserGroup, $arrGroups)) { 
            $isValid = true; 
        }
        if ($strUsers == "" && false) { 
            $isValid = true; 
        }
    }
    return $isValid; 
}

$MM_restrictGoTo = "../../../login.php";
if (!((isset($_SESSION['MM_Username'])) && isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup']))) {   
    $MM_qsChar = "?";
    $MM_referrer = $_SERVER['PHP_SELF'];
    if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
    if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) {
        $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
    }
    $MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
    header("Location: " . $MM_restrictGoTo); 
    exit;
}




$colname_uyebilgileri = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";
$stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeAdi = ?");
$stmt->execute([$colname_uyebilgileri]);
$row_uyebilgileri = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_uyebilgileri = $stmt->rowCount();



$sql = "SELECT DISTINCT teklifiste.*, uyeler.*, kategori.*
        FROM teklifiste
        INNER JOIN uyeler ON uyeler.uyeID = teklifiste.istekuyeID
        INNER JOIN kategori ON kategori.KategoriID = teklifiste.kategorialma
        WHERE 
    teklifiste.TalepID NOT IN (
        SELECT 
            tekliftopla.toplaTalepID 
        FROM 
            tekliftopla, 
            teklifata 
        WHERE 
            teklifata.ataTalepID = teklifiste.TalepID
    )
    AND uyeler.uyeAdi = :uyebilgileri
        ORDER BY teklifiste.TalepID DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['uyebilgileri' => $colname_uyebilgileri]);
$almagoruntu = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt = $pdo->query("SELECT * FROM siteconfig");
$row_ayar = $stmt->fetch(PDO::FETCH_ASSOC);


$stmt = $pdo->query("SELECT * FROM teklifata 
                     INNER JOIN teklifiste ON teklifiste.TalepID = teklifata.ataTalepID 
                     INNER JOIN kategori ON teklifiste.kategorialma = kategori.KategoriID");
$row_atanan = $stmt->fetchAll();
$totalRows_atanan = count($row_atanan);


// PDO query with placeholders
$sql = "SELECT DISTINCT teklifiste.*, uyeler.*, teklifata.*, tekliftopla.*, kategori.* 
        FROM teklifiste 
        INNER JOIN uyeler ON teklifiste.istekuyeID = uyeler.uyeID 
        INNER JOIN teklifata ON teklifata.verenuyeID = teklifiste.istekuyeID AND teklifata.ataTalepID = teklifiste.TalepID 
        INNER JOIN tekliftopla ON tekliftopla.toplaUyeID = teklifata.alanuyeID AND teklifata.ataTalepID = tekliftopla.toplaTalepID 
        INNER JOIN kategori ON teklifiste.kategorialma = kategori.KategoriID  
        WHERE uyeler.uyeAdi = :uyead";

// Prepare and execute query
$stmt = $pdo->prepare($sql);
$stmt->execute(['uyead' => $colname_uyebilgileri]);

// Fetch all results
$row_yeniatanan = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_yeniatanan = count($row_yeniatanan);  // Count results

$uyeID = isset($row_uyebilgileri['uyeID']) ? $row_uyebilgileri['uyeID'] : 0;
$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE kime = ? AND durum = 0");
$stmt->execute([$uyeID]);
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
include '../../dil/' . $_SESSION['dil'] . '.php';
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
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../../plugins/summernote/summernote-bs4.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.0/css/rowReorder.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css">

</head>
<body class="hold-transition sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">
  <!-- Navbar -->
         <?php

include __DIR__  . "/../../pages/kisayollar/ust.php";

          ?>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">

        <?php

include __DIR__  . "/../../pages/kisayollar/logo.php";
        ?>


        <?php
include __DIR__  . "/../../pages/kisayollar/solmenu.php";
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
          <h3 class="card-title"> </h3>


        </div>
        <div class="card-body">
          
  <!-- KODLARRRRRRRRRR -->
  

           <div class="row-fluid">
             <div class="span12">
    
             
               <div class="widget">
                 <div class="widget-title">
                    <div class="card card-primary card-outline">

                           <h4><i class="icon-globe"></i><?php echo $dil['tekliflerim']; ?></h4>
                           </div>
           
                <div class="widget-body">
    <table id="example" class="display table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th><?php echo $dil["alma_talep"]; ?></th>
                <th><?php echo $dil["alma_kategori"]; ?></th>
                <th><?php echo $dil["alma_firma"]; ?></th>
                <th><?php echo $dil["alma_forecast"]; ?></th>
                <th><?php echo $dil["alma_teslim"]; ?>(<?php echo $dil["teslimyeri"]; ?>)-<?php echo $dil["alma_tsure"]; ?></th>
                <th><?php echo $dil["alma_okosul"]; ?>-<?php echo $dil["alma_ovade"]; ?></th>
                <th><?php echo $dil["alma_hfiyat"]; ?></th>
                <th><?php echo $dil["alma_islem"]; ?></th>
                <th><?php echo $dil["alma_islem"]; ?></th>
                <th><?php echo $dil["alma_spec"]; ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th><?php echo $dil["alma_talep"]; ?></th>
                <th><?php echo $dil["alma_kategori"]; ?></th>
                <th><?php echo $dil["alma_firma"]; ?></th>
                <th><?php echo $dil["alma_forecast"]; ?></th>
                <th><?php echo $dil["alma_teslim"]; ?>(<?php echo $dil["teslimyeri"]; ?>)-<?php echo $dil["alma_tsure"]; ?></th>
                <th><?php echo $dil["alma_okosul"]; ?>-<?php echo $dil["alma_ovade"]; ?></th>
                <th><?php echo $dil["alma_hfiyat"]; ?></th>
                <th><?php echo $dil["alma_islem"]; ?></th>
                <th><?php echo $dil["alma_islem"]; ?></th>
                <th><?php echo $dil["alma_spec"]; ?></th>
            </tr>
        </tfoot>
        <tbody>
            <?php foreach ($almagoruntu as $row) { ?>
            <tr class="odd gradeX">
                <td><span class="badge badge-important"><?php echo isset($row['TalepID']) ? $row['TalepID'] : 'N/A'; ?></span></td>
                           <td>

   <?php if ($_SESSION["dil"] == 'tr' ) {
  ?> 
    
                  <?php echo isset($row['KategoriAdi']) ? $row['KategoriAdi'] : 'N/A'; ?>
        
        
 <?php } elseif ($_SESSION["dil"] == 'en') {
   
   
   ?> 
                <?php echo isset($row['Kategoriing']) ? $row['Kategoriing'] : 'N/A'; ?>
<?php } elseif ($_SESSION["dil"] == 'chn' ) {
        
      ?> 
                     <?php echo isset($row['Kategorichn']) ? $row['Kategorichn'] : 'N/A'; ?>         
                         
                   <?php } ?>   
                           
                           </td>
                <td><span class="badge badge-info"><?php echo isset($row['isteksirketadi']) ? $row['isteksirketadi'] : 'N/A'; ?></span></td>
                <td><?php echo isset($row['forecast']) ? $row['forecast'] : 'N/A'; ?></td>
                <td><?php echo isset($row['teslimsekli']) ? $row['teslimsekli'] : 'N/A'; ?>-(<?php echo isset($row['teslimyeri']) ? $row['teslimyeri'] : 'N/A'; ?>)-<?php echo isset($row['teslimsure']) ? $row['teslimsure'] : 'N/A'; ?></td>
                <td><?php echo isset($row['odemekosul']) ? $row['odemekosul'] : 'N/A'; ?>-<?php echo isset($row['odemevadesi']) ? $row['odemevadesi'] : 'N/A'; ?></td>
                <td><?php echo isset($row['hedeffiyat']) ? $row['hedeffiyat'] : 'N/A'; ?> (<?php echo isset($row['parabirim']) ? $row['parabirim'] : 'N/A'; ?>)</td>
                <td><a href="topla.php?toplaTalepID=<?php echo isset($row['TalepID']) ? $row['TalepID'] : '#'; ?>" target="_blank" class="btn btn-info"><?php echo $dil["teklif_durumu"]; ?></a></td>
                <td><a href="../verme/duzenle.php?TalepID=<?php echo isset($row['TalepID']) ? $row['TalepID'] : '#'; ?>" target="_blank" class="btn btn-success"><?php echo $dil["teklif_duzenle"]; ?></a></td>
                <td><a href="../../resim/spek/<?php echo isset($row['spek']) ? $row['spek'] : '#'; ?>" target="_blank" class="btn btn-warning"><?php echo $dil["verme_spec"]; ?></a></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
       
                 </div>
                 <div class="widget-body">
                   <p></p>
                 </div>
               </div>
               
                <div class="row-fluid">
             <div class="span12">
             
         
               

               
<div class="widget">
                <div class="widget-title">
                    <div class="card card-primary card-outline">
                   <h4><i class="icon-globe"></i><?php echo $dil["atananteklif"]; ?></h4>
                </div>
                 <div class="widget-body">      
                  
                   <table id="example2" class="display table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                       <thead>
                         
                         <tr>
                           <th><?php echo $dil["alma_talep"]; ?></th>
                           <th><?php echo $dil["alma_kategori"]; ?></th>
                           <th><?php echo $dil["verme_ref"]; ?></th>
                           <th><?php echo $dil["verme_ktanim"]; ?></th>
                           <th><?php echo $dil["verme_forecast"]; ?></th>
                           <th><?php echo $dil["teklifalanfirma"]; ?></th>
                           <th><?php echo $dil["verme_vade"]; ?></th>
                           <th><?php echo $dil["atananfiyat"]; ?></th>
                           <th><?php echo $dil["toplamfiyat"]; ?></th>
                           <th><?php echo $dil["verilenteklifler"]; ?></th>
                         </tr>
                     </thead>
                       <tfoot>
                         <tr>
               <th><?php echo $dil["alma_talep"]; ?></th>
                           <th><?php echo $dil["alma_kategori"]; ?></th>
                           <th><?php echo $dil["verme_ref"]; ?></th>
                           <th><?php echo $dil["verme_ktanim"]; ?></th>
                           <th><?php echo $dil["verme_forecast"]; ?></th>
                           <th><?php echo $dil["teklifalanfirma"]; ?></th>
                           <th><?php echo $dil["verme_vade"]; ?></th>
                           <th><?php echo $dil["atananfiyat"]; ?></th>
                           <th><?php echo $dil["toplamfiyat"]; ?></th>
                           <th><?php echo $dil["verilenteklifler"]; ?></th>
                         </tr>
                     </tfoot>
                       <tbody>
                          <?php foreach ($row_yeniatanan as $row1) { ?>
                         
                         <tr class="odd gradeX">
                           <td><span class="badge badge-important"><?php echo $row1['ataTalepID']; ?></span></td>
                           <td>

   <?php if ($_SESSION["dil"] == 'tr' ) {
  ?> 
    
                  <?php echo $row1['KategoriAdi']; ?>
        
        
 <?php } elseif ($_SESSION["dil"] == 'en') {
   
   
   ?> 
                <?php echo $row1['Kategoriing']; ?>
<?php } elseif ($_SESSION["dil"] == 'chn' ) {
        
      ?> 
                     <?php echo $row1['Kategorichn']; ?>         
                         
                   <?php } ?>   
                           
                           </td>
                           <td><?php echo $row1['referansno']; ?></td>
                           <td><?php echo $row1['uruntanim']; ?></td>
                           <td><?php echo $row1['forecast']; ?></td>
                           <td><span class="alert-info"><?php echo $row1['toplasirketAdi']; ?></span></td>
                           <td><?php echo $row1['toplaodemekosul']; ?><?php echo $row1['toplaodemevadesi']; ?></td>
                           <td><?php echo $row1['toplafiyat']; ?></td>
                           <td><span class="text-green3">
                             <?php
                    $sonuc=$row1['toplafiyat']*$row1['forecast'];?>
                           <?php echo $sonuc; ?><?php echo $row1['parabirim']; ?></span></td>
                           <td><a href="teklifler.php?toplaTalepID=<?php echo $row1['TalepID']; ?>" class="btn btn-info"><?php echo $dil["teklif_durumu"]; ?></a></td>
                         </tr>
                          <?php } ?>
                     </tbody>
                   </table>
                    
             
               </div>
        </div>




  <!-- KODLARRRRRRRRRR -->

        </div>


      </div>


    </section>


  <!-- ORTA ALAN -->
  </div>


     <?php
include __DIR__  . "/../../pages/kisayollar/alt.php";
     ?> 

  <aside class="control-sidebar control-sidebar-dark">

  </aside>

</div>

<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- FastClick -->
<script src="../../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../dist/js/demo.js"></script>
<!-- Summernote -->
<script src="../../plugins/summernote/summernote-bs4.min.js"></script>
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
