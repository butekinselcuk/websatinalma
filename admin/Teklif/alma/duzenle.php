<?php
require_once('../../../Connections/baglan.php'); // Assuming $pdo is defined here
require_once('../../../fonksiyon.php');
error_reporting(0);
session_start();
$tarih = date("Y-m-d");

$MM_authorizedUsers = "1,2";
$MM_donotCheckaccess = "false";

function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) {
    $isValid = false;
    if (!empty($UserName)) {
        $arrUsers = explode(",", $strUsers);
        $arrGroups = explode(",", $strGroups);
        if (in_array($UserName, $arrUsers) || in_array($UserGroup, $arrGroups)) {
            $isValid = true;
        }
    }
    return $isValid;
}

$MM_restrictGoTo = "../../../login.php";
if (!(isset($_SESSION['MM_Username']) && isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup']))) {
    $MM_qsChar = "?";
    $MM_referrer = $_SERVER['PHP_SELF'];
    if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
    if (!empty($_SERVER['QUERY_STRING'])) $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
    $MM_restrictGoTo .= $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
    header("Location: " . $MM_restrictGoTo);
    exit;
}

if (isset($_POST["MM_update"]) && $_POST["MM_update"] == "form1") {
    $sql = "UPDATE tekliftopla SET toplaodemekosul=?, toplateslimsekli=?, toplaadres=?, toplaodemevadesi=?, toplateslimsure=?, toplaUyeID=?, toplasirketAdi=?, toplafiyat=?, toplateklifsahibiID=?, toplauyeadi=? WHERE toplaTalepID=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['toplaodemekosul'],
        $_POST['toplateslimsekli'],
        $_POST['toplaadres'],
        $_POST['toplaodemevadesi'],
        $_POST['toplateslimsure'],
        $_POST['toplauyeID'],
        $_POST['toplasirketadi'],
        $_POST['toplafiyat'],
        $_POST['toplateklifsahibiID'],
        $_SESSION['MM_Username'],
        $_POST['toplaTalepID']
    ]);
    $updateGoTo = "index.php";
    if (!empty($_SERVER['QUERY_STRING'])) {
        $updateGoTo .= (strpos($updateGoTo, '?') ? "&" : "?") . $_SERVER['QUERY_STRING'];
    }
    header("Location: $updateGoTo");
}

$colname_uyebilgileri = "-1";
if (isset($_SESSION['MM_Username'])) {
    $colname_uyebilgileri = $_SESSION['MM_Username'];
}

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeAdi = :uyeAdi");
$stmt->execute(['uyeAdi' => $colname_uyebilgileri]);
$row_uyebilgileri = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_uyebilgileri = $stmt->rowCount();

// Fetch offers
$stmt = $pdo->prepare("SELECT teklifiste.TalepID, teklifiste.aciklama, teklifiste.gondermetarih, teklifiste.teklifaktif FROM teklifiste WHERE teklifiste.teklifaktif = 0 ORDER BY teklifiste.teklifaktif ASC");
$stmt->execute();
$row_teklifoku = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_teklifoku = count($row_teklifoku);

// Fetch site configuration
$stmt = $pdo->prepare("SELECT * FROM siteconfig");
$stmt->execute();
$row_ayar = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_ayar = $stmt->rowCount();

$colname_almagoruntu = "-1";
if (isset($_GET['TalepID'])) {
    $colname_almagoruntu = $_GET['TalepID'];
}

// Fetch specific offers

$stmt = $pdo->prepare("SELECT DISTINCT * FROM uyeler, teklifiste INNER JOIN kategori ON kategori.KategoriID = teklifiste.kategorialma WHERE FIND_IN_SET(teklifiste.kategorialma, uyeler.Kategori) AND :tarih < teklifiste.tbittarih AND TalepID = :TalepID");
$stmt->execute(['tarih' => $tarih, 'TalepID' => $colname_almagoruntu]);
$row_almagoruntu = $stmt->fetch(PDO::FETCH_ASSOC);


$uyeID = $row_uyebilgileri['uyeID'];

