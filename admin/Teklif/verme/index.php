<?php 
require_once('../../../Connections/baglan.php');  // PDO bağlantı dosyasını ekleyin
require_once('../../../fonksiyon.php');

if (!isset($_SESSION)) {
    session_start();
}

$tarih = date("Y/m/d");
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





if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
    $dosyaismi = $_FILES['spek']['name'];
    $isim = md5(uniqid(rand()));
    $uzanti = pathinfo($dosyaismi, PATHINFO_EXTENSION);
    $yeniad = $isim . "." . $uzanti;

    $sql = "INSERT INTO teklifiste (kategorialma, tbastarih, tbittarih, teklifaktif, spek, odemekosul, teslimsekli, hedeffiyat, adresteslim, adres, postakodu, referansno, uruntanim, forecast, odemevadesi, teslimsure, parabirim, aciklama, forecastsabit, istekuyeID, teslimyeri, isteksirketadi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['kategorialma'], 
        date('Y-m-d', strtotime($_POST['tbastarih'])),
        date('Y-m-d', strtotime($_POST['tbittarih'])),
        isset($_POST['teklifaktif']) ? 1 : 0,
        $yeniad,
        $_POST['odemekosul'],
        $_POST['teslimsekli'],
        $_POST['hedeffiyat'],
        isset($_POST['adresteslim']) ? 'Y' : 'N',
        $_POST['adres'],
        $_POST['postakodu'],
        $_POST['referansno'],
        $_POST['uruntanim'],
        $_POST['forecast'],
        $_POST['odemevadesi'],
        $_POST['teslimsure'],
        $_POST['parabirim'],
        $_POST['aciklama'],
        $_POST['forecastsabit'],
        $_POST['istekuyeID'],
        $_POST['teslimyeri'],
        $_POST['isteksirketadi']
    ]);

    // Resim yüklemesi
    $filename = $_FILES['spek']['tmp_name'];
    $destination = "../../resim/spek/" . $yeniad;
    move_uploaded_file($filename, $destination);

    header("Location: ../tekliflerim/index.php?Ekle=EklemeBasarili");
    exit;
}

$colname_uyebilgileri = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";
$stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeAdi = ?");
$stmt->execute([$colname_uyebilgileri]);
$row_uyebilgileri = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM kategori ORDER BY KategoriAdi ASC");
$row_kategorit = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM kategori ORDER BY Kategoriing ASC");
$row_kategorie = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM kategori ORDER BY Kategorichn ASC");
$row_kategoric = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM teklifiste WHERE teklifaktif = 0 ORDER BY teklifaktif ASC");
$row_teklifoku = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM siteconfig");
$row_ayar = $stmt->fetch(PDO::FETCH_ASSOC);

$uyeID = isset($row_uyebilgileri['uyeID']) ? $row_uyebilgileri['uyeID'] : null;
$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE kime = ? AND durum = 0");
$stmt->execute([$uyeID]);
$row_mesaj = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_mesaj = $stmt->rowCount();  // Count rows

    $query_teklifiste = "SELECT kategorialma, spek, odemekosul, teslimsekli, hedeffiyat, adresteslim, adres, postakodu, referansno, uruntanim, forecast, odemevadesi, teslimsure, parabirim, aciklama, gondermetarih, forecastsabit, istekuyeID FROM teklifiste";
    $stmt = $pdo->prepare($query_teklifiste);
    $stmt->execute();
    $row_teklifiste = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $totalRows_teklifiste = $stmt->rowCount();
// Fetch categories based on session language
$kategoriler = [];
$kategoriField = 'KategoriAdi';  // Default field for Turkish

if ($_SESSION["dil"] == 'en') {
    $kategoriField = 'Kategoriing';
} elseif ($_SESSION["dil"] == 'chn') {
    $kategoriField = 'Kategorichn';
}

