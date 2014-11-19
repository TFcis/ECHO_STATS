<html>
<head>
    <meta charset = 'utf-8'>
	<title>EchoStats Admin</title>
	<link href='http://fonts.googleapis.com/css?family=Lato:400,700,900' rel='stylesheet' type='text/css'>
	<link href = '../res/theme.css' rel = 'stylesheet' type = 'text/css'>
</head>
<body>

<?php
if(md5($_COOKIE["ECHOSTATSCOOKIE"]."stats")=="33de5de39a0d42085bbf72073f789f5c")echoAdminPage();
else if(!isset($_POST["pwd"])){
	echoLoginPage();
}else{
	if( md5(md5($_POST["pwd"])."stats") == "33de5de39a0d42085bbf72073f789f5c" ){
		echo "login succeeded<br/><br/>";
		setcookie("ECHOSTATSCOOKIE",md5($_POST["pwd"]),time()+86400*7);
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
$files = array("groups","names","probs","inform");
if(isset($_POST[$files[0]])){
    echo "edit ";
	foreach($files as $file){
		$content = $_POST[$file];
		if($file == "probs"){
			$ojs = array("TOJ","ZJ","UVa","GJ","TIOJ","TZJ","POJ","HOJ");
			foreach($ojs as $oj)
				$content = preg_replace("/$oj/i", $oj, $content);
			$content = str_replace(" ", "\t", $content);
		}
		if(@file_put_contents("../config/$file.dat",$content) === false)
			echo "Failed to write file: $file. Please check file permission.<br/>";
	}
	echo "done ".time()."<br/>";
}else if(isset($_POST["prev_updt"])){
	$content=$_POST["prev_updt"];
	if(@file_put_contents("../cache/prev_updt",$content) === false)
		echo "Failed to write file: prev_updt. Please check file permission.<br/>";
}else if(isset($_POST["judge_available"])){
	$content=$_POST["judge_available"];
	if(@file_put_contents("../cache/judge_available",$content) === false)
		echo "Failed to write file: judge_available. Please check file permission.<br/>";
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
<a id="updatelink" href="../proc.php" target="_blank"></a>
update: <input id="updateinput" name="update" class="cache"> <input type="button" value="Open" onclick="document.all.updatelink.href='../proc.php?'+document.all.updateinput.value;document.all.updatelink.click();document.all.updateinput.value='';">
</form>

<form method="POST">
<input type="submit" value="Submit"><br/>
<?php
foreach($files as $file){
?>
<?=$file?>:<br/>
<textarea class="config" name="<?=$file?>">
<?=htmlentities(@file_get_contents("../config/$file.dat"))?>
</textarea><br/>
<?php } ?>
<input type="hidden" name="pwd" value="<?=$_POST["pwd"]?>">
</form>

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

<form method="POST">
prev_updt: <input name="prev_updt" class="cache" value="<?=htmlentities(@file_get_contents("../cache/prev_updt"))?>"> <input type="submit" value="Submit">
</form>

<form method="POST">
judge_available: <input name="judge_available" class="cache" value="<?=htmlentities(@file_get_contents("../cache/judge_available"))?>"> <input type="submit" value="Submit">
</form>

<form method="POST">
work_flag: <input name="work_flag" class="cache" value="<?=htmlentities(@file_get_contents("../cache/work_flag"))?>"> <!--<input type="submit" value="Submit">-->
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