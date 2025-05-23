<?php

error_reporting(0);
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
        if (in_array($UserName, $arrUsers) || in_array($UserGroup, $arrGroups) || $strUsers == "") {
            $isValid = true;
        }
    }
    return $isValid;
}

$MM_restrictGoTo = "../../../login.php";
if (!((isset($_SESSION['MM_Username'])) && isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup']))) {
    $MM_qsChar = strpos($MM_restrictGoTo, "?") ? "&" : "?";
    $MM_referrer = $_SERVER['PHP_SELF'];
    if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) {
        $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
    }
    header("Location: " . $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer));
    exit;
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$colname_uyebilgileri = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";


$sql = "SELECT * FROM uyeler WHERE uyeAdi = :uyeAdi";
$stmt = $pdo->prepare($sql);
$stmt->execute(['uyeAdi' => $colname_uyebilgileri]);
$row_uyebilgileri = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_uyebilgileri = $stmt->rowCount();

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
    $stmt = $pdo->prepare("INSERT INTO teklifata (ataTalepID, alanuyeID, verenuyeID, atadurum, atatoplamfiyat, ataparabirimi) VALUES (:ataTalepID, :alanuyeID, :verenuyeID, :atadurum, :atatoplamfiyat, :ataparabirimi)");
    $stmt->execute([
        ':ataTalepID' => $_POST['ataTalepID'],
        ':alanuyeID' => $_POST['alanuyeID'],
        ':verenuyeID' => $_POST['verenuyeID'],
        ':atadurum' => $_POST['atadurum'],
        ':atatoplamfiyat' => $_POST['atatoplamfiyat'],
        ':ataparabirimi' => $_POST['ataparabirimi']
    ]);
    $insertGoTo = "durum.php";
    if (isset($_SERVER['QUERY_STRING'])) {
        $insertGoTo .= (strpos($insertGoTo, '?') ? "&" : "?") . $_SERVER['QUERY_STRING'];
    }
    header("Location: " . $insertGoTo);
}

$colname_tekliftopla = isset($_GET['toplaTalepID']) ? $_GET['toplaTalepID'] : "-1";


// Teklifler
$sql = "SELECT DISTINCT * 
        FROM tekliftopla 
        INNER JOIN uyeler ON uyeler.uyeID = tekliftopla.toplaUyeID 
        INNER JOIN kategori ON kategori.KategoriID = tekliftopla.toplakategoriID 
        WHERE uyeler.uyeID = tekliftopla.toplaUyeID 
        AND toplaTalepID = :talepID";

$stmt = $pdo->prepare($sql);
$stmt->execute([':talepID' => $colname_tekliftopla]);
$row_tekliftopla = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRow_tekliftopla = $stmt->rowCount(); // Sonuç sayısı





$stmt = $pdo->query("SELECT * FROM siteconfig");
$row_ayar = $stmt->fetch(PDO::FETCH_ASSOC);



