<?php
require_once('../../../Connections/baglan.php');
require_once('../../../fonksiyon.php');
$tarih = date("Y-m-d");
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




// Handling form submission
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
    $sql = "INSERT INTO tekliftopla (toplaTalepID, toplateklifsahibiID, toplaodemekosul, toplateslimsekli, toplaadres, toplaaciklama, toplaodemevadesi, toplateslimsure, toplaUyeID, toplasirketAdi, toplafiyat, toplakategoriID, toplauyeadi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['toplaTalepID'], 
        $_POST['toplateklifsahibiID'], 
        $_POST['toplaodemekosul'], 
        $_POST['toplateslimsekli'], 
        $_POST['toplaadres'], 
        $_POST['toplaaciklama'], 
        $_POST['toplaodemevadesi'], 
        $_POST['toplateslimsure'], 
        $_POST['toplauyeID'], 
        $_POST['toplasirketAdi'], 
        $_POST['toplafiyat'], 
        $_POST['toplakategoriID'], 
        $_POST['toplauyeadi']
    ]);
    header("Location: index.php");
}

// Fetching user information
$colname_uyebilgileri = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";
$stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeAdi = ?");
$stmt->execute([$colname_uyebilgileri]);
$row_uyebilgileri = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetching specific view information
$talepID = $_GET['TalepID'] ;  // Örnek ID


// PDO sorgusu için SQL metnini ve parametreleri hazırlayın.
$sql = "SELECT DISTINCT 
            uyeler.*, 
            teklifiste.*, 
            kategori.*
        FROM 
            uyeler,
            teklifiste
        INNER JOIN 
            kategori ON kategori.KategoriID = teklifiste.kategorialma
        WHERE 
            FIND_IN_SET(teklifiste.kategorialma, uyeler.Kategori) 
            AND ? < teklifiste.tbittarih 
            AND TalepID = ? 
        ORDER BY 
            TalepID ASC";

// Sorguyu hazırlayın ve parametreleri ile çalıştırın.
$stmt = $pdo->prepare($sql);
$stmt->execute([$tarih, $talepID]);

// Sonuçları al
$row_almagoruntu = $stmt->fetch(PDO::FETCH_ASSOC);



// Fetching configuration
$stmt = $pdo->query("SELECT * FROM siteconfig");
$row_ayar = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetching messages for the user
$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE kime = ? AND durum = 0");
$stmt->execute([$row_uyebilgileri['uyeID']]);
$row_mesaj = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_mesaj = $stmt->rowCount();  // Count rows

// Prepare a statement for execution and returns a statement object
$stmt = $pdo->prepare("SELECT uyeID, bastarih, bittarih FROM uyeler WHERE uyeAdi = :uyeAdi");

// Execute the statement with bound input parameters
$stmt->execute(['uyeAdi' => $colname_uyebilgileri]);

// Fetch the row
$row_hesap = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if we got any row
$totalRows_hesap = $stmt->rowCount(); // Get the number of rows affected


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
          <h3 class="card-title"> <?php echo $dil['teklif_verme'];?></h3>


        
        <div class="card-body">
          
  <!-- KODLARRRRRRRRRR -->
   
         <div class="row">
              <div class="col-md-12">
           
       <?php              
          $baslangic = strtotime($tarih);
                $bitis = strtotime($row_hesap['bittarih']);
                $fark= abs($bitis- $baslangic);
                $sonuc=($bitis-$baslangic)/86400 ;
                $tarih = date("Y/m/d");
   ?>             
           
                 
<?php  if($tarih==$row_hesap['bittarih']) :?>



 <?php echo $dil['alma_uyari'];?> <a href="../../../satinal.php"><?php echo $dil['satinal'];?></a>
