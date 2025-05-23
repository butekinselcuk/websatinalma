<?php
require_once('../../Connections/baglan.php');
require_once('../../fonksiyon.php');

if (!isset($_SESSION)) {
    session_start();
}

$MM_authorizedUsers = "1,2";
$MM_donotCheckaccess = "false";

function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) {
    $arrUsers = explode(",", $strUsers); 
    $arrGroups = explode(",", $strGroups); 
    return !empty($UserName) && (in_array($UserName, $arrUsers) || in_array($UserGroup, $arrGroups));
}

if (!isAuthorized("", $MM_authorizedUsers, isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : '', isset($_SESSION['MM_UserGroup']) ? $_SESSION['MM_UserGroup'] : '')) {
    $MM_qsChar = strpos($MM_restrictGoTo, "?") ? "&" : "?";
    $MM_referrer = $_SERVER['PHP_SELF'] . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
    header("Location: ../../login.php" . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer));
    exit;
}

$colname_uyebilgileri1 = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";
$stmt = $pdo->prepare("SELECT firmalogo, uyeAdi, imzasirkusu, osema, saticiBilgiDosyasi FROM uyeler WHERE uyeAdi = ?");
$stmt->execute([$colname_uyebilgileri1]);
$row_uyebilgileri1 = $stmt->fetch(PDO::FETCH_OBJ);

function handleFileUpload($field, $subfolder, $existingFileName = null) {
    if (!empty($_FILES[$field]['name'])) {
        $targetPath = "../resim/firmalar/$subfolder/";
        $newFileName = md5(uniqid(rand())) . '.' . pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
        if (move_uploaded_file($_FILES[$field]['tmp_name'], $targetPath . $newFileName)) {
            return $newFileName;
        }
    }
    return $existingFileName; // Yeni dosya yüklenmediyse veya sağlanmadıysa mevcut dosya adını döndür
}

