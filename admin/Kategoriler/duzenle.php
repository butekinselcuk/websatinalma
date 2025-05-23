<?php
require_once('../../Connections/baglan.php');
require_once('../../fonksiyon.php');

if (!isset($_SESSION)) {
    session_start();
}

$MM_authorizedUsers = "1,2";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) {
    $isValid = False;
    if (!empty($UserName)) {
        $arrUsers = explode(",", $strUsers);
        $arrGroups = explode(",", $strGroups);
        if (in_array($UserName, $arrUsers) || in_array($UserGroup, $arrGroups) || $strUsers == "") {
            $isValid = true;
        }
    }
    return $isValid;
}

$MM_restrictGoTo = "../../login.php";
if (!((isset($_SESSION['MM_Username'])) && isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup']))) {
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


// Update level


// Example to update a category
if (isset($_POST["MM_update"]) && $_POST["MM_update"] == "kategori") {
    $sql = "UPDATE kategori SET KategoriAdi = ?, Kategoriing = ?, Kategorichn = ? WHERE KategoriID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['KategoriAdi'], $_POST['Kategoriing'], $_POST['Kategorichn'], $_POST['KategoriID']]);
    $updateGoTo = "index.php?Duzenle=DuzenlemeBasarili";
    header("Location: " . $updateGoTo);
    exit;
}

// Fetching user details
$colname_uyebilgileri = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";
$sql = "SELECT * FROM uyeler WHERE uyeAdi = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$colname_uyebilgileri]);
$row_uyebilgileri = $stmt->fetch();

// Fetching settings
$sql = "SELECT * FROM siteconfig";
$stmt = $pdo->query($sql);
$row_ayar = $stmt->fetch();

// Fetching categories
$sql = "SELECT * FROM kategori ORDER BY KategoriID DESC";
$stmt = $pdo->query($sql);
$row_kategori = $stmt->fetchAll();



// Fetch bid details where bid is inactive
$stmt = $pdo->prepare("SELECT teklifiste.TalepID, teklifiste.aciklama, teklifiste.gondermetarih, teklifiste.teklifaktif FROM teklifiste WHERE teklifiste.teklifaktif = 0 ORDER BY teklifiste.teklifaktif ASC");
$stmt->execute();
$row_teklifoku = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_teklifoku = $stmt->rowCount();



// Fetch category details for editing
$colname_kategoriduzenle = isset($_GET['KategoriID']) ? $_GET['KategoriID'] : "-1";
$stmt = $pdo->prepare("SELECT * FROM kategori WHERE KategoriID = ?");
$stmt->execute([$colname_kategoriduzenle]);
$row_kategoriduzenle = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_kategoriduzenle = $stmt->rowCount();

$uyeID = isset($row_uyebilgileri['uyeID']) ? $row_uyebilgileri['uyeID'] : null;

// Fetch messages for the user
$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE kime = ? AND durum = 0");
$stmt->execute([$uyeID]);
$row_mesaj = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_mesaj = $stmt->rowCount();


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
          <h3 class="card-title">Kategori Düzenleme Sayfası</h3>


        </div>
        <div class="card-body">
          
  <!-- KODLARRRRRRRRRR -->

                      <form method="POST" action="<?php echo $editFormAction; ?>" name="kategori" class="form-horizontal" id="kategori">
                  <div class="col-5">
                  <label class="control-label">Türkçe:</label></div>
                  <div class="col-5">
                  <input name="KategoriAdi" type="text" class="form-control " id="KategoriAdi" value="<?php echo $row_kategoriduzenle['KategoriAdi']; ?>"><br></br></div>
                  <div class="col-5">
                  <label class="control-label">İngilizce:</label></div>
                  <div class="col-5">
                  <input name="Kategoriing" type="text" class="form-control " id="Kategoriing" value="<?php echo $row_kategoriduzenle['Kategoriing']; ?>"><br></br></div>
                  <div class="col-5">
                  <label class="control-label">Çince:</label></div>
                  <div class="col-5">
                  <input name="Kategorichn" type="text" class="form-control " id="Kategorichn" value="<?php echo $row_kategoriduzenle['Kategorichn']; ?>"></div>
                  <div class="col-5">
                     <button type="submit" class="btn btn-success">Güncelle</button></div>
                              <input type="hidden" name="KategoriID" value="<?php echo $row_kategoriduzenle['KategoriID']; ?>">
                     <input type="hidden" name="MM_update" value="kategori">
                </form>


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


