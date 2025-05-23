<?php
 require_once('Connections/baglan.php');
 require_once('fonksiyon.php');

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
include 'admin/dil/' . $_SESSION['dil'] . '.php';

$tarih = date("Y/m/d");

// SQL değerlerini temizleme ve güvenli hale getirme fonksiyonu
function GetSQLValueString($pdo, $theValue, $theType) {
    switch ($theType) {
        case "text":
            $theValue = !empty($theValue) ? "'" . $pdo->quote($theValue) . "'" : "NULL";
            break;
        case "long":
        case "int":
            $theValue = !empty($theValue) ? intval($theValue) : "NULL";
            break;
        case "double":
            $theValue = !empty($theValue) ? doubleval($theValue) : "NULL";
            break;
        case "date":
            $theValue = !empty($theValue) ? "'" . $pdo->quote($theValue) . "'" : "NULL";
            break;
        default:
            $theValue = "NULL";
    }
    return $theValue;
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING'], ENT_QUOTES);
}

if (isset($_POST["MM_insert"]) && $_POST["MM_insert"] == "kategori") {
    $uyeAdi = filter($_POST['uyeAdi'], 1, $pdo);
    $stmt = $pdo->prepare("SELECT * FROM uyeler WHERE uyeAdi = ?");
    $stmt->execute([$uyeAdi]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo '<div class="alert alert-error"><strong>Hata:</strong> Bu kullanıcı adı zaten alınmış.</div>';
    } else {
        $insertSQL = $pdo->prepare("INSERT INTO uyeler (uyeAdi, seviyeID, Parola, sirketAdi, VergiDairesi, VergiNumarasi, TicarisicilNo, BusinessRegNo, ToplamuretimAlani, Calisansayi, Ulke, sehir, PostaKodu, Adres, WebAdres, Tel, Fax, GecenYilciro, EkipmanYatirim, ihracatOran, HesapYoneticisiTitle, SalesMarketingTitle, RDTitle, LogisticsTitle, qualityTitle, ReferansMusteri, Kategori, HesapYoneticisiTel, HesapYoneticisiMail, PurchasingTitle, salesMarketingTel, salesMarketingMail, RDTel, RDMail, LogisticsTel, LogisticsMail, PurchasingTel, PurchasingMail, qualityTel, qualityMail, bastarih, bittarih, HukukiYapi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insertSQL->execute([
            htmlspecialchars($uyeAdi), $_POST['seviyeID'], password_hash($_POST['Parola'], PASSWORD_DEFAULT),
            $_POST['sirketAdi'], $_POST['VergiDairesi'], $_POST['VergiNumarasi'], $_POST['TicarisicilNo'],
            $_POST['BusinessRegNo'], $_POST['ToplamuretimAlani'], $_POST['Calisansayi'], $_POST['Ulke'],
            $_POST['sehir'], $_POST['PostaKodu'], $_POST['Adres'], $_POST['WebAdres'], $_POST['Tel'],
            $_POST['Fax'], $_POST['GecenYilciro'], $_POST['EkipmanYatirim'], $_POST['ihracatOran'],
            $_POST['HesapYoneticisiTitle'], $_POST['SalesMarketingTitle'], $_POST['RDTitle'],
            $_POST['LogisticsTitle'], $_POST['qualityTitle'], $_POST['ReferansMusteri'], $_POST['Kategori'],
            $_POST['HesapYoneticisiTel'], $_POST['HesapYoneticisiMail'], $_POST['PurchasingTitle'],
            $_POST['salesMarketingTel'], $_POST['salesMarketingMail'], $_POST['RDTel'], $_POST['RDMail'],
            $_POST['LogisticsTel'], $_POST['LogisticsMail'], $_POST['PurchasingTel'], $_POST['PurchasingMail'],
            $_POST['qualityTel'], $_POST['qualityMail'], $_POST['bastarih'], $_POST['bittarih'], $_POST['HukukiYapi']
        ]);
        echo '<div class="alert alert-success"><strong>Başarılı!</strong> Kullanıcı kaydedildi.</div>';
        header("Location: login.php?Ekle=EklemeBasarili");
        exit;
    }
}