// Define $editFormAction for form submission
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// Update level
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["MM_update"]) && $_POST["MM_update"] == "kategori") {
    // Handle file uploads
    $firmalogo = handleFileUpload('firmalogo', 'logo', $row_uyebilgileri1->firmalogo);
    $imzasirkusu = handleFileUpload('imzasirkusu', 'imzasirkusu', $row_uyebilgileri1->imzasirkusu);
    $osema = handleFileUpload('osema', 'osema', $row_uyebilgileri1->osema);
    $saticiBilgiDosyasi = handleFileUpload('saticiBilgiDosyasi', 'saticiBilgiDosyasi', $row_uyebilgileri1->saticiBilgiDosyasi);

    // Handling Kategori - converting array to a comma-separated string or use existing data
    $kategori = isset($_POST['Kategori']) ? implode(",", $_POST['Kategori']) : $row_uyebilgileri1->Kategori;

    // Check and use existing country and city if none provided
    $ulke = !empty($_POST['Ulke']) ? $_POST['Ulke'] : $row_uyebilgileri1->Ulke;
    $sehir = !empty($_POST['sehir']) ? $_POST['sehir'] : $row_uyebilgileri1->sehir;

    // Prepare and execute the update
    $updateSQL = "UPDATE uyeler SET 
        sirketAdi = ?, 
        VergiDairesi = ?, 
        VergiNumarasi = ?, 
        TicarisicilNo = ?, 
        BusinessRegNo = ?, 
        ToplamuretimAlani = ?, 
        Calisansayi = ?, 
        Ulke = ?, 
        sehir = ?, 
        PostaKodu = ?, 
        Adres = ?, 
        WebAdres = ?, 
        Tel = ?, 
        Fax = ?, 
        GecenYilciro = ?, 
        EkipmanYatirim = ?, 
        ihracatoran = ?, 
        HesapYoneticisiTitle = ?, 
        SalesMarketingTitle = ?, 
        RDTitle = ?, 
        LogisticsTitle = ?, 
        qualityTitle = ?, 
        ReferansMusteri = ?, 
        Kategori = ?, 
        HesapYoneticisiTel = ?, 
        HesapYoneticisiMail = ?, 
        PurchasingTitle = ?, 
        salesMarketingTel = ?, 
        salesMarketingMail = ?, 
        RDTel = ?, 
        RDMail = ?, 
        LogisticsTel = ?, 
        LogisticsMail = ?, 
        PurchasingTel = ?, 
        PurchasingMail = ?, 
        qualityTel = ?, 
        qualityMail = ?, 
        HukukiYapi = ?, 
        paylas = ?, 
        firmalogo = ?, 
        imzasirkusu = ?, 
        osema = ?, 
        saticiBilgiDosyasi = ? 
        WHERE uyeID = ?";

    $stmt = $pdo->prepare($updateSQL);
    $stmt->execute([
        $_POST['sirketAdi'], 
        $_POST['VergiDairesi'], 
        $_POST['VergiNumarasi'], 
        $_POST['TicarisicilNo'], 
        $_POST['BusinessRegNo'], 
        $_POST['ToplamuretimAlani'], 
        $_POST['Calisansayi'], 
        $ulke, // Updated to handle empty input
        $sehir, // Updated to handle empty input
        $_POST['PostaKodu'], 
        $_POST['Adres'], 
        $_POST['WebAdres'], 
        $_POST['Tel'], 
        $_POST['Fax'], 
        $_POST['GecenYilciro'], 
        $_POST['EkipmanYatirim'], 
        $_POST['IhracatOran'], 
        $_POST['HesapYoneticisiTitle'], 
        $_POST['SalesMarketingTitle'], 
        $_POST['RDTitle'], 
        $_POST['LogisticsTitle'], 
        $_POST['qualityTitle'], 
        $_POST['ReferansMusteri'], 
        $kategori, // Updated Kategori handling
        $_POST['HesapYoneticisiTel'], 
        $_POST['HesapYoneticisiMail'], 
        $_POST['PurchasingTitle'], 
        $_POST['salesMarketingTel'], 
        $_POST['salesMarketingMail'], 
        $_POST['RDTel'], 
        $_POST['RDMail'], 
        $_POST['LogisticsTel'], 
        $_POST['LogisticsMail'], 
        $_POST['PurchasingTel'], 
        $_POST['PurchasingMail'], 
        $_POST['qualityTel'], 
        $_POST['qualityMail'], 
        $_POST['HukukiYapi'], 
        $_POST['paylas'] ? 1 : 0, // Assuming checkbox for 'paylas'
        $firmalogo, 
        $imzasirkusu, 
        $osema, 
        $saticiBilgiDosyasi, 
        $_POST['uyeID']
    ]);

    // Redirect or handle the response as needed
    header("Location: index.php?Duzenle=DuzenlemeBasarili");
    exit;
}


$colname_uyebilgileri = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";
$stmt = $pdo->prepare("SELECT uyeID, seviyeID, uyeAdi, Parola, sirketAdi, VergiDairesi, VergiNumarasi, TicarisicilNo, BusinessRegNo, ToplamuretimAlani, Calisansayi, Ulke, sehir, PostaKodu, Adres, WebAdres, Tel, Fax, saticiBilgiDosyasi, imzasirkusu, osema, GecenYilciro, EkipmanYatirim, ihracatoran, HesapYoneticisiTitle, SalesMarketingTitle, RDTitle, LogisticsTitle, qualityTitle, ReferansMusteri, Kategori, HesapYoneticisiTel, HesapYoneticisiMail, PurchasingTitle, salesMarketingTel, salesMarketingMail, RDTel, RDMail, LogisticsTel, LogisticsMail, PurchasingTel, PurchasingMail, qualityTel, qualityMail, HukukiYapi, paylas, firmalogo FROM uyeler WHERE uyeAdi = ?");
$stmt->execute([$colname_uyebilgileri]);
$row_uyebilgileri = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT teklifiste.TalepID, teklifiste.aciklama, teklifiste.gondermetarih, teklifiste.teklifaktif FROM teklifiste WHERE teklifiste.teklifaktif = 0 ORDER BY teklifiste.teklifaktif ASC");
$row_teklifoku = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM siteconfig");
$row_ayar = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM kategori ORDER BY KategoriAdi ASC");
$row_kategorit = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM kategori ORDER BY Kategoriing ASC");
$row_kategorie = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM kategori ORDER BY Kategorichn ASC");
$row_kategoric = $stmt->fetchAll(PDO::FETCH_ASSOC);

