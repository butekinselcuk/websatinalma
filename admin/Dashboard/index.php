<?php
session_start();

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




<?php require_once('../../Connections/baglan.php'); ?>
<?php
$tarih = date("Y/m/d");



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


// Ayarlar sorgusu
$stmt_ayar = $pdo->query("SELECT * FROM siteconfig");
$row_ayar = $stmt_ayar->fetch();
$totalRows_ayar = $stmt_ayar->rowCount();




// Toplam üye sayısını çekme
$stmt_totaluye = $pdo->query("SELECT * FROM uyeler");
$uyeListesi = $stmt_totaluye->fetch(PDO::FETCH_ASSOC);
$totalRows_totaluye = $stmt_totaluye->rowCount();


// Aktif olmayan tekliflerin çekilmesi
$stmt_totalteklifcikan = $pdo->query("SELECT TalepID FROM teklifiste WHERE teklifaktif = 0 ORDER BY teklifaktif ASC");
$teklifCikanListesi = $stmt_totalteklifcikan->fetch(PDO::FETCH_ASSOC);
$totalRows_totalteklifcikan = $stmt_totalteklifcikan->rowCount();

// Toplam atanan para miktarı
$stmt_atananpara = $pdo->query("SELECT Sum(atatoplamfiyat) AS toplamFiyat FROM teklifata");
$row_atananpara = $stmt_atananpara->fetch();
$toplam = $row_atananpara['toplamFiyat'];
$totalRows_atananpara = $stmt_atananpara->rowCount();




// Teklif bekleyen son 5
$uyeAdi = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : ''; // Kullanıcı adını session'dan al
$sql = "SELECT DISTINCT teklifiste.kategorialma, teklifiste.teslimyeri, teklifiste.TalepID,\n                teklifiste.spek, teklifiste.odemekosul, teklifiste.teslimsekli, teklifiste.hedeffiyat,\n                teklifiste.adresteslim, teklifiste.adres, teklifiste.postakodu, teklifiste.referansno,\n                teklifiste.uruntanim, teklifiste.forecast, teklifiste.odemevadesi, teklifiste.teslimsure,\n                teklifiste.parabirim, teklifiste.aciklama, teklifiste.gondermetarih, teklifiste.teklifaktif,\n                teklifiste.forecastsabit, teklifiste.istekuyeID, teklifiste.isteksirketadi,\n                uyeler.uyeAdi, kategori.KategoriID, kategori.KategoriAdi, kategori.Kategoriing,\n                kategori.Kategorichn, teklifiste.tbastarih, teklifiste.tbittarih\n            FROM tekliftopla, uyeler, teklifiste\n            INNER JOIN kategori ON kategori.KategoriID = teklifiste.kategorialma\n            WHERE '$tarih' < teklifiste.tbittarih\n                AND FIND_IN_SET(teklifiste.kategorialma, uyeler.Kategori)\n                AND teklifiste.TalepID NOT IN (\n                    SELECT toplaTalepID FROM tekliftopla WHERE uyeler.uyeAdi = tekliftopla.toplauyeadi\n                )\n                AND uyeler.uyeAdi = '$uyeAdi'";

    // Sorguyu hazırlama ve çalıştırma
$stmt_Recordset1 = $pdo->prepare($sql);
$stmt_Recordset1->execute();
$rows_Recordset1 = $stmt_Recordset1->fetchAll(PDO::FETCH_ASSOC);




// Teklife verilen son 5
$stmt_almagoruntu1 = $pdo->prepare("SELECT DISTINCT teklifiste.TalepID, teklifiste.spek, teklifiste.isteksirketadi, kategori.KategoriAdi, kategori.Kategoriing, kategori.Kategorichn FROM tekliftopla INNER JOIN uyeler ON uyeler.uyeID = tekliftopla.toplaUyeID INNER JOIN teklifiste ON teklifiste.TalepID = tekliftopla.toplaTalepID INNER JOIN kategori ON kategori.KategoriID = teklifiste.kategorialma WHERE teklifiste.istekuyeID = tekliftopla.toplateklifsahibiID AND uyeler.uyeAdi = ? ORDER BY teklifiste.TalepID DESC, tekliftopla.toplaTalepID DESC LIMIT 0, 5");
$stmt_almagoruntu1->execute([$uyeAdi]);
$rows_almagoruntu1 = $stmt_almagoruntu1->fetchAll(PDO::FETCH_ASSOC);