try {
    $sql = "SELECT * FROM kategori ORDER BY KategoriAdi ASC, Kategoriing ASC, Kategorichn ASC";
    $stmt = $pdo->query($sql);  // Sorguyu çalıştır
    $kategoriList = $stmt->fetchAll();  // Tüm sonuçları çek
    $totalRows_Recordset1 = count($kategoriList);  // Toplam satır sayısını hesapla


    // Site konfigürasyon bilgilerini çekme
    $sql2 = "SELECT * FROM siteconfig";
    $stmt2 = $pdo->query($sql2);
    $siteConfig = $stmt2->fetch();  // Tek bir sonuç bekleniyor
    $totalRows_ayar = $stmt2->rowCount();  // Toplam satır sayısını hesapla

} catch (PDOException $e) {
    die("Sorgu hatası: " . $e->getMessage());
}

// Verileri kullanma örnekleri

foreach ($kategoriList as $kategori) {

}
?>


<head>
  
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
   <title><?php echo $dil['kayit_baslik']; ?></title>
   <link href="admin/assets/bootstrap/css/bootstrap.css" rel="stylesheet">
   <link href="admin/assets/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
   <link href="admin/assets/bootstrap/css/bootstrap-fileupload.css" rel="stylesheet">
   <link href="admin/assets/font-awesome/css/font-awesome.css" rel="stylesheet">
   <link href="admin/assets/fancybox/source/jquery.css" rel="stylesheet">
   <link rel="stylesheet" type="text/css" href="admin/assets/gritter/css/jquery.css">
   <link rel="stylesheet" type="text/css" href="admin/assets/uniform/css/uniform.css">
   <link rel="stylesheet" type="text/css" href="admin/assets/chosen-bootstrap/chosen/chosen.css">
   <link rel="stylesheet" type="text/css" href="admin/assets/jquery-tags-input/jquery.css">    
   <link rel="stylesheet" type="text/css" href="admin/assets/clockface/css/clockface.css">
   <link rel="stylesheet" type="text/css" href="admin/assets/bootstrap-wysihtml5/bootstrap-wysihtml5.css">
   <link rel="stylesheet" type="text/css" href="admin/assets/bootstrap-datepicker/css/datepicker.css">
   <link rel="stylesheet" type="text/css" href="admin/assets/bootstrap-timepicker/compiled/timepicker.css">
   <link rel="stylesheet" type="text/css" href="admin/assets/bootstrap-colorpicker/css/colorpicker.css">
   <link rel="stylesheet" href="admin/assets/bootstrap-toggle-buttons/static/stylesheets/bootstrap-toggle-buttons.css">
   <link rel="stylesheet" href="admin/assets/data-tables/DT_bootstrap.css">
   <link rel="stylesheet" type="text/css" href="admin/assets/bootstrap-daterangepicker/daterangepicker.css">
   <link href="admin/assets/css/style.css" rel="stylesheet">
   <link href="admin/assets/css/style_responsive.css" rel="stylesheet">
   <link href="admin/assets/css/style_default.css" rel="stylesheet" id="style_color">
   <link href="admin/assets/dropzone/css/dropzone.css" rel="stylesheet">
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.7.5/css/bootstrap-select.min.css">

   <link href="admin/assets/fancybox/source/jquery.css" rel="stylesheet">
   <link rel="stylesheet" type="text/css" href="admin/assets/uniform/css/uniform.css">
   
 
</head>



<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="fixed-top">
   <!-- BEGIN HEADER -->
   <div id="header" class="navbar navbar-inverse navbar-fixed-top">
  
  <!-- ################################################################################################ -->
  <div class="wrapper row1">
    <header id="header" class="hoc clear"> 
      <!-- ################################################################################################ -->
   </br> 

        <img src="admin/resim/logo/<?php echo $siteConfig['Sitelogo']; ?>" width="145" height="50" alt="QHubi.com" class="pull-left" >
   

                         		                   <ul class="nav pull-right top-menu">
                   		<li class="dropdown">
                           <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                               <span class="Dil"><?php echo $dil["dil_seciniz"]; ?></span>
                               <b class="caret"></b>
                           </a>
                           <ul class="dropdown-menu">
                               <li><a href="?dil=tr"><i class="icon-user"></i> Türkçe</a></li>
                               <li><a href="?dil=en"><i class="icon-user"></i> English</a></li>
                               <li><a href="?dil=chn"><i class="icon-user"></i> 中國語文</a></li>
                           </ul>
                       </li>
 </ul>

      <!-- ################################################################################################ --> 
    </header>


               <!-- END RESPONSIVE MENU TOGGLER -->
               
             <!-- END  NOTIFICATION -->
               <div class="top-nav"><!-- END TOP NAVIGATION MENU -->
            
           </div>
       </div>
       <!-- END TOP NAVIGATION BAR -->
   </div>

   <div class="container-fluid">
   
           <!-- BEGIN PAGE HEADER-->
           <div class="row-fluid">

           </div>
           <!-- END PAGE HEADER-->
           <!-- BEGIN PAGE CONTENT-->
           
            <?php if(isset($_GET['Ekle'])) :?>

						<?php if($_GET['Ekle']=='EklemeBasarili') ?>	
									<div class="alert alert-success">
										<button class="close"  data-dismiss="alert">×</button>
										<strong>Ekleme Başarılı!</strong> Hesap Başarıyla Eklendi.
									</div>
