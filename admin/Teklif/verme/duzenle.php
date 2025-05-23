<?php 
require_once('../../../Connections/baglan.php');
require_once('../../../fonksiyon.php');

if (!isset($_SESSION)) {
    session_start();
}

// Yetkilendirme ve yönlendirme
$MM_authorizedUsers = "1,2";
$MM_restrictGoTo = "../../../login.php";
if (!(isset($_SESSION['MM_Username']) && isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup']))) {
    $MM_qsChar = strpos($MM_restrictGoTo, "?") ? "&" : "?";
    $queryString = $_SERVER['QUERY_STRING'] ? "?" . $_SERVER['QUERY_STRING'] : "";
    header("Location: " . $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($_SERVER['PHP_SELF'] . $queryString));
    exit;
}

// Fonksiyon tanımları
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) {
    $arrUsers = explode(",", $strUsers);
    $arrGroups = explode(",", $strGroups);
    if (!empty($UserName)) {
        return in_array($UserName, $arrUsers) || in_array($UserGroup, $arrGroups) || empty($strUsers);
    }
    return false;
}

// Form işlemleri
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// TalepID'ye göre fotoğraf bilgileri almak
$fotoID = isset($_GET['TalepID']) ? $_GET['TalepID'] : null;
$stmt = $pdo->prepare("SELECT spek FROM teklifiste WHERE TalepID = :TalepID");
$stmt->execute(['TalepID' => $fotoID]);
$row_foto = $stmt->fetch();

// Fotoğraf yükleme işlemi
if (!empty($_FILES['spek']['name'])) {
    if ($row_foto && file_exists("../resim/spek/" . $row_foto['spek'])) {
        unlink("../resim/spek/" . $row_foto['spek']);
    }

    $dosyaismi = $_FILES['spek']['name'];
    $isim = md5(uniqid(rand()));
    $uzanti = pathinfo($dosyaismi, PATHINFO_EXTENSION);
    $yeniad = $isim . "." . $uzanti;
    $filename = $_FILES['spek']['tmp_name'];
    $destination = "../resim/spek/" . $yeniad;
    move_uploaded_file($filename, $destination);
}

// Güncelleme işlemi
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
    // Dil dosyalarını yükleme
    if ($_SESSION['dil'] === 'tr') {
        require("../../mail/duzenmail.php");
    } elseif ($_SESSION['dil'] === 'en') {
        require("../../mail/duzenmaileng.php");
    } elseif ($_SESSION['dil'] === 'chn') {
        require("../../mail/duzenmailchn.php");
    }

    // Güncelleme sorgusu
    $updateSQL = "UPDATE teklifiste SET odemekosul = ?, spek = ?, teslimsekli = ?, hedeffiyat = ?, adres = ?, postakodu = ?, referansno = ?, uruntanim = ?, forecast = ?, odemevadesi = ?, teslimsure = ?, parabirim = ?, aciklama = ?, tbastarih = ?, tbittarih = ? WHERE TalepID = ?";
    $stmt = $pdo->prepare($updateSQL);
    $stmt->execute([
        $_POST['odemekosul'], $yeniad, $_POST['teslimsekli'], $_POST['hedeffiyat'], $_POST['adres'],
        $_POST['postakodu'], $_POST['referansno'], $_POST['uruntanim'], $_POST['forecast'], $_POST['odemevadesi'],
        $_POST['teslimsure'], $_POST['parabirim'], $_POST['aciklama'], $_POST['tbastarih'], date('Y-m-d', strtotime($_POST['tbittarih'])),
        $_POST['TalepID']
    ]);

    // Yönlendirme
    $updateGoTo = "../tekliflerim/index.php";
    if (isset($_SERVER['QUERY_STRING'])) {
        $updateGoTo .= (strpos($updateGoTo, '?') ? "&" : "?") . $_SERVER['QUERY_STRING'];
    }
    header("Location: $updateGoTo");
}

// Kullanıcı bilgileri
$colname_uyebilgileri = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";
$stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeAdi = :uyeAdi");
$stmt->execute(['uyeAdi' => $colname_uyebilgileri]);
$row_uyebilgileri = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_uyebilgileri = $stmt->rowCount();

