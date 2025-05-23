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
include 'dil/'.$dil.'.php';
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
</head>
<body class="hold-transition sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">
  <!-- Navbar -->
         <?php
  include("../kisayollar/ust.php");
          ?>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">

        <?php
  include("../kisayollar/logo.php");
        ?>


        <?php
  include("../kisayollar/solmenu.php");
        ?> 

  </aside>


  <div class="content-wrapper">

    <section class="content-header">

    </section>


     <?php
  include("../kisayollar/orta.php");
     ?>

  </div>


     <?php
  include("../kisayollar/alt.php");
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
</body>
</html>
