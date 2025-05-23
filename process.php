$name = strip_tags($_POST['name']);
$email = strip_tags($_POST['email']);
$phone = strip_tags($_POST['phone']);
$url = strip_tags($_POST['url']);
$message = strip_tags($_POST['message']);
// gönder
mail( "info@site.com", "İletişim Formu",
"İsim: $name\nEmail: $email\nTel: $phone\nWebsite: $url\nMesaj: $message\n",
"Gönderen: &lt;info@siteniz.com&gt;" );