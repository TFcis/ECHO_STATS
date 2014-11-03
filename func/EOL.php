<?php
function handleEOL($str) {
	$str = str_replace(array("\r\n","\r","\n"),"\n",$str);
	$str = str_replace("\n",PHP_EOL,$str);
	return $str;
}
?>