// Teklif isteği bilgileri
$colname_teklifiste = isset($_GET['TalepID']) ? $_GET['TalepID'] : "-1";
$stmt = $pdo->prepare("SELECT DISTINCT * FROM teklifiste INNER JOIN uyeler ON uyeler.uyeID = teklifiste.istekuyeID WHERE uyeler.uyeID = teklifiste.istekuyeID AND TalepID = :TalepID ORDER BY TalepID ASC");
$stmt->execute(['TalepID' => $colname_teklifiste]);
$row_teklifiste = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_teklifiste = $stmt->rowCount();

// Site ayarları
$stmt = $pdo->query("SELECT * FROM siteconfig");
$row_ayar = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_ayar = $stmt->rowCount();

// Kategoriler
$stmt = $pdo->query("SELECT * FROM kategori ORDER BY KategoriID DESC");
$row_kategori = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_kategori = $stmt->rowCount();

// Mailler
$q = intval($row_teklifiste['kategorialma']);
$stmt = $pdo->prepare("SELECT sirketAdi, PurchasingMail, Kategori FROM uyeler WHERE FIND_IN_SET(:Kategori, Kategori)");
$stmt->execute(['Kategori' => $q]);
$row_mail = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_mail = $stmt->rowCount();



$stmt = $pdo->prepare("SELECT sirketAdi, PurchasingMail, Kategori FROM uyeler WHERE FIND_IN_SET(:Kategori, Kategori)");
$stmt->execute(['Kategori' => $q]);
$mails = $stmt->fetchAll(PDO::FETCH_ASSOC);



// Mesajlar
$uyeID = isset($row_uyebilgileri['uyeID']) ? $row_uyebilgileri['uyeID'] : null;
$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE kime = :kime AND durum = 0");
$stmt->execute(['kime' => $uyeID]);
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
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../../plugins/summernote/summernote-bs4.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.0/css/rowReorder.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css">
  <link rel="stylesheet" href="../../plugins/datatables/dataTables.bootstrap4.css">
  <link rel="stylesheet" href="../../plugins/daterangepicker/daterangepicker.css">

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
          <div class="card card-primary card-outline">
          <h3 class="card-title"><?php echo $q ?>--><?php echo $dil["teklif_duzenle"]; ?>--><?php echo $dil['verme_talep']; ?>:<?php echo $row_teklifiste['TalepID']; ?></h3>
 </div>

        </div>
        <div class="card-body">
          
  <!-- KODLARRRRRRRRRR -->


    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-6">


         <form name="form1" method="POST" enctype="multipart/form-data"  action="<?php echo $editFormAction; ?>" onSubmit="return check_frmm()">
              
 <?php if($row_uyebilgileri['uyeID']==$row_teklifiste['istekuyeID']) :?>                   
    
            <div class="card card-primary card-outline">
            <div class="card-body box-profile">    

                        <label class="control-label"><?php echo $dil['teklif_cik_mail']; ?></label>
						
                          <div class="form-group">
<select name="email[]" id="people" multiple size="4" data-live-search="true" data-actions-box="true">
    <?php foreach ($mails as $row_mail): ?>
    <option value="<?php echo htmlspecialchars($row_mail['PurchasingMail']); ?>" selected>
        <?php echo htmlspecialchars($row_mail['sirketAdi']); ?>
    </option>
    <?php endforeach; ?>
