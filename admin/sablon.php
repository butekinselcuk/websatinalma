<?php
require_once('../Connections/baglan.php');
require_once('../fonksiyon.php');

if (!isset($_SESSION)) {
    session_start();
}
$MM_authorizedUsers = "1,2";
$MM_donotCheckaccess = "false";

function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) {
    $arrUsers = explode(",", $strUsers);
    $arrGroups = explode(",", $strGroups);
    return in_array($UserName, $arrUsers) || in_array($UserGroup, $arrGroups);
}

if (!isAuthorized("", $MM_authorizedUsers, isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : '', isset($_SESSION['MM_UserGroup']) ? $_SESSION['MM_UserGroup'] : '')) {
    $query_string = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    header("Location: login.php?accesscheck=" . urlencode($_SERVER['PHP_SELF'] . $query_string));
    exit;
}

// Kategorileri çekme
$stmt = $pdo->query("SELECT * FROM kategori ORDER BY KategoriID DESC");
$kategori = $stmt->fetchAll(PDO::FETCH_ASSOC);





// Mail sorgusu
$q = $pdo->quote(isset($_GET['q']) ? $_GET['q'] : '');  // SQL Injection koruması
$stmt = $pdo->query("SELECT sirketAdi, HesapYoneticisiMail, Kategori FROM uyeler WHERE FIND_IN_SET($q, Kategori)");
$mails = $stmt->fetchAll(PDO::FETCH_ASSOC);




?>



<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<meta content="<?php echo $row_ayar['Metadesc']; ?>" name="description">
<meta content="<?php echo $row_ayar['MetaName']; ?>" name="keywords">
<meta content="pixel-industry" name="author">
<link href="assets/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="assets/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
<link href="assets/bootstrap/css/bootstrap-fileupload.css" rel="stylesheet">
<link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
<link href="assets/fancybox/source/jquery.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="assets/gritter/css/jquery.css">
<link rel="stylesheet" type="text/css" href="assets/uniform/css/uniform.css">
<link rel="stylesheet" type="text/css" href="assets/chosen-bootstrap/chosen/chosen.css">
<link rel="stylesheet" type="text/css" href="assets/jquery-tags-input/jquery.css">
<link rel="stylesheet" type="text/css" href="assets/clockface/css/clockface.css">
<link rel="stylesheet" type="text/css" href="assets/bootstrap-wysihtml5/bootstrap-wysihtml5.css">
<link rel="stylesheet" type="text/css" href="assets/bootstrap-datepicker/css/datepicker.css">
<link rel="stylesheet" type="text/css" href="assets/bootstrap-timepicker/compiled/timepicker.css">
<link rel="stylesheet" type="text/css" href="assets/bootstrap-colorpicker/css/colorpicker.css">
<link rel="stylesheet" href="assets/bootstrap-toggle-buttons/static/stylesheets/bootstrap-toggle-buttons.css">
<link rel="stylesheet" href="assets/data-tables/DT_bootstrap.css">
<link rel="stylesheet" type="text/css" href="assets/bootstrap-daterangepicker/daterangepicker.css">
<link href="css/style.css" rel="stylesheet">
<link href="css/style_responsive.css" rel="stylesheet">
<link href="css/style_default.css" rel="stylesheet" id="style_color">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.7.5/css/bootstrap-select.min.css">
<link href="assets/fancybox/source/jquery.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="assets/uniform/css/uniform.css">
<link href="assets/fullcalendar/fullcalendar/bootstrap-fullcalendar.css" rel="stylesheet">
<link href="assets/jqvmap/jqvmap/jqvmap.css" media="screen" rel="stylesheet" type="text/css">
<link href="css/inbox.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="css/example-styles.css">
<link rel="stylesheet" type="text/css" href="cssdemo-styles.css">
<link rel="stylesheet" type="text/css" href="assets/jquery-ui/jquery-ui-1.10.1.custom.css">

</head>
  <body>                  


<select name="email[]" class="chosen span6" multiple="multiple" size="10" tabindex="6">
    <?php foreach ($mails as $mail): ?>
        <option value="<?= htmlspecialchars($mail['HesapYoneticisiMail']) ?>" selected="selected"><?= htmlspecialchars($mail['sirketAdi']) ?></option>
    <?php endforeach; ?>
</select>
                        
                          
     <script src="js/jquery-1.8.2.js"></script>    
   <script type="text/javascript" src="assets/ckeditor/ckeditor.js"></script>
   <script src="assets/bootstrap/js/bootstrap.js"></script>
   <script type="text/javascript" src="assets/bootstrap/js/bootstrap-fileupload.js"></script>
   <script src="js/jquery.js"></script>

   <script type="text/javascript" src="assets/chosen-bootstrap/chosen/chosen.jquery.js"></script>
   <script type="text/javascript" src="assets/uniform/jquery.uniform.js"></script>
   <script type="text/javascript" src="assets/bootstrap-wysihtml5/wysihtml5-0.3.js"></script> 
   <script type="text/javascript" src="assets/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>
   <script type="text/javascript" src="assets/clockface/js/clockface.js"></script>
   <script type="text/javascript" src="assets/jquery-tags-input/jquery.tagsinput.js"></script>
   <script type="text/javascript" src="assets/bootstrap-toggle-buttons/static/js/jquery.toggle.js"></script>

   <script src="js/excanvas.js"></script>
   <script src="js/respond.js"></script>
   <script type="text/javascript" src="assets/bootstrap-inputmask/bootstrap-inputmask.js"></script>
   <script src="assets/fancybox\source/jquery.fancybox.js"></script>
   <script src="js/scripts.js"></script>
   <script>
      jQuery(document).ready(function() {       
         // initiate layout and plugins
         App.init();
      });
   </script>                          

</body>
<!-- END BODY -->
</html>

