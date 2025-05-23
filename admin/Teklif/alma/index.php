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
$uyeAdi = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";
$uyeID = isset($row_uyebilgileri['uyeID']) ? $row_uyebilgileri['uyeID'] : null;

// Kullanıcıya ait bilgileri çekme
$colname_uyebilgileri = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";
$sql = "SELECT * FROM uyeler WHERE uyeAdi = :uyeAdi";
$stmt = $pdo->prepare($sql);
$stmt->execute(['uyeAdi' => $colname_uyebilgileri]);
$row_uyebilgileri = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_uyebilgileri = $stmt->rowCount();




// Teklif ve kategori bilgilerini çekme
$stmt = $pdo->prepare("SELECT DISTINCT * FROM tekliftopla 
                        INNER JOIN uyeler ON tekliftopla.toplaUyeID = uyeler.uyeID
                        INNER JOIN teklifiste ON tekliftopla.toplaTalepID = teklifiste.TalepID
                        INNER JOIN kategori ON kategori.KategoriID = teklifiste.kategorialma
                        WHERE teklifiste.istekuyeID = tekliftopla.toplateklifsahibiID 
                        AND uyeler.uyeAdi = :uyeAdi");
$stmt->execute(['uyeAdi' => $colname_uyebilgileri]);
$row_almagoruntu1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_almagoruntu1 = $stmt->rowCount();

// Aktif teklifleri çekme
$stmt = $pdo->query("SELECT teklifiste.TalepID, teklifiste.aciklama, teklifiste.gondermetarih, teklifiste.teklifaktif 
                     FROM teklifiste WHERE teklifiste.teklifaktif = 0 
                     ORDER BY teklifiste.teklifaktif DESC");
$row_teklifoku = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_teklifoku = count($row_teklifoku);

// Site ayarlarını çekme
$stmt = $pdo->query("SELECT * FROM siteconfig");
$row_ayar = $stmt->fetch(PDO::FETCH_ASSOC);

// Kullanıcıya gelen mesajları çekme
$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE sohbet.kime = :uyeID AND sohbet.durum = 0");
$stmt->execute(['uyeID' => $row_uyebilgileri['uyeID']]);
$row_mesaj = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_mesaj = count($row_mesaj);
// Tarih ve kullanıcı adı


// Tekliflerle ilgili detaylı bilgileri çekme
    $sql = "SELECT DISTINCT teklifiste.kategorialma, teklifiste.teslimyeri, teklifiste.TalepID, 
                teklifiste.spek, teklifiste.odemekosul, teklifiste.teslimsekli, teklifiste.hedeffiyat, 
                teklifiste.adresteslim, teklifiste.adres, teklifiste.postakodu, teklifiste.referansno, 
                teklifiste.uruntanim, teklifiste.forecast, teklifiste.odemevadesi, teklifiste.teslimsure, 
                teklifiste.parabirim, teklifiste.aciklama, teklifiste.gondermetarih, teklifiste.teklifaktif, 
                teklifiste.forecastsabit, teklifiste.istekuyeID, teklifiste.isteksirketadi, 
                uyeler.uyeAdi, kategori.KategoriID, kategori.KategoriAdi, kategori.Kategoriing, 
                kategori.Kategorichn, teklifiste.tbastarih, teklifiste.tbittarih 
            FROM tekliftopla, uyeler, teklifiste
            INNER JOIN kategori ON kategori.KategoriID = teklifiste.kategorialma
            WHERE $tarih < teklifiste.tbittarih 
                AND FIND_IN_SET(teklifiste.kategorialma, uyeler.Kategori) 
                AND teklifiste.TalepID NOT IN (
                    SELECT toplaTalepID FROM tekliftopla WHERE uyeler.uyeAdi = tekliftopla.toplauyeadi 
                )
                AND uyeler.uyeAdi = '$uyeAdi'";

    // Sorguyu hazırlama ve çalıştırma
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $row_Recordset1 = $stmt->fetchAll();


// Kategorileri çekme
$stmt2 = $pdo->query("SELECT * FROM kategori");
$row_Recordset2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
$totalRows_Recordset2 = count($row_Recordset2);

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
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <!-- DataTables -->


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
                   <h4><i class="icon-globe"></i><?php echo $dil['alma_tbek']; ?></h4>
                   </div>
           
           <div class="widget-body">   
    <?php  
    // Verileri PDO ile çektiğiniz varsayılarak başlayalım
    $durum = isset($row_almagoruntu1['toplaTalepID']) ? $row_almagoruntu1['toplaTalepID'] : null;  // Null coalescing operatoru kullanarak hata yönetimi
    $durum1 = isset($row_Recordset1['TalepID']) ? $row_Recordset1['TalepID'] : null;
    $seviyeID = isset($row_uyebilgileri['seviyeID']) ? $row_uyebilgileri['seviyeID'] : null;

    if ($seviyeID != 1 && $seviyeID != 2 && $durum != $durum1) {
        echo $dil['tbmesaj'];  // Uygun dilde mesajı göster
    } else {
        ?>    
        <table id="example" class="display table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th><?php echo $dil['alma_talep']; ?></th>
                    <th><?php echo $dil['alma_kategori']; ?></th>
                    <th><?php echo $dil['alma_firma']; ?></th>
                    <th><?php echo $dil['alma_forecast']; ?></th>
                    <th><?php echo $dil['teslim']; ?> (<?php echo $dil['sekli']; ?>-<?php echo $dil['yeri']; ?>-<?php echo $dil['sure']; ?>)</th>
                    <th><?php echo $dil['odeme']; ?> (<?php echo $dil['kosul']; ?>-<?php echo $dil['vade']; ?>)</th>
                    <th><?php echo $dil['alma_hfiyat']; ?></th>
                    <th><?php echo $dil['alma_islem']; ?></th>
                    <th><?php echo $dil['alma_spec']; ?></th>
                    <th><?php echo $dil['alma_btarih']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($row_Recordset1 as $item) { ?>
                    <tr class="odd gradeX">
                        <td><span class="badge badge-important"><?php echo isset($item['TalepID']) ? $item['TalepID'] : 'N/A'; ?></span></td>
                        <td>
                            <?php
                            if ($_SESSION["dil"] == 'tr') {
                                echo isset($item['KategoriAdi']) ? $item['KategoriAdi'] : 'N/A';
                            } elseif ($_SESSION["dil"] == 'en') {
                                echo isset($item['Kategoriing']) ? $item['Kategoriing'] : 'N/A';
                            } elseif ($_SESSION["dil"] == 'chn') {
                                echo isset($item['Kategorichn']) ? $item['Kategorichn'] : 'N/A';
                            }
                            ?>
                        </td>
                        <td><span class="badge badge-info"><?php echo isset($item['isteksirketadi']) ? $item['isteksirketadi'] : 'N/A'; ?></span></td>
                        <td><?php echo isset($item['forecast']) ? $item['forecast'] : 'N/A'; ?></td>
                        <td><?php echo isset($item['teslimsekli']) ? $item['teslimsekli'] : 'N/A'; ?>-<?php echo isset($item['teslimyeri']) ? $item['teslimyeri'] : 'N/A'; ?>-<?php echo isset($item['teslimsure']) ? $item['teslimsure'] : 'N/A'; ?></td>
                        <td><?php echo isset($item['odemekosul']) ? $item['odemekosul'] : 'N/A'; ?>-<?php echo isset($item['odemevadesi']) ? $item['odemevadesi'] : 'N/A'; ?></td>
                        <td><?php echo isset($item['hedeffiyat']) ? $item['hedeffiyat'] : 'N/A'; ?> (<?php echo isset($item['parabirim']) ? $item['parabirim'] : 'N/A'; ?>)</td>
                        <td>
                            <a href="teklif.php?TalepID=<?php echo isset($item['TalepID']) ? $item['TalepID'] : '#'; ?>" class="btn btn-info"><?php echo $dil['alma_ver']; ?></a>
                            <a href="../../sohbet/gonder.php?kime=<?php echo isset($item['istekuyeID']) ? $item['istekuyeID'] : '#'; ?>&talep=<?php echo isset($item['TalepID']) ? $item['TalepID'] : '#'; ?>" class="btn btn-success"><?php echo $dil['mgonder']; ?></a>
                        </td>
                        <td>
                            <a href="../../resim/spek/<?php echo isset($item['spek']) ? $item['spek'] : '#'; ?>" target="_blank" class="btn btn-warning label-mini"><?php echo $dil['alma_spec']; ?></a>
                        </td>
                        <td><?php echo isset($item['tbittarih']) ? $item['tbittarih'] : 'N/A'; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>  
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
                   <h4><i class="icon-globe"></i><?php echo $dil['alma_tver']; ?></h4>
                </div>
				
				
				
				
<div class="widget-body">
    <?php 
    $durum = isset($row_almagoruntu1['toplaUyeID']) ? $row_almagoruntu1['toplaUyeID'] : null;

    if ($durum != $uyeID) {
        echo $dil['tvmesaj'];
    } else {
        ?>
        <table id="example2" class="display table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th><?php echo $dil['alma_talep']; ?></th>
                    <th><?php echo $dil['alma_kategori']; ?></th>
                    <th><?php echo $dil['alma_firma']; ?></th>
                    <th><?php echo $dil['alma_forecast']; ?></th>
                    <th><?php echo $dil['teslim']; ?>(<?php echo $dil['sekli']; ?>-<?php echo $dil['yeri']; ?>-<?php echo $dil['sure']; ?>)</th>
                    <th><?php echo $dil['odeme']; ?>(<?php echo $dil['kosul']; ?>-<?php echo $dil['vade']; ?>)</th>
                    <th><?php echo $dil['alma_hfiyat']; ?></th>
                    <th><?php echo $dil['alma_islem']; ?></th>
                    <th width="150" class="hidden-phone"><?php echo $dil['alma_spec']; ?></th>
                    <th width="150" class="hidden-phone"><?php echo $dil['alma_itarih']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($row_almagoruntu1 as $item) { ?>
                    <tr class="odd gradeX">
                        <td><span class="badge badge-important"><?php echo htmlspecialchars($item['TalepID']); ?></span></td>
                        <td><?php echo htmlspecialchars($item[$_SESSION["dil"] == 'tr' ? 'KategoriAdi' : ($_SESSION["dil"] == 'en' ? 'Kategoriing' : 'Kategorichn')]); ?></td>
                        <td><span class="badge badge-info"><?php echo htmlspecialchars($item['isteksirketadi']); ?></span></td>
                        <td><?php echo htmlspecialchars($item['forecast']); ?></td>
                        <td><?php echo htmlspecialchars($item['teslimsekli']); ?>-<?php echo htmlspecialchars($item['toplaaciklama']); ?>-<?php echo htmlspecialchars($item['teslimsure']); ?></td>
                        <td><?php echo htmlspecialchars($item['odemekosul']); ?>-<?php echo htmlspecialchars($item['odemevadesi']); ?></td>
                        <td><?php echo htmlspecialchars($item['hedeffiyat']); ?>( <?php echo htmlspecialchars($item['parabirim']); ?> )</td>
                        <td><a href="duzenle.php?TalepID=<?php echo htmlspecialchars($item['TalepID']); ?>" class="btn btn-success label-mini"><?php echo $dil['alma_tduzenle']; ?></a></td>
                        <td class="center hidden-phone"><a href="../../resim/spek/<?php echo htmlspecialchars($item['spek']); ?>" target="_blank" class="btn btn-warning label-mini"><?php echo $dil['alma_spec']; ?></a></td>
                        <td class="center hidden-phone"><?php echo htmlspecialchars($item['toplagondermetarih']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>  
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

<script src="../../dist/js/jquery-1.8.3.js"></script>
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->

<!-- FastClick -->
<script src="../../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../dist/js/demo.js"></script>
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

