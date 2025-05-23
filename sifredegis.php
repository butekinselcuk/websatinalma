<?php
require_once('Connections/baglan.php');
require_once('fonksiyon.php');
error_reporting(0);

if (!isset($_SESSION)) {
  session_start();
}

$MM_authorizedUsers = "1,2";
$MM_donotCheckaccess = "false";

function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) {
  $isValid = False;
  if (!empty($UserName)) {
    $arrUsers = explode(",", $strUsers);
    $arrGroups = explode(",", $strGroups);
    if (in_array($UserName, $arrUsers) || in_array($UserGroup, $arrGroups)) {
      $isValid = true;
    }
  }
  return $isValid;
}

$MM_restrictGoTo = "login.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {
  $MM_qsChar = strpos($MM_restrictGoTo, "?") ? "&" : "?";
  $MM_referrer = $_SERVER['PHP_SELF'] . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
  header("Location: ". $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer));
  exit;
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "sifre")) {
  if (strlen($_POST['Parola']) > 5) {
    $hashedPassword = password_hash($_POST['Parola'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE uyeler SET Parola = ? WHERE uyeID = ?");
    $stmt->execute([$hashedPassword, $_POST['uyeID']]);
  }
}

$stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeAdi = ?");
$stmt->execute([$_SESSION['MM_Username']]);
$row_uyebilgileri = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_uyebilgileri = $stmt->rowCount();

$stmt = $pdo->query("SELECT teklifiste.TalepID, teklifiste.aciklama, teklifiste.gondermetarih, teklifiste.teklifaktif FROM teklifiste WHERE teklifiste.teklifaktif = 0 ORDER BY teklifiste.teklifaktif ASC");
$row_teklifoku = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_teklifoku = count($row_teklifoku);

$stmt = $pdo->query("SELECT * FROM siteconfig");
$row_ayar = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_ayar = $stmt->rowCount();

$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE kime = ? AND durum = 0");
$stmt->execute([$row_uyebilgileri['uyeID']]);
$row_mesaj = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_mesaj = count($row_mesaj);
?>


<?php


# Session başlat

# Dil seçimi yapılmışsa

$dil = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
if(@$_GET['dil']) {
   # Dil seçimini session'a ata.
   $_SESSION['dil'] = mysql_real_escape_string($_GET['dil']);
   # Anasayfa'ya yönlendir.
}
# Seçili dili kontrol ediyoruz
if (@$_SESSION['dil'] == 'tr') {
   $dil = 'tr';
}
elseif (@$_SESSION['dil'] == 'en') {
   $dil = 'en';
}

elseif (@$_SESSION['dil'] == 'chn') {
   $dil = 'chn';
}

else {
   # Eğer dil seçilmemişse tarayıcı dilini varsayılan dil olarak seçiyoruz
  $_SESSION['dil'] = $dil;
}
# Dil dosyamızı include ediyoruz
include 'admin/dil/'.$dil.'.php';
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
  <link rel="stylesheet" href="admin/plugins/daterangepicker/daterangepicker.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="admin/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="admin/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="admin/plugins/select2/css/select2.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="admin/dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
   <style>
 .kullanmadahaiyi,.idareeder,.tamamdir,.azkarakter{
 display:inline-block;
 border:1px solid #000;
 min-width:120px;
 min-height:20px;
 line-height:20px;
 font-size:13px;
 font-weight:bold;
 padding-left:5px;
 }
 .kullanmadahaiyi{
 color:#FFF;
 background:#FF0000;
 }
 .tamamdir{
 color:#FFF;
 background:green;
 }
 .idareeder{
 background: yellow;
 }
 .azkarakter{
 color:#FFF;
 background:#000;
 }
 </style>
</head>
<body class="hold-transition sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">
  <!-- Navbar -->
         <?php

include __DIR__  . "/admin/pages/kisayollar/ust.php";

          ?>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">

        <?php

include __DIR__  . "/admin/pages/kisayollar/logo.php";
        ?>


        <?php
include __DIR__  . "/admin/pages/kisayollar/solmenu.php";
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
          <h3 class="card-title"></i><?php echo $dil['ksifredegis']; ?></h3>


        </div>
        <div class="card-body">
          
  <!-- KODLARRRRRRRRRR -->

                  <form action="<?php echo $editFormAction; ?>" method="POST"  name="sifre"  id="sifre">  
  <fieldset>


    <label><?php echo $dil['kayit_sifre']; ?></label>
    <input type="password" name="Parola" class="form-control col-md-3" id="Parola" value="" size="32" />
    <label><?php echo $dil['tekrarkayit_sifre']; ?></label>
    <input type="password" name="password-check" class="form-control col-md-3" id="password-check" value="" size="32" />
    <input name="uyeID" type="hidden" class="span4 " id="uyeID" value="<?php echo $row_uyebilgileri['uyeID']; ?>">
    <span id="guclu_mu"></span>
    <input type="hidden" name="MM_update" value="sifre">
                    <div class="card-footer">
       
               
    <input type="submit" class="btn btn-primary" value="<?php echo $dil['duzenle']; ?>" id="submit">
     </div>
    
  </fieldset>
</form>


  <!-- KODLARRRRRRRRRR -->

        </div>
        <!-- /.card-body -->

      </div>


    </section>


  <!-- ORTA ALAN -->
  </div>


     <?php
include __DIR__  . "/admin/pages/kisayollar/alt.php";
     ?> 

  <aside class="control-sidebar control-sidebar-dark">

  </aside>

</div>

<script src="admin/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="admin/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Select2 -->
<script src="admin/plugins/select2/js/select2.full.min.js"></script>
<!-- InputMask -->
<script src="admin/plugins/inputmask/jquery.inputmask.bundle.js"></script>
<script src="admin/plugins/moment/moment.min.js"></script>
<!-- date-range-picker -->
<script src="admin/plugins/daterangepicker/daterangepicker.js"></script>
<!-- bootstrap color picker -->
<script src="admin/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="admin/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- FastClick -->
<script src="admin/plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="admin/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="admin/dist/js/demo.js"></script>
<!-- Page script -->
<script type="text/javascript">

jQuery(function(){
        $("#submit").click(function(){
        $(".error").hide();
        var hasError = false;
        var passwordVal = $("#Parola").val();
        var checkVal = $("#password-check").val();
        if (passwordVal == '') {
            $("#Parola").after('<span class="error"><?php echo $dil['sifrehata2']; ?></span>');
            hasError = true;
        } else if (checkVal == '') {
            $("#password-check").after('<span class="error"><?php echo $dil['sifrehata1']; ?></span>');
            hasError = true;
        } else if (passwordVal != checkVal ) {
            $("#password-check").after('<span class="error"><?php echo $dil['sifreeslesmedi']; ?></span>');
            hasError = true;
        }
        if(hasError == true) {return false;}
    });
});

</script>
 <script>
 jQuery(document).ready(function($) {
 $('#password-check').keyup(function(e) {
 //Karakter Sayısı Tanımlamaları
 var guclukarakter = 8; //Güçlü saymak için gerekli sayı
 var ortakarakter = 7; //Orta güçlü saymak için gerekli sayı
 var gecerkarakter = 6; //En az gereki sayı
 //Regex ile kontrol fonksiyonları
 var strongRegex = new RegExp("^(?=.{"+guclukarakter+",})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*\\W).*$", "g");
 var mediumRegex = new RegExp("^(?=.{"+ortakarakter+",})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$", "g");
 var enoughRegex = new RegExp("(?=.{"+gecerkarakter+",}).*", "g");
 //Test başlasın... :)
 if (false == enoughRegex.test($(this).val())) {
      $('#guclu_mu').removeClass();
      $('#guclu_mu').addClass('azkarakter');
      $('#guclu_mu').html('Minimum '+gecerkarakter+' character!');

 } else if (strongRegex.test($(this).val())) {
      $('#guclu_mu').removeClass();
      $('#guclu_mu').addClass('tamamdir');
      $('#guclu_mu').html('Strong!');
 } else if (mediumRegex.test($(this).val())) {
      $('#guclu_mu').removeClass();
      $('#guclu_mu').addClass('idareeder');
      $('#guclu_mu').html('Medium!');
 } else {
      $('#guclu_mu').removeClass();
      $('#guclu_mu').addClass('Kötü');
      $('#guclu_mu').html('Weak!');
 }
 
       
 return true;
 });
 });
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