$uyeID = isset($row_uyebilgileri['uyeID']) ? $row_uyebilgileri['uyeID'] : null;
$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE kime = ? AND durum = 0");
$stmt->execute([$uyeID]);
$row_mesaj = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
  <!-- daterange picker -->
  <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="../plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="../plugins/select2/css/select2.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="../plugins/jquery-tags-input/jquery.css">
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
          <h3 class="card-title"><?php echo $dil['hesap_bilgi']; ?></h3>


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
                  
                 <form method="POST" action="<?php echo $editFormAction; ?>" name="kategori"  class="form-horizontal" id="kategori" onSubmit="return check_frmm()" enctype="multipart/form-data">
              

   <div class="row">

          <div class="col-md-6">
            <!-- Bar chart -->
            <div class="card card-primary card-outline">
              <div class="card-header">

                  
                  <h4><i class="icon-reorder"></i><?php echo $dil['kayit_sirketbilgi_baslik']; ?></h4>
                      </div>

                              <div class="form-group">
                              <label class="col-md-6"><?php echo $dil['sirketadi']; ?></label>
                              <input name="sirketAdi" type="text" class="form-control float-right col-md-6" id="sirketAdi" value="<?php echo $row_uyebilgileri['sirketAdi']; ?>" data-trigger="hover" >
                              </div>

  <div class="form-group">
                              <label class="col-md-6"><?php echo $dil['kayit_vergi']; ?></label>

                                <input name="VergiDairesi" type="text" class="form-control float-right col-md-6" id="VergiDairesi" value="<?php echo $row_uyebilgileri['VergiDairesi']; ?>" data-trigger="hover" >

                              </div>
  <div class="form-group">
                              <label class="col-md-6"><?php echo $dil['kayit_vergi_no']; ?></label>

                                <input name="VergiNumarasi" type="text" class="form-control float-right col-md-6" id="VergiNumarasi" value="<?php echo $row_uyebilgileri['VergiNumarasi']; ?>" data-trigger="hover" data-mask="99999999999">
                                       
                              </div>
  <div class="form-group">
                              <label class="col-md-6"><?php echo $dil['kayit_ticari_sicil_no']; ?></label>

                                <input name="TicarisicilNo" type="text" class="form-control float-right col-md-6" id="TicarisicilNo" value="<?php echo $row_uyebilgileri['TicarisicilNo']; ?>" data-trigger="hover"  onkeyup="SayiKontrol(event)">

                              </div>
  <div class="form-group">
                              <label class="col-md-6"><?php echo $dil['kayit_bus_reg_no']; ?></label>

                                <input name="BusinessRegNo" type="text" class="form-control float-right col-md-6" id="BusinessRegNo" value="<?php echo $row_uyebilgileri['BusinessRegNo']; ?>" data-trigger="hover" >

                              </div>
  <div class="form-group">
                              <label class="col-md-6"><?php echo $dil['kayit_hukukiyapi']; ?></label>

                                <input name="HukukiYapi" type="text" class="form-control float-right col-md-6" id="HukukiYapi" value="<?php echo $row_uyebilgileri['HukukiYapi']; ?>" data-trigger="hover">

                              </div>
  <div class="form-group">
                              <label class="col-md-6"><?php echo $dil["kayit_topla_uretim"]; ?></label>

                                <input name="ToplamuretimAlani" type="text" class="form-control float-right col-md-6" id="ToplamuretimAlani" value="<?php echo $row_uyebilgileri['ToplamuretimAlani']; ?>" data-trigger="hover"  onkeyup="SayiKontrol(event)">

                              </div>
  <div class="form-group">
                              <label class="col-md-6"><?php echo $dil['kayit_calisan_sayi']; ?></label>

                                <input name="calisansayi" type="text" class="form-control float-right col-md-6" id="calisansayi" value="<?php echo $row_uyebilgileri['Calisansayi']; ?>" data-trigger="hover"  onkeyup="SayiKontrol(event)">

                              </div>

  <div class="form-group">
                              <label class="col-md-6"><?php echo $dil['kayit_gecen_yil_ciro']; ?></label>
 
 
                                   <input name="GecenYilciro" type="text" class="form-control float-right col-md-6" id="GecenYilciro" onkeyup="SayiKontrol(event)" value="<?php echo $row_uyebilgileri['GecenYilciro']; ?>" data-trigger="hover" >
                                 </div>

  <div class="form-group">
                              <label class="col-md-6"><?php echo $dil['kayit_ekipman_yatirim']; ?></label>

                                    <input name="EkipmanYatirim" type="text" class="form-control float-right col-md-6" id="EkipmanYatirim" onkeyup="SayiKontrol(event)" value="<?php echo $row_uyebilgileri['EkipmanYatirim']; ?>" data-trigger="hover" >
                                 </div>

  <div class="form-group">
                                <label class="col-md-6"><?php echo $dil['kayit_i_orani']; ?></label>


                                    <input name="IhracatOran" type="text" class="form-control float-right col-md-6" id="ihracatOran" onkeyup="SayiKontrol(event)" value="<?php echo $row_uyebilgileri['ihracatoran']; ?>" data-trigger="hover" >

                              </div>   




            </div>
     </div>

              <div class="col-md-6">
                      <!-- Bar chart -->
            <div class="card card-primary card-outline">
              <div class="card-header">


                            <h4><i class="icon-reorder"></i><?php echo $dil['kayit_onemli_bilgi']; ?></h4>

                        </div>
                        <div class="widget-body form">

                            <!-- BEGIN FORM-->




                             <div class="form-group">
                              <label class="col-md-3"><?php echo $dil['kayit_kadi']; ?></label>

