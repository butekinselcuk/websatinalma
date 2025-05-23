<?php
require_once('../Connections/baglan.php'); // PDO bağlantısı yapılıyor
require_once('../fonksiyon.php');

session_start();

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

//mysql_select_db($database_baglan, $baglan);
//$query_teklifoku = "SELECT teklifiste.TalepID, teklifiste.aciklama, teklifiste.gondermetarih, teklifiste.teklifaktif FROM teklifiste WHERE teklifiste.teklifaktif = 0 ORDER BY teklifiste.teklifaktif ASC";
//$teklifoku = mysql_query($query_teklifoku, $baglan) or die(mysql_error());
//$row_teklifoku = mysql_fetch_assoc($teklifoku);
//$totalRows_teklifoku = mysql_num_rows($teklifoku);





// Site ayarlarını çekme
$stmt_ayar = $pdo->prepare("SELECT * FROM siteconfig");
$stmt_ayar->execute();
$row_ayar = $stmt_ayar->fetch(PDO::FETCH_ASSOC);

//$uyeID=$row_uyebilgileri['uyeID'];
//mysql_select_db($database_baglan, $baglan);
//$query_mesaj = "SELECT * FROM sohbet WHERE sohbet.kime=$uyeID AND sohbet.durum=0 ";
//$mesaj = mysql_query($query_mesaj, $baglan) or die(mysql_error());
//$row_mesaj = mysql_fetch_assoc($mesaj);
//$totalRows_mesaj = mysql_num_rows($mesaj);

?>
<?php

session_start();

// Dilin GET parametresi üzerinden alınması ve session'a kaydedilmesi
if (isset($_GET['dil'])) {
    $izinli_diller = ['tr', 'en', 'chn']; // İzin verilen diller
    $secili_dil = $_GET['dil'];

    // Güvenli bir şekilde dil seçimi yapılıyor
    if (in_array($secili_dil, $izinli_diller)) {
        $_SESSION['dil'] = $secili_dil;
		include '../admin/dil/'.$secili_dil.'.php';
    }
}

// Varsayılan dil ayarı
if (!isset($_SESSION['dil'])) {
    $_SESSION['dil'] = 'tr';  // Türkçe varsayılan dil olarak ayarlanır
}
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
<meta http-equiv="refresh" content="0; url=Dashboard/index.php" />  
<!--<meta http-equiv="refresh" content="0; url=Dashboard/index.php" />    -->

  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">
  <!-- Navbar -->
         <?php
  include("pages/kisayollar/ust.php");
          ?>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">

        <?php
  include("pages/kisayollar/logo.php");
        ?>


        <?php
  include("pages/kisayollar/solmenu.php");
        ?> 

  </aside>


  <div class="content-wrapper">

    <section class="content-header">

    </section>


     <?php
  include("pages/kisayollar/orta.php");
     ?>

  </div>


     <?php
  include("pages/kisayollar/alt.php");
     ?> 

  <aside class="control-sidebar control-sidebar-dark">

  </aside>

</div>

<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- FastClick -->
<script src="plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="pages/dist/js/demo.js"></script>
</body>
</html>
