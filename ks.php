<?php require_once('Connections/baglan.php'); ?>
<?php require_once('fonksiyon.php'); ?>
<?php

function tirnak_replace ($par)
{
	return str_replace(
		array(
			"'", "\""
			),
		array(
			"&#39;", "&quot;"
		),
		$par
	);
}
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$maxRows_duyuru = 2;
$pageNum_duyuru = 0;
if (isset($_GET['pageNum_duyuru'])) {
  $pageNum_duyuru = $_GET['pageNum_duyuru'];
}
$startRow_duyuru = $pageNum_duyuru * $maxRows_duyuru;

mysql_select_db($database_baglan, $baglan);
$query_duyuru = "SELECT * FROM duyuru WHERE duyuru.durum = '1' ORDER BY duyuruID DESC";
$query_limit_duyuru = sprintf("%s LIMIT %d, %d", $query_duyuru, $startRow_duyuru, $maxRows_duyuru);
$duyuru = mysql_query($query_limit_duyuru, $baglan) or die(mysql_error());
$row_duyuru = mysql_fetch_assoc($duyuru);

if (isset($_GET['totalRows_duyuru'])) {
  $totalRows_duyuru = $_GET['totalRows_duyuru'];
} else {
  $all_duyuru = mysql_query($query_duyuru);
  $totalRows_duyuru = mysql_num_rows($all_duyuru);
}
$totalPages_duyuru = ceil($totalRows_duyuru/$maxRows_duyuru)-1;

mysql_select_db($database_baglan, $baglan);
$query_siteayar = "SELECT * FROM siteconfig";
$siteayar = mysql_query($query_siteayar, $baglan) or die(mysql_error());
$row_siteayar = mysql_fetch_assoc($siteayar);
$totalRows_siteayar = mysql_num_rows($siteayar);
?>




<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>    
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-130647668-1"></script>
    <script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-130647668-1');
    </script>

    <meta charset="UTF-8" />
<title><?php echo $row_siteayar['SiteTitle']; ?></title>
<meta content="<?php echo $row_siteayar['Metadesc']; ?>" name="description">
  <meta content="<?php echo $row_siteayar['MetaName']; ?>" name="keywords">

    <!-- mobile responsive meta -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" sizes="57x57" href="asset/img/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="asset/img/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="asset/img/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="asset/img/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="asset/img/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="asset/img/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="asset/img/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="asset/img/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="asset/img/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="asset/img/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="asset/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="asset/img/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="asset/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="asset/img/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" href="asset/css/styled134.css?v=3.4">
    <link rel="stylesheet" href="asset/css/responsived134.css?v=3.4">
    <link rel="stylesheet" href="asset/css/qhubi134.css?v=3.4">
</head>

<?php
# Session başlat
session_start();
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

<body class="active-preloader-ovh home-page-two">



    <div class="preloader thm-gradient-two"><div class="spinner"></div></div> <!-- /.preloader -->

    <header class="header header-home-two header-home-two-inner">
      <nav class="navbar navbar-default header-navigation stricky">
        <div class="thm-container clearfix">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed mixup-icon-menu" data-toggle="collapse" data-target=".main-navigation" aria-expanded="false"> </button>
            <a class="navbar-brand" href="index.php#anasayfa"> 						<img src="admin/resim/logo/<?php echo $row_siteayar['Sitelogo']; ?>" alt="qhubi_banner"> </a> </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse main-navigation mainmenu " id="main-nav-bar">
                    <ul class="nav navbar-nav navigation-box fr">
                        
                        <li> <a href="index.php#ozellikler"><?php echo $dil['avantaj']; ?></a> </li>
                        <li> <a href="kk.php"><?php echo $dil['sss']; ?></a></li>
                        <li> <a href="index.php#kimler-kullanmali"><?php echo $dil['kp']; ?></a> </li>
                        <li> <a href="index.php#iletisim"><?php echo $dil['iletisim']; ?></a> </li>
						<li> <a href="hakkimizda.php"><?php echo $dil['hakkimizda']; ?></a> </li>
                        <li class="btn-demo"> <a href="kayit.php" class="btn-demo"><?php echo $dil['kayit_ol']; ?></a> </li>
                        <li class="btn-login"> <a href="http://www.qhubi.com/admin"  class="btn-login"><?php echo $dil['giris']; ?></a> </li>
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
          </div>
          <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
      </nav>
    </header>
    <!-- /.header -->

    <div class="inner-banner">
        <div class="thm-container clearfix">
            <div class="pull-left">
                <h3><b>Qhubi</b>--<?php echo $dil["ks"]; ?></h3>
            </div>
        </div>
    </div>

    <section class="contact-page-content grow-style-two padding-bottom-120 gray-bg-1">
        <div class="thm-container">
            <div class="row">

                <div class="col-md-12">
       
                       
                            
                     <div>	
                            
							
							<?php
