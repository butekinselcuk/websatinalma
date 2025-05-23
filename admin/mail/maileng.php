<?
require("class.phpmailer.php");

$mail = new PHPMailer();

$message  = "<html><body>";
   
   $message .= "<table width='100%' bgcolor='#e0e0e0' cellpadding='0' cellspacing='0' border='0'>";
   
   $message .= "<tr><td>";
   
   $message .= "<table align='center' width='100%' border='0' cellpadding='0' cellspacing='0' style='max-width:650px; background-color:#fff; font-family:Verdana, Geneva, sans-serif;'>";
    
   $message .= "<thead>
      <tr height='80'>
       <th colspan='4' style='background-color:#f5f5f5; border-bottom:solid 1px #bdbdbd; font-family:Verdana, Geneva, sans-serif; color:#333; font-size:34px;' >New Offer Request Available</th>
      </tr>
      </thead>";
    
   $message .= "<tbody>
      <tr align='center' height='50' style='font-family:Verdana, Geneva, sans-serif;'>
       <td style='background-color:#00a2d1; text-align:center;'><a href='http://www.qhubi.com/admin/Teklif/verme/index.php' style='color:#fff; text-decoration:none;'>Order Box</a></td>
       <td style='background-color:#00a2d1; text-align:center;'><a href='http://www.qhubi.com/admin/Teklif/alma/index.php' style='color:#fff; text-decoration:none;'>Quotation Box</a></td>
       <td style='background-color:#00a2d1; text-align:center;'><a href='http://www.qhubi.com/admin/Dashboard/index.php' style='color:#fff; text-decoration:none;' >Dashboard</a></td>
       <td style='background-color:#00a2d1; text-align:center;'><a href='http://www.qhubi.com/admin/Teklif/tekliflerim/index.php' style='color:#fff; text-decoration:none;' >My orders</a></td>
      </tr>
      
      <tr>
       <td colspan='4' style='padding:15px;'>
        
        <hr />

Payment Term :  ".$odeme=$_POST['odemekosul']." <br/>
Payment Time(Day) : ".$vade=$_POST['odemevadesi']." <br/> 
Delivery Term : ".$teslimsekli=$_POST['teslimsekli']." <br/>
Lead Time(Day) : ".$tsure=$_POST['teslimsure']." <br/>
Target Price :".$hedef=$_POST['hedeffiyat']." <br/>
Currency :".$parabirimi=$_POST['parabirim']." <br/>
Forecast :".$forecast=$_POST['forecast']." <br/>
Explanation : ".$aciklama=$_POST['aciklama']." <br/>
Address :".$adres=$_POST['adres']." <br/>


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
      
      </tbody>";
    
   $message .= "</table>";
   
   $message .= "</td></tr>";
   $message .= "</table>";
   
   $message .= "</body></html>";
   


$mail->IsSMTP();                                   // send via SMTP
$mail->Host     = "srvc93.trwww.com"; // SMTP servers
$mail->SMTPAuth = true;     // turn on SMTP authentication
$mail->Username = "info@qhubi.com";  // SMTP username
$mail->Password = "kozmo020687"; // SMTP password
$mail->CharSet = 'UTF-8';
$mail->isHTML(true);
$mail->From     = "info@qhubi.com"; // smtp kullanıcı
$mail->Fromname = "giden ismi";
$mail->Subject  =  "New Offer Request Available";


$kime = filter($_POST['email']);
$mails = explode('[;:,]',$kime);


$mailler=$_POST["email"];
//kontrol edelim
if(is_array($mailler)){
foreach($mailler as $mail_adresi){
$mail->AddBCC($mail_adresi,'');
}
}



$mail->Body = $message;
$mail->AltBody    = $message;


if(!$mail->Send())
{
   echo "Mesaj Gönderilemedi.Firma Mail adresini hatalı girmiş! <p>";
   echo "Mailer Error: " . $mail->ErrorInfo;
   exit;
}

header("Location: index.php");


?>