<?php echo $row_uyebilgileri['uyeAdi']; ?>                       
                         </div>
                           
                          
                   <div class="form-group">
                              <label class="col-md-3"><?php echo $dil['kayit_kategori']; ?></label>
   
 
 
 
 
 

 <?php
// Determine selected categories from user data
$sql = isset($row_uyebilgileri['Kategori']) ? $row_uyebilgileri['Kategori'] : '';
$sqlArray = explode(',', $sql);

// Select the appropriate language category
if ($_SESSION["dil"] == 'tr') {
    $query = "SELECT KategoriID, KategoriAdi FROM kategori ORDER BY KategoriAdi ASC";
} elseif ($_SESSION["dil"] == 'en') {
    $query = "SELECT KategoriID, Kategoriing AS KategoriAdi FROM kategori ORDER BY Kategoriing ASC";
} else {  // Assuming the else case is for Chinese
    $query = "SELECT KategoriID, Kategorichn AS KategoriAdi FROM kategori ORDER BY Kategorichn ASC";
}

$stmt = $pdo->prepare($query);
$stmt->execute();
?>

<select name="Kategori[]" class="select2" multiple="multiple" tabindex="6">
    <?php
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $selected = in_array($row['KategoriID'], $sqlArray) ? 'selected' : '';
        echo '<option value="' . htmlspecialchars($row['KategoriID']) . '" ' . $selected . '>';
        echo htmlspecialchars($row['KategoriAdi']);
        echo '</option>';
    }
    ?>