<?php endif 	;?>	

                        <?php if(isset($_GET['Duzenle'])) :?>

						<?php if($_GET['Duzenle']=='DuzenlemeBasarili') ?>	
									<div class="alert alert-info">
										<button class="close" data-dismiss="alert">×</button>
										<strong>Duzenleme Başarılı!</strong> Hesap Başarıyla Düzenlendi.
									</div>
                                    
                                    
<?php endif 	;?>	
                        <?php if(isset($_GET['Sil'])) :?>

						<?php if($_GET['Sil']=='SilmeBasarili') ?>	

									<div class="alert alert-error">
										<button class="close" data-dismiss="alert">×</button>
										<strong>Silme Başarılı!</strong> Hesap Başarıyla Silindi.
									</div>

<?php endif 	;?>	

<form method="POST" action="<?php echo $editFormAction; ?>" name="kategori"  class="form-horizontal" id="kategori" onSubmit="return check_frmm()">   




 <br> <br> <br>
         <?php
  if($_POST)
  {	echo  $bilgi;}
  ?>
           <div class="row-fluid">
<div class="span12">

  <div class="row-fluid">
               <div class="span12">
                  <!-- BEGIN SAMPLE FORM widget-->   
                  
                  </div>
                  
                  
                                          <div class="row-fluid">
                <div class="span6">
                  <div class="widget">
                     <div class="widget-title">
                        <h4><i class="icon-reorder"></i><?php echo $dil['kayit_sirketbilgi_baslik']; ?></h4>
                          
                     </div>
                     <div class="widget-body form">
                        <!-- BEGIN FORM-->
                       
                           <div class="control-group">
                              <label class="control-label"><?php echo $dil["sirketadi"]; ?> <span class="turuncu">*</span></label>
                              <div class="controls">
                                <input name="sirketAdi" type="text" class="span3  popovers" id="sirketAdi" data-trigger="hover" data-content="Şirket adı.">
                              </div>
                           </div>
                           <div class="control-group">
                              <label class="control-label"><?php echo $dil['kayit_vergi']; ?></label>
                              <div class="controls">
                                <input name="VergiDairesi" type="text" class="span3  popovers" id="VergiDairesi" data-trigger="hover" data-content="Vergi Dairesi.">

                              </div>
                           </div>
                           <div class="control-group">
                              <label class="control-label"><?php echo $dil['kayit_vergi_no']; ?></label>
                              <div class="controls">
                                <input name="VergiNumarasi" type="text" class="span3  popovers" id="VergiNumarasi" data-trigger="hover" data-content="Vergi Numarası.">

                              </div>
                           </div>
                           <div class="control-group">
                              <label class="control-label"><?php echo $dil['kayit_ticari_sicil_no']; ?></label>
                              <div class="controls">
                                <input name="TicarisicilNo" type="text" class="span3  popovers" id="TicarisicilNo" data-trigger="hover" data-content="Ticaret Sicil No.">

                              </div>
                           </div>
                           <div class="control-group">
                              <label class="control-label"><?php echo $dil['kayit_bus_reg_no']; ?></label>
                              <div class="controls">
                                <input name="BusinessRegNo" type="text" class="span3  popovers" id="BusinessRegNo" data-trigger="hover" data-content="Bussines Reg. Number.">

                              </div>
                           </div>
                           <div class="control-group">
                              <label class="control-label"><?php echo $dil['kayit_hukukiyapi']; ?></label>
                              <div class="controls">
                                <input name="HukukiYapi" type="text" class="span3  popovers" id="HukukiYapi" data-trigger="hover" data-content="Hukuki Yapı.">

                              </div>
                           </div>
                           <div class="control-group">
                              <label class="control-label"><?php echo $dil["kayit_topla_uretim"]; ?></label>
                              <div class="controls">
                                <input name="ToplamuretimAlani" type="text" class="span3  popovers" id="ToplamuretimAlani" data-trigger="hover" data-content="Toplam üretim alanı (m2).">

                              </div>
                           </div>
                         <div class="control-group">
                              <label class="control-label"><?php echo $dil['kayit_calisan_sayi']; ?></label>
                              <div class="controls">
                                <input name="calisansayi" type="text" class="span3  popovers" id="calisansayi" data-trigger="hover" data-content="Çalışan sayısı.">

                              </div>
                         </div>

                 
        </div>
      </div>
             </div>
                    <div class="span6">    