// Fetch specific collected offers
$stmt = $pdo->prepare("SELECT * FROM teklifiste, tekliftopla, uyeler WHERE tekliftopla.toplaTalepID = :toplaTalepID AND :uyeID = tekliftopla.toplaUyeID");
$stmt->execute(['toplaTalepID' => $colname_almagoruntu, 'uyeID' => $uyeID]);
$row_topladuzenle = $stmt->fetch(PDO::FETCH_ASSOC);;
$totalRows_topladuzenle = count($row_topladuzenle);

// Fetch messages
$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE sohbet.kime = :uyeID AND sohbet.durum = 0");
$stmt->execute(['uyeID' => $uyeID]);
$row_mesaj = $stmt->fetch(PDO::FETCH_ASSOC);
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
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <!-- DataTables -->


<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.0/css/rowReorder.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css">

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
          <h3 class="card-title"></i><?php echo $dil['alma_tduzenle']; ?></h3>


        
        <div class="card-body">
          
  <!-- KODLARRRRRRRRRR -->
   
  <div class="col-md-12"> 

 <div class="widget-body">
                 
                 
              <?php 
        
             $var = null;
             $i=null;
    $kategoriler = explode(",",$row_uyebilgileri['Kategori']);
    foreach ($kategoriler as $kategori) {

        if($row_almagoruntu['kategorialma']==$kategori) {
          
           $var = true;
           $i++;
              }else{
                      continue;
                      }

                      }

       if($var == true){  ?>
              
                    <table class="table table-bordered table-hover">
                       <thead1>
                         
                         <tr>
                       <th><?php echo $dil['alma_talep']; ?></th>
                         <th><?php echo $dil['alma_kategori']; ?></th>
                         <th><?php echo $dil['alma_firma']; ?></th>
                         <th><?php echo $dil['alma_ktanim']; ?></th>
                         <th><?php echo $dil['alma_forecast']; ?></th>
                         <th><?php echo $dil['alma_teslim']; ?>-<?php echo $dil['alma_tsure']; ?></th>
                         <th><?php echo $dil['alma_okosul']; ?>-<?php echo $dil['alma_ovade']; ?></th>
                         <th><?php echo $dil['alma_aciklama']; ?></th>
                         <th><?php echo $dil['alma_hfiyat']; ?></th>
                           
                           <th width="150" class="hidden-phone"><?php echo $dil['alma_spec']; ?></th>
                         </tr>
                      </thead1>
                       <tbody>
                  
                         
                         <tr class="odd gradeXs">
                           <td><?php echo $row_almagoruntu['TalepID']; ?></td>
                          <td>
 <?php if ($_SESSION["dil"] == 'tr' ) {
  ?> 
    
                  <?php echo $row_almagoruntu['KategoriAdi']; ?>
        
        
 <?php } elseif ($_SESSION["dil"] == 'en') {
   
   
   ?> 
                <?php echo $row_almagoruntu['Kategoriing']; ?>
<?php } elseif ($_SESSION["dil"] == 'chn' ) {
        
      ?> 
                     <?php echo $row_almagoruntu['Kategorichn']; ?>         
                         
                   <?php } ?>       
                         
                         </td>
                           <td><?php echo $row_almagoruntu['isteksirketadi']; ?></td>
                           <td><?php echo $row_almagoruntu['uruntanim']; ?></td>
                           <td><?php echo $row_almagoruntu['forecast']; ?>&nbsp;</td>
                           <td><?php echo $row_almagoruntu['teslimsekli']; ?>-<?php echo $row_almagoruntu['teslimsure']; ?></td>
                           <td><?php echo $row_almagoruntu['odemekosul']; ?>-<?php echo $row_almagoruntu['odemevadesi']; ?></td>
                           <td><?php echo $row_almagoruntu['aciklama']; ?></td>
                           <td><?php echo $row_almagoruntu['hedeffiyat']; ?>(&nbsp;<?php echo $row_almagoruntu['parabirim']; ?> )</td>
                           <td width="150"class="center hidden-phone"><a href="../../resim/spek/<?php echo $row_almagoruntu['spek']; ?>"><?php echo $dil['alma_spec']; ?></a>
                             
                             
                           </td>
                         </tr>
                       
                      </tbody>
                   </table>
                   
 </div>
             </div>
             
<div class="col-md-12">
          
   <form action="<?php echo $editFormAction; ?>" name="form1" method="POST" enctype="multipart/form-data"  onSubmit="return check_frmm()">              
           
                           <div class="form-group">
                              <label class="control-label"><?php echo $dil['alma_okosul']; ?></label>
                                 <select name="toplaodemekosul" class="form-control col-md-3">
                                    <option value="T/T"<?php  if ($row_topladuzenle['toplaodemekosul'] == 'T/T') {?><?php echo ' selected="selected"'; }?>>T/T-<?php echo $dil['T/T']; ?>
                                    <option value="L/C"<?php  if ($row_topladuzenle['toplaodemekosul'] == 'L/C') {?><?php echo ' selected="selected"'; }?>>L/C-<?php echo $dil['L/C']; ?>
                                    <option value="CAD"<?php  if ($row_topladuzenle['toplaodemekosul'] == 'CAD') {?><?php echo ' selected="selected"'; }?>>CAD-<?php echo $dil['CAD']; ?>
                                    <option value="CAG"<?php  if ($row_topladuzenle['toplaodemekosul'] == 'CAG') {?><?php echo ' selected="selected"'; }?>>CAG-<?php echo $dil['CAG']; ?>
                                 </select>

                           </div>
                        
                           <div class="form-group">
                                     <label class="control-label"><?php echo $dil['alma_ovade']; ?></label>
                                     <input name="toplaodemevadesi" type="text" class="form-control form-control-sm col-md-3 " id="toplaodemevadesi" value="<?php echo $row_topladuzenle['toplaodemevadesi']; ?>"  onkeypress="SayiKontrol(event)" >
                           </div>
              
                           <div class="form-group">
                              <label class="control-label"><?php echo $dil['alma_teslim']; ?></label>
                                 <select name="toplateslimsekli" class="form-control col-md-3">
                                    <option value="CFR"<?php  if ($row_topladuzenle['toplateslimsekli'] == 'CFR') {?><?php echo ' selected="selected"'; }?>>CFR-<?php echo $dil['CFR']; ?>
                                    <option value="CIF"<?php  if ($row_topladuzenle['toplateslimsekli'] == 'CIF') {?><?php echo ' selected="selected"'; }?>>CIF-<?php echo $dil['CIF']; ?>
                                    <option value="CIP"<?php  if ($row_topladuzenle['toplateslimsekli'] == 'CIP') {?><?php echo ' selected="selected"'; }?>>CIP-<?php echo $dil['CIP']; ?>
                                    <option value="CPT"<?php  if ($row_topladuzenle['toplateslimsekli'] == 'CPT') {?><?php echo ' selected="selected"'; }?>>CPT-<?php echo $dil['CPT']; ?>
                                    <option value="DAF"<?php  if ($row_topladuzenle['toplateslimsekli'] == 'DAF') {?><?php echo ' selected="selected"'; }?>>DAF-<?php echo $dil['DAF']; ?>
                                    <option value="DES"<?php  if ($row_topladuzenle['toplateslimsekli'] == 'DES') {?><?php echo ' selected="selected"'; }?>>DES-<?php echo $dil['DES']; ?>
                                    <option value="DDP"<?php  if ($row_topladuzenle['toplateslimsekli'] == 'DDP') {?><?php echo ' selected="selected"'; }?>>DDP-<?php echo $dil['DDP']; ?>
                                    <option value="EXW"<?php  if ($row_topladuzenle['toplateslimsekli'] == 'EXW') {?><?php echo ' selected="selected"'; }?>>EXW-<?php echo $dil['EXW']; ?>
                                    <option value="FAS"<?php  if ($row_topladuzenle['toplateslimsekli'] == 'FAS') {?><?php echo ' selected="selected"'; }?>>FAS-<?php echo $dil['FAS']; ?>
                                    <option value="FCA"<?php  if ($row_topladuzenle['toplateslimsekli'] == 'FCA') {?><?php echo ' selected="selected"'; }?>>FCA-<?php echo $dil['FCA']; ?>
                                    <option value="FOB"<?php  if ($row_topladuzenle['toplateslimsekli'] == 'FOB') {?><?php echo ' selected="selected"'; }?>>FOB-<?php echo $dil['FOB']; ?>
                                    <option value="DEQ"<?php  if ($row_topladuzenle['toplateslimsekli'] == 'DEQ') {?><?php echo ' selected="selected"'; }?>>DEQ-<?php echo $dil['DEQ']; ?>
                                    <option value="DDU"<?php  if ($row_topladuzenle['toplateslimsekli'] == 'DDU') {?><?php echo ' selected="selected"'; }?>>DDU-<?php echo $dil['DDU']; ?>
                                 </select>

                           </div>
              
              
              
              
                           <div class="form-group">
                                  <label class="control-label"><?php echo $dil['alma_tsure']; ?></label>
                                  <input name="toplateslimsure" type="text" class="form-control form-control-sm col-md-3 " id="toplateslimsure" value="<?php echo $row_topladuzenle['toplateslimsure']; ?>" onkeypress="SayiKontrol(event)" >
                           </div>


                      
                           <div class="form-group">
                                  <label class="control-label"><?php echo $dil['alma_teklif']; ?>(<?php echo $row_almagoruntu['parabirim']; ?> )</label>
                                  <input name="toplafiyat" type="text" class="form-control form-control-sm col-md-3 " id="toplafiyat" value="<?php echo $row_topladuzenle['toplafiyat']; ?>"  onkeypress="SayiKontrol(event)" >
                           </div>

                           <div class="form-group">
                                      <label class="control-label"><?php echo $dil['alma_adres']; ?></label>
                                      <textarea name="toplaadres" rows="6" readonly class="form-control form-control-sm col-md-3 " id="toplaadres"><?php echo $row_uyebilgileri['Adres']; ?></textarea>
                           </div>

                                              
                          
                      <span class="controls">
                      </span>
                              <input name="toplaTalepID" type="hidden" id="toplaTalepID" value="<?php echo $row_almagoruntu['TalepID']; ?>">
                              <input type="hidden" name="toplasirketadi" id="toplasirketAdi" value="<?php echo $row_uyebilgileri['sirketAdi']; ?>">
                              <input type="hidden" name="toplauyeID" id="toplauyeID" value="<?php echo $row_uyebilgileri['uyeID']; ?>">
                              <input type="hidden" name="toplauyeadi" id="toplauyeadi" value="<?php echo $_SESSION['MM_Username']; ?>">
                              <input type="hidden" name="toplateklifsahibiID" id="toplateklifsahibiID" value="<?php echo $row_almagoruntu['istekuyeID']; ?>">
                              <button type="submit" class="btn btn-success"><?php echo $dil['kaydet']; ?></button>
                                              
                           </div>
                           </div>
                  
                   
                   <input type="hidden" name="MM_update" value="form1">
            
                     </form>       
                           
                   </div>
                 
                                      
                                      
                                      
<?php }else{ ?>
                                                     
                               <li>
                                   <p><?php echo $dil['alma_uyari'];?></p>
                              </li>


<?php }?>


                 </div>



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

