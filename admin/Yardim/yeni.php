<?php
require_once('../../Connections/baglan.php');
require_once('../../fonksiyon.php');

session_start();

$MM_authorizedUsers = "1,2";
$MM_donotCheckaccess = "false";

function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) {
    $isValid = false;
    if (!empty($UserName)) {
        $arrUsers = explode(",", $strUsers);
        $arrGroups = explode(",", $strGroups);
        if (in_array($UserName, $arrUsers) || in_array($UserGroup, $arrGroups) || $strUsers == "") {
            $isValid = true;
        }
    }
    return $isValid;
}

$MM_restrictGoTo = "../../login.php";
if (!(isset($_SESSION['MM_Username']) && isAuthorized("", $MM_authorizedUsers, isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : '', isset($_SESSION['MM_UserGroup']) ? $_SESSION['MM_UserGroup'] : ''))) {
    $MM_qsChar = strpos($MM_restrictGoTo, "?") ? "&" : "?";
    $MM_referrer = $_SERVER['PHP_SELF'] . (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0 ? "?" . $_SERVER['QUERY_STRING'] : "");
    header("Location: " . $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer));
    exit;
}

$editFormAction = $_SERVER['PHP_SELF'] . (isset($_SERVER['QUERY_STRING']) ? "?" . htmlentities($_SERVER['QUERY_STRING']) : "");

if (isset($_POST["MM_insert"]) && $_POST["MM_insert"] == "form1") {
    $sql = "INSERT INTO destek (kimdenadi, seviyeID, durum, departman, oncelik, mesaj, dosya, mail, ID, kimden, kime, baslik) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['kimdenadi'],
        $_POST['seviyeID'],
        $_POST['durum'],
        $_POST['departman'],
        $_POST['oncelik'],
        $_POST['mesaj'],
        $_POST['dosya'],
        $_POST['mail'],
        $_POST['ID'],
        $_POST['kimden'],
        $_POST['kime'],
        $_POST['baslik']
    ]);

    $insertGoTo = "index.php" . (isset($_SERVER['QUERY_STRING']) ? (strpos($insertGoTo, '?') ? "&" : "?") . $_SERVER['QUERY_STRING'] : "");
    header("Location: $insertGoTo");
}

$colname_uyebilgileri = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";
$stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeAdi = ?");
$stmt->execute([$colname_uyebilgileri]);
$row_uyebilgileri = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_uyebilgileri = $stmt->rowCount();

$stmt = $pdo->query("SELECT * FROM siteconfig");
$row_ayar = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_ayar = $stmt->rowCount();

$uyeID = isset($row_uyebilgileri['uyeID']) ? $row_uyebilgileri['uyeID'] : null;

$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE kime = ? AND durum = 0");
$stmt->execute([$uyeID]);
$row_mesaj = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_mesaj = $stmt->rowCount();

$query = "SELECT teklifiste.TalepID, teklifiste.aciklama, teklifiste.gondermetarih, teklifiste.teklifaktif FROM teklifiste WHERE teklifiste.teklifaktif = 0 ORDER BY teklifiste.teklifaktif ASC";
$stmt = $pdo->query($query);
$row_teklifoku = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_teklifoku = $stmt->rowCount();


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
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../plugins/summernote/summernote-bs4.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

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
          <div class="card card-primary card-outline">
          <h3 class="card-title"><?php echo $dil['yardim_merkezi']; ?></h3>
 </div>

        </div>
        <div class="card-body">
          
  <!-- KODLARRRRRRRRRR -->
          <div class="card-body">

                <div class="form-group">

                                  <form action="<?php echo $editFormAction; ?>" name="form1" method="POST" enctype="multipart/form-data" >        
        

        <div class="row">
        <div class="col-md-6">              

                <div class="form-group">
                                 <label class="col-md-6"><?php echo $dil['kayit_hesap_yonetici']; ?></label>
                                 <input type="text" class="form-control select2 col-md-6" name="kimdenadi" id="kimdenadi" value="<?php echo $row_uyebilgileri['HesapYoneticisiTitle']; ?>">
                </div>



                <div class="form-group">
                                 <label class="col-md-6"><?php echo $dil['kayit_email']; ?></label>
                                 <input type="text" name="mail" id="mail" class="form-control select2 col-md-6" value="<?php echo $row_uyebilgileri['HesapYoneticisiMail']; ?>">
                </div>


                <div class="form-group"> 
                                 <label class="col-md-3"><?php echo $dil['departman']; ?></label>
                                       <select name="departman"  id="departman" class="form-control select2 col-md-6">
                                           <option value="1"><?php echo $dil['destek']; ?>
                                           <option value="2"><?php echo $dil['obildirim']; ?>
                                           <option value="3"><?php echo $dil['diger']; ?>
                                       </select>  
                </div>


                <div class="form-group"> 
                                 <label class="col-md-3"><?php echo $dil['oncelik']; ?></label>
                                       <select name="oncelik"  id="oncelik" class="form-control select2 col-md-6">
                                           <option value="1"><?php echo $dil['yuksek']; ?>
                                           <option value="2"><?php echo $dil['orta']; ?>
                                           <option value="3"><?php echo $dil['dusuk']; ?>
                                       </select>
                </div>


           
                <div class="form-group"> 
                                    <label class="col-md-6"><?php echo $dil['dyukle']; ?></label>
                                    <input type="file" class="form-control select2 col-md-6" name="dosya" id="dosya">
                </div>


                <div class="form-group">
                                 <label class="col-md-6"><?php echo $dil['baslik']; ?></label>
                                 <input type="text" name="baslik" id="baslik" class="form-control select2 col-md-6">            
                </div>


                <div class="form-group">  
                                 <label class="col-md-6"><?php echo $dil['mesaj']; ?></label>
                                 <textarea class="form-control" id="comment-md" name="mesaj" placeholder="........."></textarea>
                </div>

   
 </div>  </div> 

                <?php 
           $kimden=$row_uyebilgileri['uyeID'];
           function uniqerand($len = 7) { 
    $word = array_merge( range('0', '9')); 
    shuffle($word); 
    return substr(implode($word), 0, $len); 
} 

$ID=$kimden.uniqerand();  
            ?>
                
                
                
                         <input type="hidden" name="ID" id="ID" value="<?php echo $ID; ?>"> 
                         <input type="hidden" name="kimden" id="kimden" value="<?php echo $kimden; ?>"> 
                         <input type="hidden" name="seviyeID" id="seviyeID" value="1">  
                         <input type="hidden" name="kime" id="kime" value="1">  
                         <input type="hidden" name="durum" id="durum" value="0">  
    
                <div class="form-group">  
                          <button type="submit" class="btn btn-success"><?php echo $dil['gonder']; ?></button>
                </div>    

                          <input type="hidden" name="MM_insert" value="form1">
                          </form>   


            </div>



        </div>
    </div>


  <!-- KODLARRRRRRRRRR -->

        </div>


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
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- Summernote -->
<script src="../plugins/summernote/summernote-bs4.min.js"></script>
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
