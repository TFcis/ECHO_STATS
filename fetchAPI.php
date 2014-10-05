<?php
function getTOJStatus($probs, $uid){
	$TOJstats = Array();
	
	$data = array(
			'reqtype' => 'AC',
			'acct_id' => $uid
		);
		
	$context = array();
	$context['http'] = array (
		'timeout'   => 60,
		'method'	=> 'POST',
		'content'   => http_build_query($data, '', '&'),
	);
	
	$response = file_get_contents('http://210.70.137.215/oj/be/api', false, stream_context_create($context));
	
	//echo $uid.'TOJ: ';

	$response = substr($response, 8, -2);
	$AClist = explode(',', $response);
	foreach($probs as $p){
		if (in_array($p, $AClist)){
			//echo '1,';
			$TOJstats[] = 1;
		} else {
			//echo '0,';
			$TOJstats[] = 0;  
		}
	}

	return $TOJstats;
}

/*function getUVaStatus($probs, $uid){
	$UVAstats = Array();
	foreach($probs as $p){
		$UVAstats[] = fetchprobUVa($p, $uid);
	}
	return $UVAstats;
}

function fetchprobUVa($pid, $uid){

	$raw_data = file_get_contents('http://uhunt.felix-halim.net/api/subs-nums/'.$uid.'/'.$pid.'/0');
	$raw_data = substr($raw_data, strpos($raw_data, '"subs":') + 9, -6);
	
	//echo $raw_data;
	
	$res = 0;
	
	if($raw_data == ''){			//........................N/A
		return 0;
	} else {
	
	$subs = explode('],[', $raw_data);
	
	for($i = 0; $i < count($subs); ++$i){
		$temp = explode(',', $subs[$i]);
		$subs[$i] = $temp[2];
		if ($subs[$i] == '90'){	 //........................AC
			return 1;
		}
		
	}
	
	return -1;                      //........................WA, etc.
	
	}
	
}*/

function getUVaStatus($probs, $uid){
	$UVAstats = Array();
	
	$probs_csv = implode(',', $probs);
	
	$data = file_get_contents("http://uhunt.felix-halim.net/api/subs-nums/$uid/$probs_csv/0");
	$data = json_decode($data, true);
	$data = $data[$uid]['subs'];
	
	$verdict = Array();
	foreach($data as $sub){
		if($sub[2]==90)
			$verdict[getUVaProbNum($sub[1])] = 1;
		else if($verdict[getUVaProbNum($sub[1])] != 1)
			$verdict[getUVaProbNum($sub[1])] = -1;
	}
	
	foreach($probs as $prob){
		if(!array_key_exists($prob, $verdict))
			$UVAstats[] = 0;
		else
			$UVAstats[] = $verdict[$prob];
	}
	
	return $UVAstats;
}

$UVAdata = file_get_contents("http://uhunt.felix-halim.net/api/p/");
$UVAdata = json_decode($UVAdata, true);
function getUVaProbNum($probID){
	//todo
	global $UVAdata;
	foreach($UVAdata as $prob){
		if($prob[0]==$probID)
			return $prob[1];
	}
}

function getZJstatus($prom,$ZJID){
	$ZJ_stats = Array();
	/*
	$response=false;
	$reloadtimes=0;
	while($response==false&&$reloadtimes<=3){
		$reloadtimes++;
		$response=file_get_contents("http://zerojudge.tw/UserStatistic?account=".$ZJID);
	}
	*/
	$response=file_get_contents("http://zerojudge.tw/UserStatistic?account=".$ZJID);
	if($response){
		foreach ($prom as $q){
				$start=strpos($response,"?problemid=".$q);
				$end=strpos($response,">".$q."</a>");
				$html=substr($response,$start,$end-$start);
				//print '<td>';
				
				if(strpos($html,'class="acstyle"')){
					$ZJ_stats[] = 1;
				} else if(strpos($html,'color: #666666; font-weight: bold;')){
					$ZJ_stats[] = -1;
				} else if(strpos($html,'color: #666666')) {
					$ZJ_stats[] = 0;
				} else {
					//THROW ERROR
				}
		}

	} else {
		//THROW ERROR
	}
	
	return $ZJ_stats;
}
?>
