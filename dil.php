<?php
session_start();
# oturuma gore dil seciyoruz
$dil = strip_tags($_GET["dil"]);
if( $dil == "tr" || $dil == "en"){
	$_SESSION["dil"] = $dil;
		header("Location:kayit.php");
	}else{
		header("Location:kayit.php");
}

?>