</select>
                          </div>

             
                          <div class="form-group">
                              <label class="control-label"><?php echo $dil['verme_okosul']; ?></label>
                                  <select name="odemekosul" id="odemekosul" class="form-control col-md-3">
                                    <option value="T/T"<?php  if ($row_teklifiste['odemekosul'] == 'T/T') {?><?php echo ' selected="selected"'; }?>>T/T-<?php echo $dil['T/T']; ?>
                                    <option value="L/C"<?php  if ($row_teklifiste['odemekosul'] == 'L/C') {?><?php echo ' selected="selected"'; }?>>L/C-<?php echo $dil['L/C']; ?>
                                    <option value="CAD"<?php  if ($row_teklifiste['odemekosul'] == 'CAD') {?><?php echo ' selected="selected"'; }?>>CAD-<?php echo $dil['CAD']; ?>
                                    <option value="CAG"<?php  if ($row_teklifiste['odemekosul'] == 'CAG') {?><?php echo ' selected="selected"'; }?>>CAG-<?php echo $dil['CAG']; ?>
                                  </select>
                          </div>


                          <div class="form-group">
                                 <label class="control-label"><?php echo $dil['verme_vade']; ?></label>
                                 <input name="odemevadesi" type="text" class="form-control form-control-sm col-md-3" id="odemevadesi" value="<?php echo $row_teklifiste['odemevadesi']; ?>" onkeypress="SayiKontrol(event)">
                          </div>
                          
                          
                          <div class="form-group">
                              <label class="control-label"><?php echo $dil['verme_teslimsekli']; ?></label>
                                 <select name="teslimsekli" id="teslimsekli" class="form-control col-md-3">
                                    <option value="CFR"<?php  if ($row_teklifiste['teslimsekli'] == 'CFR') {?><?php echo ' selected="selected"'; }?>>CFR-<?php echo $dil['CFR']; ?>
                                    <option value="CIF"<?php  if ($row_teklifiste['teslimsekli'] == 'CIF') {?><?php echo ' selected="selected"'; }?>>CIF-<?php echo $dil['CIF']; ?>
                                    <option value="CIP"<?php  if ($row_teklifiste['teslimsekli'] == 'CIP') {?><?php echo ' selected="selected"'; }?>>CIP-<?php echo $dil['CIP']; ?>
                                    <option value="CPT"<?php  if ($row_teklifiste['teslimsekli'] == 'CPT') {?><?php echo ' selected="selected"'; }?>>CPT-<?php echo $dil['CPT']; ?>
                                    <option value="DAF"<?php  if ($row_teklifiste['teslimsekli'] == 'DAF') {?><?php echo ' selected="selected"'; }?>>DAF-<?php echo $dil['DAF']; ?>
                                    <option value="DES"<?php  if ($row_teklifiste['teslimsekli'] == 'DES') {?><?php echo ' selected="selected"'; }?>>DES-<?php echo $dil['DES']; ?>
                                    <option value="DDP"<?php  if ($row_teklifiste['teslimsekli'] == 'DDP') {?><?php echo ' selected="selected"'; }?>>DDP-<?php echo $dil['DDP']; ?>
                                    <option value="EXW"<?php  if ($row_teklifiste['teslimsekli'] == 'EXW') {?><?php echo ' selected="selected"'; }?>>EXW-<?php echo $dil['EXW']; ?>
                                    <option value="FAS"<?php  if ($row_teklifiste['teslimsekli'] == 'FAS') {?><?php echo ' selected="selected"'; }?>>FAS-<?php echo $dil['FAS']; ?>
                  <option value="FCA"<?php  if ($row_teklifiste['teslimsekli'] == 'FCA') {?><?php echo ' selected="selected"'; }?>>FCA-<?php echo $dil['FCA']; ?>
                  <option value="FOB"<?php  if ($row_teklifiste['teslimsekli'] == 'FOB') {?><?php echo ' selected="selected"'; }?>>FOB-<?php echo $dil['FOB']; ?>
                                    <option value="DEQ"<?php  if ($row_teklifiste['teslimsekli'] == 'DEQ') {?><?php echo ' selected="selected"'; }?>>DEQ-<?php echo $dil['DEQ']; ?>
                                    <option value="DDU"<?php  if ($row_teklifiste['teslimsekli'] == 'DDU') {?><?php echo ' selected="selected"'; }?>>DDU-<?php echo $dil['DDU']; ?>
                                 </select>
                          </div>
                          
                          <div class="form-group">
                                 <label class="control-label"><?php echo $dil['verme_teslimsuresi']; ?></label>
                                 <input name="teslimsure" type="text" class="form-control form-control-sm col-md-3" id="teslimsure" value="<?php echo $row_teklifiste['teslimsure']; ?>" onkeypress="SayiKontrol(event)">
                          </div>

                      
                          <div class="form-group">
                                 <label class="control-label"><?php echo $dil['verme_adres']; ?></label>
                                 <textarea name="adres" rows="6" class="form-control form-control-sm col-md-3" id="adres"><?php echo $row_teklifiste['adres']; ?></textarea>
                          </div>
                       
                          <div class="form-group">
                              <label class="control-label"><?php echo $dil['verme_posta']; ?></label>
                              <input name="postakodu" type="text" class="form-control form-control-sm col-md-3" id="postakodu" value="<?php echo $row_teklifiste['postakodu']; ?>">
                          </div>