// Fetch teklifata
$stmt = $pdo->prepare("SELECT DISTINCT tekliftopla.*, teklifata.*, teklifiste.*, uyeler.*
                        FROM teklifata 
                        INNER JOIN teklifiste ON teklifata.ataTalepID = teklifiste.TalepID AND teklifiste.istekuyeID = teklifata.verenuyeID
                        INNER JOIN uyeler ON uyeler.uyeID = teklifata.verenuyeID
                        INNER JOIN tekliftopla ON tekliftopla.toplaTalepID = teklifiste.TalepID
                        WHERE tekliftopla.toplaTalepID = :toplaTalepID
                        ORDER BY tekliftopla.toplaTalepID ASC");
$stmt->execute([':toplaTalepID' => $colname_tekliftopla]);
$row_teklifata = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_teklifata = count($row_teklifata);

// Fetch teklifalan
$stmt = $pdo->prepare("SELECT DISTINCT teklifata.*, uyeler.*
                        FROM teklifata
                        INNER JOIN uyeler ON teklifata.alanuyeID = uyeler.uyeID
                        WHERE ataTalepID = :ataTalepID
                        ORDER BY ataTalepID ASC");
$stmt->execute([':ataTalepID' => $colname_tekliftopla]);
$row_teklifalan = $stmt->fetch(PDO::FETCH_ASSOC);



// Fetch teklifisteyen
$stmt = $pdo->prepare("SELECT DISTINCT teklifiste.*, kategori.*
                        FROM teklifiste
                        INNER JOIN kategori ON teklifiste.kategorialma = kategori.KategoriID
                        WHERE TalepID = :TalepID");
$stmt->execute([':TalepID' => $colname_tekliftopla]);
$row_teklifisteyen = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_teklifisteyen = $row_teklifisteyen ? 1 : 0;

// Fetch durum
$stmt = $pdo->prepare("SELECT DISTINCT *
                        FROM tekliftopla, teklifiste
                        INNER JOIN teklifata ON teklifiste.TalepID = teklifata.ataTalepID
                        WHERE tekliftopla.toplaTalepID = :toplaTalepID AND FIELD(tekliftopla.toplaTalepID, teklifata.ataTalepID)");

$stmt->execute([':toplaTalepID' => $colname_tekliftopla]);
$row_durum = $stmt->fetch(PDO::FETCH_ASSOC);

$talep = isset($row_teklifisteyen['TalepID']) ? $row_teklifisteyen['TalepID'] : null;
 
 

 $uyeID=$row_uyebilgileri['uyeID'];
$stmt = $pdo->prepare("SELECT * FROM sohbet WHERE kime = :kime AND durum = 0");
$stmt->execute([':kime' => $row_uyebilgileri['uyeID']]);
$row_mesaj = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_mesaj = $stmt->rowCount();  // Count rows
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
          <h3 class="card-title"><?php echo $dil["teklif_durumu"]; ?>  </h3>
 </div>

        </div>
        <div class="card-body">
          
  <!-- KODLARRRRRRRRRR -->
      <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">


            <div class="card card-primary">
              <div class="card-header">
                  <h3 class="profile-username text-center"><?php echo $dil["alma_talep"]; ?></h3>
                </div>

                <h3 class="profile-username text-center"><?php echo $talep; ?></h3>


                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b><?php echo $dil["alma_kategori"]; ?></b> <a class="float-right"><?php if ($_SESSION["dil"] == 'tr' ) {
  ?> 
    
                  <?php echo $row_teklifisteyen['KategoriAdi']; ?>
        
        
 <?php } elseif ($_SESSION["dil"] == 'en') {
   
   
   ?> 
                <?php echo $row_teklifisteyen['Kategoriing']; ?>
<?php } elseif ($_SESSION["dil"] == 'chn' ) {
        
      ?> 
                     <?php echo $row_teklifisteyen['Kategorichn']; ?>         
                         
                   <?php } ?></a>
                  </li>
                  <li class="list-group-item">
                    <b><?php echo $dil["verme_ref"]; ?></b> <a class="float-right"><?php echo $row_teklifisteyen['referansno']; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b><?php echo $dil["alma_ktanim"]; ?></b> <a class="float-right"><?php echo $row_teklifisteyen['uruntanim']; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b><?php echo $dil["alma_forecast"]; ?></b> <a class="float-right"><?php echo $row_teklifisteyen['forecast']; ?></a>
                  </li>
                   <li class="list-group-item">
                    <b><?php echo $dil["alma_aciklama"]; ?></b> <a class="float-right"><?php echo $row_teklifisteyen['aciklama']; ?></a>
                  </li>
                    <li class="list-group-item">
                    <b><?php echo $dil["verme_para"]; ?></b> <a class="float-right"><?php echo $row_teklifisteyen['parabirim']; ?></a>
                  </li>
                </ul>

              </div>
              <!-- /.card-body -->
            </div>

  <div class="col-md-9">
    <div class="card">
      <div class="card-header p-2">
                 
                 
               
 <form action="<?php echo $editFormAction; ?>" name="form1" method="POST" enctype="multipart/form-data" onSubmit="return check_frmm()">  
   <?php if($row_uyebilgileri['uyeID']==$row_tekliftopla['toplateklifsahibiID']) :?>
                  
                 
                  <?php if(  $row_durum['atadurum']!='1'  ): 
				  
				 
				  ?>
                  
                    <table id="example" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                       <thead>                       
                         <tr>
                           <th><?php echo $dil["alma_firma"]; ?></th>
                           <th><?php echo $dil["alma_teslim"]; ?>-<?php echo $dil["alma_tsure"]; ?></th>
                           <th><?php echo $dil["alma_okosul"]; ?>-<?php echo $dil["alma_ovade"]; ?></th>
                           <th><?php echo $dil["alma_teklifi"]; ?></th>
                           <th><?php echo $dil["alma_islem"]; ?></th>
                           <th width="150" class="hidden-phone"><?php echo $dil["teklifata"]; ?> </th>
                         </tr>

                     </thead>
                       <tbody>
            
                    
   <?php 
   
   // Teklifler
$sql = "SELECT DISTINCT * 
        FROM tekliftopla 
        INNER JOIN uyeler ON uyeler.uyeID = tekliftopla.toplaUyeID 
        INNER JOIN kategori ON kategori.KategoriID = tekliftopla.toplakategoriID 
        WHERE uyeler.uyeID = tekliftopla.toplaUyeID 
        AND toplaTalepID = :talepID";

$stmt = $pdo->prepare($sql);
$stmt->execute([':talepID' => $colname_tekliftopla]);
$rows_tekliftopla = $stmt->fetchAll(PDO::FETCH_ASSOC);

   ?>
   
  
	<?php foreach ($rows_tekliftopla as $row_tekliftopla): ?>		
                         <tr class="odd gradeX">
                           <td><?php echo $row_tekliftopla['toplasirketAdi']; ?></td>
                           <td><?php echo $row_tekliftopla['toplateslimsekli']; ?>-<?php echo $row_tekliftopla['toplateslimsure']; ?></td>
                           <td><?php echo $row_tekliftopla['toplaodemekosul']; ?>-<?php echo $row_tekliftopla['toplaodemevadesi']; ?></td>
                           <td><?php echo $row_tekliftopla['toplafiyat']; ?><?php echo $row_teklifisteyen['parabirim']; ?></td>
                           <td><a href="../../sohbet/gonder.php?kime=<?php echo $row_tekliftopla['toplaUyeID']; ?>&talep=<?php echo $talep; ?>" class="btn btn-success"><?php echo $dil["mgonder"]; ?></a></td>
                           
                           <td>
                             <input name="alanuyeID" type="radio" id="alanuyeID"  value="<?php echo $row_tekliftopla['toplaUyeID']; ?>"  >
                            
                             <?php 
               
               $verenfiyat=$row_tekliftopla['toplafiyat'];
               $adet=$row_teklifisteyen['forecast'];
               $verentoplamfiyat=$verenfiyat*$adet
               
               ?>
                             
                          </td>

                           <input type="hidden" name="verenuyeID" id="verenuyeID" value="<?php echo $row_tekliftopla['toplateklifsahibiID']; ?>">
                           <input type="hidden" name="ataTalepID" id="ataTalepID" value="<?php echo $row_teklifisteyen['TalepID']; ?>">
                           <input type="hidden" name="atadurum" id="atadurum" value="1">
                           <input type="hidden" name="atatoplamfiyat" id="atatoplamfiyat" value="<?php echo $verentoplamfiyat; ?>">
                           <input type="hidden" name="ataparabirimi" id="ataparabirimi" value="<?php echo $row_teklifisteyen['parabirim']; ?>">
                         </tr>
						       <?php endforeach; ?>
 
    </tbody>
                   </table>
                  
                       
                    <button type="submit" class="btn btn-success" value="<?php echo $row_tekliftopla['uyeID']; ?>" ><?php echo $dil["teklifata"]; ?></button>
            <a href="../verme/duzenle.php?TalepID=<?php echo $row_teklifisteyen['TalepID']; ?>" class="btn btn-success"><?php echo $dil["teklif_duzenle"]; ?></a>

  
                   <?php else :?>
                 <h4> <?php echo $dil["teklifalanfirma"]; ?></h4>

                          <div class="row-fluid">
          <div class="span6">       
          
                
                    <div class="widget">
                        
                        <div class="widget-body form">
                    
                                 <ul class="nav nav-tabs nav-stacked">
                                            <li><a href="javascript:void(1)"><?php echo $dil["sirketadi"]; ?>   <?php echo $row_teklifalan['sirketAdi']; ?></a></li>
                                            <li><a href="javascript:void(1)"><?php echo $dil["kayit_adres"]; ?>  <?php echo $row_teklifalan['Adres']; ?><br>
                                              <?php echo $row_teklifalan['sehir']; ?> <?php echo $row_teklifalan['Ulke']; ?></a></li>
                                            <li><a href="javascript:void(1)"><?php echo $dil["kayit_hesap_yonetici"]; ?><?php echo $row_teklifalan['HesapYoneticisiTitle']; ?></a></li>
                                            <li><a href="javascript:void(1)"><?php echo $dil["kayit_email"]; ?> :   <?php echo $row_teklifalan['HesapYoneticisiMail']; ?></a></li>
                                            <li><a href="javascript:void(1)"><?php echo $dil["kayit_tel_no"]; ?>   <?php echo $row_teklifalan['Tel']; ?></a></li>

                                            <li><a href="javascript:void(1)"><?php echo $dil["alma_tsure"]; ?> :   <?php echo $row_durum['toplateslimsure']; ?></a></li>
                                            <li><a href="javascript:void(1)"><?php echo $dil["alma_teslim"]; ?> :   <?php echo $row_durum['toplateslimsekli']; ?></a></li>
                                            <li><a href="javascript:void(1)"><?php echo $dil["alma_okosul"]; ?> :   <?php echo $row_durum['toplaodemekosul']; ?></a></li>
                                            <li><a href="javascript:void(1)"><?php echo $dil["verme_vade"]; ?> :   <?php echo $row_durum['toplaodemevadesi']; ?></a></li>
                                            <li><a href="javascript:void(1)"><?php echo $dil["atananfiyat"]; ?> :   <?php echo $row_durum['toplafiyat']; ?><?php echo $row_teklifisteyen['parabirim']; ?></a></li>
                                          </ul>
                                          </div>
                                          </div>
                                            </div>
                                              </div>
                    <?php endif ;?>
                   
                                                       <?php else :?>
                                                     
                              
                                 <div class="info-box bg-gradient-warning">
                                   <?php echo $dil["toplahata"]; ?> </div>
                               

                           </ul>  
                           
                           <?php endif ;?>
                           <input type="hidden" name="MM_insert" value="form1">
                           
 </form> 



  
                <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
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
