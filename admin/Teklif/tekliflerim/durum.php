<?php 
require_once('../../../Connections/baglan.php'); 
require_once('../../../fonksiyon.php');

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
        if (in_array($UserName, $arrUsers) || in_array($UserGroup, $arrGroups) || ($strUsers == "" && false)) {
            $isValid = true;
        }
    }
    return $isValid;
}

$MM_restrictGoTo = "../../../login.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {
    $MM_qsChar = strpos($MM_restrictGoTo, "?") ? "&" : "?";
    $MM_referrer = $_SERVER['PHP_SELF'] . (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0 ? "?" . $_SERVER['QUERY_STRING'] : "");
    $MM_restrictGoTo .= $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
    header("Location: " . $MM_restrictGoTo);
    exit;
}

$colname_uyebilgileri = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";

$stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeAdi = :uyeAdi");
$stmt->execute([':uyeAdi' => $colname_uyebilgileri]);
$row_uyebilgileri = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_uyebilgileri = $stmt->rowCount();

$stmt = $pdo->query("SELECT * FROM siteconfig");
$row_ayar = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_ayar = $stmt->rowCount();

$colname_tekliftopla = isset($_GET['toplaTalepID']) ? $_GET['toplaTalepID'] : "-1";

$stmt = $pdo->prepare("SELECT DISTINCT * FROM uyeler INNER JOIN teklifata ON teklifata.verenuyeID = uyeler.uyeID WHERE ataTalepID = :ataTalepID ORDER BY ataTalepID ASC");
$stmt->execute([':ataTalepID' => $colname_tekliftopla]);
$row_teklifveren = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_teklifveren = $stmt->rowCount();

$stmt = $pdo->prepare("SELECT DISTINCT * FROM teklifata INNER JOIN uyeler ON teklifata.alanuyeID = uyeler.uyeID WHERE ataTalepID = :ataTalepID ORDER BY ataTalepID ASC");
$stmt->execute([':ataTalepID' => $colname_tekliftopla]);
$row_teklifalan = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_teklifalan = $stmt->rowCount();

$stmt = $pdo->prepare("SELECT DISTINCT * FROM teklifata INNER JOIN tekliftopla ON tekliftopla.toplaTalepID = teklifata.ataTalepID WHERE teklifata.alanuyeID = tekliftopla.toplaUyeID AND ataTalepID = :ataTalepID ORDER BY ataTalepID ASC");
$stmt->execute([':ataTalepID' => $colname_tekliftopla]);
$row_talep = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_talep = $stmt->rowCount();

$stmt = $pdo->prepare("SELECT DISTINCT * FROM teklifiste INNER JOIN teklifata ON teklifata.ataTalepID = teklifiste.TalepID WHERE ataTalepID = :ataTalepID ORDER BY ataTalepID ASC");
$stmt->execute([':ataTalepID' => $colname_tekliftopla]);
$row_atabilgiler = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_atabilgiler = $stmt->rowCount();

$uyeID = $row_uyebilgileri['uyeID'];
$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE sohbet.kime = :kime AND sohbet.durum = 0");
$stmt->execute([':kime' => $uyeID]);
$row_mesaj = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../../plugins/summernote/summernote-bs4.css">
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
          <h3 class="card-title"><?php echo $dil["teklifpaneli"]; ?>  </h3>
 </div>

        </div>
        <div class="card-body">
          
  <!-- KODLARRRRRRRRRR -->


