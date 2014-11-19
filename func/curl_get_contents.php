<?php
/*function __construct(){
	zjcore = new zjcore;
	zjcore->websiteurl = "http://zerojudge.tw/";
	zjcore->classname  = "class_zerojudge";
	cookiefile = new privatedata();
}*/
function curl_get_contents($url,$timeout,$post=null,$usepost =true){
	if(is_array($post)){
		ksort( $post );
		$post = http_build_query( $post );
	}
	$curlHandle = curl_init();
	curl_setopt( $curlHandle , CURLOPT_URL, $url );
	curl_setopt( $curlHandle , CURLOPT_ENCODING, "UTF-8" );
	if($usepost){
		curl_setopt( $curlHandle , CURLOPT_POST, true );
		curl_setopt( $curlHandle , CURLOPT_POSTFIELDS , $post );
	}
	curl_setopt( $curlHandle , CURLOPT_RETURNTRANSFER, true ); 
	//curl_setopt ($curlHandle , CURLOPT_COOKIEFILE, cookiefile->name() );
	//curl_setopt ($curlHandle , CURLOPT_COOKIEJAR , cookiefile->name() );
	curl_setopt( $curlHandle , CURLOPT_TIMEOUT_MS, $timeout ); 
	$result = curl_exec( $curlHandle ); 
	curl_close( $curlHandle ); 
	return $result; 
} 
/*function curl_get_contents($url,$timeout){
	$ctx = stream_context_create(
    	array(
            'http' => array(
                'timeout' => $timeout/1000
            )
        )
    ); 
    return file_get_contents($url, 0, $ctx); 
}*/
?>