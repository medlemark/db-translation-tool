<html>
<head>
<title></title>
<meta charset='utf-8' />
<style>
	.current input {
    background: #eaeaea;
}
</style>
</head>
<body>
<?php
echo "IP  : ".$_SERVER['REMOTE_ADDR'];

if(!in_array($_SERVER['REMOTE_ADDR'],['ipaddess1','ipaddress2']) ){
	die(' not allowed to access this page ');
	exit();
}

$user ="myusername";
$pass ="password";
$database = "mydatabase";
if(!isset($_GET['lang'])){
	header('location: tool.php?lang=en');
}
if(isset($_GET['lang']) && in_array($_GET['lang'],['en','ru','fr']) ){
$lang = $_GET['lang'];
$table_name  ="table_".$lang;
$code  =$lang;	
}
$arr = ['fr'=>'french','ru'=>'russian','en'=>'english'];

echo "<form action='t.php' method='get'>";
echo " you are editing now : <select onchange='this.form.submit();' name='lang'>";
foreach($arr as $k => $v){
	$selected  = $code == $k ? 'selected' : '';
	echo "<option value='$k' $selected >$v</option>";
}
echo "<select></form>";
$q = new mysqli("localhost",$user,$pass,$database);
if (isset($_POST['catid'])) {
	$u_q ="UPDATE $table_name SET";
	$comma = ' ';
	foreach ($_POST['data'] as $key => $value) {
		$u_q.= $comma.$key.'="'.mb_convert_encoding($value, 'HTML-ENTITIES', 'UTF-8').'"';
		$comma =', ';
	}
	$u_q.=" WHERE id = ".$_POST['catid'].";";
	$q->query($u_q) or die($q->error." sql = ".$u_q);
	$q->close();
	exit();
}

$data = $q->query("select catid,name,seo,details,mtitle,mdesc,mtags,scriptolution_cattag1,scriptolution_cattag2,scriptolution_cattag3 from $table_name");

 
?>

<table>
<tr>
<th>name</th>
<th>seo</th>
<th>details</th>
<th>mtitle</th>
<th>mdesc</th>
<th>mtags</th>
<th>cat tag1</th>
<th>cat tag2</th>
<th>cat tag3</th>
<th>translate it</th>
</tr>

<?php
while($re= $data->fetch_assoc()){
	$i=0;
	echo "<tr>";
	if (empty($columns)) {
        		$columns = array_keys($re);
   	 }
	foreach($columns as $k => $v){
 		 
			
		echo "<td><input type='text' name='".$v."' value='".htmlspecialchars_decode($re[$v])."'/></td>";
	}
	 
	echo "<td><input type='hidden' id='ref' value='".htmlspecialchars_decode($re['catid'])."'/><button id='translateit'>translate</button></td>";
	echo "<td><button onclick='submit();'>submit</button></td>";
	echo "<td><button onclick='resubmit(this);'>resubmit</button></td>";
	echo "</tr>";
	//var_dump($re);
}
?>
</table>
<script src="jquery-3.3.1.min.js"></script>
<script>
$(document).ready(function(){
console.log("it works");
$(document).on('click','#translateit',function(){
	var tr = $(this).closest('tr');
	console.log(tr);
	tr.addClass('current');
	var elms = tr.find('input:not(#ref)');
	for (var i = 0; i < elms.length; i++) {
		translateWords($(elms[i]).val(),elms[i]);
	}
 });
});
function translateWords(str,row){
	$.get('https://translate.googleapis.com/translate_a/single?client=gtx&sl=de&tl=<?php echo $code; ?>&dt=t&q='+str,function(re){
		console.log(re)
		$(row).val(re[0][0][0]);
	});
}
function copyto(){
	var p = $('.current input');
	var data = {};
	for (var i = 0; i < p.length; i++) {
		var k = {};
		data[$(p[i]).attr('name')] = $(p[i]).val();
		//var v = $(p[i]).val();
	}


}
function submit(rid){
	var p = $('.current input:not(#ref)');
	var id = $('.current #ref').val();
	var data = {};
	for (var i = 0; i < p.length; i++) {
		var k = {};
		data[$(p[i]).attr('name')] =$(p[i]).val();
		//var v = $(p[i]).val();
	}
 
	$.post('tool.php',{'data':data,'catid':id},function(){

		$('.current input:not(#ref)').css({'background':'green'});
		$('tr').removeClass('current');
	});
}
function resubmit(tr) {
	var tr = $(tr).closest('tr');
 		 var p = $(tr).find('input:not(#ref)');
		var id = $(tr).find('#ref').val();
		var data = {};
		for (var i = 0; i < p.length; i++) {
			var k = {};
			data[$(p[i]).attr('name')] =$(p[i]).val();
			//var v = $(p[i]).val();
		}
	 
		$.post('tool.php?lang=<?php echo $code; ?>',{'data':data,'catid':id},function(){
			$(tr).find('input:not(#ref)').css({'background':'green'});
	 	});
}
	

</script>
<?php
$q->close();