</div>
        
            </div>
            </div>


  <div class="col-md-6">


            <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                          <div class="control-group">
                                    <a href="../../resim/spek/<?php echo $row_teklifiste['spek']; ?>"><i class="icon-picture"></i><?php echo $dil['mspek']; ?></a>   
                          </div>


                          <div class="form-group">
                                        <label class="control-label"><?php echo $dil['mspekdegis']; ?></label>
                                        <input name="spek" type="file" class="default" id="spek">
                          </div>


                          <div class="form-group">
                                 <label class="control-label"><?php echo $dil['verme_ref']; ?></label>
                                 <input name="referansno" type="text" class="form-control form-control-sm col-md-3" id="referansno" value="<?php echo $row_teklifiste['referansno']; ?>">
                          </div>


                          <div class="form-group">
                                 <label class="control-label"><?php echo $dil['verme_ktanim']; ?></label>
                                 <input name="uruntanim" type="text" class="form-control form-control-sm col-md-3" id="uruntanim" value="<?php echo $row_teklifiste['uruntanim']; ?>">
                          </div>
 
                          <div class="form-group">
                                   <label class="control-label"><?php echo $dil['verme_forecast']; ?></label>
                                   <input name="forecast" type="text" class="form-control form-control-sm col-md-3" id="forecast" value="<?php echo $row_teklifiste['forecast']; ?>" onkeypress="SayiKontrol(event)">                                            
                          </div>

                          <div class="form-group">
                                 <label class="control-label"><?php echo $dil['verme_hedef']; ?></label>
                                 <input name="hedeffiyat" type="text" class="form-control form-control-sm col-md-3" id="hedeffiyat" value="<?php echo $row_teklifiste['hedeffiyat']; ?>" onkeypress="SayiKontrol(event)">
                          </div>         
                           
                          <div class="form-group">
                                    <label class="control-label"><?php echo $dil['verme_para']; ?></label>
                                 <select name="parabirim" id="parabirim" class="form-control col-md-3">
                                    <option value="TL"<?php  if ($row_teklifiste['parabirim'] == 'TL') {?><?php echo ' selected="selected"'; }?>>TL
                                    <option value="USD"<?php  if ($row_teklifiste['parabirim'] == 'USD') {?><?php echo ' selected="selected"'; }?>>USD
                                    <option value="EUR"<?php  if ($row_teklifiste['parabirim'] == 'EUR') {?><?php echo ' selected="selected"'; }?>>EUR
                                 </select>
                          </div>
                      
                          <div class="form-group">
                                 <label class="control-label"><?php echo $dil['verme_aciklama']; ?></label>
                                 <textarea name="aciklama" rows="6" class="form-control form-control-sm col-md-3" id="aciklama"><?php echo $row_teklifiste['aciklama']; ?></textarea>
                          </div>
              
                          <div class="form-group">
                              <label class="control-label"><?php echo $dil['teslim_gecerli']; ?></label>
                              <input type="hidden" name="TalepID" id="TalepID" value="<?php echo $row_teklifiste['TalepID']; ?>">
                              <input name="tbastarih" type="text"  disabled class="form-control form-control-sm col-md-3" value="<?php echo $row_teklifiste['tbastarih']; ?>" >
                              <input name="tbittarih"  type="text"  class="form-control form-control-sm col-md-3" id="tarih" value="<?php echo $row_teklifiste['tbittarih']; ?>"   onkeyup="SayiKontrol(event)">
                          </div>
        </div>
        </div>


   <button type="submit" class="btn btn-success"><?php echo $dil['duzenle']; ?></button>
                      <input type="hidden" name="MM_update" value="form1">
                    </form>
                      
            <?php else :?>
                                        <div class="info-box bg-gradient-warning">
                                                          <h4><?php echo $dil['verme_uyari']; ?></h4>                  
                                        </div> 
                                                         
            <?php endif ;?>  
                


                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->

    </section>



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
<!-- FastClick -->
<script src="../../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../dist/js/demo.js"></script>
<!-- Summernote -->
<script src="../../plugins/summernote/summernote-bs4.min.js"></script>
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
 <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
 <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>         
    <script language="JavaScript">
