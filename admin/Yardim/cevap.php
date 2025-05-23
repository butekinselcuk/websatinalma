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
    $arrUsers = explode(",", $strUsers);
    $arrGroups = explode(",", $strGroups);

    if (!empty($UserName)) {
        if (in_array($UserName, $arrUsers) || in_array($UserGroup, $arrGroups)) {
            $isValid = true;
        }
        if ($strUsers == "" && false) {
            $isValid = true;
        }
    }
    return $isValid;
}

// Redirect if not authorized
if (!(isset($_SESSION['MM_Username']) && isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup']))) {
    $MM_restrictGoTo = "../../login.php";
    $MM_qsChar = "?";
    $MM_referrer = $_SERVER['PHP_SELF'];
    if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
    if (!empty($_SERVER['QUERY_STRING'])) {
        $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
    }
    header("Location: " . $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer));
    exit;
}

// Define $editFormAction for form submission
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}




if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
    // Prepare the insert statement using placeholders for security
    $sql = "INSERT INTO destek (kimden, seviyeID, durum, departman, oncelik, mesaj, baslik, ID, oku, kimdenadi, kime) 
            VALUES (:kimden, :seviyeID, :durum, :departman, :oncelik, :mesaj, :baslik, :ID, :oku, :kimdenadi, :kime)";
    $stmt = $pdo->prepare($sql);

    // Bind the values from the form to the prepared statement
    $stmt->bindParam(':kimden', $_POST['kimden'], PDO::PARAM_INT);
    $stmt->bindParam(':seviyeID', $_POST['seviyeID'], PDO::PARAM_INT);
    $stmt->bindParam(':durum', $_POST['durum'], PDO::PARAM_INT);
    $stmt->bindParam(':departman', $_POST['departman'], PDO::PARAM_STR);
    $stmt->bindParam(':oncelik', $_POST['oncelik'], PDO::PARAM_STR);
    $stmt->bindParam(':mesaj', $_POST['mesaj'], PDO::PARAM_STR);
    $stmt->bindParam(':baslik', $_POST['baslik'], PDO::PARAM_STR);
    $stmt->bindParam(':ID', $_POST['ID'], PDO::PARAM_INT);
    $stmt->bindParam(':oku', $_POST['oku'], PDO::PARAM_INT);
    $stmt->bindParam(':kimdenadi', $_POST['kimdenadi'], PDO::PARAM_STR);
    $stmt->bindParam(':kime', $_POST['kime'], PDO::PARAM_INT);

    // Execute the statement and check for errors
    if($stmt->execute()) {
        $insertGoTo = "index.php";
        if (isset($_SERVER['QUERY_STRING'])) {
            $insertGoTo .= (strpos($insertGoTo, '?') !== false) ? "&" : "?";
            $insertGoTo .= $_SERVER['QUERY_STRING'];
        }
        header("Location: $insertGoTo");
    } else {
        echo "Error: " . $stmt->errorInfo()[2];
    }
}




$colname_uyebilgileri = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";
$stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeAdi = :uyeAdi");
$stmt->execute(['uyeAdi' => $colname_uyebilgileri]);
$row_uyebilgileri = $stmt->fetch();
$totalRows_uyebilgileri = $stmt->rowCount();


$stmt = $pdo->prepare("SELECT TalepID, aciklama, gondermetarih, teklifaktif FROM teklifiste WHERE teklifaktif = 0 ORDER BY teklifaktif ASC");
$stmt->execute();
$row_teklifoku = $stmt->fetchAll();
$totalRows_teklifoku = count($row_teklifoku);

$stmt = $pdo->prepare("SELECT * FROM siteconfig");
$stmt->execute();
$row_ayar = $stmt->fetch();
$totalRows_ayar = $stmt->rowCount();

$ID = filter_input(INPUT_GET, 'D', FILTER_SANITIZE_NUMBER_INT);
$uyeID = htmlspecialchars($row_uyebilgileri['uyeID']);
$seviyeID = htmlspecialchars($row_uyebilgileri['seviyeID']);

$stmt = $pdo->prepare("SELECT * FROM destek WHERE ID = :ID AND (kime = :uyeID OR kimden = :uyeID OR seviyeID = :seviyeID)");
$stmt->execute(['ID' => $ID, 'uyeID' => $uyeID, 'seviyeID' => $seviyeID]);
$rows_destek = $stmt->fetchAll(PDO::FETCH_ASSOC);
$row_destek = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_destek = $stmt->rowCount();

$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE kime = :uyeID AND durum = 0");
$stmt->execute(['uyeID' => $uyeID]);
$row_mesaj = $stmt->fetchAll();
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


