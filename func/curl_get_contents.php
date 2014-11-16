<?php
/*function curl_get_contents($url,$timeout){ 
	$curlHandle = curl_init(); 
	curl_setopt( $curlHandle , CURLOPT_URL, $url ); 
	curl_setopt( $curlHandle , CURLOPT_RETURNTRANSFER, 1 ); 
	curl_setopt( $curlHandle , CURLOPT_TIMEOUT_MS, $timeout ); 
	$result = curl_exec( $curlHandle ); 
	curl_close( $curlHandle ); 
	return $result; 
} */
function curl_get_contents($url,$timeout){
	$ctx = stream_context_create(
    	array(
            'http' => array(
                'timeout' => $timeout/1000
            )
        )
    ); 
    return file_get_contents($url, 0, $ctx); 
}
?>