<div class="row-fluid" id="ALANI_TANIMLAYAN_AD">    
            <div class="invoice p-3 mb-3">
              <!-- title row -->
              <div class="row">
                <div class="col-12">
                  <h4>
                   <img src="../../resim/logo/<?php echo $row_ayar['Sitelogo']; ?>"width="130" height="65">
                    <small class="float-right"><?php echo $dil["tarih"]; ?>: <?php  echo date('d.m.Y', time()); ?></small>
                  </h4>
                </div>
                <!-- /.col -->
              </div>
              <!-- info row -->
              <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                  <strong><?php echo $dil["durumteklifisteyen"]; ?></strong>
                  <address>
                                        <?php echo $dil["sirketadi"]; ?><?php echo $row_teklifveren['sirketAdi']; ?><br>
                                        <?php echo $dil["kayit_adres"]; ?><?php echo $row_teklifveren['Adres']; ?><br>
                                        <?php echo $row_teklifveren['sehir']; ?>
                                        <?php echo $row_teklifveren['Ulke']; ?><br>                                        
                                        <?php echo $dil["kayit_hesap_yonetici"]; ?><?php echo $row_teklifveren['HesapYoneticisiTitle']; ?><br>
                                        <?php echo $dil["kayit_email"]; ?> :<?php echo $row_teklifveren['HesapYoneticisiMail']; ?><br>
                                        <?php echo $dil["kayit_tel_no"]; ?><?php echo $row_teklifveren['Tel']; ?><br>
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                  <strong><?php echo $dil["durumteklifalanin"]; ?></strong>
                  <address>
                                            <?php echo $dil["sirketadi"]; ?><?php echo $row_teklifalan['sirketAdi']; ?><br>
                                            <?php echo $dil["kayit_adres"]; ?><?php echo $row_teklifalan['Adres']; ?><br>
                                            <?php echo $row_teklifalan['sehir']; ?><?php echo $row_teklifalan['Ulke']; ?><br>
                                            <?php echo $dil["kayit_hesap_yonetici"]; ?><?php echo $row_teklifalan['HesapYoneticisiTitle']; ?><br>
                                            <?php echo $dil["kayit_email"]; ?> :<?php echo $row_teklifalan['HesapYoneticisiMail']; ?><br>
                                            <?php echo $dil["kayit_tel_no"]; ?><?php echo $row_teklifalan['Tel']; ?><br>
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                  <b><?php echo $dil["alma_talepno"]; ?>:</b><?php echo $row_teklifveren['ataTalepID']; ?><br>
                                            <?php echo $dil["alma_tsure"]; ?> :<?php echo $row_talep['toplateslimsure']; ?><br>
                                            <?php echo $dil["alma_teslim"]; ?> :<?php echo $row_talep['toplateslimsekli']; ?><br>
                                            <?php echo $dil["alma_okosul"]; ?> :<?php echo $row_talep['toplaodemekosul']; ?><br>
                                            <?php echo $dil["verme_vade"]; ?> :<?php echo $row_talep['toplaodemevadesi']; ?><br>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <!-- Table row -->
              <div class="row">
                <div class="col-12 table-responsive">
                  <table class="table table-striped">
                    <thead>
                    <tr>
                                        <th width="32%"><span class="hidden-480"><?php echo $dil["alma_ktanim"]; ?></span></th>
                                        <th width="30%" class="hidden-480"><?php echo $dil["verme_ref"]; ?></th>
                                        <th width="23%" class="hidden-480"><?php echo $dil["birimfiyat"]; ?></th>
                                        <th width="15%" class="hidden-480"><?php echo $dil["alma_forecast"]; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                                        <td><?php echo $row_atabilgiler['uruntanim']; ?></td>
                                        <td class="hidden-480"><?php echo $row_atabilgiler['referansno']; ?></td>
                                        <td class="hidden-480"><?php echo $row_talep['toplafiyat']; ?><?php echo $row_atabilgiler['parabirim']; ?></td>
                                        <td class="hidden-480"><?php echo $row_atabilgiler['forecast']; ?></td>
                    </tr>



                    </tbody>
                  </table>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <div class="row">
                <!-- accepted payments column -->
                <div class="col-6">
                  <p class="lead"></p>
                  <img src="../../dist/img/credit/visa.png" alt="Visa">
                  <img src="../../dist/img/credit/mastercard.png" alt="Mastercard">
                  <img src="../../dist/img/credit/american-express.png" alt="American Express">
                  <img src="../../dist/img/credit/paypal2.png" alt="Paypal">

                  <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                   
                   </p>
                </div>
                <!-- /.col -->
                <div class="col-6">
                  <p class="lead">  </p>

                  <div class="table-responsive">
                    <table class="table">
                      <tr>
                        <th style="width:50%"><?php echo $dil['toplamfiyat']; ?>:</th>
                        <td><?php 
                    $sonuc=number_format($row_talep['toplafiyat']*$row_atabilgiler['forecast']);?>
                    <?php echo $sonuc; ?><?php echo $row_atabilgiler['parabirim']; ?></td>
                      </tr>
                    </table>
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <!-- this row will not appear when printing -->
              <div class="row no-print">
                <div class="col-12">                            

                  <form name="form1" method="post" action="../../mail/atamail.php" target="_blank" >
                            <input type="hidden" name="TalepID" id="TalepID" value="<?php echo $row_teklifveren['ataTalepID']; ?>">
                            <input type="hidden" name="birsirket" id="birsirket" value="<?php echo $row_teklifveren['sirketAdi']; ?>">
                            <input type="hidden" name="biradres" id="biradres" value="<?php echo $row_teklifveren['Adres']; ?>/<?php echo $row_teklifveren['sehir']; ?>/<?php echo $row_teklifveren['Ulke']; ?>">
                            <input type="hidden" name="hyonet" id="hyonet" value="<?php echo $row_teklifveren['HesapYoneticisiTitle']; ?>">
                            <input type="hidden" name="birmail" id="birmail" value="<?php echo $row_teklifveren['HesapYoneticisiMail']; ?>">
                            <input type="hidden" name="birtel" id="birtel" value="<?php echo $row_teklifveren['Tel']; ?>">
                            <input type="hidden" name="aciklama" id="aciklama" value="<?php echo $row_atabilgiler['aciklama']; ?>">
                            <input type="hidden" name="ikisirket" id="ikisirket" value="<?php echo $row_teklifalan['sirketAdi']; ?>">
                            <input type="hidden" name="ikiadres" id="ikiadres" value="<?php echo $row_teklifalan['Adres']; ?>/<?php echo $row_teklifalan['sehir']; ?>/<?php echo $row_teklifalan['Ulke']; ?>">
                           
                            <input type="hidden" name="ikihyonet" id="ikihyonet" value="<?php echo $row_teklifalan['HesapYoneticisiTitle']; ?>">
                             <input type="hidden" name="ikimail" id="ikimail" value="<?php echo $row_teklifalan['HesapYoneticisiMail']; ?>">
                              <input type="hidden" name="ikitel" id="ikitel" value="<?php echo $row_teklifalan['Tel']; ?>">
                              
                               <input type="hidden" name="teslimsure" id="teslimsure" value="<?php echo $row_talep['toplateslimsure']; ?>">
                                <input type="hidden" name="teslimsekli" id="teslimsekli" value="<?php echo $row_talep['toplateslimsekli']; ?>">
                                 <input type="hidden" name="odemekosul" id="odemekosul" value="<?php echo $row_talep['toplaodemekosul']; ?>">
                                  <input type="hidden" name="odemevadesi" id="odemevadesi" value="<?php echo $row_talep['toplaodemevadesi']; ?>">
                                   <input type="hidden" name="forecast" id="forecast" value="<?php echo $row_atabilgiler['forecast']; ?>">
                                    <input type="hidden" name="parabirim" id="parabirim" value="<?php echo $row_atabilgiler['parabirim']; ?>">
                                     <input type="hidden" name="bfiyat" id="bfiyat" value="<?php echo $row_talep['toplafiyat']; ?>">
                                      <input type="hidden" name="tfiyat" id="tfiyat" value="<?php echo $sonuc; ?>">
                   <a onclick="birAlaniYazdir()" class="btn btn-default"><?php echo $dil['yazdir']; ?><i class="icon-print icon-big"></a>
                  <input type="submit" name="gönder" class="btn btn-info btn-large" value="<?php echo $dil['mailgonder']; ?>">
 </form>

                </div>
              </div>
            </div>
            <!-- /.invoice -->
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
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

<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- FastClick -->
<script src="../../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../dist/js/demo.js"></script>
<!-- Summernote -->
<script src="../../plugins/summernote/summernote-bs4.min.js"></script>
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
    function birAlaniYazdir() {
        var basilacakIcerik= document.getElementById('ALANI_TANIMLAYAN_AD').innerHTML;
        var orjinalSayfa= document.body.innerHTML;
        document.body.innerHTML = basilacakIcerik;
        window.print();
        document.body.innerHTML = orjinalSayfa;
    } 
</script>

   <script type="text/javascript" src="../../js/jquery-2.2.4.min.js"></script>
   <script type="text/javascript" src="../../js/jquery.multi-select.js"></script>
   <script type="text/javascript">
    $(function(){
        $('#people').multiSelect();
        $('#line-wrap-example').multiSelect({
            positionMenuWithin: $('.position-menu-within')
        });
        $('#categories').multiSelect({
            noneText: 'All categories',
            presets: [
                {
                    name: 'All categories',
                    options: []
                },
                {
                    name: 'My categories',
                    options: ['a', 'c']
                }
            ]
        })
    });
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
