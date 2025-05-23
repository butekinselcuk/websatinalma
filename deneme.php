<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Başlıksız Belge</title>
<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
<link href="../assets/css/plugins.min.css" rel="stylesheet">
<link href="../assets/css/plugins.css" rel="stylesheet">
<link href="admin/assets/magicsuggest/magicsuggest-min.css" rel="stylesheet">

</head>

div class="panel panel-default">
div class="panel-heading">4 - Select Species ?
div class="panel-body">
div class="btn-group" id="speciesSelect">
button id="allspecies" type="button" class="btn btn-default btn-sm">All Species
button id="aquatic" type="button" class="btn btn-default btn-sm">Aquatic
button id="terrestria" type="button" class="btn btn-default btn-sm">Terrestrial
/div>
select class="selectpicker" id="selectPicker" class="form-control" data-container="body" data-live-search="true">
option value="0">Select From The List
/div>
/div>
    
<body>
<script src="jquery.js" type="text/javascript"></script>
<script src="../assets/magicsuggest/magicsuggest-min.js"></script>
<script type="text/javascript"> function list1(GonderilenId) {
$('#list').load('islem.php?id='+GonderilenId );
} </script>
</body>
</html>
