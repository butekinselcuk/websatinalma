<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta content="text/html; charset=UTF-8"
 http-equiv="content-type">
  <title></title>
<script type="text/javascript">

RegExp.prototype.harfRakam=function(str){
return  (this.test(str)) ? str.replace(this,"") : str ;
}

function f3(bu){
var re=/[^0-9]+/g;
bu.value= re.harfRakam(bu.value);
}

function f4(bu){
var re2=/[^A-z ]+/g;
bu.value= re2.harfRakam(bu.value);
}

</script>
</head>
<body>
rakam yaz: <input type="text" name="r" onkeyup="f3"><br>

harf yaz: <input type="text" name="h" onkeyup="f4(this)">
<br>
</body>
</html>