if ($_SESSION['dil'] == "en")  {
    echo'<ol>
	<li>
		<strong>
			 Introduction
		</strong>
	</li>
</ol>
<p>
	<strong>
		 1.1 Contract
	</strong>
</p>
<p>
	You agree that by clicking &ldquo;Register&rdquo;, &ldquo;Join Qhubi&rdquo;, &ldquo;Sign Up&rdquo; or similar, registering, accessing or using our services (described below),
	<strong>
		 you are agreeing to enter into a legally binding contract
	</strong>
	 with Qhubi (even if you are using our Services on behalf of a company). If you do not agree to this contract (&ldquo;Contract&rdquo; or &ldquo;User Agreement&rdquo;), do
	<strong>
		 not
	</strong>
	 click &ldquo;Join Now&rdquo; (or similar) and do not access or otherwise use any of our Services. If you wish to terminate this contract, at any time you can do so by closing your account and no longer accessing or using our Services.
</p>
<p>
	<strong>
		 Services
	</strong>
</p>
<p>
	This Contract applies to www.qhubi.com that states that they are offered under this Contract (&ldquo;Services&rdquo;), including the offsite collection of data for those Services. Registered users of our Services are &ldquo;Members&rdquo; and unregistered users are &ldquo;Visitors&rdquo;. This Contract applies to both Members and Visitors.
</p>
<p>
	<strong>
		 Qhubi
	</strong>
</p>
<p>
	You are entering into this Contract with Qhubi (also referred to as &ldquo;we&rdquo; and &ldquo;us&rdquo;).
</p>
<p>
	We use the term &ldquo;Designated Countries&rdquo; to refer to countries in the European Union (EU), European Economic Area (EEA), and Switzerland.
</p>
<p>
	As a Visitor or Member of our Services, the collection, use and sharing of your personal data is subject to this Privacy Policy (which includes our Cookie Policy and other documents referenced in this Privacy Policy) and updates.
</p>
<h3>
	1.2 Members and Visitors
</h3>
<p>
	When you register and join the Qhubi Service, you become a Member. If you have chosen not to register for our Services, you may access certain features as a &ldquo;Visitor.&rdquo;
</p>
<h3>
	1.3 Change
</h3>
<p>
	We may modify this Contract, our Privacy Policy and our Cookies Policies from time to time. If we make material changes to it, we will provide you notice through our Services, or by other means, to provide you the opportunity to review the changes before they become effective. We agree that changes cannot be retroactive. If you object to any changes, you may close your account. Your continued use of our Services after we publish or send a notice about our changes to these terms means that you are consenting to the updated terms.
</p>
<p>
	&nbsp;
</p>
<p>
	&nbsp;
</p>
<p>
	&nbsp;
</p>
<ol>
	<li>
		<strong>
			 Rights and Limits
		</strong>
	</li>
</ol>
<p>
	<strong>
		 2.1. Your License to Qhubi
	</strong>
</p>
<p>
	As between you and Qhubi, you own the content and information that you submit or post to the Services, and you are only granting Qhubi and our
	<em>
		 affiliates
	</em>
	 the following non-exclusive license:
</p>
<p>
	A worldwide, transferable and sublicensable right to use, copy, modify, distribute, publish, and process, information and content that you provide through our Services and the services of others, without any further consent, notice and/or compensation to you or others. These rights are limited in the following ways:
</p>
<ol>
	<li>
		You can end this license for specific content by deleting such content from the Services, or generally by closing your account, except (a) to the extent you shared it with others as part of the Service and they copied, re-shared it or stored it and (b) for the reasonable time it takes to remove from backup and other systems.
	</li>
	<li>
		We will not include your content in advertisements for the products and services of third parties to others without your separate consent (including sponsored content). However, we have the right, without payment to you or others, to serve ads near your content and information, and your
		<em>
			 social actions
		</em>
		 may be visible and included with ads.
	</li>
	<li>
		We will get your consent if we want to give others the right to publish your content beyond the Services.
	</li>
</ol>
<p>
	You and Qhubi agree that we may access, store, process and use any information and personal data that you provide in accordance with the terms of the Privacy Policy and your choices (including settings).
</p>
<p>
	By submitting suggestions or other feedback regarding our Services to Qhubi, you agree that Qhubi can use and share (but does not have to) such feedback for any purpose without compensation to you.
</p>
<p>
	You agree to only provide content or information that does not violate the law nor anyone&rsquo;s rights (including intellectual property rights). You also agree that your profile information will be truthful. Qhubi may be required by law to remove certain information or content in certain countries.
</p>
<p>
	&nbsp;
</p>
<p>
	<strong>
		 2.2 Service Availability
	</strong>
</p>
<p>
	We may change or discontinue any of our Services. We don&rsquo;t promise to store or keep showing any information and content that you&rsquo;ve posted.
</p>
<p>
	Qhubi is not a storage service. You agree that we have no obligation to store, maintain or provide you a copy of any content or information that you or others provide, except to the extent required by applicable law.
</p>
<p>
	<strong>
		 2.3 Other Content, Sites and Apps
	</strong>
</p>
<p>
	By using the Services, you may encounter content or information that might be inaccurate, incomplete, delayed, misleading, illegal, offensive or otherwise harmful. Qhubi generally does not review content provided by our Members or others. You agree that we are not responsible for others&rsquo; (including other Members&rsquo;) content or information. We cannot always prevent this misuse of our Services, and you agree that we are not responsible for any such misuse.
</p>
<p>
	<strong>
		 2.4 Limits
	</strong>
</p>
<p>
	Qhubi reserves the right to limit your use of the Services. Qhubi reserves the right to restrict, suspend, or terminate your account if Qhubi believes that you may be in breach of this Contract or law or are misusing the Services.
</p>
<p>
	<strong>
		 2.5 Intellectual Property Rights
	</strong>
</p>
<p>
	Qhubi reserves all of its intellectual property rights in the Services. Using the Services does not give you any ownership in our Services or the content or information made available through our Services. Trademarks and logos used in connection with the Services are the trademarks of their respective owners. Qhubi and other Qhubi trademarks, service marks, graphics, and logos used for our Services are trademarks or registered trademarks of Qhubi.
</p>
<h2>
	3. Obligations
</h2>
<h3>
	3.1 Service Eligibility
</h3>
<p>
	The Services are not for use by anyone under the age of 18.
</p>
<p>
	To use the Services, you agree that: (1) you must be the &ldquo;Minimum Age&rdquo; (described below) or older; (2) you will only have one Qhubi account, which must be in your real name; and (3) you are not already restricted by Qhubi from using the Services. Creating an account with false information is a violation of our terms, including accounts registered on behalf of others or persons under the age of 18.
</p>
<p>
	&ldquo;Minimum Age&rdquo; means 18 years old. However, if law requires that you must be older in order for Qhubi to lawfully provide the Services to you without parental consent (including using of your personal data) then the Minimum Age is such older age.
</p>
<h3>
	3.2 Your Account
</h3>
<p>
	Members are account holders. You agree to: (1) try to choose a strong and secure password; (2) keep your password secure and confidential; (3) not transfer any part of your account (e.g., connections) and (4) follow the law and our list of Dos and Don&rsquo;ts. You are responsible for anything that happens through your account unless you close it or report misuse.
</p>
<p>
	As between you and others (including your employer), your account belongs to you.
</p>
<h3>
	3.3 Payment
</h3>
<p>
	If you buy any of our paid Services (&ldquo;Premium Services&rdquo;), you agree to pay us the applicable fees and taxes and to additional terms specific to the paid Services. Failure to pay these fees will result in the termination of your paid Services. Also, you agree that:
</p>
<ul>
	<li>
		Your purchase may be subject to foreign exchange fees or differences in prices based on location (e.g. exchange rates).
	</li>
	<li>
		We may store and continue billing your payment method (e.g. credit card) even after it has expired, to avoid interruptions in your Services and to use to pay other Services you may buy.
	</li>
	<li>
		If you purchase a subscription, your payment method automatically will be charged at the start of each subscription period for the fees and taxes applicable to that period. To avoid future charges, cancel before the renewal date. Learn how to cancel or suspend your Premium Services.
	</li>
	<li>
		All of your purchases of Services are subject to Qhubi&rsquo;s refund policy.
	</li>
	<li>
		We may calculate taxes payable by you based on the billing information that you provide us at the time of purchase.
	</li>
</ul>
<h3>
	3.4 Notices and Messages
</h3>
<p>
	You agree that we will provide notices and messages to you in the following ways: (1) within the Service, or (2) sent to the contact information you provided us (e.g., email, mobile number, physical address). You agree to keep your contact information up to date.
</p>
<p>
	Please review your settings to control and limit messages you receive from us.
</p>
<h3>
	3.5 Sharing
</h3>
<p>
	Our Services allow messaging and sharing of information in many ways, such as your profile and blogs. Information and content that you share or post may be seen by other Members, Visitors or others (including off of the Services).
</p>
<p>
	We are not obligated to publish any information or content on our Service and can remove it in our sole discretion, with or without notice.
</p>
<h2>
	4. Disclaimer and Limit of Liability
</h2>
<h3>
	4.1 No Warranty
</h3>
<p>
	TO THE EXTENT ALLOWED UNDER LAW, QHUBI AND ITS AFFILIATES (AND THOSE THAT QHUBI WORKS WITH TO PROVIDE THE SERVICES) (A) DISCLAIM ALL IMPLIED WARRANTIES AND REPRESENTATIONS (E.G. WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, ACCURACY OF DATA, AND NONINFRINGEMENT); (B) DO NOT GUARANTEE THAT THE SERVICES WILL FUNCTION WITHOUT INTERRUPTION OR ERRORS, AND (C) PROVIDE THE SERVICE (INCLUDING CONTENT AND INFORMATION) ON AN &ldquo;AS IS&rdquo; AND &ldquo;AS AVAILABLE&rdquo; BASIS.
</p>
<p>
	SOME LAWS DO NOT ALLOW CERTAIN DISCLAIMERS, SO SOME OR ALL OF THESE DISCLAIMERS MAY NOT APPLY TO YOU.
</p>
<p>
	&nbsp;
</p>
<h3>
	4.2 Exclusion of Liability
</h3>
<p>
	TO THE EXTENT PERMITTED UNDER LAW (AND UNLESS QHUBI HAS ENTERED INTO A SEPARATE WRITTEN AGREEMENT THAT OVERRIDES THIS CONTRACT), QHUBI AND ITS AFFILIATES (AND THOSE THAT QHUBI WORKS WITH TO PROVIDE THE SERVICES) SHALL NOT BE LIABLE TO YOU OR OTHERS FOR ANY INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL OR PUNITIVE DAMAGES, OR ANY LOSS OF DATA, OPPORTUNITIES, REPUTATION, PROFITS OR REVENUES, RELATED TO THE SERVICES (E.G. OFFENSIVE OR DEFAMATORY STATEMENTS, DOWN TIME OR LOSS, USE OF, OR CHANGES TO, YOUR INFORMATION OR CONTENT).
</p>
<p>
	THIS LIMITATION OF LIABILITY IS PART OF THE BASIS OF THE BARGAIN BETWEEN YOU AND QHUBI AND SHALL APPLY TO ALL CLAIMS OF LIABILITY (E.G. WARRANTY, TORT, NEGLIGENCE, CONTRACT, LAW) AND EVEN IF QHUBI OR ITS AFFILIATES HAS BEEN TOLD OF THE POSSIBILITY OF ANY SUCH DAMAGE, AND EVEN IF THESE REMEDIES FAIL THEIR ESSENTIAL PURPOSE.
</p>
<p>
	SOME LAWS DO NOT ALLOW THE LIMITATION OR EXCLUSION OF LIABILITY, SO THESE LIMITS MAY NOT APPLY TO YOU.
</p>
<ol>
	<li>
		<strong>
			 Termination
		</strong>
	</li>
</ol>
<p>
	Both you and Qhubi may terminate this Contract at any time with notice to the other. On termination, you lose the right to access or use the Services. The following shall survive termination:
</p>
<ul>
	<li>
		Our rights to use and disclose your feedback;
	</li>
	<li>
		Members and/or Visitors&rsquo; rights to further re-share content and information you shared through the Service to the extent copied or re-shared prior to termination;
	</li>
	<li>
		Sections 4, 6, 7, and 8.2 of this Contract;
	</li>
	<li>
		Any amounts owed by either party prior to termination remain owed after termination.
	</li>
</ul>
<h2>
	6. General Terms
</h2>
<p>
	If a court with authority over this Contract finds any part of it unenforceable, you and we agree that the court should modify the terms to make that part enforceable while still achieving its intent. If the court cannot do that, you and we agree to ask the court to remove that unenforceable part and still enforce the rest of this Contract.
</p>
<p>
	To the extent allowed by law, the English language version of this Contract is binding and other translations are for convenience only. This Contract (including additional terms that may be provided by us when you engage with a feature of the Services) is the only agreement between us regarding the Services and supersedes all prior agreements for the Services.
</p>
<p>
	If we don^t act to enforce a breach of this Contract, that does not mean that Qhubi has waived its right to enforce this Contract. You may not assign or transfer this Contract (or your membership or use of Services) to anyone without our consent. However, you agree that Qhubi may assign this Contract to its affiliates or a party that buys it without your consent. There are no third-party beneficiaries to this Contract.
</p>
<p>
	You agree that the only way to provide us legal notice is at the addresses provided in Section 10.
</p>
<ol>
	<li>
		<strong>
			 Qhubi &ldquo;Dos and Don&rsquo;ts&rdquo;
		</strong>
	</li>
</ol>
<p>
	<strong>
		 7.1. Dos
	</strong>
</p>
<p>
	<strong>
		 You agree that you will:
	</strong>
</p>
<ol>
	<li>
		Comply with all applicable laws, including, without limitation, privacy laws, intellectual property laws, anti-spam laws, export control laws, tax laws, and regulatory requirements;
	</li>
	<li>
		Provide accurate information to us and keep it updated;
	</li>
	<li>
		Use your real name on your profile; and
	</li>
	<li>
		Use the Services in a professional manner.
	</li>
</ol>
<p>
	<strong>
		 7.2. Don&rsquo;ts
	</strong>
</p>
<p>
	<strong>
		 You agree that you will
		<em>
			 not
		</em>
		 :
	</strong>
</p>
<ol>
	<li>
		Create a false identity on Qhubi, misrepresent your identity, create a Member profile for anyone other than yourself (a real person), or use or attempt to use another&rsquo;s account;
	</li>
	<li>
		Develop, support or use software, devices, scripts, robots, or any other means or processes (including crawlers, browser plugins and add-ons, or any other technology) to scrape the Services or otherwise copy profiles and other data from the Services;
	</li>
	<li>
		Override any security feature or bypass or circumvent any access controls or use limits of the Service (such as caps on keyword searches or profile views);
	</li>
	<li>
		Copy, use, disclose or distribute any information obtained from the Services, whether directly or through third parties (such as search engines), without the consent of Qhubi;
	</li>
	<li>
		Disclose information that you do not have the consent to disclose (such as confidential information of others (including your employer));
	</li>
	<li>
		Violate the intellectual property rights of others, including copyrights, patents, trademarks, trade secrets, or other proprietary rights. For example, do not copy or distribute (except through the available sharing functionality) the posts or other content of others without their permission, which they may give by posting under a Creative Commons license;
	</li>
	<li>
		Violate the intellectual property or other rights of Qhubi, including, without limitation, (i) copying or distributing our learning videos or other materials or (ii) copying or distributing our technology, unless it is released under open source licenses; (iii) using the word &ldquo;Qhubi&rdquo; or our logos in any business name, email, or URL.
	</li>
	<li>
		Post anything that contains software viruses, worms, or any other harmful code;
	</li>
	<li>
		Reverse engineer, decompile, disassemble, decipher or otherwise attempt to derive the source code for the Services or any related technology that is not open source;
	</li>
	<li>
		Imply or state that you are affiliated with or endorsed by Qhubi without our express consent (e.g., representing yourself as an accredited Qhubi trainer);
	</li>
	<li>
		Rent, lease, loan, trade, sell/re-sell or otherwise monetize the Services or related data or access to the same, without Qhubi&rsquo;s consent;
	</li>
	<li>
		Deep-link to our Services for any purpose other than to promote your profile or a Group on our Services, without Qhubi&rsquo;s consent;
	</li>
	<li>
		Use bots or other automated methods to access the Services, add or download contacts, send or redirect messages;
	</li>
	<li>
		Monitor the Services&rsquo; availability, performance or functionality for any competitive purpose;
	</li>
	<li>
		Engage in &ldquo;framing,&rdquo; &ldquo;mirroring,&rdquo; or otherwise simulating the appearance or function of the Services;
	</li>
	<li>
		Overlay or otherwise modify the Services or their appearance (such as by inserting elements into the Services or removing, covering, or obscuring an advertisement included on the Services);
	</li>
	<li>
		Interfere with the operation of, or place an unreasonable load on, the Services (e.g., spam, denial of service attack, viruses, gaming algorithms); and/or
	</li>
</ol>
<h2>
	8. Complaints Regarding Content
</h2>
<p>
	We respect the intellectual property rights of others. We require that information posted by Members be accurate and not in violation of the intellectual property rights or other rights of third parties. We provide a policy and process for complaints concerning content posted by our Members.
</p>
<h2>
	9. How To Contact Us
</h2>
<p>
	If you want to send us notices or service of process, please contact us.
</p>
<p>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</p>
';
} elseif ($_SESSION['dil'] == "tr")  {
    echo '<p><ul type="disc">
        <b>1. GİRİŞ</b></ul>
    </p>
    <p>
        1.1. EAS Yazılım Bilişim ve Danışmanlık Limited Şirketi (“QHUBI”),
        alıcı şirket (“Alıcı”) ile tedarikçiler (“Tedarikçi”) arasında,
        işletmeler arası web tabanlı ve güvenli satın alma faaliyetlerini ve
        iletişimlerini içeren e-satınalma platformunu (“Platform”) hizmete
        sunmaktadır.
    </p>
    <p>
        1.2. Platform, Tedarikçiler’in, mal ve hizmet tedariki hakkında fiyat
        teklifi talebinde bulunmasına bağlı olarak Fiyat Talebi (“RFQ”) ve
        çeşitli e-ihale (“E-İhale”) etkinliklerini içermektedir. Alıcı,
        Tedarikçi^nin söz konusu etkinliklere katılımı için güvenli platform
        üzerinden istek göndermektedir. Tedarikçi sistem üzerinden gelen istek
        üzerine teklif (“Teklif”) göndererek çevrimiçi satınalma ve ihale
        sürecine (“Süreç”) katılım sağlayabilmektedir.
    </p>
    <p>
        Alıcı ve Tedarikçi, Platform^a kayıt suretiyle işbu Kullanıcı
        Sözleşmesi ("Sözleşme") ile bağlı olduğunu kabul eder. İşbu Sözleşme
        kapsamında QHUBI&#39;e bilgi veren bütün çalışanların, Alıcı ve
        Tedarikçi&#39;yi temsil etmek amacıyla yetkiye sahip olması gerekmektedir.
    </p>
    <p>
        <b>2. HİZMET KULLANIMI</b>
    </p>
    <p>
        2.1. Tek tip hizmet modeli vardır. Model detayı aşağıda belirtildiği
        gibidir:
    </p>
    <p>
        (a) Üyelik Paketi: Alıcı, kullanım sıklığı ve diğer gereksinimleri
        doğrultusunda aylık ve yıllık olarak sunulan üyelik paketlerinden
        seçecektir. Üyelik paketi modelinde Alıcı, kendi isteğine bağlı olarak
        üyelik paketini yükseltebilecek veya düşürebilecektir.
    </p>
    <p>
        2.2. Hizmet modelinde de karar ve politikaları belirleyen Alıcı’dır.
        Alıcı kullanıcılar, Süreç detaylarına ve Süreçler’in yönetimine
        kendileri karar verir. QHUBI, Platform sağlayıcı olarak hareket eder ve
        iletişimlerden, sözleşmelerden ve Alıcı ve Tedarikçi arasındaki işten
        sorumlu değildir.
    </p>
    <p>
        2.3. Süreçler’in devamlılığı QHUBI&#39;in sorumluluğunun dışındadır.
        Süreç’i oluşturan Alıcı, Süreç’ten sorumludur. Süreçler’den kaynaklanan
        üçüncü tarafın tüm iddiaları için QHUBI, Alıcı tarafından tazmin
        edilecektir.
    </p>
    <p>
        2.4. Alıcı, QHUBI’in kurumsal web sitesindeki (https://www.Qhubi.com)
        kayıt formunu doldurarak Platform&#39;a kayıt olur. Alıcı, kayıt formunda
        talep edilen bilgileri tam ve doğru olarak sağlayacağını ve bu
        bilgileri güncel tutacağını taahhüt eder.
    </p>
    <p>
        2.5. Tedarikçiler, Alıcı tarafından Platform&#39;da tayin edilen mallar ve
        hizmetler için Teklif sunar. Alıcı, Süreç’in hüküm ve şartlarını
        belirler ve Platform&#39;a iştirak eden Tedarikçiler bu hüküm ve şartları
        üstlendiklerini kabul eder.
    </p>
    <p>
        2.6. Alıcı, Satınalma talebini yeniden açma, sona erdirme ya da iptal
        etme hakkını saklı tutar. Eğer herhangi bir Tedarikçi Teklif’ini geri
        çekmek isterse veya Süreç’ten geri çekilmek isterse Tedarikçi Alıcı ile
        iletişime geçmelidir. Alıcı, Teklif’i iptal etme ya da Tedarikçi’yi
        Süreç’ten çıkarma hakkına sahiptir.
    </p>
    <p>
        2.7. Alıcılar&#39;ın ve Tedarikçiler&#39;in kullanıcıları kişisel kullanıcı adı
        ve parolalarını güvenli ve gizli tutacaklarını taahhüt ederler. Bir
        kuruluşun bünyesinde veya dışında oturum açma detaylarının paylaşımı
        kesin suretle yasaklanmıştır ve bu tüm taraflar için önemli risklere
        yol açar. Oturum açma bilgisinin gizliliğinin ihlalinden doğan bütün
        zararlar bu oturum açma bilgisinden sorumlu taraf tarafından tazmin
        edilir.
    </p>
    <p>
        2.8. QHUBI, Tedarikçiler nezdinde mal ve hizmetlerin tedarikinden
        sorumlu değildir. QHUBI, Alıcı ve Tedarikçiler’in arasındaki herhangi
        bir ticari şartın ya da sözleşmenin tarafı değildir. Ek olarak, Alıcı,
        Tedarikçiler’in seçimini, onaylanmasını ve denetlemesini üstlenir.
    </p>
    <p>
        2.9. Alıcı, Teklif’leri fiyata, performansa ve kalite kriterlerine göre
        değerlendirecektir. Alıcı en düşük Teklif’i veren Tedarikçi’yle
        anlaşmak zorunda değildir.
    </p>
    <p>
        2.10. QHUBI muhtelif zamanlarda ihbarsız ve kendi takdirinde işbu
        Sözleşme’yi güncelleme ve değiştirme hakkını saklı tutar. Alıcı ve
        Tedarikçiler değişiklikler için QHUBI&#39;in kurumsal web sitesi olan
        (https://www.Qhubi.com) kontrol etmek zorundadırlar.
    </p>
    <p>
        2.11. QHUBI Platform&#39;a kesintisiz erişim için çalışmaktadır. Ancak
        internetin doğası gereği her an Platform&#39;a erişim askıya alınabilir,
        sınırlandırılabilir ya da sona erdirilebilir. Alıcı ya da Tedarikçi
        kullanıcılarının QHUBI vasıtasıyla gönderdiği iletimlere ilişkin
        QHUBI’in herhangi bir sorumluluğu olmayacaktır ve kullanıcılar bu
        iletimleri, riski tamamen kendi üzerlerinde olacak şekilde yaparlar.
    </p>
    <p>
        2.12. Süreç zamanında anlaşmazlık olduğu herhangi bir zamanda, QHUBI
        sunucu zamanı referans olacaktır. Tedarikçi bunun bilincinde olmalıdır
        ve QHUBI&#39;in sunucu zamanına göre teklifte bulunmalıdır.
    </p>
    <p>
        2.13. Alıcı, QHUBI’e Alıcı’nın faaliyetlerinden elde edilen verileri
        istatistik ve pazarlama amacıyla kullanabilmesi için onay verir. Gizli
        bilgiler QHUBI tarafından yayınlanamayacaktır.
    </p>
    <p>
        <b>3. ÜCRETSİZ SÜRÜM</b>
    </p>
    <p>
        3.1. Alıcı, Platform’un ücretsiz sürümü için QHUBI kurumsal web sitesi
        olan https://www.qhubi.com adresinden kayıt olabilir. Bir ya da daha
        fazla QHUBI hizmeti ücretsiz sürüm kapsamında Alıcı için kullanılabilir
        olacaktır. Alıcı, ücretsiz sürüm kullanımı süresince Platform üzerinden
        dilediği üyelik paketi alımını gerçekleştirilebilir.
    </p>
    <p>
        3.2. Ücretsiz sürüm kullanımında Alıcı tarafından girilen veriler
        Platform&#39;dan silinmeyecektir. Eğer Alıcı üyelik alımına karar verirse,
        ücretsiz sürüm kullanımında girilen veriler erişilebilir olacaktır.
    </p>
    <p>
        <b>4. DESTEK</b>
    </p>
    <p>
        4.1. Destek, QHUBI destek ekibi tarafından e-posta ve telefon üzerinden
        verilecektir.
    </p>
    <p>
        4.2. Alıcı ve Tedarikçi kullanıcıları her zaman QHUBI destek ekibi ile
        teknik altyapı ayarlamaları ve Platform&#39;un kullanımı sırasında meydana
        gelen Platform sunucu sorunları hakkında iletişime geçebilirler ve
        arızaları bildirebilirler. QHUBI her türlü problem ve hata durumda
        09.00 – 18.00 (GMT+3) çalışma saatleri içinde yardım servisi
        vermektedir.
    </p>
    <p>
        4.3. Alıcı ve Tedarikçi kullanıcılarına kendi hizmet yöntemlerine ve
        kullanıcı rollerine göre kullanım kılavuzları sağlanacaktır.
    </p>
    <p>
        4.4. Talep edilen uyarlama ve değişiklikler ayrı ayrı
        değerlendirilecektir, sistemdeki önemli değişikliklere neden olan
        talepler ayrı şekilde fiyatlandırılabilecektir.
    </p>
    <p>
        4.5. QHUBI Platform&#39;un önceden belirlenen güncelleme ve planlı bakım
        dönemlerini duyuracaktır.
    </p>
    <p>
        <b>5. ÜCRETLER VE ÖDEMELER</b>
    </p>
    <p>
        5.1. QHUBI Platform fiyatlandırması hizmet modeline göre belirlenir.
        Hizmet kredisi modeli, talep edilen kredi miktarına göre
        ücretlendirilecektir. Üyelik paketi modeli, talep edilen üyelik paketi
        seviyesine göre aylık veya yıllık olarak ücretlendirilecektir.
        Platform’a ilişkin hizmet ücretleri, ödeme koşulları, ücretlerin
        yürürlük tarihleri QHUBI kurumsal web sitesinin ilgili bölümlerinde
        ilan edilecektir.
    </p>
    <p>
        5.2. Hizmet ücreti, her hizmet modelinde Platform&#39;un bakım ve
        barındırma hizmetini içerir.
    </p>
    <p>
        5.3. Üyelik paketi modelinde Alıcı, kendi isteğine bağlı olarak üyelik
        paketini yükseltebilecek veya düşürebilecektir. Buna ilişkin talepler,
        QHUBI tarafından aksi öngörülmedikçe ilgili üyelik döneminin sonunda
        gerçekleştirilecektir. Alıcı’nın üyelik süresi boyunca üyelik paketine
        ilişkin ücret ve ödeme koşullarında yapılacak değişiklikler, Alıcı’nın
        üyelik döneminin sona ermesine dek uygulanmayacak, yeni ücretler ve
        ödeme koşulları yeni üyelik döneminin başlamasıyla geçerli olacaktır.
        Üyelik dönemi boyunca Sözleşme’nin feshi de dahil olmak üzere üyeliğin
        herhangi bir nedenle sona ermesi halinde geri ödeme yapılmayacaktır.
    </p>
    <p>
        5.4. Alıcı tarafından üyelik dönemi bitiminden on dört (14) gün
        öncesine kadar aksi talep edilmediği sürece her üyelik döneminin
        bitiminde Alıcı’nın üyeliği otomatik olarak yenilenecektir.
    </p>
    <p>
        5.5. QHUBI, Alıcı tarafından iletilen iletişim adresine üyelik
        döneminin başlangıcında hizmet ücretlerine ilişkin faturayı
        iletecektir. Alıcı, faturadaki ilgili tutarı fatura tarihini takip eden
        14 (on dört) gün içinde ödeyecektir. İlgili ücretlere ilişkin vergi ve
        harçların ödenmesinden Alıcı sorumludur.
    </p>
    <p>
        5.6. Ödemeler havale yöntemiyle veya Online Ödeme Sistemi ile banka
        kartı, kredi kartı veya benzer bir ödeme aracı ile yapılacaktır. Online
        Ödeme Sistemi ile ödeme seçeneği kullanılarak yapılan ödemelerde,
        kartın hamili haricinde bir başkası tarafından hukuka aykırı şekilde
        kullanılması halinde 23.02.2006 tarihli 5464 sayılı Banka Kartları ve
        Kredi Kartları Kanunu ve 10.03.2007 tarihli ve 26458 sayılı Resmi
        Gazete’de yayımlanan Banka Kartları ve Kredi Kartları Hakkında
        Yönetmelik hükümlerine göre işlem yapılır.
    </p>
    <p>
        5.7. Alıcı, QHUBI veya QHUBI tarafından onaylanmış üçüncü kişiler
        üyeliğe ve ödemeye ilişkin işlemler veya banka entegrasyonunu ve ilgili
        güncellemeleri gerçekleştirmek için Alıcı’nın kredi kartı ve ödeme
        bilgilerini saklayabilecektir.
    </p>
    <p>
        <b>6. YÜKÜMLÜLÜKLER</b>
    </p>
    <p>
        6.1. Taraflar işbu Sözleşme’de düzenlenen yükümlülükleri tamamıyla
        yerine getireceğini üstlenir ve kabul eder. Karşı taraf
        yükümlülüklerini yerine getirmeme ya da uygulamama durumunda tarafların
        Sözleşme’yi askıya alma ya da sona erdirme hakkı vardır.
    </p>
    <p>
        6.2. Alıcı’nın izin verdiği kendi iş alanındaki dahili kullanıcılar
        ("Dahili Kullanıcı") ve Tedarikçiler Platform&#39;a giriş yapar ve
        kullanır. QHUBI dahili kullanıcı olmayan gerçek ya da tüzel kişiyi ya
        da Tedarikçi&#39;yi Platform&#39;dan derhal çıkartma ve Platform’u
        kullanmalarını yasaklama hakkına sahiptir. Alıcı kendi iş alanındaki
        kullanıcıların kullanıcı hesaplarını gizli ve özel tutmaktan sorumludur
        ve bu kullanıcı hesaplarını hiçbir üçüncü partiye vermeyeceğini ve
        yayınlamayacağını kabul eder.
    </p>
    <p>
        Alıcı’nın kullanıcı hesaplarının kullanıldığı süre boyunca verilen her
        ifade ve uygulanan her davranış ve ihmal için Alıcı sorumludur. QHUBI,
        Alıcı’nın kullanıcı hesaplarını gizli tutmamasından meydana gelen
        herhangi bir güvenlik ihlalinden sorumlu tutulamaz. Alıcı kullanıcı
        hesaplarının kaybolması veya çalınması ya da bir şekilde kullanıcı
        hesaplarının gizliliğinin ihlaline inanırsa ya da Platform’un
        kullanıldığını anlarsa ya da bunlar için yetkisiz bir şekilde
        kullanılması ihtimali olması durumunda, Alıcı derhal QHUBI’i
        bilgilendireceğini kabul eder. QHUBI, Alıcı’nın kullanıcı hesaplarını
        ön bildirim ile iptal etme hakkını saklı tutar.
    </p>
    <p>
        6.3. QHUBI tarafından yerine getirilen işbu Sözleşme’deki faaliyetler
        ve hizmetler için gereken tüm yetki ve izinleri Alıcı verir ve QHUBI’in
        bu amaca ilişkin bütün gerekli ve uygun kararları alacağını kabul eder.
        Alıcı, Platform’da kullanılmak üzere adını, logosunu, markalarını
        çoğaltma ve kullanma hakkını herhangi bir ücrete tabi olmadan QHUBI’e
        verir.
    </p>
    <p>
        6.4. Alıcı, Platform’a (i) üçüncü tarafların her türlü fikri mülkiyet
        hakkını ihlal edecek nitelikte (ii) iftira, hakaret ya da tehdit içeren
        ahlak ve örf ve adetlere karşı veya kanuna aykırı veya (iii) virüsler,
        “solucanlar”, “Trojan” veya diğer zarar verici özellikleri içeren
        herhangi bir program – veri yüklemeyeceğini kabul eder. Alıcı,
        Platform’u kanuna aykırı amaçlarla kullanmamayı veya bu tür amaçları
        fark edeceğini kabul eder. QHUBI, kendi takdirinde olarak bu hükümlere
        karşı olan Süreç’leri iptal etme, silme ve değiştirme hakkını saklı
        tutar.
    </p>
    <p>
        <b>7. GİZLİLİK</b>
    </p>
    <p>
        7.1. Taraflar, üçüncü taraflardan gizlenmesi gereken tüm bilgileri
        gizli tutacağını taahhüt eder. Tarafların bu yükümlülüğü, işbu
        Sözleşme’nin sona ermesinden itibaren 5 yıl boyunca devam eder. Hukuken
        yanlarında bulundurmaları gereken belgeler hariç olmak üzere, taraflar
        Sözleşme’nin sona ermesiyle beraber, talep üzerine kendilerine verilmiş
        belgeleri gecikmeksizin iade veya imha etmeyi taahhüt ederler. Ayrıca,
        taraflar işbu Sözleşme’nin konusuyla ilişkili olan çalışanlarının ve
        bağlı şirketlerinin de işbu Sözleşme kapsamındaki gizlilik
        yükümlülüğüne ve karşılıklı gizlilik hükmüne riayet etmelerini sağlar.
        Aksi halde, taraflar karşılıklı gizliliğe dair bu hükmü ihlal eden
        çalışanları/bağlı şirketleriyle beraber müştereken ve müteselsilen
        sorumlu olmayı kabul ederler.
    </p>
    <p>
        7.2. Gizlilik yükümlülüğü ve gizli bilgilerin kullanımı hükmü aşağıda
        belirtilen bilgileri kapsamaz:
    </p>
    <ul type="disc">
        <li>
            (i) Bilgiyi edinen tarafın ilgili bilgiyi karşı tarafın
            açıklamasından önce bildiğini ispatladığı bilgiler,
        </li>
        <li>
            (ii) Karşı tarafın hakları ihlal edilmeksizin, bilgiyi edinen
            tarafa üçüncü kişilerce sağlanan bilgiler,
        </li>
        <li>
            (iii) Bilgiyi edinen tarafın herhangi bir müdahalesi olmaksızın
            kamusal alana düşen bilgiler,
        </li>
        <li>
            (iv) Bilgiyi edinen tarafın yetkili hukuk gereği açıklamak zorunda
            olduğu bilgiler,
        </li>
        <li>
            (v) Menkul Kıymetler Hukuku kuralları kapsamında kamuya sunulan
            bilgiler ve gerekli veya tavsiye niteliğindeki ürün bilgileri.
        </li>
    </ul>
    <p>
        7.3. İşbu Sözleşme kapsamında taraflar arasındaki bilgi alışverişi,
        işbu Sözleşme’nin uygulanması için gerekli bilgi ile sınırlıdır.
        Taraflar, fiyat ve pazarlama politikaları, kar marjı veya kullanım
        kapasitesi gibi rekabet açısından hassas nitelikte olan bilgi
        alışverişinde bulunmamalıdırlar.
    </p>
    <p>
        <b>8. MÜLKİYET HAKLARI</b>
    </p>
    <p>
        8.1. Platform üzerindeki fikri mülkiyet haklarına ilişkin tüm haklar
        QHUBI’e aittir. Kullanıcıların bu hizmetin doğası gereği kullanmak
        zorunda oldukları haklar hariç, işbu Sözleşme fikri haklara dair
        herhangi bir hak veya lisans tanımaz.
    </p>
    <p>
        8.2. QHUBI, Platform ve QHUBI markaları üzerindeki tüm mülkiyet ve
        tasarruf haklarına sahiptir. İşbu Sözleşme’deki hiçbir hüküm, Alıcı’ya
        mülkiyet, bağlantılı hak veya yetki sağlamaz. Alıcı, sadece Platform’a
        münhasır olmayan ve sınırlı bir erişim hakkı elde eder. Bu erişim
        hakkı, alım işlemlerine dair kurum içi kullanım amacına dairdir ve
        bunları kullanım hakkını da ihtiva eder. Keza, bu hak, işbu Sözleşme
        süresince geçerlidir ve fakat başka kişilere temlik edilemez. Aksi
        kararlaştırılmadığı müddetçe, Alıcı, Platform ve QHUBI markalarını
        veyahut bunların bir kısmını kullanamaz, çoğaltamaz, taklit edemez,
        geliştiremez, teşhir amaçlı kullanamaz, dağıtamaz, yayınlayamaz,
        dönüştüremez, değiştiremez ve bunlardan işleme eserler oluşturamaz.
        Dahası, Alıcı, bunlara dair alt-lisans veremez, bunları iletemez,
        devredemez, ticari amaçlarla kullanamaz veya başka benzer eylemlerde
        bulunamaz. Alıcı, Platform’dan yetkili olarak aldığı tüm nüshaları,
        QHUBI’in telif hakları, ticari markası ve diğer mülkiyet haklarına dair
        olacak şekilde bandroller. Alıcı, bu bandrolleri silmemeli, ortadan
        kaldırmamalı, başka bandrollerle kaplamamalı veyahut bu bandrollerin
        üzerine herhangi bir işaret veya uyarı koymamalıdır. Alıcı, üçüncü
        tarafların, Platform’dan gelen malzemeleri çoğaltmasına, kullanmasına
        veya teşhir amaçlı kullanmasına izin vermemelidir.
    </p>
    <p>
        8.3. QHUBI, Alıcı ve Tedarikçi’nin gizlilik mühürleri altında,
        satınalmaya dair bilgi nüshalarını saklamalıdır. QHUBI, bu bilgileri
        Platform’un yüklenmesi ve çalışması ve de QHUBI’in hizmetlere dair
        yükümlülüklerinin ve sorumluluklarının yürütülmesi ve yerine
        getirilmesi amacıyla kullanabilir. Bu kullanıma çoğaltma, dağıtma,
        yayınlama ve işleme de dahildir.
    </p>
    <p>
        <b>9. MÜCBİR SEBEP</b>
    </p>
    <p>
        Mücbir sebepten kaynaklandığı ölçüde, tarafların edimlerini geç veya
        hiç ifa etmemesi durumu mazur görülür. İşbu Sözleşme kapsamında mücbir
        sebep, mücbir sebep iddiasında bulunan tarafça öngörülmesi mümkün
        olmayan, bu kişi tarafından sebep olunmayan veyahut bu kişinin
        kontrolünde gerçekleşmeyen durumlara denilir. Mücbir sebep kavramı
        kapsamına, QHUBI’in ve Alıcı’ların hareketleri dışında kalan ve QHUBI’i
        hizmet vermekten alıkoyan doğa olayları, yangın, sel, patlama, isyan,
        savaş, kasırga, terör saldırısı, vandalizm, kaza, hükümet
        sınırlandırmaları, hükümet tasarrufları, internet sistemleri arızaları,
        mahkeme kararları, grev; etkilenen tarafın – bu tarafın ilgili
        olayların, hareketlerin etkilerini engelleme, bu etkilerinden kaçınma,
        bu etkileri geciktirme veya azaltma çabalarına rağmen – makul tahmini
        ve kontrolünün ötesinde olan olaylar ve de meydana gelmeleri veyahut
        etkileri taraflardan birinin işbu Sözleşme kapsamındaki
        yükümlülüklerini yerine getirmemesinden ötürü taraflara atfedilemeyen
        olayları girer.
    </p>
    <p>
        <b>10. SÖZLEŞMENİN FESHİ</b>
    </p>
    <p>
        10.1.1. Alıcı, QHUBI’in bu hükümleri değiştirmesi halinde, işbu
        Sözleşme’yi feshedebilir. Bununla birlikte Alıcı, bu hakkını sadece
        hükümlerin esaslı ölçüde değiştirilmesi halinde kullanabilir. Esaslı
        olmayan değişiklikler haklı bir fesih sebebi oluşturmaz.
    </p>
    <p>
        10.1.2. QHUBI, Alıcı’nın herhangi bir bedeli vadesinden itibaren 14 gün
        içerisinde ödememesi durumunda, işbu Sözleşme’yi feshedebilir.
    </p>
    <p>
        10.1.3. QHUBI bu hükümlerin ihlali durumunda, herhangi bir uygulamayı
        derhal sona erdirebilir ve Alıcı kullanıcılarının Platform’u
        kullanmasını yasaklayabilir.
    </p>
    <p>
        10.1.4. QHUBI, Platform’a kayıtları veya erişimi reddetme ve geçici
        veyahut belirsiz bir süre için kayıtlı bir kullanıcının hesabını askıya
        almak hakkına sahiptir. Herhangi bir haklı sebep olmaksızın askıya alma
        durumunda, QHUBI, hesabın geri kalan kullanım süresine denk bir bedel
        ödemek zorundadır.
    </p>
    <p>
        10.1.5. QHUBI, Platform’u her an ve hiçbir bildirimde bulunmaksızın
        değiştirebilir, geçici olarak askıya alabilir veya sonlandırabilir.
    </p>
    <p>
        10.1.6. QHUBI kullanıcı adlarının ve/veya şifrelerinin tehlikeye
        girdiği kanısına varırsa, kullanıcı hesaplarını her an ve hiçbir
        bildirimde bulunmaksızın askıya alabilir.
    </p>
    <p>
        10.2. Alıcı, borçlu olduğu hizmet ücretlerini tümüyle ödediği müddetçe
        Sözleşme’yi fesih hakkını haizdir.
    </p>
    <p>
        10.3. Bu hükümlerin sona erdirilmesi halinde, Platform’a erişim sona
        erer ve diğer tarafa dair tüm bilgiler ya iade edilir veyahut tamamen
        imha edilir. Bilgilerin iadesi için gerekli hükümler ve zaman çizelgesi
        hazırlanacaktır.
    </p>
    <p>
        <b>11. HUKUK VE YETKİ</b>
    </p>
    <p>
        İşbu Sözleşme Türk kanunlarına tabi olacak ve Türk kanunlarına göre
        yorumlanacaktır. Ayrıca, taraflar, işbu Sözleşme’ye dair TR İstanbul
        Mahkemelerinin ve İcra Dairelerinin münhasıran yetkili olduğunu kabul
        etmektedirler.
    </p>
    <p>
        <b>12. DİĞER</b>
    </p>
    <p>
        12.1. Bu hükümlere yönelik değişiklikler, derhal yürürlüğe girer.
        Platform’un değişikliklerden sonra Alıcı ve Tedarikçiler tarafından
        kullanılması durumunda, bu, Alıcı ve Tedarikçiler’in yeni hükümlerle
        bağlı olmak istediği anlamına gelir.
    </p>
    <p>
        12.2. Alıcı, QHUBI’in yazılı onayı olmaksızın, hiçbir bir hakkını veya
        yükümlülüğünü devredemez.
    </p>
    <p>
        12.3. Alıcı, kayıt esnasında kendisi tarafından sunulan adreslerin
        kendisinin kalıcı adresleri olduğunu ve bu adreslerde meydana gelen
        değişiklikleri derhal QHUBI’e bildireceğini kabul eder. Aksi halde,
        sunulan adreslere yapılan bildirimler geçerli olacaktır.
    </p>
    <p>
        12.4. Tarafların işbu Sözleşme kapsamındaki haklarını, yetkilerini
        veyahut ayrıcalıklarını kullanmaması veya bunları geç kullanması,
        kendilerinin bunlardan feragat ettiği anlamına gelmez. Aynı şekilde, bu
        hak, yetki veyahut ayrıcalıkların kısmen kullanılması, kullanılmayan
        kısmın veya kullanılmayan diğer hak, yetki ve ayrıcalıkların artık
        kullanılamayacağı anlamını taşımaz. İşbu Sözleşme kapsamındaki hüküm ve
        şartlara dair hiçbir feragat, bu hüküm ve şartlardan sürekli feragat
        edildiği anlamını taşıyacak veya feragatin kapsamını genişletecek
        şekilde yorumlanamaz.
    </p>
    <p>
        12.5. İşbu Sözleşme, tarafların üzerinde anlaştığı her hususu kapsar.
        İşbu Sözleşme, taraflar arasında daha önce işbu Sözleşme’nin konusuna
        giren diğer tüm sözlü ve yazılı sözleşmelerin yerine geçer.
    </p>
    <p>
        12.6. İşbu Sözleşme’nin herhangi bir hükmünün geçersiz veya hükümsüz
        olması halinde, söz konusu hüküm etkisiz hale gelecek (geçersiz veya
        uygulanamaz olduğu ölçüde) ve işbu Sözleşme’de yer alan diğer
        hükümlerin geçerliliğini etkilemeksizin bu Sözleşme’ye dâhil edilmemiş
        sayılacaktır. Söz konusu geçersiz Sözleşme hükmü, kendisine anlamsal ve
        amaçsal açıdan en yakın bir hükümle değiştirilir.
    </p>
    <br/>';
	
}
elseif ($_SESSION['dil'] == "chn")  {
    echo '<ol>
	<li>
		<strong>
			 Introduction
		</strong>
	</li>
</ol>
<p>
	<strong>
		 1.1 Contract
	</strong>
</p>
<p>
	You agree that by clicking &ldquo;Register&rdquo;, &ldquo;Join Qhubi&rdquo;, &ldquo;Sign Up&rdquo; or similar, registering, accessing or using our services (described below),
	<strong>
		 you are agreeing to enter into a legally binding contract
	</strong>
	 with Qhubi (even if you are using our Services on behalf of a company). If you do not agree to this contract (&ldquo;Contract&rdquo; or &ldquo;User Agreement&rdquo;), do
	<strong>
		 not
	</strong>
	 click &ldquo;Join Now&rdquo; (or similar) and do not access or otherwise use any of our Services. If you wish to terminate this contract, at any time you can do so by closing your account and no longer accessing or using our Services.
</p>
<p>
	<strong>
		 Services
	</strong>
</p>
<p>
	This Contract applies to www.qhubi.com that states that they are offered under this Contract (&ldquo;Services&rdquo;), including the offsite collection of data for those Services. Registered users of our Services are &ldquo;Members&rdquo; and unregistered users are &ldquo;Visitors&rdquo;. This Contract applies to both Members and Visitors.
</p>
<p>
	<strong>
		 Qhubi
	</strong>
</p>
<p>
	You are entering into this Contract with Qhubi (also referred to as &ldquo;we&rdquo; and &ldquo;us&rdquo;).
</p>
<p>
	We use the term &ldquo;Designated Countries&rdquo; to refer to countries in the European Union (EU), European Economic Area (EEA), and Switzerland.
</p>
<p>
	As a Visitor or Member of our Services, the collection, use and sharing of your personal data is subject to this Privacy Policy (which includes our Cookie Policy and other documents referenced in this Privacy Policy) and updates.
</p>
<h3>
	1.2 Members and Visitors
</h3>
<p>
	When you register and join the Qhubi Service, you become a Member. If you have chosen not to register for our Services, you may access certain features as a &ldquo;Visitor.&rdquo;
</p>
<h3>
	1.3 Change
</h3>
<p>
	We may modify this Contract, our Privacy Policy and our Cookies Policies from time to time. If we make material changes to it, we will provide you notice through our Services, or by other means, to provide you the opportunity to review the changes before they become effective. We agree that changes cannot be retroactive. If you object to any changes, you may close your account. Your continued use of our Services after we publish or send a notice about our changes to these terms means that you are consenting to the updated terms.
</p>
<p>
	&nbsp;
</p>
<p>
	&nbsp;
</p>
<p>
	&nbsp;
</p>
<ol>
	<li>
		<strong>
			 Rights and Limits
		</strong>
	</li>
</ol>
<p>
	<strong>
		 2.1. Your License to Qhubi
	</strong>
</p>
<p>
	As between you and Qhubi, you own the content and information that you submit or post to the Services, and you are only granting Qhubi and our
	<em>
		 affiliates
	</em>
	 the following non-exclusive license:
</p>
<p>
	A worldwide, transferable and sublicensable right to use, copy, modify, distribute, publish, and process, information and content that you provide through our Services and the services of others, without any further consent, notice and/or compensation to you or others. These rights are limited in the following ways:
</p>
<ol>
	<li>
		You can end this license for specific content by deleting such content from the Services, or generally by closing your account, except (a) to the extent you shared it with others as part of the Service and they copied, re-shared it or stored it and (b) for the reasonable time it takes to remove from backup and other systems.
	</li>
	<li>
		We will not include your content in advertisements for the products and services of third parties to others without your separate consent (including sponsored content). However, we have the right, without payment to you or others, to serve ads near your content and information, and your
		<em>
			 social actions
		</em>
		 may be visible and included with ads.
	</li>
	<li>
		We will get your consent if we want to give others the right to publish your content beyond the Services.
	</li>
</ol>
<p>
	You and Qhubi agree that we may access, store, process and use any information and personal data that you provide in accordance with the terms of the Privacy Policy and your choices (including settings).
</p>
<p>
	By submitting suggestions or other feedback regarding our Services to Qhubi, you agree that Qhubi can use and share (but does not have to) such feedback for any purpose without compensation to you.
</p>
<p>
	You agree to only provide content or information that does not violate the law nor anyone&rsquo;s rights (including intellectual property rights). You also agree that your profile information will be truthful. Qhubi may be required by law to remove certain information or content in certain countries.
</p>
<p>
	&nbsp;
</p>
<p>
	<strong>
		 2.2 Service Availability
	</strong>
</p>
<p>
	We may change or discontinue any of our Services. We don&rsquo;t promise to store or keep showing any information and content that you&rsquo;ve posted.
</p>
<p>
	Qhubi is not a storage service. You agree that we have no obligation to store, maintain or provide you a copy of any content or information that you or others provide, except to the extent required by applicable law.
</p>
<p>
	<strong>
		 2.3 Other Content, Sites and Apps
	</strong>
</p>
<p>
	By using the Services, you may encounter content or information that might be inaccurate, incomplete, delayed, misleading, illegal, offensive or otherwise harmful. Qhubi generally does not review content provided by our Members or others. You agree that we are not responsible for others&rsquo; (including other Members&rsquo;) content or information. We cannot always prevent this misuse of our Services, and you agree that we are not responsible for any such misuse.
</p>
<p>
	<strong>
		 2.4 Limits
	</strong>
</p>
<p>
	Qhubi reserves the right to limit your use of the Services. Qhubi reserves the right to restrict, suspend, or terminate your account if Qhubi believes that you may be in breach of this Contract or law or are misusing the Services.
</p>
<p>
	<strong>
		 2.5 Intellectual Property Rights
	</strong>
</p>
<p>
	Qhubi reserves all of its intellectual property rights in the Services. Using the Services does not give you any ownership in our Services or the content or information made available through our Services. Trademarks and logos used in connection with the Services are the trademarks of their respective owners. Qhubi and other Qhubi trademarks, service marks, graphics, and logos used for our Services are trademarks or registered trademarks of Qhubi.
</p>
<h2>
	3. Obligations
</h2>
<h3>
	3.1 Service Eligibility
</h3>
<p>
	The Services are not for use by anyone under the age of 18.
</p>
<p>
	To use the Services, you agree that: (1) you must be the &ldquo;Minimum Age&rdquo; (described below) or older; (2) you will only have one Qhubi account, which must be in your real name; and (3) you are not already restricted by Qhubi from using the Services. Creating an account with false information is a violation of our terms, including accounts registered on behalf of others or persons under the age of 18.
</p>
<p>
	&ldquo;Minimum Age&rdquo; means 18 years old. However, if law requires that you must be older in order for Qhubi to lawfully provide the Services to you without parental consent (including using of your personal data) then the Minimum Age is such older age.
</p>
<h3>
	3.2 Your Account
</h3>
<p>
	Members are account holders. You agree to: (1) try to choose a strong and secure password; (2) keep your password secure and confidential; (3) not transfer any part of your account (e.g., connections) and (4) follow the law and our list of Dos and Don&rsquo;ts. You are responsible for anything that happens through your account unless you close it or report misuse.
</p>
<p>
	As between you and others (including your employer), your account belongs to you.
</p>
<h3>
	3.3 Payment
</h3>
<p>
	If you buy any of our paid Services (&ldquo;Premium Services&rdquo;), you agree to pay us the applicable fees and taxes and to additional terms specific to the paid Services. Failure to pay these fees will result in the termination of your paid Services. Also, you agree that:
</p>
<ul>
	<li>
		Your purchase may be subject to foreign exchange fees or differences in prices based on location (e.g. exchange rates).
	</li>
	<li>
		We may store and continue billing your payment method (e.g. credit card) even after it has expired, to avoid interruptions in your Services and to use to pay other Services you may buy.
	</li>
	<li>
		If you purchase a subscription, your payment method automatically will be charged at the start of each subscription period for the fees and taxes applicable to that period. To avoid future charges, cancel before the renewal date. Learn how to cancel or suspend your Premium Services.
	</li>
	<li>
		All of your purchases of Services are subject to Qhubi&rsquo;s refund policy.
	</li>
	<li>
		We may calculate taxes payable by you based on the billing information that you provide us at the time of purchase.
	</li>
</ul>
<h3>
	3.4 Notices and Messages
</h3>
<p>
	You agree that we will provide notices and messages to you in the following ways: (1) within the Service, or (2) sent to the contact information you provided us (e.g., email, mobile number, physical address). You agree to keep your contact information up to date.
</p>
<p>
	Please review your settings to control and limit messages you receive from us.
</p>
<h3>
	3.5 Sharing
</h3>
<p>
	Our Services allow messaging and sharing of information in many ways, such as your profile and blogs. Information and content that you share or post may be seen by other Members, Visitors or others (including off of the Services).
</p>
<p>
	We are not obligated to publish any information or content on our Service and can remove it in our sole discretion, with or without notice.
</p>
<h2>
	4. Disclaimer and Limit of Liability
</h2>
<h3>
	4.1 No Warranty
</h3>
<p>
	TO THE EXTENT ALLOWED UNDER LAW, QHUBI AND ITS AFFILIATES (AND THOSE THAT QHUBI WORKS WITH TO PROVIDE THE SERVICES) (A) DISCLAIM ALL IMPLIED WARRANTIES AND REPRESENTATIONS (E.G. WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, ACCURACY OF DATA, AND NONINFRINGEMENT); (B) DO NOT GUARANTEE THAT THE SERVICES WILL FUNCTION WITHOUT INTERRUPTION OR ERRORS, AND (C) PROVIDE THE SERVICE (INCLUDING CONTENT AND INFORMATION) ON AN &ldquo;AS IS&rdquo; AND &ldquo;AS AVAILABLE&rdquo; BASIS.
</p>
<p>
	SOME LAWS DO NOT ALLOW CERTAIN DISCLAIMERS, SO SOME OR ALL OF THESE DISCLAIMERS MAY NOT APPLY TO YOU.
</p>
<p>
	&nbsp;
</p>
<h3>
	4.2 Exclusion of Liability
</h3>
<p>
	TO THE EXTENT PERMITTED UNDER LAW (AND UNLESS QHUBI HAS ENTERED INTO A SEPARATE WRITTEN AGREEMENT THAT OVERRIDES THIS CONTRACT), QHUBI AND ITS AFFILIATES (AND THOSE THAT QHUBI WORKS WITH TO PROVIDE THE SERVICES) SHALL NOT BE LIABLE TO YOU OR OTHERS FOR ANY INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL OR PUNITIVE DAMAGES, OR ANY LOSS OF DATA, OPPORTUNITIES, REPUTATION, PROFITS OR REVENUES, RELATED TO THE SERVICES (E.G. OFFENSIVE OR DEFAMATORY STATEMENTS, DOWN TIME OR LOSS, USE OF, OR CHANGES TO, YOUR INFORMATION OR CONTENT).
</p>
<p>
	THIS LIMITATION OF LIABILITY IS PART OF THE BASIS OF THE BARGAIN BETWEEN YOU AND QHUBI AND SHALL APPLY TO ALL CLAIMS OF LIABILITY (E.G. WARRANTY, TORT, NEGLIGENCE, CONTRACT, LAW) AND EVEN IF QHUBI OR ITS AFFILIATES HAS BEEN TOLD OF THE POSSIBILITY OF ANY SUCH DAMAGE, AND EVEN IF THESE REMEDIES FAIL THEIR ESSENTIAL PURPOSE.
</p>
<p>
	SOME LAWS DO NOT ALLOW THE LIMITATION OR EXCLUSION OF LIABILITY, SO THESE LIMITS MAY NOT APPLY TO YOU.
</p>
<ol>
	<li>
		<strong>
			 Termination
		</strong>
	</li>
</ol>
<p>
	Both you and Qhubi may terminate this Contract at any time with notice to the other. On termination, you lose the right to access or use the Services. The following shall survive termination:
</p>
<ul>
	<li>
		Our rights to use and disclose your feedback;
	</li>
	<li>
		Members and/or Visitors&rsquo; rights to further re-share content and information you shared through the Service to the extent copied or re-shared prior to termination;
	</li>
	<li>
		Sections 4, 6, 7, and 8.2 of this Contract;
	</li>
	<li>
		Any amounts owed by either party prior to termination remain owed after termination.
	</li>
</ul>
<h2>
	6. General Terms
</h2>
<p>
	If a court with authority over this Contract finds any part of it unenforceable, you and we agree that the court should modify the terms to make that part enforceable while still achieving its intent. If the court cannot do that, you and we agree to ask the court to remove that unenforceable part and still enforce the rest of this Contract.
</p>
<p>
	To the extent allowed by law, the English language version of this Contract is binding and other translations are for convenience only. This Contract (including additional terms that may be provided by us when you engage with a feature of the Services) is the only agreement between us regarding the Services and supersedes all prior agreements for the Services.
</p>
<p>
	If we don^t act to enforce a breach of this Contract, that does not mean that Qhubi has waived its right to enforce this Contract. You may not assign or transfer this Contract (or your membership or use of Services) to anyone without our consent. However, you agree that Qhubi may assign this Contract to its affiliates or a party that buys it without your consent. There are no third-party beneficiaries to this Contract.
</p>
<p>
	You agree that the only way to provide us legal notice is at the addresses provided in Section 10.
</p>
<ol>
	<li>
		<strong>
			 Qhubi &ldquo;Dos and Don&rsquo;ts&rdquo;
		</strong>
	</li>
</ol>
<p>
	<strong>
		 7.1. Dos
	</strong>
</p>
<p>
	<strong>
		 You agree that you will:
	</strong>
</p>
<ol>
	<li>
		Comply with all applicable laws, including, without limitation, privacy laws, intellectual property laws, anti-spam laws, export control laws, tax laws, and regulatory requirements;
	</li>
	<li>
		Provide accurate information to us and keep it updated;
	</li>
	<li>
		Use your real name on your profile; and
	</li>
	<li>
		Use the Services in a professional manner.
	</li>
</ol>
<p>
	<strong>
		 7.2. Don&rsquo;ts
	</strong>
</p>
<p>
	<strong>
		 You agree that you will
		<em>
			 not
		</em>
		 :
	</strong>
</p>
<ol>
	<li>
		Create a false identity on Qhubi, misrepresent your identity, create a Member profile for anyone other than yourself (a real person), or use or attempt to use another&rsquo;s account;
	</li>
	<li>
		Develop, support or use software, devices, scripts, robots, or any other means or processes (including crawlers, browser plugins and add-ons, or any other technology) to scrape the Services or otherwise copy profiles and other data from the Services;
	</li>
	<li>
		Override any security feature or bypass or circumvent any access controls or use limits of the Service (such as caps on keyword searches or profile views);
	</li>
	<li>
		Copy, use, disclose or distribute any information obtained from the Services, whether directly or through third parties (such as search engines), without the consent of Qhubi;
	</li>
	<li>
		Disclose information that you do not have the consent to disclose (such as confidential information of others (including your employer));
	</li>
	<li>
		Violate the intellectual property rights of others, including copyrights, patents, trademarks, trade secrets, or other proprietary rights. For example, do not copy or distribute (except through the available sharing functionality) the posts or other content of others without their permission, which they may give by posting under a Creative Commons license;
	</li>
	<li>
		Violate the intellectual property or other rights of Qhubi, including, without limitation, (i) copying or distributing our learning videos or other materials or (ii) copying or distributing our technology, unless it is released under open source licenses; (iii) using the word &ldquo;Qhubi&rdquo; or our logos in any business name, email, or URL.
	</li>
	<li>
		Post anything that contains software viruses, worms, or any other harmful code;
	</li>
	<li>
		Reverse engineer, decompile, disassemble, decipher or otherwise attempt to derive the source code for the Services or any related technology that is not open source;
	</li>
	<li>
		Imply or state that you are affiliated with or endorsed by Qhubi without our express consent (e.g., representing yourself as an accredited Qhubi trainer);
	</li>
	<li>
		Rent, lease, loan, trade, sell/re-sell or otherwise monetize the Services or related data or access to the same, without Qhubi&rsquo;s consent;
	</li>
	<li>
		Deep-link to our Services for any purpose other than to promote your profile or a Group on our Services, without Qhubi&rsquo;s consent;
	</li>
	<li>
		Use bots or other automated methods to access the Services, add or download contacts, send or redirect messages;
	</li>
	<li>
		Monitor the Services&rsquo; availability, performance or functionality for any competitive purpose;
	</li>
	<li>
		Engage in &ldquo;framing,&rdquo; &ldquo;mirroring,&rdquo; or otherwise simulating the appearance or function of the Services;
	</li>
	<li>
		Overlay or otherwise modify the Services or their appearance (such as by inserting elements into the Services or removing, covering, or obscuring an advertisement included on the Services);
	</li>
	<li>
		Interfere with the operation of, or place an unreasonable load on, the Services (e.g., spam, denial of service attack, viruses, gaming algorithms); and/or
	</li>
</ol>
<h2>
	8. Complaints Regarding Content
</h2>
<p>
	We respect the intellectual property rights of others. We require that information posted by Members be accurate and not in violation of the intellectual property rights or other rights of third parties. We provide a policy and process for complaints concerning content posted by our Members.
</p>
<h2>
	9. How To Contact Us
</h2>
<p>
	If you want to send us notices or service of process, please contact us.
</p>
<p>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</p>';
} else {
	
	}
?>
    					
    
</div>

							
							
					
		



    </section><!-- /.faq-style-one -->

<footer class="footer-style-two">
        <div class="footer-bottom text-center">
			 	    <div class="thm-container">
<p class="MsoNormal" align="center" style="text-align:center">
	<span style="font-size:10.0pt;line-height:107%">
		<a href="gs.php" target="_blank"><?php echo $dil['gs']; ?></a> 
		<span style="mso-spacerun:yes">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span>
		<a href="ks.php" target="_blank"><?php echo $dil['ks']; ?></a> 
		<span style="mso-spacerun:yes">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span>
		<a href="cp.php" target="_blank"><?php echo $dil['cp']; ?></a> </span></p>
						
	</div>   
            <div class="thm-container">
                <p>&copy; <a href="https://www.qhubi.com/" target="_blank">Qhubi.com <?php echo $dil['b1']; ?><?php echo $dil['34']; ?></p></a> 

            </div><!-- /.thm-container -->
        </div><!-- /.footer-bottom text-center -->

	
    </footer><!-- /.footer-style-one -->


    <div class="scroll-to-top scroll-to-target thm-gradient-two" data-target="html"><i class="fa fa-angle-up"></i></div>

    <script src="asset/js/jquery.js"></script>

    <script src="asset/js/bootstrap.min.js"></script>
    <script src="asset/js/bootstrap-select.min.js"></script>
    <script src="asset/js/jquery.validate.min.js"></script>
    <script src="asset/js/jqBootstrapValidation.js"></script>
    <script src="asset/js/owl.carousel.min.js"></script>
    <script src="asset/js/isotope.js"></script>
    <script src="asset/js/jquery.magnific-popup.min.js"></script>
    <script src="asset/js/waypoints.min.js"></script>
    <script src="asset/js/jquery.counterup.min.js"></script>
    <script src="asset/js/wow.min.js"></script>
    <script src="asset/js/jquery.easing.min.js"></script>
    <script src="asset/js/customd134.js?v=3.4"></script>
    <script src="asset/js/qhubi.js"></script>
    <script src="asset/js/demo.js"></script>
</body>


</html>