<?php 
require_once('../../../Connections/baglan.php');
require_once('../../../fonksiyon.php');

if (!isset($_SESSION)) {
    session_start();
}

$MM_authorizedUsers = "1";
$MM_donotCheckaccess = "false";

// Define authorization logic
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
    $isValid = False;
    $arrUsers = Explode(",", $strUsers);
    $arrGroups = Explode(",", $strGroups);

    if (!empty($UserName)) { 
        if (in_array($UserName, $arrUsers) || in_array($UserGroup, $arrGroups)) { 
            $isValid = true;
        }
    }

    return $isValid;
}

// Redirect unauthorized users
$MM_restrictGoTo = "../../../login.php";
if (!(isset($_SESSION['MM_Username']) && isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup']))) {   
    $MM_qsChar = "?";
    $MM_referrer = $_SERVER['PHP_SELF'];
    if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
    if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
        $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
    $MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
    header("Location: " . $MM_restrictGoTo); 
    exit;
}


// Define $editFormAction for form submission
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}



// Handle form submission
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "seviyekaydet")) {
    $stmt = $pdo->prepare("INSERT INTO seviye (SeviyeAdi) VALUES (?)");
    $stmt->execute([$_POST['SeviyeAdi']]);

    $insertGoTo = "index.php?Ekle=EklemeBasarili";
    if (isset($_SERVER['QUERY_STRING'])) {
        $insertGoTo .= (strpos($insertGoTo, '?') === false ? "?" : "&") . $_SERVER['QUERY_STRING'];
    }
    header("Location: " . $insertGoTo);
}

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeAdi = ?");
$stmt->execute([$_SESSION['MM_Username']]);
$uyebilgileri = $stmt->fetch();
$totalRows_uyebilgileri = $stmt->rowCount();

// Fetch offers
$stmt = $pdo->prepare("SELECT * FROM teklifiste WHERE teklifaktif = 0 ORDER BY teklifaktif ASC");
$stmt->execute();
$teklifoku = $stmt->fetchAll();
$totalRows_teklifoku = count($teklifoku);

// Fetch settings
$stmt = $pdo->prepare("SELECT * FROM siteconfig");
$stmt->execute();
$ayar = $stmt->fetch();
$totalRows_ayar = $stmt->rowCount();

// Fetch level details
if (isset($_GET['SeviyeID'])) {
    $stmt = $pdo->prepare("SELECT * FROM seviye WHERE SeviyeID = ?");
    $stmt->execute([$_GET['SeviyeID']]);
    $seviye = $stmt->fetch();
    $totalRows_seviye = $stmt->rowCount();
}

// Fetch messages
$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE kime = ? AND durum = 0");
$stmt->execute([$uyebilgileri['uyeID']]);
$mesaj = $stmt->fetchAll();
$totalRows_mesaj = count($mesaj);
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
  <link rel="stylesheet" href="../../plugins/datatables/dataTables.bootstrap4.css">
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
          <h3 class="card-title">Seviye Görüntüleme Ekranı</h3>


        </div>
        <div class="card-body">
          
  <!-- KODLARRRRRRRRRR -->
 

    <div class="row-fluid">
      <div class="span12">
        <div class="widget">
                 <div class="widget-title">
                   <h4><i class="icon-plus"></i>Seviye Ekleme Formu</h4>
                 </div>
                 <div class="widget-body">
                   <p>
                   
                   <form method="POST" action="<?php echo $editFormAction; ?>" name="seviyekaydet" class="form-horizontal" id="seviyekaydet">
                   
                   
                   
                   <div class="control-group">
                     <label class="control-label">Seviye Adı :</label>
                              <div class="controls">
                                 <input name="SeviyeAdi" type="text" class="span6 " id="SeviyeAdi">
                                

                              <button type="submit" class="btn btn-success">Ekle</button>
                              <input type="hidden" name="MM_insert" value="seviyekaydet">

                                
                   
                   </form>

                   </p>
                  
                 </div>
               </div>
             </div>
           </div>
     </div>
     </div>

  <!-- KODLARRRRRRRRRR -->

        </div>
        <!-- /.card-body -->

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
<!-- DataTables -->
<script src="../../plugins/datatables/jquery.dataTables.js"></script>
<script src="../../plugins/datatables/dataTables.bootstrap4.js"></script>
<!-- FastClick -->
<script src="../../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../dist/js/demo.js"></script>

<script>
  $(function () {
    $("#example1").DataTable();
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
    });
  });
</script>
</body>
</html>