// Teklife çıkılan son 5
$stmt_almagoruntu = "SELECT DISTINCT T1.TalepID, T1.spek, T1.odemekosul, T1.odemevadesi, T1.teslimsekli, T1.teslimsure, T1.hedeffiyat, T1.parabirim, T1.forecast, T1.isteksirketadi, T3.KategoriAdi, T3.Kategoriing, T3.Kategorichn FROM teklifiste T1 INNER JOIN uyeler T2 ON T2.uyeID = T1.istekuyeID INNER JOIN kategori T3 ON T3.KategoriID = T1.kategorialma INNER JOIN tekliftopla T4 ON T1.TalepID = T4.toplaTalepID WHERE T4.toplauyeadi = :uyeAdi ORDER BY T1.TalepID DESC LIMIT 0, 5";
$stmt_almagoruntu_executed = $pdo->prepare($stmt_almagoruntu);
$stmt_almagoruntu_executed->execute(['uyeAdi' => isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : '']);
$almagoruntu = $stmt_almagoruntu_executed->fetchAll(PDO::FETCH_ASSOC);




// Sohbet mesajları çekme
$uyeID = isset($_SESSION['uyeID']) ? $_SESSION['uyeID'] : 0; // Kullanıcı ID'sini session'dan al
$stmt_mesaj = $pdo->prepare("SELECT * FROM sohbet WHERE kime = :uyeID AND durum = 0");
$stmt_mesaj->bindParam(':uyeID', $uyeID, PDO::PARAM_INT);
$stmt_mesaj->execute();
$mesajlar = $stmt_mesaj->fetch(PDO::FETCH_ASSOC);
$totalRows_mesaj = $stmt_mesaj->rowCount();

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
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
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
          <h3 class="card-title"> </h3>


        </div>
        <div class="card-body">
          
  <!-- KODLARRRRRRRRRR -->
   
  





          <section class="content">
      <div class="container-fluid">            
            <div class="row">
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cog"></i></span>

              <div class="info-box-content">
                <span class="info-box-text"><?php echo $dil['yuye']; ?></span>
                <span class="info-box-number">
                  <?php echo $stmt_totaluye->rowCount(); ?>
                  <small></small>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-thumbs-up"></i></span>

              <div class="info-box-content">
                <span class="info-box-text"><?php echo $dil['norders']; ?></span>
                <span class="info-box-number"><?php echo $stmt_totalteklifcikan->rowCount(); ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <!-- fix for small devices only -->
          <div class="clearfix hidden-md-up"></div>

          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>

              <div class="info-box-content">
                <span class="info-box-text"><?php echo $dil['sales']; ?></span>
                <span class="info-box-number"><?php 
                  
                  $ntoplam=$row_atananpara['toplamFiyat'];
                  $ttoplam=number_format($ntoplam);
                  echo $ttoplam; ?>$</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>

              <div class="info-box-content">
                <span class="info-box-text"><?php echo $dil['yuye']; ?></span>
                <span class="info-box-number"><?php echo $totalRows_totaluye ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
 

 </div> </div>

         <div class="row">
                  <div class="col-md-4">
                             <div class="card">
        <div class="card card-primary card-outline">
                        <h4><i class="icon-reorder"></i><?php echo $dil['tbekson']; ?></h4>

                     </div>

                 <div class="widget-body"> 

                    
                 <table id="example" class="table table-bordered table-striped" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th><?php echo $dil['alma_talep']; ?></th>
            <th><?php echo $dil['alma_kategori']; ?></th>
            <th><?php echo $dil['alma_firma']; ?></th>
            <th><?php echo $dil['alma_spec']; ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows_Recordset1 as $row): ?>
        <tr>
            <td><a href="../Teklif/alma/teklif.php?TalepID=<?php echo $row['TalepID']; ?>"><span class="badge badge-important"><?php echo $row['TalepID']; ?></span></a></td>
            <td><?php echo ($lang == 'en') ? $row['Kategoriing'] : (($lang == 'chn') ? $row['Kategorichn'] : $row['KategoriAdi']); ?></td>
            <td><span class="badge badge-info"><?php echo $row['isteksirketadi']; ?></span></td>
            <td width="150" class="center hidden-phone"><a href="../resim/spek/<?php echo $row['spek']; ?>" target="_blank"><span class="badge btn-warning label-mini"><?php echo $dil['alma_spec']; ?></span></a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($rows_Recordset1)): ?>
        <tr>
            <td colspan="4">No data found</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>
                  
                  
 </div> </div> </div>




                       <!-- BEGIN GRID SAMPLE PORTLET-->
                  <div class="col-md-4">
                             <div class="card">
        <div class="card card-primary card-outline">
                        <h4><i class="icon-reorder"></i><?php echo $dil['tverson']; ?></h4>

                     </div>
                     <div class="widget-body">
                       
                      

            <table id="example1" class="table table-bordered table-striped dt-responsive nowrap" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th><?php echo $dil['alma_talep']; ?></th>
            <th><?php echo $dil['alma_kategori']; ?></th>
            <th><?php echo $dil['alma_firma']; ?></th>
            <th><?php echo $dil['alma_islem']; ?></th>
            <th width="150" class="hidden-phone"><?php echo $dil['alma_spec']; ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows_almagoruntu1 as $row): ?>
        <tr class="odd gradeX">
            <td><span class="badge badge-important"><?php echo $row['TalepID']; ?></span></td>
            <td>
                <?php
                    $lang = $_SESSION['dil'];
                    if ($lang == 'en') {
                        echo $row['Kategoriing'];
                    } elseif ($lang == 'chn') {
                        echo $row['Kategorichn'];
                    } else {
                        echo $row['KategoriAdi'];
                    }
                ?>
            </td>
            <td><span class="badge badge-info"><?php echo $row['isteksirketadi']; ?></span></td>
            <td><a href="../Teklif/alma/duzenle.php?TalepID=<?php echo $row['TalepID']; ?>" target="_blank" class="btn btn-success"><?php echo $dil['alma_tduzenle']; ?></a></td>
            <td width="150" class="center hidden-phone"><a href="../resim/spek/<?php echo $row['spek']; ?>" target="_blank" class="btn btn-warning"><?php echo $dil['alma_spec']; ?></a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($rows_almagoruntu1)): ?>
            <tr>
                <td colspan="5">No data found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
                   

                       
                     </div>
                  </div>
                   </div>
                            <!-- END GRID SAMPLE PORTLET-->


                            <!-- BEGIN GRID SAMPLE PORTLET-->
                  <div class="col-md-4">
                             <div class="card">
       <div class="card card-primary card-outline">
                        <h4><i class="icon-reorder"></i><?php echo $dil['tekcikson']; ?></h4>

                     </div>
                     <div class="widget-body">
                       


                      <table id="example2" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th><?php echo $dil["alma_talep"]; ?></th>
            <th><?php echo $dil["alma_kategori"]; ?></th>
            <th><?php echo $dil["alma_firma"]; ?></th>
            <th><?php echo $dil["alma_forecast"]; ?></th>
            <th><?php echo $dil["alma_teslim"]; ?>-<?php echo $dil["alma_tsure"]; ?></th>
            <th><?php echo $dil["alma_okosul"]; ?>-<?php echo $dil["alma_ovade"]; ?></th>
            <th><?php echo $dil["alma_hfiyat"]; ?></th>
            <th><?php echo $dil["alma_islem"]; ?></th>
            <th><?php echo $dil["alma_islem"]; ?></th>
            <th><?php echo $dil["alma_spec"]; ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($almagoruntu as $row): ?>
        <tr class="odd gradeX">
            <td><span class="badge badge-important"><?php echo $row['TalepID']; ?></span></td>
            <td>
                <?php
                    $lang = $_SESSION['dil'];
                    if ($lang == 'en') {
                        echo $row['Kategoriing'];
                    } elseif ($lang == 'chn') {
                        echo $row['Kategorichn'];
                    } else {
                        echo $row['KategoriAdi'];
                    }
                ?>
            </td>
            <td><span class="badge badge-info"><?php echo $row['isteksirketadi']; ?></span></td>
            <td><?php echo $row['forecast']; ?></td>
            <td><?php echo $row['teslimsekli']; ?>-<?php echo $row['teslimsure']; ?></td>
            <td><?php echo $row['odemekosul']; ?>-<?php echo $row['odemevadesi']; ?></td>
            <td><?php echo $row['hedeffiyat']; ?> (<?php echo $row['parabirim']; ?>)</td>
            <td><a href="../Teklif/tekliflerim/topla.php?toplaTalepID=<?php echo $row['TalepID']; ?>" target="_blank" class="btn btn-info"><?php echo $dil["teklif_durumu"]; ?></a></td>
            <td><a href="../Teklif/verme/duzenle.php?TalepID=<?php echo $row['TalepID']; ?>" target="_blank" class="btn btn-success"><?php echo $dil["teklif_duzenle"]; ?></a></td>
            <td><a href="../resim/spek/<?php echo $row['spek']; ?>" target="_blank" class="btn btn-warning"><?php echo $dil["verme_spec"]; ?></a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($almagoruntu)): ?>
            <tr>
                <td colspan="10">No data found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

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
<!-- DataTables -->

<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
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
        //dom: '',
        //buttons: []
    } );
} );
</script>
<script>
$(document).ready(function() {
    $('#example1').DataTable( {
        //dom: '',
        //buttons: []
    } );
} );
</script>
<script>
$(document).ready(function() {
    $('#example2').DataTable( {
        //dom: '',
        //buttons: []
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