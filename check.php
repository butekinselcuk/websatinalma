<?php 
if(isSet($_POST['uyeAdi']))
{
$username = filter($_POST['uyeAdi']);

include("Connections/baglan.ph");


$sql_check = mysql_query("SELECT uyeAdi FROM {$prefix}uyeler WHERE uyeAdi='$username'");

if(mysql_num_rows($sql_check))
{
echo '<span style="color: red;"> <b>'.$username.'</b> başkası tarafından kullanılıyor !</span>';
}
else
{
echo 'OK';
}}
?>