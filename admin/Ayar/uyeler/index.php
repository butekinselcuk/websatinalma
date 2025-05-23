<?php 
require_once('../../../Connections/baglan.php');
require_once('../../../fonksiyon.php');

if (!isset($_SESSION)) {
    session_start();
}
$MM_authorizedUsers = "1";
$MM_donotCheckaccess = "false";

// Access Control Simplified Version
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) {
    $arrUsers = explode(",", $strUsers);
    $arrGroups = explode(",", $strGroups);
    return (!empty($UserName) && (in_array($UserName, $arrUsers) || in_array($UserGroup, $arrGroups)));
}

// Redirect if not authorized
if (!isAuthorized("", $MM_authorizedUsers, isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : '', isset($_SESSION['MM_UserGroup']) ? $_SESSION['MM_UserGroup'] : '')) {
    $queryString = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    header("Location: ../../../login.php?accesscheck=" . urlencode($_SERVER['PHP_SELF'] . $queryString));
    exit;
}

// Query operations using PDO
$stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeAdi = ?");
$stmt->execute([isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : '-1']);
$row_uyebilgileri = $stmt->fetch();
$totalRows_uyebilgileri = $stmt->rowCount();

$stmt = $pdo->query("SELECT teklifiste.TalepID, teklifiste.aciklama, teklifiste.gondermetarih, teklifiste.teklifaktif FROM teklifiste WHERE teklifiste.teklifaktif = 0 ORDER BY teklifiste.teklifaktif ASC");
$row_teklifoku = $stmt->fetchAll();
$totalRows_teklifoku = $stmt->rowCount();

// Ayar sorgusu
$query_ayar = "SELECT * FROM siteconfig";
$stmt_ayar = $pdo->prepare($query_ayar);
$stmt_ayar->execute();
$row_ayar = $stmt_ayar->fetch(PDO::FETCH_ASSOC);
$totalRows_ayar = $stmt_ayar->rowCount();



// 

    $stmt = $pdo->prepare("SELECT uyeID, seviyeID, uyeAdi, sirketAdi, bastarih, bittarih FROM uyeler ORDER BY uyeID ASC");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt = $pdo->query("SELECT uyeID, bastarih, bittarih FROM uyeler");
$row_hesap = $stmt->fetchAll();
$totalRows_hesap = count($row_hesap);

$uyeID = isset($row_uyebilgileri['uyeID']) ? $row_uyebilgileri['uyeID'] : 0;
$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE kime = ? AND durum = 0");
$stmt->execute([$uyeID]);
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
          <h3 class="card-title">Site Ayarları</h3>


        </div>
        <div class="card-body">
          
  <!-- KODLARRRRRRRRRR -->

                
 <table id="example1" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>UyeID</th>
            <th>Üye Adı</th>
            <th>Şirket Adı</th>
            <th>Başlangıç Tarihi</th>
            <th>Bitiş Tarihi</th>
            <th>Yetki Durumu</th>
            <th>İşlemler</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['uyeID']); ?></td>
            <td><?php echo htmlspecialchars($row['uyeAdi']); ?></td>
            <td><?php echo htmlspecialchars($row['sirketAdi']); ?></td>
            <td><?php echo htmlspecialchars($row['bastarih']); ?></td>
            <td><?php echo htmlspecialchars($row['bittarih']); ?></td>
            <td><?php echo htmlspecialchars($row['seviyeID']); ?></td>
            <td>
                <div class="btn-group">
                    <a class="btn" href="#"><i class="icon-cog"></i> İşlem Seç</a>
                    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                        <span class="icon-caret-down"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="duzenle.php?uyeID=<?php echo $row['uyeID']; ?>"><i class="icon-edit"></i> Düzenle</a></li>
                    </ul>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
                



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