<div class="widget">
                        <div class="widget-title">
                            <h4><i class="icon-reorder"></i><?php echo $dil['kayit_onemli_bilgi']; ?></h4>

                        </div>
                        <div class="widget-body form">
                            <!-- BEGIN FORM-->

                           <div class="control-group">
                              <label class="control-label"><?php echo $dil['kayit_kadi']; ?> <span class="turuncu">*</span> </label>
                              <div class="controls">
                             


<div>
    <input type="text" id="uyeAdi" class="span3  popovers" name="uyeAdi"/> <span id="status"></span>
</div>
                              </div>
                         </div>
                           <div class="control-group">
                              <label class="control-label"><?php echo $dil['kayit_sifre']; ?> <span class="turuncu">*</span> </label>
                              <div class="controls">
                                <input name="Parola" type="password" class="span3  popovers" id="Parola" data-trigger="hover" data-content="Şifrenizi giriniz.">
                              </div>
                            </div>
                          
                          
                          <div class="control-group">
                            <label class="control-label"> <?php echo $dil['kayit_kategori']; ?> <span class="turuncu">*</span> </label>
                            <div class="controls">
                                
                               
                              <strong>
                              
                               
<div class="control-group">
      <?php
     // Kategori bilgilerini çekme
$sql = "SELECT * FROM kategori ORDER BY KategoriAdi ASC, Kategoriing ASC, Kategorichn ASC";
$stmt = $pdo->query($sql);  // Sorguyu çalıştır
$kategoriList = $stmt->fetchAll(PDO::FETCH_ASSOC);  // Tüm sonuçları çek

// Dil seçimine göre uygun kategori alanını seçmek
$kategoriField = "KategoriAdi";  // Varsayılan olarak Türkçe
if ($_SESSION["dil"] == 'en') {
    $kategoriField = "Kategoriing";  // İngilizce
} elseif ($_SESSION["dil"] == 'chn') {
    $kategoriField = "Kategorichn";  // Çince
}
?>

<select name="Kategori[]" id="Kategori" class="selectpicker" multiple data-size="10" data-live-search="true" data-actions-box="true">
    <?php foreach ($kategoriList as $kategori): ?>
        <option value="<?php echo htmlspecialchars($kategori['KategoriID']); ?>">
            <?php echo htmlspecialchars($kategori[$kategoriField]); ?>
        </option>
    <?php endforeach; ?>
</select>

                        </div>                
                                </strong>
                    
                                 
                            </div>
                          </div>

					<div class="control-group">
                              <label class="control-label"><?php echo $dil['kayit_ref_musteri']; ?></label>
                              <div class="controls">
                                 <input name="ReferansMusteri" type="text"  class="m-wra tags" id="tags_1" >
                   </div>
                          </div>

                     </br>     
                          
                          </br>   
                           </br>   
                            </br>    
                          
                                 </br> 
                                        </br> 
                                               </br> 
                                                      </br> 
                                                             </br> 
                                                                    </br> 
                          
                          
                          
          </div>
                    </div>

  </div>
    </div>

                        <div class="row-fluid">
                <div class="span6">