</select>



                
    
                
                          </div>

           <div class="form-group">
                              <label class="col-md-3"><?php echo $dil['kayit_ref_musteri']; ?></label>
                
                                 <input name="ReferansMusteri" type="text" class="form-control" id="tags_2" value="<?php echo $row_uyebilgileri['ReferansMusteri']; ?>">
   
                              </div>
                          
            <div class="form-group">
                              <label class="col-md-3"><?php echo $dil['iletisim_paylas']; ?></label>

                                 <label class="checkbox">
                                 <input <?php if (!(strcmp($row_uyebilgileri['paylas'],1))) {echo "checked=\"checked\"";} ?> name="paylas" type="checkbox" id="paylas" value=""> Paylaş
                                 </label>
 

          </div>

            <div class="form-group">
                                <label class="col-md-3"><?php echo $dil['ologo']; ?></label>

                                        <img src="../resim/firmalar/logo/<?php echo $row_uyebilgileri['firmalogo']; ?>" width="150" height="100">

          </div>
                        
                        
                        
                        
              <div class="form-group">
                                    <label class="col-md-3"><?php echo $dil['slogo']; ?></label>

                                        <input name="firmalogo" type="file" class="default" id="firmalogo">

            </div>
                
              <div class="form-group">
                                    <label class="col-md-3"><?php echo $dil['sbdosyasi']; ?></label>

                                        <input name="saticiBilgiDosyasi" type="file" class="default" id="saticiBilgiDosyasi"><a href="../resim/firmalar/saticiBilgiDosyasi/<?php echo $row_uyebilgileri['saticiBilgiDosyasi']; ?>"target=\"_blank\"><?php echo $dil['sbdosyasi']; ?></a>

            </div>

              <div class="form-group">
                                    <label class="col-md-3"><?php echo $dil['isirku']; ?></label>

                                        <input name="imzasirkusu" type="file" class="default" id="imzasirkusu"><a href="../resim/firmalar/imzasirkusu/<?php echo $row_uyebilgileri['imzasirkusu']; ?>"target=\"_blank\"><?php echo $dil['isirku']; ?></a>

            </div>
                
                
              <div class="form-group">
                                    <label class="col-md-3"><?php echo $dil['osemasi']; ?></label>

                                        <input name="osema" type="file" class="default" id="osema"><a href="../resim/firmalar/osema/<?php echo $row_uyebilgileri['osema']; ?>"target=\"_blank\"><?php echo $dil['osemasi']; ?></a>

            </div>



            </div>
            </div>
            </div>
</div>



           <div class="row">
              <div class="col-md-6">
                      <!-- Bar chart -->
            <div class="card card-primary card-outline">
              <div class="card-header">

                            <h4><i class="icon-reorder"></i><?php echo $dil['kayit_baslik_adres']; ?></h4>
                        </div>
                        <div class="widget-body form">
                            <!-- BEGIN FORM-->
                            
   <div class="form-group">
                              <label class="col-md-3"><?php echo $dil['kayit_ulke']; ?></label>

                              
                             
 
 <select name ="Ulke" class="form-control select2" id="country" onChange="print_state('state', this.selectedIndex);" >

</select>
                     
                                

                           </div>
<div class="form-group">
                              <label class="col-md-3"><?php echo $dil['kayit_sehir']; ?></label>

                              <select name ="sehir" class="form-control select2" id ="state"></select>
                              </div>
<div class="form-group">
                              <label class="col-md-3"><?php echo $dil['kayit_posta_kodu']; ?></label>

                                <input name="PostaKodu" type="text" class="col-md-6" id="PostaKodu" value="<?php echo $row_uyebilgileri['PostaKodu']; ?>" data-trigger="hover"  onkeyup="SayiKontrol(event)">
                              </div>
<div class="form-group">
                              <label class="col-md-3"><?php echo $dil['kayit_adres']; ?></label>

                                <textarea name="Adres" rows="3" class="col-md-6" id="Adres" type="text" data-trigger="hover" ><?php echo $row_uyebilgileri['Adres']; ?></textarea>
                              </div>
<div class="form-group">
                              <label class="col-md-3"><?php echo $dil['kayit_web_adres']; ?></label>

                                <input name="WebAdres" type="text" class="span5  popovers" id="WebAdres" value="<?php echo $row_uyebilgileri['WebAdres']; ?>" data-trigger="hover" >
                              </div>
<div class="form-group">
                                    <label class="col-md-3"><?php echo $dil['kayit_tel_no']; ?></label>

                                        <input name="Tel" type="text" class="span5" id="Tel" placeholder="" value="<?php echo $row_uyebilgileri['Tel']; ?>" data-mask="(999) 999-999-99-99">
                                        <span class="help-inline">(999) 999-999-99-99</span>
                                    </div>
