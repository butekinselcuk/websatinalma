<?php 
require_once('../../Connections/baglan.php'); 
require_once('../../fonksiyon.php');

if (!isset($_SESSION)) {
    session_start();
}

$MM_authorizedUsers = "1,2";
$MM_donotCheckaccess = "false";

function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
    $isValid = False; 
    if (!empty($UserName)) { 
        $arrUsers = explode(",", $strUsers); 
        $arrGroups = explode(",", $strGroups); 
        if (in_array($UserName, $arrUsers) || in_array($UserGroup, $arrGroups) || ($strUsers == "" && false)) { 
            $isValid = true; 
        } 
    }
    return $isValid; 
}

$MM_restrictGoTo = "../../login.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
    $MM_qsChar = strpos($MM_restrictGoTo, "?") ? "&" : "?";
    $MM_referrer = $_SERVER['PHP_SELF'] . (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0 ? "?" . $_SERVER['QUERY_STRING'] : "");
    $MM_restrictGoTo .= $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
    header("Location: " . $MM_restrictGoTo); 
    exit;
}

$colname_uyebilgileri = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";

$stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeAdi = :uyeAdi");
$stmt->execute([':uyeAdi' => $colname_uyebilgileri]);
$row_uyebilgileri = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_uyebilgileri = $stmt->rowCount();

$stmt = $pdo->query("SELECT * FROM siteconfig");
$row_ayar = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_ayar = $stmt->rowCount();

$uyeID = $row_uyebilgileri['uyeID'];
$seviyeID = $row_uyebilgileri['seviyeID'];



$stmt = $pdo->prepare("SELECT * FROM destek WHERE kime = :kime AND tarih IN (SELECT max(tarih) FROM destek GROUP BY kime, ID ORDER BY tarih DESC)");
$stmt->execute([':kime' => $uyeID]);
$rows_destek1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
$row_destek1 = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_destek1 = $stmt->rowCount();




$query_teklifoku = "SELECT teklifiste.TalepID, teklifiste.aciklama, teklifiste.gondermetarih, teklifiste.teklifaktif 
                    FROM teklifiste 
                    WHERE teklifiste.teklifaktif = 0 
                    ORDER BY teklifiste.teklifaktif ASC";

$stmt = $pdo->prepare($query_teklifoku);
$stmt->execute();
$row_teklifoku = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_teklifoku = $stmt->rowCount();


$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE kime = :kime AND durum = 0");
$stmt->execute([':kime' => $uyeID]);
$row_mesaj = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_mesaj = $stmt->rowCount();

?>


<?php

error_reporting(0); 
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
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../plugins/summernote/summernote-bs4.css">
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
          <div class="card card-primary card-outline">
          <h3 class="card-title"><?php echo $dil['yardim_merkezi']; ?></h3>
 </div>

        </div>
        <div class="card-body">
          
  <!-- KODLARRRRRRRRRR -->
     
<th width="12%"><?php echo $row_destek1['kime'] ?></th>

                 <div class="row-fluid">
             <div class="span12">
               <a href="yeni.php"><button class="btn btn-success"></i><?php echo $dil['ymesaj']; ?></button></a>
  
             
                 <div class="widget">
                 <div class="widget-title">
                   <h4></h4>
                   </div>
                   <?php
           $durum=$row_destek1['kime'];
           $durum1=$row_destek1['kimden'];
             ?>
                   
          <?php 
       
      
        ?>
             <div class="widget-body">  
             
         <?php echo $dil['gmesaj']; ?>
         </div>
      
         
                 <div class="widget-body">     
          
                   <table id="example" class="display table-striped table-bordered dt-responsive nowrap " cellspacing="0" width="100%">
                     <thead>
                       <tr>
                         <th width="12%"><?php echo $dil['departman']; ?></th>
                         <th width="55%"><?php echo $dil['baslik']; ?></th>
                         <th width="9%"><?php echo $dil['durum']; ?></th>
                         <th width="17%"><?php echo $dil['durum']; ?></th>
                         <th width="24%"><?php echo $dil['tarih']; ?></th>
                       </tr>
                     </thead>
                     
                    <tbody>
<?php foreach ($rows_destek1 as $row_destek1): ?>
    <tr class="odd gradeX">
        <td>
            <div style="text-align:center">
                <?php
                if ($row_destek1['departman'] == 1) {
                    echo 'destek';
                } elseif ($row_destek1['departman'] == 2) {
                    echo 'odeme bildirimi';
                } else {
                    echo 'Diğer';
                }
                ?>
            </div>
        </td>
        <td>
            <div style="text-align:center">
                <a href="cevap.php?D=<?php echo $row_destek1['ID']; ?>&K=<?php echo $row_destek1['kimden']; ?>">
                    #<?php echo $row_destek1['ID']; ?> - <?php echo $row_destek1['baslik']; ?>
                </a>
            </div>
        </td>
        <td>
            <div style="text-align:center">
                <?php
                switch ($row_destek1['durum']) {
                    case 0:
                        echo '<button class="btn btn-success">' . $dil['acik'] . '</button>';
                        break;
                    case 1:
                        echo '<button class="btn btn-warning">' . $dil['yanitlandi'] . '</button>';
                        break;
                    case 2:
                        echo '<button class="btn btn-danger">' . $dil['kapandi'] . '</button>';
                        break;
                    default:
                        echo '<button class="btn btn-success">' . $dil['acik'] . '</button>';
                        break;
                }
                if ($seviyeID == "1") {
                    echo '<a href="kapat.php?s=' . $row_destek1['destekID'] . '" onclick="return confirm(\'Destek Kapatılsın mı?\')"><button class="btn btn-danger">' . $dil['kapat'] . '</button></a>';
                }
                ?>
            </div>
        </td>
        <td>
            <?php echo $row_destek1['oku'] == 0 ? $dil['okunmadi'] : $dil['okundu']; ?>
        </td>
        <td>
            <div style="text-align:center">
                <?php echo $row_destek1['tarih']; ?>
            </div>
        </td>
    </tr>
<?php endforeach; ?>
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
include __DIR__  . "/../pages/kisayollar/alt.php";
     ?> 

  <aside class="control-sidebar control-sidebar-dark">

  </aside>

</div>

<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- Summernote -->
<script src="../plugins/summernote/summernote-bs4.min.js"></script>
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
