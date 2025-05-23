<?
require("class.phpmailer.php");

$mail = new PHPMailer();

$message  = "<html><body>
   
<table width='100%' bgcolor='#e0e0e0' cellpadding='0' cellspacing='0' border='0'>
   
<tr><td>
<table align='center' width='100%' border='0' cellpadding='0' cellspacing='0' style='max-width:650px; background-color:#fff; font-family:Verdana, Geneva, sans-serif;'>
    
<thead>
      <tr height='80'>
       <th colspan='4' style='background-color:#f5f5f5; border-bottom:solid 1px #bdbdbd; font-family:Verdana, Geneva, sans-serif; color:#333; font-size:34px;' >".$TalepID     = $_POST['TalepID']." No Order has been assigned to you!</th>
      </tr>
      </thead>
    
<tbody>
      <tr align='center' height='50' style='font-family:Verdana, Geneva, sans-serif;'>
       <td style='background-color:#00a2d1; text-align:center;'><a href='http://www.qhubi.com/admin/Teklif/verme/index.php' style='color:#fff; text-decoration:none;'>Order Box</a></td>
       <td style='background-color:#00a2d1; text-align:center;'><a href='http://www.qhubi.com/admin/Teklif/alma/index.php' style='color:#fff; text-decoration:none;'>Quotation Box</a></td>
       <td style='background-color:#00a2d1; text-align:center;'><a href='http://www.qhubi.com/admin/Dashboard/index.php' style='color:#fff; text-decoration:none;' >Dashboard</a></td>
       <td style='background-color:#00a2d1; text-align:center;'><a href='http://www.qhubi.com/admin/Teklif/tekliflerim/index.php' style='color:#fff; text-decoration:none;' >My orders</a></td>
      </tr>
      
      <tr>
       <td colspan='4' style='padding:15px;'>
        
        <hr />

	<br/>
<b>COMMERCIAL INFORMATION OF BUYER</b><br/>

<b>Company Name:</b>".$sadi=$_POST['birsirket']." <br/>
<b>Address:</b>".$adres=$_POST['biradres']." <br/>
<b>Account Admin:</b>".$hyonet=$_POST['hyonet']." <br/>
<b>E-mail :</b>".$birmail=$_POST['birmail']." <br/>
<b>Phone Number:</b>".$birtel=$_POST['birtel']." <br/>
<b>Explanation:</b> ".$aciklama=$_POST['aciklama']." <br/>

<br/>
<b>YOUR QUOTATION</b><br/>

<b>Company Name :</b>".$ikiadi=$_POST['ikisirket']." <br/>
<b>Address :</b>".$ikiadres=$_POST['ikiadres']." <br/>
<b>Account Admin :</b>".$ikihyonet=$_POST['ikihyonet']." <br/>
<b>E-mail :</b>".$ikimail=$_POST['ikimail']." <br/>
<b>Phone Number :</b>".$ikitel=$_POST['ikitel']." <br/>

<b>Lead Time(Day) :</b> ".$tsure=$_POST['teslimsure']." <br/>
<b>Delivery Term :</b> ".$teslimsekli=$_POST['teslimsekli']." <br/>
<b>Payment Term :</b>  ".$odeme=$_POST['odemekosul']." <br/>
<b>Payment Time(Day) :</b> ".$vade=$_POST['odemevadesi']." <br/> 
<b>Forecast :</b>".$forecast=$_POST['forecast']." <br/>
<b>Currency :</b>".$parabirimi=$_POST['parabirim']." <br/>
<b>Unit Price :</b>".$bfiyat=$_POST['bfiyat']." <br/>
<b>Total Price :</b>".$tfiyat=$_POST['tfiyat']." <br/>



       </td>
      </tr>
      
      <tr height='80'>
       <td colspan='4' align='center' style='background-color:#f5f5f5; border-top:dashed #00a2d1 2px; font-size:24px; '>
       <label>
       Contact : 
       <a href='https://www.facebook.com/qhubicom/' target='_blank'><img style='vertical-align:middle' src='https://cdnjs.cloudflare.com/ajax/libs/webicons/2.0.0/webicons/webicon-facebook-m.png' /></a>
       <a href='https://twitter.com/qhubicom' target='_blank'><img style='vertical-align:middle' src='https://cdnjs.cloudflare.com/ajax/libs/webicons/2.0.0/webicons/webicon-twitter-m.png' /></a>
       <a href='https://plus.google.com/u/0/100139891007804344502' target='_blank'><img style='vertical-align:middle' src='https://cdnjs.cloudflare.com/ajax/libs/webicons/2.0.0/webicons/webicon-googleplus-m.png' /></a>
       </label>
       </td>
      </tr>
      
      </tbody>
        </table>
   
  </td></tr>
   </table>
   
  </body></html>";
   
   
    
	
	
	
	
	$TalepID     = $_POST['TalepID'];


$mail->IsSMTP();                                   // send via SMTP
$mail->Host     = "srvc93.trwww.com"; // SMTP servers
$mail->SMTPAuth = true;     // turn on SMTP authentication
$mail->Username = "info@qhubi.com";  // SMTP username
$mail->Password = "kozmo020687"; // SMTP password
$mail->CharSet = "utf-8";
$mail->Encoding = "base64";
$mail->isHTML(true);
$mail->From     = "info@qhubi.com"; 
$mail->Fromname = "giden ismi";
$mail->Subject  =  "$TalepID No Order has been assigned to you!";





$mailler=$_POST["gidenmail"];
//kontrol edelim
$mail->AddBCC($mailler,'');

$mail->Body = $message;
$mail->AltBody    = $message;


if(!$mail->Send())
{
   echo "Mesaj Gönderilemedi <p>";
   echo "Mailer Error: " . $mail->ErrorInfo;
   exit;
}

header("Location: index.php");
?>

