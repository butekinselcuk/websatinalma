<?php

	
include 'phpmailer/class.phpmailer.php';
function GetIP(){
	if(getenv("HTTP_CLIENT_IP")) {
 		$ip = getenv("HTTP_CLIENT_IP");
 	}elseif(getenv("HTTP_X_FORWARDED_FOR")) {
 		$ip = getenv("HTTP_X_FORWARDED_FOR");
 		if (strstr($ip, ',')) {
 			$tmp = explode (',', $ip);
 			$ip = trim($tmp[0]);
 		}
 	}else{
 	$ip = getenv("REMOTE_ADDR");
 	}
	return $ip;
}

$ip_adress 	= GetIP();
$name       = addslashes(strip_tags($_POST['name'])); 
$sub 		= addslashes(strip_tags($_POST['subject']));
$email     	= addslashes(strip_tags($_POST['email'])); 
$message    = addslashes(strip_tags($_POST['message'])); 
 
 if(empty($name) || empty($email) || empty($message) || empty($sub)){header("Location:form.php?empty"); }else{
  
$mail = new PHPMailer();
$mail->IsSMTP();                                   
$mail->Host     = "mail.kurumsaleposta.com"; // smtp host
$mail->Port     = "25";  // Port
$mail->SMTPAuth = true;    
$mail->Username = "info@worldpurnet.com";  //mail address
$mail->Password = "RF:5A9bp3h0_:vE="; //email password
$mail->From     = "info@worldpurnet.com"; // from mail address
$mail->Fromname = "worldpurnet"; // From Name
$mail->AddAddress("info@worldpurnet.com","worldpurnet"); //your mail address and name
$mail->WordWrap     = 50; 
$mail->Subject      = $sub; // Mail Subject
$mail->Body         = "	Name : ".$name. "
						<br>E-mail: ".$email. "
					<br>Message: ".$message . " 
					<br>IP : ".$ip_adress ; 
     

$mail->AddReplyTo($email,"Contact Form");
$mail->AddAddress('info@worldpurnet.com');  //mail address
$mail->IsHTML(true); 

if($mail->Send()) 

echo "";

}

 

?>