<div class="form-group">
                                    <label class="col-md-3"><?php echo $dil['kayit_fax_no']; ?></label>

                                        <input name="Fax" type="text" class="span5" id="Fax" placeholder="" value="<?php echo $row_uyebilgileri['Fax']; ?>" data-mask="(999) 999-999-99-99">
                                        <span class="help-inline">(999) 999-999-99-99</span>
                                    </div>
                                </div>
                            



            </div>
</div>


           
         <div class="col-md-6">
            <!-- Bar chart -->
            <div class="card card-primary card-outline">
              <div class="card-header">

                           <h4><i class="icon-globe"></i><?php echo $dil['kayit_kontak_bilgi']; ?></h4>
                   
                        </div>
                        <div class="widget-body">
                            <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th width="140"></th>
                                    <th width="140" class="hidden-phone"><?php echo $dil['kayit_personel']; ?></th>
                                    <th width="140" class="hidden-phone"><?php echo $dil['kayit_tel']; ?></th>
                                    <th width="140" class="hidden-phone"><?php echo $dil['kayit_email']; ?></th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr class="odd gradeX">
                                  <td width="140"><?php echo $dil['kayit_hesap_yonetici']; ?></td>
                                    <td width="140" class="hidden-phone"><input name="HesapYoneticisiTitle" type="text" class="form-control" id="HesapYoneticisiTitle" value="<?php echo $row_uyebilgileri['HesapYoneticisiTitle']; ?>"></td>
                                  <td width="140" class="center hidden-phone"><input name="HesapYoneticisiTel" type="text" class="form-control" id="HesapYoneticisiTel" value="<?php echo $row_uyebilgileri['HesapYoneticisiTel']; ?>"></td>
                                    <td width="140" class="hidden-phone"><input name="HesapYoneticisiMail" type="email" class="form-control" id="HesapYoneticisiMail" value="<?php echo $row_uyebilgileri['HesapYoneticisiMail']; ?>"></td>
                                </tr>
                                <tr class="odd gradeX">
                                  <td width="140"><?php echo $dil['kayit_sales']; ?></td>
                                    <td width="140" class="hidden-phone"><input name="salesMarketingTitle" type="text" class="form-control" id="salesMarketingTitle" value="<?php echo $row_uyebilgileri['SalesMarketingTitle']; ?>"></td>
                                  <td width="140" class="center hidden-phone"><input name="salesMarketingTel" type="text" class="form-control" id="salesMarketingTel" value="<?php echo $row_uyebilgileri['salesMarketingTel']; ?>"></td>
                                    <td width="140" class="hidden-phone"><input name="salesMarketingMail" type="email" class="form-control" id="salesMarketingMail" value="<?php echo $row_uyebilgileri['salesMarketingMail']; ?>"></td>
                                </tr>
                                <tr class="odd gradeX">
                                  <td width="140"><?php echo $dil['kayit_rd']; ?></td>
                                    <td width="140" class="hidden-phone"><input name="RDTitle" type="text" class="form-control" id="RDTitle" value="<?php echo $row_uyebilgileri['RDTitle']; ?>"></td>
                                  <td width="140" class="center hidden-phone"><input name="RDTel" type="text" class="form-control" id="RDTel" value="<?php echo $row_uyebilgileri['RDTel']; ?>"></td>
                                    <td width="140" class="hidden-phone"><input name="RDMail" type="email" class="form-control" id="RDMail" value="<?php echo $row_uyebilgileri['RDMail']; ?>"></td>
                                </tr>
                                <tr class="odd gradeX">
                                  <td width="140"><?php echo $dil['kayit_logis']; ?></td>
                                    <td width="140" class="hidden-phone"><input name="LogisticsTitle" type="text" class="form-control" id="LogisticsTitle" value="<?php echo $row_uyebilgileri['LogisticsTitle']; ?>"></td>
                                  <td width="140" class="center hidden-phone"><input name="LogisticsTel" type="text" class="form-control" id="LogisticsTel" value="<?php echo $row_uyebilgileri['LogisticsTel']; ?>"></td>
                                    <td width="140" class="hidden-phone"><input name="LogisticsMail" type="email" class="form-control" id="LogisticsMail" value="<?php echo $row_uyebilgileri['LogisticsMail']; ?>"></td>
                                </tr>
                                <tr class="odd gradeX">
                                  <td width="140"><?php echo $dil['kayit_purc']; ?></td>
                                    <td width="140" class="hidden-phone"><input name="PurchasingTitle" type="text" class="form-control" id="PurchasingTitle" value="<?php echo $row_uyebilgileri['PurchasingTitle']; ?>"></td>
                                  <td width="140" class="center hidden-phone"><input name="PurchasingTel" type="text" class="form-control" id="PurchasingTel" value="<?php echo $row_uyebilgileri['PurchasingTel']; ?>"></td>
                                    <td width="140" class="hidden-phone"><input name="PurchasingMail" type="email" class="form-control" id="PurchasingMail" value="<?php echo $row_uyebilgileri['PurchasingMail']; ?>"></td>
                                </tr>
                                <tr class="odd gradeX">
                                  <td width="140"><?php echo $dil['kayit_kalite']; ?></td>
                                    <td width="140" class="hidden-phone"><input name="qualityTitle" type="text" class="form-control" id="qualityTitle" value="<?php echo $row_uyebilgileri['qualityTitle']; ?>"></td>
                                  <td width="140" class="center hidden-phone"><input name="qualityTel" type="text" class="form-control" id="qualityTel" value="<?php echo $row_uyebilgileri['qualityTel']; ?>"></td>
                                    <td width="140" class="hidden-phone"><input name="qualityMail" type="email" class="form-control" id="qualityMail" value="<?php echo $row_uyebilgileri['qualityMail']; ?>"></td>
                                </tr>
                              </tbody>
                        </table>
            </div>
            </div>
            </div>
            </div>
            </div>
           
