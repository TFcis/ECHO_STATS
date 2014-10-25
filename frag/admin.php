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
    echo "edit ";
	foreach($files as $file){
		$content = $_POST[$file];
		if($file == "probs"){
			$ojs = array("TOJ","ZJ","UVa","GJ","TIOJ");
			foreach($ojs as $oj)
				$content = preg_replace("/$oj/i", $oj, $content);
			$content = str_replace(" ", "\t", $content);
		}
		if(@file_put_contents("../config/$file.dat",$content) === false)
			echo "Failed to write file: $file. Please check file permission.<br/>";
	}
	echo "done ".time()."<br/>";
}else if(isset($_POST["url"])){
    $url = $_POST["url"];
    $url = explode("#", $url); $url = $url[0];
    $url = explode("index.php", $url); $url = $url[0];
    if(substr($url, -1)!="/")
        $url .= "/";
    $url .= "config/";
    echo "clone setting from $url  ";
	foreach($files as $file){
	    $content = "";
	    $dataUrl = $url.$file.".dat";
        if(($content = @file_get_contents($dataUrl)) === false)
            echo "Unable to fetch data from: ".$dataUrl."<br/>";
        else if(@file_put_contents("../config/$file.dat",$content) === false)
			echo "Failed to write file: $file. Please check file permission.<br/>";
	}
	echo "done ".time()."<br/>";
}else if(isset($_POST["deleteCache"])){
    echo "Delete Cache:<br/>";
    foreach (glob("../cache/*.dat") as $filename) {
        echo "$filename - size " . filesize($filename) . "<br/>";
        unlink($filename);
    }
    echo "done";
}else if(isset($_POST["deleteWorkFlag"])){
    echo "Delete Work Flag:<br/>";
    unlink("../cache/work_flag");
    echo "done";
}
?>

<style>
.config{
	width:600px;
	height:300px;
}
</style>

<form method="POST">
<input type="submit" value="Submit"><br/>
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

<br/><br/>

Clone setting from another ECHO_STATS:
<form method="POST">
<input type="text" name="url" placeholder="ECHO_STATS URL">
<input type="submit" value="Download">
<input type="hidden" name="pwd" value="<?=$_POST["pwd"]?>">
<br/>Waring: It will replace all the setting data here.
</form>

<form method="POST">
<input type="submit" value="Delete Cache">
<input type="hidden" name="pwd" value="<?=$_POST["pwd"]?>">
<input type="hidden" name="deleteCache" value="true">
</form>

<form method="POST">
<input type="submit" value="Delete Work Flag">
<input type="hidden" name="pwd" value="<?=$_POST["pwd"]?>">
<input type="hidden" name="deleteWorkFlag" value="true">
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