<?php
require_once('../../Connections/baglan.php');
require_once('../../fonksiyon.php');

if (!isset($_SESSION)) {
    session_start();
}

$MM_authorizedUsers = "1";
$MM_donotCheckaccess = "false";

function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) {
    $isValid = False;
    $arrUsers = explode(",", $strUsers);
    $arrGroups = explode(",", $strGroups);
    if (!empty($UserName)) {
        if (in_array($UserName, $arrUsers) || in_array($UserGroup, $arrGroups)) {
            $isValid = true;
        }
    }
    return $isValid;
}

$MM_restrictGoTo = "../../login.php";
if (!(isset($_SESSION['MM_Username']) && isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup']))) {
    $MM_qsChar = "?";
    $MM_referrer = $_SERVER['PHP_SELF'];
    if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
    $redirectURL = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer . ($_SERVER['QUERY_STRING'] ? "?" . $_SERVER['QUERY_STRING'] : ""));
    header("Location: " . $redirectURL);
    exit;
}


// Fetch User Information
$colname_uyebilgileri = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";
$stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeAdi = ?");
$stmt->execute([$colname_uyebilgileri]);
$row_uyebilgileri = $stmt->fetch();

// Tekliflerin çekilmesi
$stmt = $pdo->prepare("SELECT * FROM teklifiste WHERE teklifaktif = 0 ORDER BY teklifaktif ASC");
$stmt->execute();
$row_teklifoku = $stmt->fetchAll();
$totalRows_teklifoku = count($row_teklifoku);

// Site ayarlarının çekilmesi
$stmt = $pdo->prepare("SELECT * FROM siteconfig");
$stmt->execute();
$row_ayar = $stmt->fetch();
$totalRows_ayar = $stmt->rowCount();

// Kategorilerin çekilmesi
$stmt = $pdo->prepare("SELECT * FROM kategori ORDER BY KategoriID DESC");
$stmt->execute();
$row_kategori = $stmt->fetchAll();
$totalRows_kategori = $stmt->rowCount();




// Kullanıcı mesajlarının çekilmesi
$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE kime = ? AND durum = 0");
$stmt->execute([$row_uyebilgileri['uyeID']]);
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
          <h3 class="card-title">Kategoriler Tablosu</h3>


        </div>
        <div class="card-body">
          
  <!-- KODLARRRRRRRRRR -->
   
   
                        <?php if(isset($_GET['Ekle'])) :?>

            <?php if($_GET['Ekle']=='EklemeBasarili') ?>  
                  <div class="alert alert-success">
                    <button class="close" data-dismiss="alert">×</button>
                    <strong>Ekleme Başarılı!</strong> Kategori Başarıyla Eklendi.
                  </div>
<?php endif   ;?> 

                        <?php if(isset($_GET['Duzenle'])) :?>

            <?php if($_GET['Duzenle']=='DuzenlemeBasarili') ?>  
                  <div class="alert alert-info">
                    <button class="close" data-dismiss="alert">×</button>
                    <strong>Duzenleme Başarılı!</strong> Kategori Başarıyla Düzenlendi.
                  </div>
                                    
                                    
<?php endif   ;?> 
                        <?php if(isset($_GET['Sil'])) :?>

            <?php if($_GET['Sil']=='SilmeBasarili') ?>  

                  <div class="alert alert-error">
                    <button class="close" data-dismiss="alert">×</button>
                    <strong>Silme Başarılı!</strong> Kategori Başarıyla Silindi.
                  </div>

<?php endif   ;?>                       
                        
                        
                          <script type="text/javascript">
setTimeout(function(){
    $(".alert").fadeTo(500,0,function(){
        $(this).remove()
        })
    },3000)
  
  </script>
 
<table id="example1" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Kategori Adı (Türkçe)</th>
            <th>Kategori Adı (İngilizce)</th>
            <th>Kategori Adı (Çince)</th>
            <th width="150" class="hidden-phone">İşlemler</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($row_kategori as $kategori) { ?>
            <tr class="odd gradeX">
                <td><?php echo htmlspecialchars($kategori['KategoriAdi']); ?></td>
                <td><?php echo htmlspecialchars($kategori['Kategoriing']); ?></td>
                <td><?php echo htmlspecialchars($kategori['Kategorichn']); ?></td>
                <td width="150" class="center hidden-phone">
                    <div class="btn-group">
                        <a class="btn" href="#"><i class="icon-cog"></i> İşlem Seç</a>
                        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><span class="icon-caret-down"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="duzenle.php?KategoriID=<?php echo $kategori['KategoriID']; ?>"><i class="icon-edit"></i> Düzenle</a></li>
                            <li onClick="GP_popupConfirmMsg('Bu Kategoriyi Silmek İstediğinize Eminmisiniz?');return document.MM_returnValue"><a href="sil.php?KategoriID=<?php echo $kategori['KategoriID']; ?>"><i class="icon-remove"></i> Sil</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>


  <!-- KODLARRRRRRRRRR -->

        </div>
        <!-- /.card-body -->
        <div class="col-md-2"><a href="ekle.php"><button type="button" class="btn btn-block btn-outline-primary">Kategori Ekle</button> </a></div>

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