<input name="uyeID" type="hidden" class="span4 " id="uyeID" value="<?php echo $row_uyebilgileri['uyeID']; ?>">


                  <div class="form-actions">
                    <button type="submit" class="btn btn-success"><?php echo $dil['kaydet']; ?></button>
                    <button type="button" class="btn btn-danger"><?php echo $dil['iptal']; ?></button>
                  </div>
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
<!-- Select2 -->
<script src="../plugins/select2/js/select2.full.min.js"></script>
<!-- InputMask -->
<script src="../plugins/inputmask/jquery.inputmask.bundle.js"></script>
<script src="../plugins/moment/moment.min.js"></script>
<!-- date-range-picker -->
<script src="../plugins/daterangepicker/daterangepicker.js"></script>
<!-- bootstrap color picker -->
<script src="../plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>


<script type="text/javascript" src="../plugins/jquery-tags-input\jquery.tagsinput.js"></script>
<script type= "text/javascript" src = "../country_dropdown/countries3.js"></script>

<!-- DataTables -->
<script src="../plugins/datatables/jquery.dataTables.js"></script>
<script src="../plugins/datatables/dataTables.bootstrap4.js"></script>


  <script>
      jQuery(document).ready(function() {       
         // initiate layout and plugins
         App.init();
      });
   </script>
   
