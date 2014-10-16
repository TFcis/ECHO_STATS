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

<style>
.config{
	width:600px;
	height:300px;
}
</style>

<input type="submit">
<form method="POST">
<?php
$files = array("groups","names","probs");
foreach($files as $file){
?>
<?=$file?>:<br/>
<textarea class="config" name="<?=$file?>">
<?=file_get_contents("../config/$file.dat")?>
</textarea><br/>
<?php } ?>
</form>


<?php } ?>



<?php
function echoLoginPage(){  ?>
<form method="POST">
Password: <input type="password" name="pwd">
</form>
<?php } ?>