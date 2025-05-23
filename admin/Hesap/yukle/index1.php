<?php
require_once('../../../Connections/baglan.php'); // Ensure this file is using PDO connection
require_once('../../../fonksiyon.php');

session_start();

$MM_authorizedUsers = "1,2";

function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) {
    $arrUsers = explode(",", $strUsers);
    $arrGroups = explode(",", $strGroups);
    return !empty($UserName) && (in_array($UserName, $arrUsers) || in_array($UserGroup, $arrGroups));
}

if (!isAuthorized("", $MM_authorizedUsers, isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : '', isset($_SESSION['MM_UserGroup']) ? $_SESSION['MM_UserGroup'] : '')) {
    $queryString = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    header("Location: ../../../login.php?accesscheck=" . urlencode($_SERVER['PHP_SELF'] . $queryString));
    exit;
}

// Define $editFormAction for form submission
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}



if (isset($_POST["MM_insert"]) && $_POST["MM_insert"] == "form1") {
    foreach ($_FILES['resim']['name'] as $i => $name) {
        if ($_FILES['resim']['error'][$i] == 0) {
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $newName = md5(uniqid(rand())) . '.' . $ext;
            $destination = "../../resim/sertifika/" . $newName;
            if (move_uploaded_file($_FILES['resim']['tmp_name'][$i], $destination)) {
                $sql = "INSERT INTO resim (uyeID, yer, resim) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_POST['uyeID'], $_POST['yer'], $newName]);
            }
        }
    }
    $insertGoTo = "index.php?Ekle=EklemeBasarili";
    header("Location: $insertGoTo");
    exit;
}

// Kullanıcı bilgilerini çekme
$colname_uyebilgileri = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";
$stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeAdi = ?");
$stmt->execute([$colname_uyebilgileri]);
$row_uyebilgileri = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT teklifiste.TalepID, teklifiste.aciklama, teklifiste.gondermetarih, teklifiste.teklifaktif FROM teklifiste WHERE teklifaktif = 0 ORDER BY teklifaktif ASC");
$row_teklifoku = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM siteconfig");
$row_ayar = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE kime = ? AND durum = 0");
$stmt->execute([$row_uyebilgileri['uyeID']]);
$row_mesaj = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_mesaj = $stmt->rowCount();  // Count rows

// sertifika resimlerini çekme
$stmt = $pdo->prepare("SELECT resim.ID, resim.resim FROM uyeler INNER JOIN resim ON uyeler.uyeID = resim.uyeID WHERE resim.yer = 'sertifika' AND uyeler.uyeID = ?");
$stmt->execute([$row_uyebilgileri['uyeID']]);
$row_sertifikaresim = $stmt->fetchAll(PDO::FETCH_ASSOC);


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
          <h3 class="card-title"><?php echo $dil['sertifikaresimleri']; ?></h3>


        </div>
        <div class="card-body">
          
  <!-- KODLARRRRRRRRRR -->
   
  
 <?php if(isset($_GET['Ekle'])) :?>

            <?php if($_GET['Ekle']=='EklemeBasarili') ?>  
                  <div class="alert alert-success">
                    <button class="close" data-dismiss="alert">×</button>
                    <strong><?php echo $dil['basarili']; ?></strong> <?php echo $dil['basarieklendi']; ?>
                  </div>
<?php endif   ;?> 

                        <?php if(isset($_GET['Duzenle'])) :?>

            <?php if($_GET['Duzenle']=='DuzenlemeBasarili') ?>  
                  <div class="alert alert-info">
                    <button class="close" data-dismiss="alert">×</button>
                    <strong><?php echo $dil['basarili']; ?></strong> <?php echo $dil['basariduzenlendi']; ?>
                  </div>
                                    
                                    
<?php endif   ;?> 
                        <?php if(isset($_GET['Sil'])) :?>

            <?php if($_GET['Sil']=='SilmeBasarili') ?>  

                  <div class="alert alert-error">
                    <button class="close" data-dismiss="alert">×</button>
                    <strong><?php echo $dil['basarili']; ?></strong> <?php echo $dil['basarisilindi']; ?>
                  </div>

<?php endif   ;?>  
<div class="span12">
    <?php if (empty($row_sertifikaresim)) { ?>
        <img src="../../resim/yok.png" width="50" height="50">
    <?php } else { ?>
        <?php foreach ($row_sertifikaresim as $resim) { ?>
            <div class="span1">
                <div class="thumbnail">
                    <div class="item">
                        <a class="fancybox-button" data-rel="fancybox-button" title="Photo" href="../../resim/sertifika/<?php echo htmlspecialchars($resim['resim']); ?>">
                            <div class="zoom">
                                <img src="../../resim/sertifika/<?php echo htmlspecialchars($resim['resim']); ?>" width="100" height="100" alt="Photo">
                                <div class="zoom-icon"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <a href="sil1.php?sil=<?php echo htmlspecialchars($resim['ID']); ?>"><i class="icon-remove"></i><?php echo $dil['sil']; ?></a>
            </div>
        <?php } ?>
    <?php } ?>
</div>
                            
               <div class="widget">
                 <div class="widget-title">
                   
                 </div>
                 <div class="widget-body">
                 
                 
         <form method="POST" action="<?php echo $editFormAction; ?>" name="form1"   id="form1"  enctype="multipart/form-data" >
                    

      <div class="row-fluid">
          <div class="span6">       
         
                   <div class="control-group">
                                    <label class="control-label"><?php echo $dil['resimyukle']; ?></label>
                                    <div class="controls">
                                        <input name="resim[]" type="file" id="resim" multiple>
                                    </div>
                        </div>
      
                  <input name="yer" type="hidden" class="span4 " id="yer" value="sertifika">
                    <input name="uyeID" type="hidden" class="span4 " id="uyeID" value="<?php echo $row_uyebilgileri['uyeID']; ?>">
                    <input name="isteksirketadi" type="hidden" class="span4 " id="isteksirketadi" value="<?php echo $row_uyebilgileri['sirketAdi']; ?>">
                  

                                                        <div class="card-footer">
         
              
                      <button type="submit" class="btn btn-success"><?php echo $dil['kaydet']; ?></button>
                    <input type="hidden" name="MM_insert" value="form1">

                    
      </div>  
             </div>   
              </div>  
       
              </form>

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


