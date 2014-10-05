<?php
	//fetch API for UVa
	function getUVAstats($probs, $uid){
		$UVAstats = '';
		
		$data = file_get_contents("http://uhunt.felix-halim.net/api/subs-nums/$uid/".implode(',', $probs)."/0");
		if($data === false){ return false; }
		
		$data = json_decode($data, true);
		$data = $data[$uid]['subs'];
		
		$verdict = array();
		foreach($data as $sub){
			if($sub[2] == 90){
				$verdict[getUVaProbNum($sub[1])] = 9;
				
			} else if($verdict[getUVaProbNum($sub[1])] != 1){
				$verdict[getUVaProbNum($sub[1])] = 8;
				
			}
		}
		
		foreach($probs as $prob){
			if(!array_key_exists($prob, $verdict))
				$UVAstats .= 0;
			else
				$UVAstats .= $verdict[$prob];
		}
		
		//echo $UVAstats.'<br>';
		return $UVAstats;
	}
	
	//DUMMY VER.
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
	
	
	
    //fetch API for TOJ
    //TODO: Add support for WA
	function getTOJstats($probs, $uid){
		$TOJstats = '';
		
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
		if($response === false) return false;
		//echo $uid.'TOJ: ';
	
		$response = substr($response, 8, -2);
		$AClist = explode(', ', $response);
		foreach($probs as $p){
			if (in_array($p, $AClist)){
				$TOJstats .= 9;
			} else {
				$TOJstats .= 0;  
			}
		}
	
		return $TOJstats;
	}
			
	
	
    //fetch API for ZJ
	function getZJstats($prom,$ZJID){
		$ZJ_stats = '';
		/*
		$response=false;
		$reloadtimes=0;
		while($response==false&&$reloadtimes<=3){
			$reloadtimes++;
			$response=file_get_contents("http://zerojudge.tw/UserStatistic?account=".$ZJID);
		}
		*/
		$response=file_get_contents("http://zerojudge.tw/UserStatistic?account=".$ZJID);
		if(!$response) return false;
		
		foreach ($prom as $q){
			$start=strpos($response,"?problemid=".$q);
			$end=strpos($response,">".$q."</a>");
			$html=substr($response,$start,$end-$start);
			//print '<td>';
			
			if(strpos($html,'class="acstyle"')){
				$ZJ_stats .= 9;
			} else if(strpos($html,'color: #666666; font-weight: bold;')){
				$ZJ_stats .= 8;
			} else if(strpos($html,'color: #666666')) {
				$ZJ_stats .= 0;
			} else {
				//THROW ERROR
			}
		}

		return $ZJ_stats;
	}
?>