<div class="card card-prirary cardutline direct-chat direct-chat-primary">
 <div class="card-body">

<?php foreach ($rows_destek as $row_destek): ?>
                       <div class="timeline-messages">
                       </div>
                       <!-- Comment -->
                       <?php 
        $kimden = $row_destek['kimden'];
        $kime = filter_input(INPUT_GET, 'K', FILTER_SANITIZE_NUMBER_INT);
        $dpr = $row_destek['departman']; 
        $onc = $row_destek['oncelik'];
        $baslik = $row_destek['baslik'];

        // Preparing a PDO statement to fetch user data
        $stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeID = :kimden");
        $stmt->bindParam(':kimden', $kimden, PDO::PARAM_INT);
        $stmt->execute();
        $row_isimal = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalRows_isimal = $stmt->rowCount();
    ?>

                                 
                                    
                        <?php 
                                     if($row_destek['kimden']== $row_uyebilgileri['uyeID']){

                   ?>



                                     <div class="direct-chat-msg">
                                          <div class="direct-chat-info clearfix">
                                            <span class="direct-chat-name float-left"><?php echo $row_uyebilgileri['sirketAdi']; ?>(<?php echo $row_uyebilgileri['uyeAdi']; ?>)</span>
                                            <span class="direct-chat-timestamp float-right"><?php echo $row_destek['tarih']; ?></span>
                                          </div>
                                          <!-- /.direct-chat-info -->
                                          <img class="direct-chat-img" src="../dist/img/user1-128x128.jpg" alt="Message User Image">
                                          <!-- /.direct-chat-img -->
                                           <div class="direct-chat-text">
                                            <?php echo $row_destek['mesaj']; ?> 
                                          </div>
                                          <!-- /.direct-chat-text -->
                                    </div>


                           <?php } else { ?>


                <!-- Message to the right -->
                                   <div class="direct-chat-msg right">
                                          <div class="direct-chat-info clearfix">
                                            <span class="direct-chat-name float-right"><?php echo $row_isimal['sirketAdi']; ?>(<?php echo $row_isimal['uyeAdi']; ?>)</span>
                                            <span class="direct-chat-timestamp float-left"><?php echo $row_destek['tarih']; ?></span>
                                          </div>
                                          <!-- /.direct-chat-info -->
                                          <img class="direct-chat-img" src="../dist/img/user3-128x128.jpg" alt="Message User Image">
                                          <!-- /.direct-chat-img -->
                                          <div class="direct-chat-text">
                                            <?php echo $row_destek['mesaj']; ?>
                                          </div>
                                          <!-- /.direct-chat-text -->
                                    </div>


<?php 
    $sid = $row_destek['destekID'];

    // Preparing a PDO statement to update data securely
    $stmt = $pdo->prepare("UPDATE destek SET oku = :oku WHERE destekID = :destekID");
    $stmt->bindParam(':oku', $oku, PDO::PARAM_INT);
    $oku = 1;  // Assuming 'oku' is intended to be set to 1
    $stmt->bindParam(':destekID', $sid, PDO::PARAM_INT);
    $stmt->execute();
?>

                                    

                           <?php }  ?>
<?php endforeach; ?>



          <div class="card-footer">
                                  
                          <form action="<?php echo $editFormAction; ?>" name="form1" method="POST" enctype="multipart/form-data">  
                 <div class="input-group">

                   <input name="ID" type="hidden" id="sohbetTalepID" value="<?php echo $ID; ?>">
                   <input type="hidden" name="kime" id="kime" value="<?php echo  $kime; ?>">
                   <input type="hidden" name="baslik" id="baslik" value="<?php echo  $baslik; ?>">
                   <input type="hidden" name="seviyeID" id="seviyeID" value="1">
                   <input type="hidden" name="oku" id="oku" value="0">
                   <input type="hidden" name="departman" id="departman" value="<?php echo $dpr; ?>">
                   <input type="hidden" name="oncelik" id="oncelik" value="<?php echo  $onc; ?>">
                   <input type="hidden" name="kimden" id="kimden" value="<?php echo $row_uyebilgileri['uyeID']; ?>">
                   <input type="hidden" name="kimdenadi" id="kimdenadi" value="<?php echo $row_uyebilgileri['uyeAdi']; ?>">
                   <input type="hidden" name="durum" id="durum" value="1">   
                   <input type="hidden" name="ID" id="ID" value="<?php echo $ID; ?>">

                  <input type="text" id="comment-md" name="mesaj" placeholder="Type Message ..." class="form-control">

                   
                                          <span class="input-group-append">
                   <button type="submit" class="btn btn-primary"><?php echo $dil['gonder']; ?></button>
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
