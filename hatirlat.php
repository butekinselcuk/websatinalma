// şifre talep formu

function sifretalep(){

global $siteadi;  //benim sitemin adı siz isterseniz elle yazabilirsiniz ben global olarak cektim

echo"<div class="kaydol_crv">



<div class="kaydol_crv_ust">

<div class="kaydol_crv_logo"><img src="images/logo.png" width="45" height="45" /></div>

<div class="kaydol_crv_slogan">$siteadi Şifre Talep Formuna Hoşgeldiniz.</div>

<div class="kaydol_crv_cizgi"></div>

</div>

<div class="kaydol_crv_ortagiris">

<form action="sayfa.php?Git=uyehesabi&sayfa=talep" method="POST">

<div class="kaydol_crv_frmkutu">

<div class="kaydol_crv_frmtext">Adınız :</div>

<div class="kaydol_crv_frm"><input class="form" name="yazaradi" type="text" /></div>

</div>

<div class="kaydol_crv_frmkutu">

<div class="kaydol_crv_frmtext">E-postanız :</div>

<div class="kaydol_crv_frm"><input class="form" name="email" type="text" /></div>

</div>

<div class="www.siteadiniz.com"><input class="submit_Kaydol" type="submit" value="?ifre talep"/></div>

</form>

</div>;

}