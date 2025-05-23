<?php
require_once('../../Connections/baglan.php'); // Ensure this path is correct to include your PDO connection
require_once('../../fonksiyon.php');

session_start();

$MM_authorizedUsers = "1,2";
$MM_donotCheckaccess = "false";

function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) {
    $isValid = False;
    if (!empty($UserName)) {
        $arrUsers = explode(",", $strUsers);
        $arrGroups = explode(",", $strGroups);
        if (in_array($UserName, $arrUsers) || in_array($UserGroup, $arrGroups)) {
            $isValid = true;
        }
    }
    return $isValid;
}

if (!(isset($_SESSION['MM_Username']) && isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup']))) {
    $MM_qsChar = "?";
    $MM_referrer = $_SERVER['PHP_SELF'];
    $MM_restrictGoTo = "../../login.php" . (strpos($MM_restrictGoTo, "?") ? "&" : $MM_qsChar) . "accesscheck=" . urlencode($MM_referrer);
    header("Location: " . $MM_restrictGoTo);
    exit;
}

$colname_uyebilgileri = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";

$stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeAdi = :uyeAdi");
$stmt->execute(['uyeAdi' => $colname_uyebilgileri]);
$row_uyebilgileri = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_uyebilgileri = $stmt->rowCount();

$stmt = $pdo->query("SELECT * FROM siteconfig");
$row_ayar = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_ayar = count($row_ayar);

$uyeID = $row_uyebilgileri['uyeID'];

$stmt = $pdo->query("SELECT teklifiste.TalepID, teklifiste.aciklama, teklifiste.gondermetarih, teklifiste.teklifaktif FROM teklifiste WHERE teklifiste.teklifaktif = 0 ORDER BY teklifiste.teklifaktif ASC");
$row_teklifoku = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_teklifoku = count($row_teklifoku);

$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE kime = ? AND durum = 0");
$stmt->execute([$uyeID]);
$row_mesaj = $stmt->fetchall(PDO::FETCH_ASSOC);
$totalRows_mesaj = $stmt->rowCount();  // Count rows



$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE kime = :kime AND sil = 0 AND mesajtarihi IN (SELECT MAX(mesajtarihi) FROM sohbet GROUP BY kimden, ID ORDER BY mesajtarihi DESC)");
$stmt->execute(['kime' => $uyeID]);
$rows_mesajlar = $stmt->fetchAll(PDO::FETCH_ASSOC);
$row_mesajlar = $stmt->fetch(PDO::FETCH_ASSOC);


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
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.jqueryui.min.css">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
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
          <h3 class="card-title"></i> <?php echo $dil['mana']; ?>  </h3>


        
        <div class="card-body">
          
  <!-- KODLARRRRRRRRRR -->
  


  <div class="col-md-12"> 

                          <div class="form-group">
                 
        <?php
       
       
        ?>
           <!-- END PAGE HEADER-->
           <!-- BEGIN PAGE CONTENT-->
           <div class="row-fluid">
             <div class="span12">
               <a href="ymesaj.php"><button class="btn btn-success"></i><?php echo $dil['ymesaj']; ?></button></a>
   
       

                 <div class="widget">

         
                          <div class="form-group">    
          
                   <table id="example" class="display table-striped table-bordered dt-responsive nowrap " cellspacing="0" width="100%">
                     <thead>
                       <tr>
                         <th width="5%"><?php echo $dil['mid']; ?></th>
                         <th width="26%"><?php echo $dil['alma_talep']; ?>-<?php echo $dil['mkonu']; ?></th>
                         <th width="27%"><?php echo $dil['kimden']; ?></th>
                         <th width="17%"><?php echo $dil['durum']; ?></th>
                         <th width="18%"><?php echo $dil['tarih']; ?></th>
                         <th width="7%"><?php echo $dil['alma_islem']; ?></th>
                       </tr>
                     </thead>
                     <tbody>
<?php foreach ($rows_mesajlar as $row_mesajlar): ?>

                        <?php 

                  $durum= $row_mesajlar['durum'];
				  $stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeID = :uyeID");
$stmt->execute(['uyeID' => $row_mesajlar['kimden']]);  // Değerler güvenli bir şekilde bağlanıyor
$row_isimal = $stmt->fetch(PDO::FETCH_ASSOC);  // Sonuç tek bir satır olarak çekiliyor

// Toplam satır sayısını almak için rowCount kullanılır
$totalRows_isimal = $stmt->rowCount();
				  
				  
				  
                  ?>
  <tr class="odd gradeX">
    <td><span class="badge badge-important"><?php echo 
  $sid=$row_mesajlar['sohbetID'];
  $sid; ?></span></td>
    <td><?php echo $row_mesajlar['sohbetTalepID']; ?></td>
    <td><span class="badge badge-info"><?php echo $row_isimal['sirketAdi']; ?></span></td>
    <td><?php 
  $okuma=$row_mesajlar['durum'];
  
  ?>
    
  <?php if($okuma==0 ){?><?php echo $dil['okunmadi']; ?><?php }else{?><?php echo $dil['okundu']; ?><?php }?>
    
  </td>
    <td><?php echo $row_mesajlar['mesajtarihi']; ?></td>
    <td><a href="mesaj.php?I=<?php echo $row_mesajlar['ID']; ?>&K=<?php echo $row_mesajlar['kimden']; ?>"><span class="btn btn-info"><?php echo $dil['moku']; ?></span></a>
    <a href="sil.php?s=<?php echo $row_mesajlar['sohbetID']; ?>" onclick="return confirm('<?php echo $dil['siluyari']; ?>')"><button class="btn btn-danger"><i class="icon-remove icon-white"></i> <?php echo $dil['sil']; ?></button></a></td>
    
    
  </tr>
 
<?php endforeach; ?>
  
                     </tbody>
                   </table>
                            

     
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
     <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
   <script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
   <script src="https://cdn.datatables.net/1.10.13/js/dataTables.jqueryui.min.js"></script>
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
    "order": [[ 4, "desc" ]],
        dom: 'Bfrtip',
       buttons: [
            
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
  $(function () {
    // Summernote
    $('.textarea').summernote()
  })
</script>

</body>
</html>
