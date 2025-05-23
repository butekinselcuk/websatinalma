<?php 
require_once('../../Connections/baglan.php');

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

// Veritabanı yedekleme işlemi
if (isset($_POST['backupDatabase'])) {
    $backupDir = '../backup'; // Yedeklerin saklanacağı dizin
    $backupFile = $backupDir . '/' . $database . '_' . date('Y-m-d_H-i-s') . '.sql';
    $command = "mysqldump --host={$hostname} --user={$username} --password={$password} {$database} > {$backupFile}";

    system($command, $output);

    if ($output === 0) {
        echo "<script>alert('Veritabanı yedeği başarıyla alındı.');</script>";
    } else {
        echo "<script>alert('Veritabanı yedeği alınırken bir hata oluştu.');</script>";
    }
}
if (isset($_POST["MM_update"]) && ($_POST["MM_update"] == "ayar ")) {
    $fotoID = $_POST['AyarID'];

    // Handle file upload
    if (!empty($_FILES['Sitelogo']['name'])) {
        $oldLogoQuery = $pdo->prepare("SELECT Sitelogo FROM siteconfig WHERE AyarID = ?");
        $oldLogoQuery->execute([$fotoID]);
        $oldLogo = $oldLogoQuery->fetchColumn();

        if ($oldLogo && file_exists("../resim/logo/$oldLogo")) {
            unlink("../resim/logo/$oldLogo");
        }

        $newFilename = md5(uniqid()) . '.' . pathinfo($_FILES['Sitelogo']['name'], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES['Sitelogo']['tmp_name'], "../resim/logo/$newFilename");
    } else {
        $newFilename = $pdo->query("SELECT Sitelogo FROM siteconfig WHERE AyarID = $fotoID")->fetchColumn();
    }

    $updateSQL = "UPDATE siteconfig SET Sitelogo = ?, SiteTitle = ?, Metadesc = ?, MetaName = ?, GCode = ?, SiteBakim = ?, facebook = ?, twitter = ?, youtube = ?, instagram = ? WHERE AyarID = ?";
    $stmt = $pdo->prepare($updateSQL);
    $stmt->execute([
        $newFilename, $_POST['SiteTitle'], $_POST['Metadesc'], $_POST['MetaName'], $_POST['GCode'], 
        isset($_POST['SiteBakim']) ? "1" : "0", $_POST['facebook'], $_POST['twitter'], $_POST['youtube'], $_POST['instagram'], $fotoID
    ]);

    $updateGoTo = "index.php?Duzenle=DuzenlemeBasarili";
    if (isset($_SERVER['QUERY_STRING'])) {
        $updateGoTo .= (strpos($updateGoTo, '?') ? "&" : "?") . $_SERVER['QUERY_STRING'];
    }
    header("Location: $updateGoTo");
}

// User information
$colname_uyebilgileri = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";
$stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeAdi = ?");
$stmt->execute([$colname_uyebilgileri]);
$row_uyebilgileri = $stmt->fetch();
$totalRows_uyebilgileri = $stmt->rowCount();

// Offer information
$stmt = $pdo->prepare("SELECT TalepID, aciklama, gondermetarih, teklifaktif FROM teklifiste WHERE teklifaktif = 0 ORDER BY teklifaktif ASC");
$stmt->execute();
$row_teklifoku = $stmt->fetchAll();
$totalRows_teklifoku = count($row_teklifoku);

// Settings information
$colname_ayar = isset($_GET['AyarID']) ? (int)$_GET['AyarID'] : -1;
$stmt = $pdo->prepare("SELECT * FROM siteconfig WHERE AyarID = ?");
$stmt->execute([$colname_ayar]);
$row_ayar = $stmt->fetch();
$totalRows_ayar = $stmt->rowCount();

