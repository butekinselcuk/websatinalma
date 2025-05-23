<?php require_once('../../Connections/baglan.php'); ?>
<?php require_once('../../fonksiyon.php'); ?>

<?php

  session_start();
$tarih = date("Y/m/d");



// Erişim yetki kontrolü
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
    }
    return $isValid;
}

$MM_restrictGoTo = "../login.php";
if (!isset($_SESSION['MM_Username']) || !isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])) {
    $MM_qsChar = "?";
    $MM_referrer = $_SERVER['PHP_SELF'];
    if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
    if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0)
        $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
    $MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
    header("Location: " . $MM_restrictGoTo);
    exit;
}

// Kullanıcı bilgilerini çekme
$colname_uyebilgileri = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";
$stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeAdi = :uyeAdi");
$stmt->bindParam(':uyeAdi', $colname_uyebilgileri, PDO::PARAM_STR);
$stmt->execute();
$row_uyebilgileri = $stmt->fetch(PDO::FETCH_ASSOC);


// Teklifoku sorgusu
$query_teklifoku = "SELECT TalepID, aciklama, gondermetarih, teklifaktif FROM teklifiste WHERE teklifaktif = 0 ORDER BY teklifaktif ASC";
$stmt_teklifoku = $pdo->prepare($query_teklifoku);
$stmt_teklifoku->execute();
$row_teklifoku = $stmt_teklifoku->fetch(PDO::FETCH_ASSOC);
$totalRows_teklifoku = $stmt_teklifoku->rowCount();

// Siteayar sorgusu
$query_Siteayar = "SELECT * FROM siteconfig ORDER BY AyarID ASC";
$stmt_Siteayar = $pdo->prepare($query_Siteayar);
$stmt_Siteayar->execute();
$row_Siteayar = $stmt_Siteayar->fetch(PDO::FETCH_ASSOC);
$totalRows_Siteayar = $stmt_Siteayar->rowCount();

// Ayar sorgusu
$query_ayar = "SELECT * FROM siteconfig";
$stmt_ayar = $pdo->prepare($query_ayar);
$stmt_ayar->execute();
$row_ayar = $stmt_ayar->fetch(PDO::FETCH_ASSOC);
$totalRows_ayar = $stmt_ayar->rowCount();

// Sohbet mesajları sorgusu
$uyeID = $row_uyebilgileri['uyeID'];
$query_mesaj = "SELECT * FROM sohbet WHERE kime = :uyeID AND durum = 0";
$stmt_mesaj = $pdo->prepare($query_mesaj);
$stmt_mesaj->bindParam(':uyeID', $uyeID, PDO::PARAM_INT);
$stmt_mesaj->execute();
$row_mesaj = $stmt_mesaj->fetch(PDO::FETCH_ASSOC);
$totalRows_mesaj = $stmt_mesaj->rowCount();





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
   <title><?php echo $row_Siteayar['SiteTitle']; ?></title>
   <!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="css" -->
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<meta content="<?php echo $row_Siteayar['Metadesc']; ?>" name="description">
<meta content="<?php echo $row_Siteayar['MetaName']; ?>" name="keywords">
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
                           <div class="widget-body">
                 
                                         <?php if(isset($_GET['Ekle'])) :?>

            <?php if($_GET['Ekle']=='EklemeBasarili') ?>  
                  <div class="alert alert-success">
                    <button class="close" data-dismiss="alert">×</button>
                    <strong>Ekleme Başarılı!</strong> Site Ayarları Başarıyla Eklendi.
                  </div>
<?php endif   ;?> 

                        <?php if(isset($_GET['Duzenle'])) :?>

            <?php if($_GET['Duzenle']=='DuzenlemeBasarili') ?>  
                  <div class="alert alert-info">
                    <button class="close" data-dismiss="alert">×</button>
                    <strong>Duzenleme Başarılı!</strong> Site Ayarları Başarıyla Düzenlendi.
                  </div>
                                    
                                    
<?php endif   ;?> 
                        <?php if(isset($_GET['Sil'])) :?>

            <?php if($_GET['Sil']=='SilmeBasarili') ?>  

                  <div class="alert alert-error">
                    <button class="close" data-dismiss="alert">×</button>
                    <strong>Silme Başarılı!</strong> Site Ayarları Başarıyla Silindi.
                  </div>

<?php endif   ;?>                       
                        
                        
                          <script type="text/javascript">
setTimeout(function(){
    $(".alert").fadeTo(500,0,function(){
        $(this).remove()
        })
    },3000)
  
  </script>
                 
                 
                 
                   <p><strong>Site Title :</strong><?php echo $row_Siteayar['SiteTitle']; ?></p>
                   <p><strong>Site Desc :</strong><?php echo $row_Siteayar['Metadesc']; ?></p>
                   <p><strong>Site Name :</strong><?php echo $row_Siteayar['MetaName']; ?></p>
                   <p><strong>Facebook :</strong><?php echo $row_Siteayar['facebook']; ?></p>
                   <p><strong>Twitter :</strong><?php echo $row_Siteayar['twitter']; ?></p>
                   <p><strong>Instagram :</strong><?php echo $row_Siteayar['instagram']; ?></p>
                   <p><strong>Youtube :</strong><?php echo $row_Siteayar['youtube']; ?></p>
                   <p><strong>Site Logo :</strong><img src="../resim/logo/<?php echo $row_Siteayar['Sitelogo']; ?>" width="160" height="60"></p>
                   <p><strong>Site Bakım Durumu :</strong><?php
         
         if($row_Siteayar['SiteBakim']==1) :
?>
  <button type="button" class="btn btn-success">Yayında</button>
  
                <?php
        else : ?>
     <button type="button" class="btn btn-danger">Site Bakımda</button>
     
      <?php
        endif ?></p>
                
                                                      <div class="btn-group">
                                        <a class="btn" href="#"><i class="icon-cog"></i> İşlem Seç</a><a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><span class="icon-caret-down"></span>
                                        </a>
                                                        <ul class="dropdown-menu">
                                          <li><a href="duzenle.php?AyarID=<?php echo $row_Siteayar['AyarID']; ?>">Düzenle</a></li>
                                          
                                          
                                        </ul>
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