<?php else :?>
                            
     
           
                <div class="row">
                  <div class="col-lg-12">
               <div class="widget">

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
                         <th><?php echo $dil['alma_teslim']; ?>(<?php echo $dil['teslimyeri']; ?>)-<?php echo $dil['alma_tsure']; ?></th>
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
                           <td><?php echo $row_almagoruntu['teslimsekli']; ?>(<?php echo $row_almagoruntu['teslimyeri']; ?>)-<?php echo $row_almagoruntu['teslimsure']; ?></td>
                           <td><?php echo $row_almagoruntu['odemekosul']; ?>-<?php echo $row_almagoruntu['odemevadesi']; ?></td>
                           <td><?php echo $row_almagoruntu['aciklama']; ?></td>
                           <td><?php echo $row_almagoruntu['hedeffiyat']; ?>(&nbsp;<?php echo $row_almagoruntu['parabirim']; ?> )</td>
                           <td width="150" class="center hidden-phone"><a href="../../resim/spek/<?php echo $row_almagoruntu['spek']; ?>"><?php echo $dil['alma_spec']; ?></a>
                             
                             
                           </td>
                         </tr>
                       
                      </tbody>
                   </table>
                        </div>

                    </div>
 
                   
               
                <div class="row">
                  <div class="col-lg-12">
               <div class="widget">

                 <div class="widget-body">
          
         
          
   <form action="<?php echo $editFormAction; ?>" name="form1" method="POST" enctype="multipart/form-data"  onSubmit="return check_frmm()">              

                  <div class="form-group">
                              <label class="control-label"><?php echo $dil['alma_okosul']; ?></label>
                                 <select name="toplaodemekosul" class="form-control col-md-3" id="toplaodemekosul" tabindex="1" data-placeholder="Choose a Category" >
                                 <option value="">
                                    <option value="T/T">T/T-<?php echo $dil['T/T']; ?>
                                    <option value="L/C">L/C-<?php echo $dil['L/C']; ?>
                                    <option value="CAD">CAD-<?php echo $dil['CAD']; ?>
                                    <option value="CAG">CAG-<?php echo $dil['CAG']; ?>
                                 </select>
                              </div>
                  <div class="form-group">
                              <label class="control-label"><?php echo $dil['alma_ovade']; ?></label>
                                <input name="toplaodemevadesi" type="text" class="form-control col-md-3" id="toplaodemevadesi" onkeypress="SayiKontrol(event)" >
                  </div>
              
                  <div class="form-group">
                              <label class="control-label"><?php echo $dil['alma_teslim']; ?></label>
                                <select name="toplateslimsekli" class="form-control col-md-3" id="toplateslimsekli" tabindex="1" data-placeholder="Choose a Category" >
                                  <option value="">
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
                                <label class="control-label"><?php echo $dil['teslimyeri']; ?></label>
                                <input name="toplaaciklama" type="text" class="form-control col-md-3" id="toplaaciklama">

                  </div>
                  <div class="form-group">
                                <label class="control-label"><?php echo $dil['alma_tsure']; ?></label>
                                <input name="toplateslimsure" type="text" class="form-control col-md-3" id="toplateslimsure" onkeypress="SayiKontrol(event)" >
                  </div>

 
                  <div class="form-group">
                                  <label class="control-label"><?php echo $dil['alma_teklif']; ?>(<?php echo $row_almagoruntu['parabirim']; ?> )</label>
                                  <input name="toplafiyat" type="text" class="form-control col-md-3" id="toplafiyat" onkeypress="SayiKontrol(event)" >

                  </div>
                  <div class="form-group">
                                 <label class="control-label"><?php echo $dil['alma_adres']; ?></label>

                                 <textarea name="toplaadres" rows="6" readonly class="form-control col-md-3" id="toplaadres"><?php echo $row_uyebilgileri['Adres']; ?></textarea>
                  </div>
                                              
                          
                      <span class="controls">

                      </span>
                      <input name="toplaTalepID" type="hidden" id="toplaTalepID" value="<?php echo $row_almagoruntu['TalepID']; ?>">
                      <input type="hidden" name="toplasirketAdi" id="toplasirketAdi" value="<?php echo $row_uyebilgileri['sirketAdi']; ?>">
                      <input type="hidden" name="toplauyeID" id="toplauyeID" value="<?php echo $row_uyebilgileri['uyeID']; ?>">
                      <input type="hidden" name="toplauyeadi" id="toplauyeadi" value="<?php echo isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : ''; ?>">
                      <input type="hidden" name="toplateklifsahibiID" id="toplateklifsahibiID" value="<?php echo $row_almagoruntu['istekuyeID']; ?>">
                      <input type="hidden" name="toplakategoriID" id="toplakategoriID" value="<?php echo $row_almagoruntu['KategoriID']; ?>">
                      
                                              
          
                              <button type="submit" class="btn btn-success"><?php echo $dil['kaydet']; ?></button>
                
                   <input type="hidden" name="MM_insert" value="form1">
</div>
        
                                   
                     </form>       
                           
                   </div>    
                   
          <?php }else{ ?>
                                                     
                               <li>
                                   <p><?php echo $dil['alma_uyari'];?></p>
                              </li>


<?php }?>
                           <?php endif ;  ?>
                           
               
     
<p></p></div>
                 </div>
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