function check_frmm(){

if (document.form1.people.value == ""){
alert ("<?php echo $dil['teklif_cik_mail']; ?>:<?php echo $dil['eksik']; ?>");
document.form1.email.focus();
return false; 
}

if (document.form1.odemekosul.value == ""){
alert ("<?php echo $dil['verme_okosul']; ?>:<?php echo $dil['eksik']; ?>");
document.form1.odemekosul.focus();
return false; 
}
if (document.form1.teslimsekli.value == ""){
alert ("<?php echo $dil['verme_teslimsekli']; ?>:<?php echo $dil['eksik']; ?>");
document.form1.teslimsekli.focus();
return false; 
}
if (document.form1.adres.value == ""){
alert ("<?php echo $dil['verme_adres']; ?>:<?php echo $dil['eksik']; ?>");
document.form1.adres.focus();
return false; 
}
if (document.form1.forecast.value == ""){
alert ("<?php echo $dil['verme_forecast']; ?>:<?php echo $dil['eksik']; ?>");
document.form1.forecast.focus();
return false; 
}
if (document.form1.odemevadesi.value == ""){
alert ("<?php echo $dil['verme_vade']; ?>:<?php echo $dil['eksik']; ?>");
document.form1.odemevadesi.focus();
return false; 
}
if (document.form1.teslimsure.value == ""){
alert ("<?php echo $dil['alma_tsure']; ?>:<?php echo $dil['eksik']; ?>");
document.form1.teslimsure.focus();
return false; 
}
if (document.form1.parabirim.value == ""){
alert ("<?php echo $dil['verme_para']; ?>:<?php echo $dil['eksik']; ?>");
document.form1.parabirim.focus();
return false; 
}

if (document.form1.tbittarih.value == ""){
alert ("<?php echo $dil['alma_btarih']; ?>:<?php echo $dil['eksik']; ?>");
document.form1.odemekosul.focus();
return false; 
}
}
</script>
   


       <script>
 $( function() {
 
 $( "#tarih" ).datepicker({
 
 dateFormat: "yy/mm/dd",
 altFormat: "yy/mm/dd",
 altField:"#tarih-db",
 monthNames: [ "<?php echo $dil['ocak']; ?>", "<?php echo $dil['subat']; ?>", "<?php echo $dil['mart']; ?>", "<?php echo $dil['nisan']; ?>", "<?php echo $dil['mayis']; ?>", "<?php echo $dil['haziran']; ?>", "<?php echo $dil['temmuz']; ?>", "<?php echo $dil['agustos']; ?>", "<?php echo $dil['eylul']; ?>", "<?php echo $dil['ekim']; ?>", "<?php echo $dil['kasim']; ?>", "<?php echo $dil['aralik']; ?>" ],
 dayNamesMin: [ "<?php echo $dil['pazar']; ?>", "<?php echo $dil['pazartesi']; ?>", "<?php echo $dil['sali']; ?>", "<?php echo $dil['carsamba']; ?>", "<?php echo $dil['persembe']; ?>", "<?php echo $dil['cuma']; ?>", "<?php echo $dil['cumartesi']; ?>" ],
 minDate: 'today',
 firstDay:1
});
 
 } );
 
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