<div class="widget">
                        <div class="widget-title">
                            <h4><i class="icon-reorder"></i><?php echo $dil['kayit_baslik_adres']; ?></h4>
                        </div>
                        <div class="widget-body form">
                            <!-- BEGIN FORM-->
                            
                                  <div class="control-group">
                              <label class="control-label"><?php echo $dil['kayit_ulke']; ?> <span class="turuncu">*</span> </label>
                              <div class="controls">
                                <select onChange="print_state('state', this.selectedIndex);" id="country" name ="Ulke" ></select>

                              </div>
                           </div>
                                  <div class="control-group">
                              <label class="control-label"><?php echo $dil['kayit_sehir']; ?> <span class="turuncu">*</span> </label>
                              <div class="controls">
                                <select name ="sehir" id ="state" ></select>
                              </div>
                           </div>
                                  <div class="control-group">
                              <label class="control-label"><?php echo $dil['kayit_posta_kodu']; ?></label>
                              <div class="controls">
                                <input name="PostaKodu" type="text" class="span3  popovers" id="PostaKodu" data-trigger="hover" data-content="Posta Kodu.">
                              </div>
                           </div>
       
                                                           <div class="control-group">
                              <label class="control-label"><?php echo $dil['kayit_adres']; ?><span class="turuncu">*</span> </label>
                              <div class="controls">
                                <textarea name="Adres" rows="3" class="span6  popovers" id="Adres" type="text" data-trigger="hover" data-content="Adres."></textarea>
                              </div>
                           </div>
           
                                                           <div class="control-group">
                              <label class="control-label"><?php echo $dil['kayit_web_adres']; ?></label>
                              <div class="controls">
                                <input name="WebAdres" type="text" class="span3  popovers" id="WebAdres" data-trigger="hover" data-content="Web Adresi.">
                              </div>
                           </div>
                                <div class="control-group">
                                    <label class="control-label"><?php echo $dil['kayit_tel_no']; ?> <span class="turuncu">*</span> </label>
                                    <div class="controls">
                                        <input name="Tel" type="text" class="span5" id="Tel" placeholder="" data-mask="(999) 999-999-99-99">
                                        <span class="help-inline">(999) 999-999-99-99</span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label"><?php echo $dil['kayit_fax_no']; ?></label>
                                    <div class="controls">
                                        <input name="Fax" type="text" class="span5" id="Fax" placeholder="" data-mask="(999) 999-999-99-99">
                                        <span class="help-inline">(999) 999-999-99-99</span>
                                    </div>
                                </div>
                            



                        </div>
                  </div>
                         </div>               

                    
                                    <div class="span6">
                    <!-- BEGIN widget-->
                    <div class="widget">
                        <div class="widget-title">
                            <h4><i class="icon-reorder"></i><?php echo $dil['kayit_finans_bilgi']; ?></h4>

                        </div>
                        <div class="widget-body form">
                            <!-- BEGIN FORM-->
                          
						<div class="control-group">
                              <label class="control-label"><?php echo $dil['kayit_gecen_yil_ciro']; ?></label>
                              <div class="controls">
                                 <div class="input-prepend input-append">
                                    <span class="add-on">$</span><input name="GecenYilciro"" type="text" class="span8 popovers" id="GecenYilciro" data-trigger="hover" data-content="Geçen yıl ciro."><span class="add-on">.00</span>
                                 </div>
                              </div>
                         </div>
                              <label class="control-label">Ekipman yatırımı</label>
                              <div class="controls">
                                 <div class="input-prepend input-append">
                                    <span class="add-on">$</span><input name="EkipmanYatirim"" type="text" class="span8 popovers" id="EkipmanYatirim" data-trigger="hover" data-content="Ekipman yatırımı."><span class="add-on">.00</span>
                                 </div>
                              </div>
                              <label class="control-label"><?php echo $dil['kayit_i_orani']; ?></label>
                              <div class="controls">
                                 <div class="input-prepend input-append">
                                    <span class="add-on">%</span><input name="IhracatOran" type="text" class="span3 popovers" id="ihracatOran" data-trigger="hover" data-content="Yüzdelik olarak giriniz.">
                                 </div>
                              </div>   
            
                                
                          



                        </div>
                    </div>
                    