<script language="JavaScript">
function check_frmm(){
if (document.kategori.people.value == ""){
alert ("<?php echo $dil['kayit_kategori']; ?><?php echo $dil['eksik']; ?>");
document.kategori.uyeAdi.focus();
return false; 
}

if (document.kategori.sirketAdi.value == ""){
alert ("<?php echo $dil['sirketdi']; ?><?php echo $dil['eksik']; ?>");
document.kategori.sirketAdi.focus();
return false; 
}
if (document.kategori.Ulke.value == ""){
alert ("<?php echo $dil['kayit_ulke']; ?><?php echo $dil['eksik']; ?>");
document.kategori.Ulke.focus();
return false; 
}
if (document.kategori.sehir.value == ""){
alert ("<?php echo $dil['kayit_sehir']; ?><?php echo $dil['eksik']; ?>");
document.kategori.sehir.focus();
return false; 
}
if (document.kategori.Adres.value == ""){
alert ("<?php echo $dil['kayit_adres']; ?><?php echo $dil['eksik']; ?>");
document.kategori.Adres.focus();
return false; 
}
if (document.kategori.Tel.value == ""){
alert ("<?php echo $dil['kayit_tel']; ?><?php echo $dil['eksik']; ?>");
document.kategori.Tel.focus();
return false; 
}
if (document.kategori.HesapYoneticisiTitle.value == ""){
alert ("<?php echo $dil['kayit_hesap_yonetici']; ?><?php echo $dil['kayit_personel']; ?><?php echo $dil['eksik']; ?>");
document.kategori.HesapYoneticisiTitle.focus();
return false; 
}
if (document.kategori.HesapYoneticisiTel.value == ""){
alert ("<?php echo $dil['kayit_hesap_yonetici']; ?><?php echo $dil['kayit_tel']; ?><?php echo $dil['eksik']; ?>");
document.kategori.HesapYoneticisiTel.focus();
return false; 
}
if (document.kategori.HesapYoneticisiMail.value ==0 )
  {
    alert("<?php echo $dil['kayit_hesap_yonetici']; ?><?php echo $dil['kayit_email']; ?><?php echo $dil['eksik']; ?>");
    document.kategori.HesapYoneticisiMail.focus();
    return (false);
  }    
    if    ((document.kategori.HesapYoneticisiMail.value.indexOf("@"))<1)
    {
    alert("<?php echo $dil['kayit_hesap_yonetici']; ?><?php echo $dil['kayit_email']; ?><?php echo $dil['eksik']; ?>");
    document.kategori.HesapYoneticisiMail.focus();
    return false;
    }
    if    ((document.kategori.HesapYoneticisiMail.value.lastIndexOf("."))-(document.kategori.HesapYoneticisiMail.value.indexOf("@"))<2)
    {
    alert("<?php echo $dil['kayit_hesap_yonetici']; ?><?php echo $dil['kayit_email']; ?><?php echo $dil['eksik']; ?>");
    document.kategori.HesapYoneticisiMail.focus();
    return false;
    }

}
</script>
    <script language="javascript">print_country("country");
$('#country').val('<?php echo $row_uyebilgileri['Ulke']; ?>');
print_state('state',$('#country')[0].selectedIndex);
  $('#state').val('<?php echo $row_uyebilgileri['sehir']; ?>');  
    </script>
  
<script>
function SayiKontrol(e) {
  olay = document.all ? window.event : e;
  tus = document.all ? olay.keyCode : olay.which;
  if(tus<48||tus>57) {
    if(document.all) { olay.returnValue = false; } else { olay.preventDefault(); }
  }
}

function HarfKontrol(e) {
  olay = document.all ? window.event : e;
  tus = document.all ? olay.keyCode : olay.which;
  if(tus>=48&&tus<=57) {
    if(document.all) { olay.returnValue = false; } else { olay.preventDefault(); }
  }
}
</script>
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

<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2()

    //Datemask dd/mm/yyyy
    $('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })
    //Datemask2 mm/dd/yyyy
    $('#datemask2').inputmask('mm/dd/yyyy', { 'placeholder': 'mm/dd/yyyy' })
    //Money Euro
    $('[data-mask]').inputmask()

    //Date range picker
    $('#reservation').daterangepicker()
    //Date range picker with time picker
    $('#reservationtime').daterangepicker({
      timePicker: true,
      timePickerIncrement: 30,
      locale: {
        format: 'MM/DD/YYYY hh:mm A'
      }
    })
    //Date range as a button
    $('#daterange-btn').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      },
      function (start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
      }
    )

    //Timepicker
    $('#timepicker').datetimepicker({
      format: 'LT'
    })

    //Colorpicker
    $('.my-colorpicker1').colorpicker()
    //color picker with addon
    $('.my-colorpicker2').colorpicker()

    $('.my-colorpicker2').on('colorpickerChange', function(event) {
      $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
    });
  })
</script>
</body>
</html>