$stmt = $pdo->prepare("SELECT KategoriID, $kategoriField AS KategoriAdi FROM kategori ORDER BY $kategoriField ASC");
$stmt->execute();
$kategoriler = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
          <h3 class="card-title"><?php echo $dil['verme_baslik']; ?></h3>


        </div>
        <div class="card-body">
          
  <!-- KODLARRRRRRRRRR -->


            <div class="card card-primary card-outline">   
            <div class="row">
              <div class="col-md-6">
                          <div class="form-group">
                 <script type="text/javascript">
    //<![CDATA[
        $(window).load(function() { // makes sure the whole site is loaded
            $('#status1').fadeOut(); // will first fade out the loading animation
            $('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
            $('body').delay(350).css({'overflow':'visible'});
        })
    //]]>
</script>

                                   <form method="POST" action="<?php echo $editFormAction; ?>" name="form1"   id="form1" onSubmit="return check_frmm()" enctype="multipart/form-data" >
                    
                           
                           
                                 <label class="control-label"><?php echo $dil['verme_kategori']; ?></label>

 
        <select name="kategorialma" id="kategorialma" onchange="showUser(this.value)" class="form-control col-md-3">
            <?php foreach ($kategoriler as $kategori) { ?>
                <option value="<?php echo $kategori['KategoriID']; ?>">
                    <?php echo $kategori['KategoriAdi']; ?>
                </option>
            <?php } ?>
        </select>
        <div id="txtHint"><?php echo $dil['kategori_bilgi']; ?></div>
        <?php echo $dil['firmasec']; ?>
                   
                             </div>
                   

                          <div class="form-group">
                                    <label class="control-label"><?php echo $dil['verme_spec']; ?></label>
                                    <div class="controls">
                                        <input name="spek" type="file" id="spek" >
                                    </div>
                          </div>
 
                          <div class="form-group">
                              <label class="control-label"><?php echo $dil['verme_okosul']; ?></label>
                                 <select name="odemekosul"  id="odemekosul" class="form-control col-md-3">
                                    <option value="T/T">T/T-<?php echo $dil['T/T']; ?>
                                    <option value="L/C">L/C-<?php echo $dil['L/C']; ?>
                                    <option value="CAD">CAD-<?php echo $dil['CAD']; ?>
                                    <option value="CAG">CAG-<?php echo $dil['CAG']; ?>
                                 </select>
                          </div>
                          <div class="form-group">
                                 <label class="control-label"><?php echo $dil['verme_vade']; ?></label>
                                 <input name="odemevadesi" type="text" class="form-control form-control-sm col-md-3" id="odemevadesi" onkeypress="SayiKontrol(event)" >
                         </div>
             
                          
                          <div class="form-group">
                                    <label class="col-md-6"><?php echo $dil['verme_teslimsekli']; ?></label>
                                    <select name="teslimsekli"  id="teslimsekli" class="form-control col-md-3">
                                    <option value="CFR">CFR-<?php echo $dil['CFR']; ?>
                                    <option value="CIF">CIF-<?php echo $dil['CIF']; ?>
                                    <option value="CIP">CIP-<?php echo $dil['CIP']; ?>
                                    <option value="CPT">CPT-<?php echo $dil['CPT']; ?>
                                    <option value="DAF">DAF-<?php echo $dil['DAF']; ?>
                                    <option value="DES">DES-<?php echo $dil['DES']; ?>
                                    <option value="DDP">DDP-<?php echo $dil['DDP']; ?>
                                    <option value="EXW">EXW-<?php echo $dil['EXW']; ?>
                                    <option value="FAS">FAS-<?php echo $dil['FAS']; ?>
                                    <option value="FCA">FCA-<?php echo $dil['FCA']; ?>
                                    <option value="FOB">FOB-<?php echo $dil['FOB']; ?>
                                    <option value="DEQ">DEQ-<?php echo $dil['DEQ']; ?>
                                    <option value="DDU">DDU-<?php echo $dil['DDU']; ?>
                                 </select>
                          </div>
                          <div class="form-group">
                                 <label class="col-md-6"><?php echo $dil['teslimyeri']; ?></label>
                                 <input name="teslimyeri" type="text" class="form-control form-control-sm col-md-3 " id="teslimyeri" >
                          </div>
                      
                          <div class="form-group">
                                 <label class="control-label"><?php echo $dil['verme_teslimsuresi']; ?></label>
                                 <input name="teslimsure" type="text" class="form-control form-control-sm col-md-3 " id="teslimsure" onkeypress="SayiKontrol(event)" >

                         </div>
                           
                          <div class="form-group">
                                  <label class="control-label"><?php echo $dil['verme_adres']; ?></label>
                                  <textarea name="adres" rows="3" class="form-control col-md-6" id="adres" ><?php echo $row_uyebilgileri['Adres']; ?></textarea>
                          </div>

                          <div class="form-group">
                                   <label class="control-label"><?php echo $dil['verme_posta']; ?></label>
                                   <input name="postakodu" type="text" class="form-control form-control-sm col-md-3 " id="postakodu" value="<?php echo $row_uyebilgileri['PostaKodu']; ?>">
                          </div>
            </div>   

                            
              <div class="col-md-6">

                          <div class="form-group">
                                    <label class="control-label"><?php echo $dil['verme_ref']; ?></label>
                                    <input name="referansno" type="text" class="form-control form-control-sm col-md-3 " id="referansno">
                          </div>
                          
                           <div class="form-group">
                                     <label class="control-label"><?php echo $dil['verme_ktanim']; ?></label>
                                     <input name="uruntanim" type="text" class="form-control form-control-sm col-md-3 " id="uruntanim">
                           </div>
 
                           <div class="form-group">
                                        <label class="control-label"><?php echo $dil['verme_forecast']; ?></label>
                                        <input name="forecast" type="text" class="form-control form-control-sm col-md-3" id="forecast" onkeypress="SayiKontrol(event)" >
                           </div>

                           <div class="form-group">
                                        <label class="control-label"><?php echo $dil['verme_hedef']; ?></label>
                                        <input name="hedeffiyat" type="text" onkeypress="SayiKontrol(event)" class="form-control form-control-sm col-md-3 " id="hedeffiyat">
                           </div>

                           <div class="form-group">
                                        <label class="control-label"><?php echo $dil['verme_para']; ?></label>
                                        <select name="parabirim" class="form-control col-md-3" id="parabirim">
                                        <option value="TL">TL
                                        <option value="USD">USD
                                        <option value="EUR">EUR
                                        </select>
                           </div>
                      
                           <div class="form-group">
                                        <label class="control-label"><?php echo $dil['verme_aciklama']; ?></label>
                                        <textarea name="aciklama" rows="3" class="form-control form-control-sm col-md-6 " id="aciklama"></textarea>
                           </div>


                           <div class="form-group">
                        
                                        <label class="control-label"><?php echo $dil['teslim_gecerli']; ?></label>
                                        <input name="tbastarih1" type="text" disabled class="form-control form-control-sm col-md-3"   value="<?php echo $tarih; ?>">
                                        <input name="tbittarih" type="text" class="form-control form-control-sm col-md-3" id="tarih"  size="16">



                           </div>
      

                          
                                        <input name="tbastarih" type="hidden" class="form-control form-control-sm col-md-3"  id="tbastarih" value="<?php echo $tarih; ?>">
                                        <input name="istekuyeID" type="hidden" class="form-control form-control-sm col-md-3 " id="istekuyeID" value="<?php echo $row_uyebilgileri['uyeID']; ?>">
                                        <input name="isteksirketadi" type="hidden" class="form-control form-control-sm col-md-3 " id="isteksirketadi" value="<?php echo $row_uyebilgileri['sirketAdi']; ?>">
                           <div class="form-group"> 
                                        <button type="submit" class="btn btn-success"><?php echo $dil['kaydet']; ?></button>
                                        <input type="hidden" name="MM_insert" value="form1">
                           </div>
            </div>   
            </div>
            </div>             

              </form>




  <!-- KODLARRRRRRRRRR -->




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
<script src="../../plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="../../plugins/daterangepicker/daterangepicker.js"></script>
<!-- DataTables -->
<script src="../../plugins/datatables/jquery.dataTables.js"></script>
<script src="../../plugins/datatables/dataTables.bootstrap4.js"></script>
<!-- FastClick -->
<script src="../../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../dist/js/demo.js"></script>

 <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
 <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>    

 <script language="JavaScript">
function check_frmm(){

if (document.form1.kategorialma.value == ""){
alert ("<?php echo $dil['kayit_kategori']; ?><?php echo $dil['eksik']; ?>");
document.form1.kategorialma.focus();
return false; 
}
if (document.form1.spek.value == ""){
alert ("<?php echo $dil['verme_spec']; ?><?php echo $dil['eksik']; ?>");
document.form1.spek.focus();
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
function showUser(str) {
  
    if (str == "") {
        document.getElementById("txtHint").innerHTML = "";
        return;
    } else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
        };
    
        xmlhttp.open("GET","../../sablon.php?q="+str,true);
        xmlhttp.send();
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


