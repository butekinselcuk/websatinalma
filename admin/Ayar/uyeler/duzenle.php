<?php
require_once('../../../Connections/baglan.php'); // PDO bağlantısını içeriyor
require_once('../../../fonksiyon.php');

if (!isset($_SESSION)) {
    session_start();
}

$MM_authorizedUsers = "1";
$MM_donotCheckaccess = "false";

function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) {
    $isValid = False;
    if (!empty($UserName)) {
        $arrUsers = explode(",", $strUsers);
        $arrGroups = explode(",", $strGroups);
        if (in_array($UserName, $arrUsers) || in_array($UserGroup, $arrGroups)) {
            $isValid = true;
        }
        if ($strUsers == "" && $strGroups == "") {
            $isValid = true;
        }
    }
    return $isValid;
}

$MM_restrictGoTo = "../../../yetki.php";
if (!(isset($_SESSION['MM_Username']) && isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup']))) {
    $MM_qsChar = "?";
    $MM_referrer = $_SERVER['PHP_SELF'];
    if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
    $query_string = $_SERVER['QUERY_STRING'] ? "?" . $_SERVER['QUERY_STRING'] : "";
    $MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer . $query_string);
    header("Location: " . $MM_restrictGoTo);
    exit;
}

// Define $editFormAction for form submission
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}



if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "duzenle")) {
    $sql = "UPDATE uyeler SET seviyeID = ?, bastarih = ?, bittarih = ? WHERE uyeID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        filter_input(INPUT_POST, 'seviyeID', FILTER_SANITIZE_NUMBER_INT),
        filter_input(INPUT_POST, 'bastarih', FILTER_SANITIZE_STRING),
        filter_input(INPUT_POST, 'bittarih', FILTER_SANITIZE_STRING),
        filter_input(INPUT_POST, 'uyeID', FILTER_SANITIZE_NUMBER_INT)
    ]);

    $updateGoTo = "index.php" . ($_SERVER['QUERY_STRING'] ? "?" . $_SERVER['QUERY_STRING'] : "");
    header("Location: $updateGoTo");
    exit;
}

$colname_uyebilgileri = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";

$stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeAdi = ?");
$stmt->execute([$colname_uyebilgileri]);
$row_uyebilgileri = $stmt->fetch();

$stmt = $pdo->prepare("SELECT TalepID, aciklama, gondermetarih, teklifaktif FROM teklifiste WHERE teklifaktif = 0 ORDER BY teklifaktif ASC");
$stmt->execute();
$row_teklifoku = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM siteconfig");
$stmt->execute();
$row_ayar = $stmt->fetch();

if (isset($_GET['uyeID'])) {
    $stmt = $pdo->prepare("SELECT uyeID, seviyeID, uyeAdi, sirketAdi, bastarih, bittarih FROM uyeler WHERE uyeID = ?");
    $stmt->execute([intval($_GET['uyeID'])]);
    $row_duzen = $stmt->fetch();
}

$uyeID = $row_uyebilgileri['uyeID'];
$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE kime = ? AND durum = 0");
$stmt->execute([$uyeID]);
$row_mesaj = $stmt->fetchAll();
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
  <!-- daterange picker -->
  <link rel="stylesheet" href="../../plugins/daterangepicker/daterangepicker.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="../../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="../../plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="../../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="../../plugins/select2/css/select2.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
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
          <h3 class="card-title">Üye Seviyeleri</h3>


        </div>
        <div class="card-body">
          
  <!-- KODLARRRRRRRRRR -->

 <form method="POST" action="<?php echo $editFormAction; ?>" name="duzenle" class="form-horizontal" id="duzenle">
                            <table class="table table-striped table-bordered" id="sample_1">
                            <thead>
                                <tr>
                                    <th style="width:8px;"><span class="hidden-phone">UyeID</span></th>
                                    <th>Üye Adı</th>
                                    <th class="hidden-phone">Şirket Adı</th>
                                    <th class="hidden-phone">Başlangıç Tarihi</th>
                                    <th class="hidden-phone">Bitiş Tarihi</th>
                                    <th class="hidden-phone">Yetki Durumu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="odd gradeX">
                                    <td><?php echo $row_duzen['uyeID']; ?></td>
                                    <td><?php echo $row_duzen['uyeAdi']; ?></td>
                                    <td class="hidden-phone"><?php echo $row_duzen['sirketAdi']; ?></td>
                                    <td class="hidden-phone"><input name="bastarih" type="text" class="form-control float-right" id="ui_date_picker_range_from" value="<?php echo $row_duzen['bastarih']; ?>" size="16"></td>
                                    <td class="hidden-phone"><input name="bittarih" type="text" class="form-control float-right" id="ui_date_picker_range_to" value="<?php echo $row_duzen['bittarih']; ?>" size="16"></td>
                                    <td class="hidden-phone"><input name="seviyeID" type="text" class="form-control float-right " id="seviyeID" value="<?php echo $row_duzen['seviyeID']; ?>"></td>
                                </tr>
                                </tbody>
                        </table>
                

                              <button type="submit" class="btn btn-success">Submit</button>
                              
                              <input type="hidden" name="uyeID" value="<?php echo $row_duzen['uyeID']; ?>">
                              <input type="hidden" name="MM_update" value="duzenle">
                              
                              </form>


  <!-- KODLARRRRRRRRRR -->

        </div>
        <!-- /.card-body -->

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
<!-- Select2 -->
<script src="../../plugins/select2/js/select2.full.min.js"></script>
<!-- InputMask -->
<script src="../../plugins/inputmask/jquery.inputmask.bundle.js"></script>
<script src="../../plugins/moment/moment.min.js"></script>
<!-- date-range-picker -->
<script src="../../plugins/daterangepicker/daterangepicker.js"></script>
<!-- bootstrap color picker -->
<script src="../../plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="../../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- FastClick -->
<script src="../../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../dist/js/demo.js"></script>
<!-- Page script -->
<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2()

    //Datemask dd/mm/yyyy
    $('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })
    //Datemask2 mm/dd/yyyy
    $('#datemask2').inputmask('mm/dd/yyyy', { 'placeholder': 'mm/dd/yyyy' })
    //Money Euro
    $('[data-mask]').inputmask()

    //Date range picker
    $('#reservation').daterangepicker()
    //Date range picker with time picker
    $('#reservationtime').daterangepicker({
      timePicker: true,
      timePickerIncrement: 30,
      locale: {
        format: 'MM/DD/YYYY hh:mm A'
      }
    })
    //Date range as a button
    $('#daterange-btn').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      },
      function (start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
      }
    )

    //Timepicker
    $('#timepicker').datetimepicker({
      format: 'LT'
    })

    //Colorpicker
    $('.my-colorpicker1').colorpicker()
    //color picker with addon
    $('.my-colorpicker2').colorpicker()

    $('.my-colorpicker2').on('colorpickerChange', function(event) {
      $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
    });
  })
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


