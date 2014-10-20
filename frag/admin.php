<html>
<head>
    <meta charset = 'utf-8'>
	<title>EchoStats Admin</title>
	<link href='http://fonts.googleapis.com/css?family=Lato:400,700,900' rel='stylesheet' type='text/css'>
	<link href = '../res/theme.css' rel = 'stylesheet' type = 'text/css'>
</head>
<body>

<?php

if(!isset($_POST["pwd"])){
	echoLoginPage();
}else{
	if( md5(md5($_POST["pwd"])."stats") == "33de5de39a0d42085bbf72073f789f5c" ){
		echo "login succeeded<br/><br/>";
		echoAdminPage();
	}else{
		echo "wrong password<br/><br/>";
		echoLoginPage();
	}
}

?>


<?php
function echoAdminPage(){  ?>

<?php
$files = array("groups","names","probs");
if(isset($_POST[$files[0]])){
	foreach($files as $file)
		$content = $_POST[$file];
		if($file == "probs"){
			$ojs = array("TOJ","ZJ","UVa");
			foreach($ojs as $oj)
				$content = preg_replace("/$oj/i", $oj, $content);
			$content = str_replace(" ", "\t", $content);
		}
		if(!@file_put_contents("../config/$file.dat",$content))
			echo "Failed to write file: $file. Please check file permission.<br/>";
	echo "done<br/>";
}
?>

<style>
.config{
	width:600px;
	height:300px;
}
</style>

<form method="POST">
<input type="submit"><br/>
<?php
foreach($files as $file){
?>
<?=$file?>:<br/>
<textarea class="config" name="<?=$file?>">
<?=@file_get_contents("../config/$file.dat")?>
</textarea><br/>
<?php } ?>
<input type="hidden" name="pwd" value="<?=$_POST["pwd"]?>">
</form>


<?php } ?>



<?php
function echoLoginPage(){  ?>
<form method="POST">
Password: <input type="password" name="pwd">
</form>
<?php } ?>

</body>
</html>