<div class="widget">
                        <div class="widget-title">
                           <h4><i class="icon-globe"></i><?php echo $dil['kayit_kontak_bilgi']; ?></h4>
                   
                        </div>
                        <div class="widget-body">
                            <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th width="140"></th>
                                    <th width="140" class="hidden-phone"><?php echo $dil['kayit_personel']; ?></th>
                                    <th width="140" class="hidden-phone"><?php echo $dil['kayit_tel']; ?></th>
                                    <th width="140" class="hidden-phone"><?php echo $dil['kayit_email']; ?></th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr class="odd gradeX">
                                  <td width="140"><?php echo $dil['kayit_hesap_yonetici']; ?> <span class="turuncu">*</span> </td>
                                    <td width="140" class="hidden-phone"><input name="HesapYoneticisiTitle" type="text" class="span10" id="HesapYoneticisiTitle"></td>
                                  <td width="140" class="center hidden-phone"><input name="HesapYoneticisiTel" type="text" class="span10" id="HesapYoneticisiTel"></td>
                                    <td width="140" class=" "><div class="input-icon left"><i class="icon-envelope"></i> <input placeholder="Email Address" name="HesapYoneticisiMail" type="text" class="span10" id="HesapYoneticisiMail" ></div></td>
                                </tr>
                                <tr class="odd gradeX">
                                  <td width="140"><?php echo $dil['kayit_sales']; ?></td>
                                    <td width="140" class="hidden-phone"><input name="salesMarketingTitle" type="text" class="span10" id="salesMarketingTitle"></td>
                                  <td width="140" class="center hidden-phone"><input name="salesMarketingTel" type="text" class="span10" id="salesMarketingTel"></td>
                                    <td width="140" class="hidden-phone"><div class="input-icon left"><i class="icon-envelope"></i> <input placeholder="Email Address" name="salesMarketingMail" type="text" class="span10" id="salesMarketingMail"></div></td>
                                </tr>
                                <tr class="odd gradeX">
                                  <td width="140"><?php echo $dil['kayit_rd']; ?></td>
                                    <td width="140" class="hidden-phone"><input name="RDTitle" type="text" class="span10" id="RDTitle"></td>
                                  <td width="140" class="center hidden-phone"><input name="RDTel" type="text" class="span10" id="RDTel"></td>
                                    <td width="140" class="hidden-phone"><div class="input-icon left"><i class="icon-envelope"></i> <input placeholder="Email Address"name="RDMail" type="text" class="span10" id="RDMail"></div></td>
                                </tr>
                                <tr class="odd gradeX">
                                  <td width="140"><?php echo $dil['kayit_logis']; ?></td>
                                    <td width="140" class="hidden-phone"><input name="LogisticsTitle" type="text" class="span10" id="LogisticsTitle"></td>
                                  <td width="140" class="center hidden-phone"><input name="LogisticsTel" type="text" class="span10" id="LogisticsTel"></td>
                                    <td width="140" class="hidden-phone"><div class="input-icon left"><i class="icon-envelope"></i> <input placeholder="Email Address" name="LogisticsMail" type="text" class="span10" id="LogisticsMail"></div></td>
                                </tr>
                                <tr class="odd gradeX">
                                  <td width="140"><?php echo $dil['kayit_purc']; ?></td>
                                    <td width="140" class="hidden-phone"><input name="PurchasingTitle" type="text" class="span10" id="PurchasingTitle"></td>
                                  <td width="140" class="center hidden-phone"><input name="PurchasingTel" type="text" class="span10" id="PurchasingTel"></td>
                                    <td width="140" class="hidden-phone"><div class="input-icon left"><i class="icon-envelope"></i> <input placeholder="Email Address" name="PurchasingMail" type="text" class="span10" id="PurchasingMail"></div></td>
                                </tr>
                                <tr class="odd gradeX">
                                  <td width="140"><?php echo $dil['kayit_kalite']; ?></td>
                                    <td width="140" class="hidden-phone"><input name="qualityTitle" type="text" class="span10" id="qualityTitle"></td>
                                  <td width="140" class="center hidden-phone"><input name="qualityTel" type="text" class="span10" id="qualityTel"></td>
                                    <td width="140" class="hidden-phone"><div class="input-icon left"><i class="icon-envelope"></i> <input placeholder="Email Address" name="qualityMail" type="text" class="span10" id="qualityMail"></div></td>
                                <input type="hidden" name="seviyeID" id="seviyeID" value="2">
                                <input type="hidden" name="bastarih" id="bastarih" value="<?php echo $tarih; ?>">
                                <input type="hidden" name="bittarih" id="bittarih" value="<?php echo date('Y/m/d',strtotime($tarih." +1 months"));?>">
                                </tr>
                              </tbody>
                        </table>
                </div>
              </div>




                  <div class="form-actions">
                    <button type="submit"  class="btn btn-success"><?php echo $dil['kaydet']; ?></button>
                    <button type="button" class="btn"><?php echo $dil['iptal']; ?></button>
                  </div>
</div>
                        </div>
</div>
               <input type="hidden" name="MM_insert" value="kategori">
            

           </form>         
                    
                    
                    
