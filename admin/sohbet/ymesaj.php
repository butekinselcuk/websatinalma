<?php
require_once('../../Connections/baglan.php'); // Ensure this points to the correct path where PDO connection is set up
require_once('../../fonksiyon.php');

session_start();

$MM_authorizedUsers = "1,2";
$MM_donotCheckaccess = "false";

function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) {
    $arrUsers = explode(",", $strUsers);
    $arrGroups = explode(",", $strGroups);
    $isValid = false;
    if (!empty($UserName)) {
        if (in_array($UserName, $arrUsers) || in_array($UserGroup, $arrGroups) || $strUsers == "") {
            $isValid = true;
        }
    }
    return $isValid;
}

if (!(isset($_SESSION['MM_Username']) && isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup']))) {
    $redirectLogin = "../../login.php";
    $queryString = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    header("Location: $redirectLogin?accesscheck=" . urlencode($_SERVER['PHP_SELF'] . $queryString));
    exit;
}

$colname_uyebilgileri = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";

$stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeAdi = :uyeAdi");
$stmt->execute(['uyeAdi' => $colname_uyebilgileri]);
$row_uyebilgileri = $stmt->fetch();
$totalRows_uyebilgileri = $stmt->rowCount();

$stmt = $pdo->query("SELECT * FROM siteconfig");
$row_ayar = $stmt->fetch();
$totalRows_ayar = $stmt->rowCount();

$uyeID = $row_uyebilgileri['uyeID'];

$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE kime = :kime AND durum = 0");
$stmt->execute(['kime' => $uyeID]);
$row_mesaj = $stmt->fetchAll();
$totalRows_mesaj = $stmt->rowCount();

$stmt = $pdo->query("SELECT * FROM teklifiste WHERE teklifaktif = 0 ORDER BY teklifaktif ASC");
$row_teklifoku = $stmt->fetchAll();
$totalRows_teklifoku = $stmt->rowCount();

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}



if (isset($_POST["MM_insert"]) && $_POST["MM_insert"] == "form1") {
    $stmt = $pdo->prepare("INSERT INTO sohbet (sohbetTalepID, kime, mesaj, kimden, durum, ID) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['sohbetTalepID'],
        $_POST['kime'],
        $_POST['mesaj'],
        $_POST['kimden'],
        $_POST['durum'],
        $_POST['ID']
    ]);
    $insertGoTo = "index.php";
    $queryString = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    header("Location: $insertGoTo$queryString");
}



$query_mail = "SELECT sirketAdi, uyeID FROM uyeler";
$stmt_mail = $pdo->prepare($query_mail);
$stmt_mail->execute();
$row_mail = $stmt_mail->fetchAll(PDO::FETCH_ASSOC);
$totalRows_mail = $stmt_mail->rowCount();

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
include '../dil/'.$dil.'.php';
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
          <h3 class="card-title"></i> <?php echo $dil['mana']; ?> </h3>


        
        <div class="card-body">
          
  <!-- KODLARRRRRRRRRR -->
  


          <div class="widget-body form">

<?php
// Fetching users for the select dropdown
$stmt = $pdo->query("SELECT uyeID, sirketAdi FROM uyeler");
$users = $stmt->fetchAll();

?>

<div class="control-group">
    <form action="<?php echo htmlspecialchars($editFormAction); ?>" name="form1" method="POST" enctype="multipart/form-data">    
        <label class="control-label"><?php echo $dil['mesajsec']; ?></label>
        <select name="kime" onchange="showUser(this.value)" class="form-control col-md-3">
            <?php foreach ($users as $row_mail): ?>
                <option value="<?php echo $row_mail['uyeID']; ?>"><?php echo $row_mail['sirketAdi']; ?></option>
            <?php endforeach; ?>
        </select>

        <div class="control-group">
            <label class="control-label"  ><?php echo $dil['mkonu']; ?></label>

			<input type="text" name="konu" id="konu" class="form-control col-md-3">

            <input type="hidden" name="sohbetTalepID" id="sohbetTalepID" class="form-control col-md-3">
			
			                                                         <?php 
                                                 $kimden=$row_uyebilgileri['uyeID'];
                                                 function uniqerand($len = 7) { 
                                          $word = array_merge( range('0', '9')); 
                                          shuffle($word); 
                                          return substr(implode($word), 0, $len); 
                                      } 
                                      $ID=$kimden.uniqerand();  
                                                  ?>
        </div>


</div>

                       <div class="control-group">
                                <label class="control-label"><?php echo $dil['mesaj']; ?></label>
                                <textarea  id="comment-md" name="mesaj" rows="3" class="form-control form-control-sm col-md-3"></textarea>

                                <input type="hidden" name="kimden" id="kimden" value="<?php echo $kimden; ?>">
                                <input type="hidden" name="durum" id="durum" value="0">   
                                <input type="hidden" name="ID" id="ID" value="<?php echo $ID; ?>">
           
                
                                <button type="submit" class="btn btn-success"><?php echo $dil['gonder']; ?></button>
                                <input type="hidden" name="MM_insert" value="form1">
                      </form>           
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
 

<!-- ie8 fixes -->
<!--[if lt IE 9]>

  <![endif]-->
<!-- END JAVASCRIPTS -->
<!-- InstanceEndEditable -->
<script>
  $(function () {
    // Summernote
    $('.textarea').summernote()
  })
</script>

</body>
</html>


