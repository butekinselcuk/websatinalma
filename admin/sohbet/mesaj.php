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
        if (in_array($UserName, $arrUsers)) { 
            $isValid = true; 
        } 
        if (in_array($UserGroup, $arrGroups)) { 
            $isValid = true; 
        } 
        if (($strUsers == "") && false) { 
            $isValid = true; 
        } 
    } 
    return $isValid; 
} 

$MM_restrictGoTo = "../../login.php"; 
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) { 
    $MM_qsChar = "?"; 
    $MM_referrer = $_SERVER['PHP_SELF']; 
    if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&"; 
    if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
    $MM_referrer .= "?" . $_SERVER['QUERY_STRING']; 
    $MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer); 
    header("Location: " . $MM_restrictGoTo); 
    exit; 
} 



$editFormAction = $_SERVER['PHP_SELF']; 
if (isset($_SERVER['QUERY_STRING'])) { 
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']); 
} 

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) { 
    $pdo->exec("UPDATE sohbet SET durum='1' WHERE ID='17'"); 
    $insertSQL = "INSERT INTO sohbet (sohbetTalepID, kime, mesaj, kimden, durum, ID) VALUES (?, ?, ?, ?, ?, ?)"; 
    $stmt = $pdo->prepare($insertSQL); 
    $stmt->execute([$_POST['sohbetTalepID'], $_POST['kime'], $_POST['mesaj'], $_POST['kimden'], $_POST['durum'], $_POST['ID']]); 

    $insertGoTo = ""; 
    if (isset($_SERVER['QUERY_STRING'])) { 
        $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?"; 
        $insertGoTo .= $_SERVER['QUERY_STRING']; 
    } 
    header(sprintf("Location: %s", $insertGoTo)); 
} 

$colname_uyebilgileri = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";

$stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeAdi = :uyeAdi");
$stmt->execute(['uyeAdi' => $colname_uyebilgileri]);
$row_uyebilgileri = $stmt->fetch(PDO::FETCH_ASSOC);


$stmt = $pdo->query("SELECT * FROM teklifiste WHERE teklifaktif = 0 ORDER BY teklifaktif ASC"); 
$row_teklifoku = $stmt->fetch(PDO::FETCH_ASSOC); 

$stmt = $pdo->query("SELECT * FROM siteconfig"); 
$row_ayar = $stmt->fetch(PDO::FETCH_ASSOC); 

$ID = isset($_GET['I']) ? $_GET['I'] : null;


$uyeID = $row_uyebilgileri['uyeID']; 

$stmt = $pdo->prepare("SELECT DISTINCT * FROM sohbet WHERE sohbet.ID = ? AND (sohbet.kime = ? OR sohbet.kimden = ?)"); 
$stmt->execute([$ID, $uyeID, $uyeID]); 
$rows_sohbet = $stmt->fetchAll(PDO::FETCH_ASSOC);
$row_sohbet = $stmt->fetch(PDO::FETCH_ASSOC); 


$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE sohbet.kime = ? AND sohbet.durum = 0"); 
$stmt->execute([$uyeID]); 
$row_mesaj = $stmt->fetch(PDO::FETCH_ASSOC); 
$totalRows_mesaj = $stmt->rowCount();  // Count rows




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
            <div class="card card-prirary cardutline direct-chat direct-chat-primary">
 <div class="card-body">



<?php foreach ($rows_sohbet as $row_sohbet): ?>
    <?php 
           $talep=$row_sohbet['sohbetTalepID'];
		   $kime = filter_input(INPUT_GET, 'K', FILTER_SANITIZE_STRING);
            ?>
            
             <?php 
    // Uye bilgilerini çekme
    $stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeID = :kime");
    $stmt->bindParam(':kime', $kime, PDO::PARAM_INT);
    $stmt->execute();
    $row_isimal = $stmt->fetch(PDO::FETCH_ASSOC);
                  
                  ?>
                                   


                  <!-- Message. Default to the left -->
                                   
                                         <?php 
        if($row_sohbet['kimden']== $row_uyebilgileri['uyeID']){

                   ?>
                                     <div class="direct-chat-msg">
                                          <div class="direct-chat-info clearfix">
                                            <span class="direct-chat-name float-left"><?php echo $row_uyebilgileri['sirketAdi']; ?>(<?php echo $row_uyebilgileri['uyeAdi']; ?>)</span>
                                            <span class="direct-chat-timestamp float-right"><?php echo $row_sohbet['mesajtarihi']; ?></span>
                                          </div>
                                          <!-- /.direct-chat-info -->
                                          <img class="direct-chat-img" src="../dist/img/user1-128x128.jpg" alt="Message User Image">
                                          <!-- /.direct-chat-img -->
                                           <div class="direct-chat-text">
                                            <?php echo $row_sohbet['mesaj']; ?> 
                                          </div>
                                          <!-- /.direct-chat-text -->
                                    </div>
                                                          <!-- /.direct-chat-msg -->

           <?php } else { ?>

                <!-- Message to the right -->
                                   <div class="direct-chat-msg right">
                                          <div class="direct-chat-info clearfix">
                                            <span class="direct-chat-name float-right"><?php echo $row_isimal['sirketAdi']; ?>(<?php echo $row_isimal['uyeAdi']; ?>)</span>
                                            <span class="direct-chat-timestamp float-left"><?php echo $row_sohbet['mesajtarihi']; ?></span>
                                          </div>
                                          <!-- /.direct-chat-info -->
                                          <img class="direct-chat-img" src="../dist/img/user3-128x128.jpg" alt="Message User Image">
                                          <!-- /.direct-chat-img -->
                                          <div class="direct-chat-text">
                                            <?php echo $row_sohbet['mesaj']; ?>
                                          </div>
                                          <!-- /.direct-chat-text -->
                                    </div>
                                        <!-- /.direct-chat-msg -->


 <?php 
                          $sid = $row_sohbet['sohbetID'];
            $updateStmt = $pdo->prepare("UPDATE sohbet SET durum = 1 WHERE sohbetID = :sid");
            $updateStmt->bindParam(':sid', $sid, PDO::PARAM_INT);
            $updateStmt->execute();?>
                                  

                                    <?php   }
                                        ?>

                                        
                <!-- Contacts are loaded here -->



<?php endforeach; ?>
                                  <!-- /.card-body -->
                                  <div class="card-footer">
                                    <form action="<?php echo $editFormAction; ?>" name="form1" method="POST" enctype="multipart/form-data">  
                                      <div class="input-group">
                                          <input name="sohbetTalepID" type="hidden" id="sohbetTalepID" value="<?php echo $talep; ?>">
                                          <input type="hidden" name="kime" id="kime" value="<?php echo  $kime; ?>">
                                          <input type="hidden" name="kimden" id="kimden" value="<?php echo $row_uyebilgileri['uyeID']; ?>">
                                          <input type="hidden" name="durum" id="durum" value="0">   
                                          <input type="hidden" name="ID" id="ID" value="<?php echo $ID; ?>">
                                          <input type="text" id="comment-md" name="mesaj" placeholder="Type Message ..." class="form-control">
                                          <span class="input-group-append">
                                          <button type="submit" class="btn btn-primary"><?php echo $dil['kaydet']; ?></button>
                                        </span>
                                      </div>
                                      <input type="hidden" name="MM_insert" value="form1">
                                    </form>
                                  </div>
                                  <!-- /.card-footer-->
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