<!-- END CONTAINER -->
   <!-- BEGIN FOOTER -->
   <div id="footer">
       2016 Qhubi.com
         <div class="span pull-right">
         <span class="go-top"><i class="icon-arrow-up"></i></span>
      </div>

   <!-- END FOOTER -->
   <!-- BEGIN JAVASCRIPTS -->    
   <!-- Load javascripts at bottom, this will reduce page load time -->
   
   
   <script language="JavaScript">
function check_frmm(){

if (document.kategori.uyeAdi.value == ""){
alert ("<?php echo $dil['kayit_kadi']; ?><?php echo $dil['eksik']; ?>");
document.kategori.uyeAdi.focus();
return false; 
}
if (document.kategori.Kategori.value == ""){
alert ("<?php echo $dil['kayit_kategori']; ?><?php echo $dil['eksik']; ?>");
document.kategori.Kategori.focus();
return false; 
}
if (document.kategori.Parola.value == ""){
alert ("<?php echo $dil['kayit_sifre']; ?><?php echo $dil['eksik']; ?>");
document.kategori.Parola.focus();
return false; 
}
if (document.kategori.sirketAdi.value == ""){
alert ("<?php echo $dil['sirketadi']; ?><?php echo $dil['eksik']; ?>");
document.kategori.sirketAdi.focus();
return false; 
}
if (document.kategori.Ulke.value == ""){
alert ("<?php echo $dil['kayit_ulke']; ?><?php echo $dil['eksik']; ?>");
document.kategori.Ulke.focus();
return false; 
}
if (document.kategori.sehir.value == ""){
alert ("<?php echo $dil['kayit_sehir']; ?><?php echo $dil['eksik']; ?>");
document.kategori.sehir.focus();
return false; 
}
if (document.kategori.Adres.value == ""){
alert ("<?php echo $dil['kayit_adres']; ?><?php echo $dil['eksik']; ?>");
document.kategori.Adres.focus();
return false; 
}
if (document.kategori.Tel.value == ""){
alert ("<?php echo $dil['kayit_tel']; ?><?php echo $dil['eksik']; ?>");
document.kategori.Tel.focus();
return false; 
}
if (document.kategori.HesapYoneticisiTitle.value == ""){
alert ("<?php echo $dil['kayit_hesap_yonetici']; ?><?php echo $dil['kayit_personel']; ?><?php echo $dil['eksik']; ?>");
document.kategori.HesapYoneticisiTitle.focus();
return false; 
}
if (document.kategori.HesapYoneticisiTel.value == ""){
alert ("<?php echo $dil['kayit_hesap_yonetici']; ?><?php echo $dil['kayit_tel']; ?><?php echo $dil['eksik']; ?>");
document.kategori.HesapYoneticisiTel.focus();
return false; 
}
if (document.kategori.HesapYoneticisiMail.value ==0 )
  {
    alert("<?php echo $dil['kayit_hesap_yonetici']; ?><?php echo $dil['kayit_email']; ?><?php echo $dil['eksik']; ?>");
    document.kategori.HesapYoneticisiMail.focus();
    return (false);
  }    
    if    ((document.kategori.HesapYoneticisiMail.value.indexOf("@"))<1)
    {
    alert("<?php echo $dil['kayit_hesap_yonetici']; ?><?php echo $dil['kayit_email']; ?><?php echo $dil['eksik']; ?>");
    document.kategori.HesapYoneticisiMail.focus();
    return false;
    }
    if    ((document.kategori.HesapYoneticisiMail.value.lastIndexOf("."))-(document.kategori.HesapYoneticisiMail.value.indexOf("@"))<2)
    {
    alert("<?php echo $dil['kayit_hesap_yonetici']; ?><?php echo $dil['kayit_email']; ?><?php echo $dil['eksik']; ?>");
    document.kategori.HesapYoneticisiMail.focus();
    return false;
    }

}
</script>
   
   
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">

$(document).ready(function(){

$("#uyeAdi").change(function() { 

var usr = $("#uyeAdi").val();

if(usr.length >= 3)
{
$("#status").html('<img align="absmiddle" src="loader.gif" /> Kontrol ediliyor...');

$.ajax({ 
type: "POST", 
url: "kontrol.php", 
data: "uyeAdi="+ usr, 
success: function(msg){ 

$("#status").ajaxComplete(function(event, request, settings){ 

if(msg == 'OK')
{ 
$(this).html(' <font color="green"><?php echo $dil['userhata1']; ?></font> ');
} 
else 
{ 
$(this).html(msg);
}});}});}
else
{
$("#status").html('<?php echo $dil['userhata2']; ?>');
}});});

//-->