// Messages
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
          <h3 class="card-title">Site Ayarları</h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
              <i class="fas fa-minus"></i></button>
            <button type="button" class="btn btn-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
              <i class="fas fa-times"></i></button>
          </div>
        </div>
        <div class="card-body">
          


 <form method="POST" action="<?php echo $editFormAction; ?>" name="ayar " class="form-horizontal" id="kategori" enctype="multipart/form-data">
                  
            <div class="control-group">
                  <label class="control-label">Site Title:</label>
                      <div class="controls">

                  <input name="SiteTitle" type="text" class="form-control " id="SiteTitle" value="<?php echo $row_ayar['SiteTitle']; ?>">
                  
            </div>
                      </div>

            <div class="control-group">
                  <label class="control-label">Meta desc:</label>
                      <div class="controls">
                  <input name="Metadesc" type="text" class="form-control " id="Metadesc" value="<?php echo $row_ayar['Metadesc']; ?>">
                  
            </div>
                      </div>
                                              
            <div class="control-group">
                  <label class="control-label">Meta Keywords:</label>
                      <div class="controls">
                  <input name="MetaName" type="text" class="form-control " id="MetaName" value="<?php echo $row_ayar['MetaName']; ?>">
                  
            </div>
                      </div>
                        
            <div class="control-group">
                  <label class="control-label">Web Master Code:</label>
                      <div class="controls">
                          <textarea name="GCode" class="form-control " id="GCode"><?php echo $row_ayar['GCode']; ?></textarea>
                  
                  </div>
              </div>
                        
                        <div class="control-group">
                  <label class="control-label">Facebook:</label>
                      <div class="controls">
                  <input name="facebook" type="text" class="form-control " id="facebook" value="<?php echo $row_ayar['facebook']; ?>">
                  
            </div>
                      </div>
                        
                        <div class="control-group">
                  <label class="control-label">Twitter:</label>
                      <div class="controls">
                  <input name="twitter" type="text" class="form-control " id="twitter" value="<?php echo $row_ayar['twitter']; ?>">
                  
            </div>
                      </div>
                        
                        <div class="control-group">
                  <label class="control-label">Youtube:</label>
                      <div class="controls">
                  <input name="youtube" type="text" class="form-control " id="youtube" value="<?php echo $row_ayar['youtube']; ?>">
                  
            </div>
                      </div>
                        
                        <div class="control-group">
                  <label class="control-label">İnstagram:</label>
                      <div class="controls">
                  <input name="instagram" type="text" class="form-control " id="instagram" value="<?php echo $row_ayar['instagram']; ?>">
                  
            </div>
                      </div>
                        
                     
                        <div class="control-group">
                                    <label class="control-label">Önceki Site Logo :</label>
                                    <div class="controls">
                                        <img src="../resim/logo/<?php echo $row_ayar['Sitelogo']; ?>" width="150" height="100">
                                    </div>
                </div>
                        
                        
                        
                        
                        <div class="control-group">
                                    <label class="control-label">Site Logo :</label>
                                    <div class="controls">
                                        <input name="Sitelogo" type="file" class="default" id="Sitelogo">
                                    </div>
                </div>
                
                <div class="control-group">
                              <label class="control-label">Site Durumu :</label>
                              <div class="controls">
                                 <label class="checkbox">
                                 <input <?php if (!(strcmp($row_ayar['SiteBakim'],1))) {echo "checked=\"checked\"";} ?> name="SiteBakim" type="checkbox" id="SiteBakim" value=""> Seçili site açık,Kapalı ise site bakımda!
                                 </label>

                              </div>
                           </div>
                           
                           
                        
                                             
                        
                  <button type="submit" class="btn btn-success">Ekle</button>
                  
                  <input type="hidden" name="AyarID" value="<?php echo $row_ayar['AyarID']; ?>">
                  <input type="hidden" name="MM_update" value="ayar ">
                   </form>


    <div class="content-wrapper">
        <section class="content-header">
            <h1>Veri Tabanı</h1>
        </section>
        <section class="content">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Veritabanı Yedekleme</h3>
                </div>
                <div class="card-body">
                    <form method="post">
                        <button type="submit" name="backupDatabase" class="btn btn-primary">Veritabanı Yedeğini Al</button>
                    </form>
                </div>
            </div>
        </section>
    </div>



        </div>
        <!-- /.card-body -->

      </div>


    </section>


  <!-- ORTA ALAN -->
  </div>


     <?php
  include("../pages/kisayollar/alt.php");
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
</body>
</html>
