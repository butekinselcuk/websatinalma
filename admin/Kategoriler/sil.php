<?php
require_once('../../Connections/baglan.php');
require_once('../../fonksiyon.php');

if (!isset($_SESSION)) {
    session_start();
}

$MM_authorizedUsers = "1";
$MM_donotCheckaccess = "false";

function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) {
    $isValid = false;
    $arrUsers = explode(",", $strUsers);
    $arrGroups = explode(",", $strGroups);
    if (!empty($UserName)) {
        if (in_array($UserName, $arrUsers) || in_array($UserGroup, $arrGroups)) {
            $isValid = true;
        }
        if ($strUsers == "" && $strGroups == "") {
            $isValid = true;
        }
    }
    return $isValid;
}

$MM_restrictGoTo = "../../login.php";
if (!(isset($_SESSION['MM_Username']) && isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup']))) {
    $MM_qsChar = strpos($MM_restrictGoTo, "?") ? "&" : "?";
    $MM_referrer = $_SERVER['PHP_SELF'] . (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0 ? "?" . $_SERVER['QUERY_STRING'] : "");
    header("Location: " . $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer));
    exit;
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// Handle insert operation
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "kategori")) {
    $stmt = $pdo->prepare("INSERT INTO kategori (KategoriAdi) VALUES (?)");
    $stmt->execute([filter($_POST['KategoriAdi'])]);

    $insertGoTo = "index.php";
    if (isset($_SERVER['QUERY_STRING'])) {
        $insertGoTo .= (strpos($insertGoTo, '?') ? "&" : "?") . $_SERVER['QUERY_STRING'];
    }
    header("Location: $insertGoTo");
    exit;
}

// Handle update operation
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "kategori")) {
    $stmt = $pdo->prepare("UPDATE kategori SET KategoriAdi = ? WHERE KategoriID = ?");
    $stmt->execute([filter($_POST['KategoriAdi']), $_POST['KategoriID']]);

    $updateGoTo = "index.php";
    if (isset($_SERVER['QUERY_STRING'])) {
        $updateGoTo .= (strpos($updateGoTo, '?') ? "&" : "?") . $_SERVER['QUERY_STRING'];
    }
    header("Location: $updateGoTo");
    exit;
}

// Handle delete operation
if ((isset($_GET['KategoriID'])) && ($_GET['KategoriID'] != "")) {
    $stmt = $pdo->prepare("DELETE FROM kategori WHERE KategoriID = ?");
    $stmt->execute([$_GET['KategoriID']]);

    $deleteGoTo = "index.php?Sil=SilmeBasarili";
    if (isset($_SERVER['QUERY_STRING'])) {
        $deleteGoTo .= (strpos($deleteGoTo, '?') ? "&" : "?") . $_SERVER['QUERY_STRING'];
    }
    header("Location: $deleteGoTo");
    exit;
}

// Fetch user details
$colname_uyebilgileri = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";
$stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeAdi = ?");
$stmt->execute([$colname_uyebilgileri]);
$row_uyebilgileri = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch system settings
$stmt = $pdo->prepare("SELECT * FROM siteconfig");
$stmt->execute();
$row_ayar = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch messages for the user
$uyeID = isset($row_uyebilgileri['uyeID']) ? $row_uyebilgileri['uyeID'] : null;
$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE kime = ? AND durum = 0");
$stmt->execute([$uyeID]);
$row_mesaj = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
          <h3 class="card-title">Kategori Ekleme Formu</h3>


        </div>
        <div class="card-body">
          
  <!-- KODLARRRRRRRRRR -->



  <!-- KODLARRRRRRRRRR -->

        </div>
        <!-- /.card-body -->

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
<!-- DataTables -->
<script src="../plugins/datatables/jquery.dataTables.js"></script>
<script src="../plugins/datatables/dataTables.bootstrap4.js"></script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>

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