</script>
<script src="admin/js/jquery-1.8.3.js"></script>
<script src="admin/assets/bootstrap/js/bootstrap.js"></script>

<script src="admin/js/jquery.js"></script>
<!-- ie8 fixes -->
   <!--[if lt IE 9]>

   <![endif]-->
   <script type= "text/javascript" src = "admin/country_dropdown/countries3.js"></script>
        <script language="javascript">print_country("country");</script>	
        


<script src="web/layout/scripts/jquery.backtotop.js"></script> 
<script src="web/layout/scripts/jquery.mobilemenu.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.7.5/js/bootstrap-select.min.js"></script>
   <script type="text/javascript" src="admin/assets/chosen-bootstrap/chosen/chosen.jquery.js"></script>
   <script type="text/javascript" src="admin/assets/uniform/jquery.uniform.js"></script>
   <script type="text/javascript" src="admin/assets/bootstrap-wysihtml5/wysihtml5-0.3.js"></script> 
   <script type="text/javascript" src="admin/assets/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>
   <script type="text/javascript" src="admin/assets/clockface/js/clockface.js"></script>
   <script type="text/javascript" src="admin/assets/jquery-tags-input/jquery.tagsinput.js"></script>
   <script type="text/javascript" src="admin/assets/bootstrap-toggle-buttons/static/js/jquery.toggle.js"></script>
   <script type="text/javascript" src="admin/assets/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>   
   <script type="text/javascript" src="admin/assets/bootstrap-daterangepicker/date.js"></script>
   <script type="text/javascript" src="admin/assets/bootstrap-daterangepicker/daterangepicker.js"></script> 
   <script type="text/javascript" src="admin/assets/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>  
   <script type="text/javascript" src="admin/assets/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
   <script type="text/javascript" src="admin/assets/bootstrap-inputmask/bootstrap-inputmask.js"></script>
   <script type="text/javascript" src="admin/assets/data-tables/jquery.js"></script>
   <script type="text/javascript" src="admin/assets/data-tables/DT_bootstrap.js"></script>
   
   <script src="admin/assets/jquery-ui/jquery-ui-1.10.1.custom.js" type="text/javascript"></script>

    <script src="admin/assets/jqvmap/jqvmap/jquery.js" type="text/javascript"></script>
	<script src="admin/assets/jqvmap/jqvmap/maps/jquery.vmap.js" type="text/javascript"></script>
	<script src="admin/assets/jqvmap/jqvmap/maps/jquery.vmap (1).js" type="text/javascript"></script>
	<script src="admin/assets/jqvmap/jqvmap/maps/jquery.vmap (2).js" type="text/javascript"></script>
	<script src="admin/assets/jqvmap/jqvmap/maps/jquery.vmap (3).js" type="text/javascript"></script>
	<script src="admin/assets/jqvmap/jqvmap/maps/jquery.vmap (4).js" type="text/javascript"></script>
	<script src="admin/assets/jqvmap/jqvmap/data/jquery.vmap.js" type="text/javascript"></script>
	<script src="admin/assets/jquery-knob/js/jquery.js"></script>
	<script src="admin/assets/flot/jquery.js"></script>
	<script src="admin/assets/flot/jquery.flot.js"></script>

    <script src="admin/assets/flot/jquery.flot (1).js"></script>
    <script src="admin/assets/flot/jquery.flot (2).js"></script>
    <script src="admin/assets/flot/jquery.flot (3).js"></script>

	<script src="admin/js/jquery.peity.js"></script>
	<script type="text/javascript" src="admin/assets/uniform/jquery.uniform.js"></script>
    <script src="admin/js/scripts.js"></script>
    <script src="admin/js/ui-jqueryui.js"></script>
   <script>
      jQuery(document).ready(function() {       
         // initiate layout and plugins
         App.init();
		 UIJQueryUI.init();
      });
   </script>
   	<script>
		jQuery(document).ready(function() {
			// initiate layout and plugins
			App.setMainPage(true);
			App.init();
		});
	</script>
    <script type="text/javascript">
    //<![CDATA[
        $(window).load(function() { // makes sure the whole site is loaded
            $('#status1').fadeOut(); // will first fade out the loading animation
            $('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
            $('body').delay(350).css({'overflow':'visible'});
        })
    //]]>
</script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-103837152-1', 'auto');
  ga('send', 'pageview');

</script>
   <!-- END JAVASCRIPTS -->   
</body>
<!-- END BODY -->
</html>
