<?php
	$cookie_jar = './cookie.txt';
	$url='http://zerojudge.tw/Login';
	$postdata='account=tester123123&passwd=123123';
	
    $resource = curl_init();
    curl_setopt($resource, CURLOPT_URL, $url);
    curl_setopt($resource, CURLOPT_POST, 1);
    curl_setopt($resource, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($resource, CURLOPT_COOKIEFILE, $cookie_jar);
    curl_setopt($resource, CURLOPT_COOKIEJAR, $cookie_jar);
    curl_setopt($resource, CURLOPT_RETURNTRANSFER, 1);
    curl_exec($resource);
	echo $resource.'<br><br><br>';
	
	$url='http://zerojudge.tw/UserStatistic?account=hawaii';
	curl_setopt($resource, CURLOPT_URL, $url);
    curl_setopt($resource, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($resource, CURLOPT_COOKIEFILE, $cookie_jar);
    $content = curl_exec($resource);
	echo $resource.'<br><br><br>';
} 
?>