<script src="../../dist/js/jquery-1.8.3.js"></script>
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->

<!-- FastClick -->
<script src="../../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../dist/js/demo.js"></script>
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
   <script language="JavaScript">
function check_frmm(){

if (document.form1.toplafiyat.value == ""){
alert ("<?php echo $dil['alma_teklif']; ?>:<?php echo $dil['eksik']; ?>");
document.form1.toplafiyat.focus();
return false; 
}

if (document.form1.toplaodemekosul.value == ""){
alert ("<?php echo $dil['verme_okosul']; ?>:<?php echo $dil['eksik']; ?>");
document.form1.toplaodemekosul.focus();
return false; 
}
if (document.form1.toplateslimsekli.value == ""){
alert ("<?php echo $dil['verme_teslimsekli']; ?>:<?php echo $dil['eksik']; ?>");
document.form1.toplateslimsekli.focus();
return false; 
}
if (document.form1.toplaadres.value == ""){
alert ("<?php echo $dil['verme_adres']; ?>:<?php echo $dil['eksik']; ?>");
document.form1.toplaadres.focus();
return false; 
}
if (document.form1.toplaodemevadesi.value == ""){
alert ("<?php echo $dil['verme_vade']; ?>:<?php echo $dil['eksik']; ?>");
document.form1.toplaodemevadesi.focus();
return false; 
}
if (document.form1.toplateslimsure.value == ""){
alert ("<?php echo $dil['alma_tsure']; ?>:<?php echo $dil['eksik']; ?>");
document.form1.toplateslimsure.focus();
return false; 
}


}
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


