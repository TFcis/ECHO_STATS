<?php
function curl_get_contents($url,$timeout){ 
	$curlHandle = curl_init(); 
	curl_setopt( $curlHandle , CURLOPT_URL, $url ); 
	curl_setopt( $curlHandle , CURLOPT_RETURNTRANSFER, 1 ); 
	curl_setopt( $curlHandle , CURLOPT_TIMEOUT, $timeout ); 
	$result = curl_exec( $curlHandle ); 
	curl_close( $curlHandle ); 
	return $result; 
} 
?>