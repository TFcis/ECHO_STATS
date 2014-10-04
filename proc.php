<?php
	//prevent overlapping processing tasks
	$flag = fopen('./cache/work_flag', 'w');

	$problist = fopen('./dat/problemlist.dat', 'r');
	
	$type = [];
	$index = [];
	
	$TOJproblist = [];
	//$TOJgroups = [];
	
	$UVAproblist = [];
	//$UVAgroups = [];
	
	$ZJ_problist = [];
	//$ZJ_groups = [];
	
	$probcount = 0;
	
	if($problist){
		while($n = fscanf($problist, "%d\t%s\t%s\n")){
			
			if(!$n[1] == 'ZJ'){
				$n[2] = (int)$n[2];
			}
			
			if($n[1] == 'UVa'){
				//$UVAgroups[] = $n[0];
				$UVAproblist[] = $n[2];
				
			} else if ($n[1] == 'TOJ') {
				//$TOJgroups[] = $n[0];
				$TOJproblist[] = $n[2];
				
			} else if ($n[1] == 'ZJ') {
				//$ZJ_groups[] = $n[0];
				$ZJ_problist[] = $n[2];
				
			}
			
			$type[$probcount] = $n[1];
			$index[$probcount] =$n[2];

			++$probcount;
		
		}
		fclose($problist);
	} else {
		//THROW ERROR
	}



	//LOAD USER DATA
	$namelist = fopen('./dat/namelist.dat', 'r');
	
	$names = [];
	$TOJid = [];
	$UVAid = [];
	$ZJ_id = [];
	
	$namecount = 0;
	
	if($namelist){
		
		while($n = fscanf($namelist, "%d\t%s\t%d\t%d\t%s\n")){
			//$group[$namecount] = (int)$n[0];
			$names[$namecount] = $n[1];
			$TOJid[$namecount] = (int)$n[2];
			$UVAid[$namecount] = (int)$n[3];
			$ZJ_id[$namecount] = $n[4];
			/*
			$filename = './cache/'.$n[1].'.dat';
			if(!file_exists($file)){
				//cho 'create file';
				$file = fopen($filename, 'w');
				fclose($file);
				//if($file){echo 'ok';}
			}
			*/
			++$namecount;
		}
		fclose($namelist);
		
	} else {
		//THROW ERROR
	}

	$TOJstats = [];
	function getTOJStatus($probs, $uid){
		global $TOJstats;
		
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

	}

	$UVAstats = [];	
	function getUVaStatus($probs, $uid){
		global $UVAstats;
		foreach($probs as $p){
			$UVAstats[] = fetchprobUVa($p, $uid);
		}
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
			$subs[$i] = explode(',', $subs[$i])[2];
			if ($subs[$i] == '90'){	 //........................AC
				return 1;
			}
			
		}
		
		return -1;                      //........................WA, etc.
		
		}
		
	}
	
	
	$ZJ_stats = [];
	
	function getZJstatus($prom,$ZJID){
	global $ZJ_stats;
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
	}




for ($i = 0; $i < $namecount; ++$i){
	echo '<br>LOG FOR '.$names[$i].'<br>';
	$filename = './cache/'.$TOJid[$i].'.dat';
	
	$cache_raw = file_get_contents($filename);
	
	unset($cache);
	$cache[] = explode(',', $cache_raw);
	
	unset($TOJstats);                
	unset($UVAstats);
	unset($ZJ_stats);

	getTOJstatus($TOJproblist, $TOJid[$i]);
	getUVaStatus($UVAproblist, $UVAid[$i]);
	getZJstatus($ZJ_problist, $ZJ_id[$i]);
	
	$TOJp = 0;
	$UVAp = 0;
	$ZJ_p = 0;
	
	
	for($j = 0; $j < $probcount; ++$j){
		$res = 0;
		if($cache[$j] == 1) {
			//AC, do nothing
		} else if($type[$j] == 'TOJ'){
			$cache[$j] = $TOJstats[$TOJp];
			//$res = $TOJstats[$TOJp];
			++$TOJp;
			
		} else if ($type[$j] == 'UVa') {
			$cache[$j] = $UVAstats[$UVAp];
			++$UVAp; 
			
		} else if ($type[$j] == 'ZJ') {
			$cache[$j] = $ZJ_stats[$ZJ_p];
			++$ZJ_p;
			
		} else {
			//THROW ERROR
		}
	}

	$update = implode(',', $cache);
	echo $update.'<br>';
	
	$file = fopen($filename, 'w');
	fwrite($file, $update);
	fclose($file);
			
}

$tlog = fopen('./cache/prev_uptd', 'w');
fwrite($tlog, time());
fclose($tlog);

fclose($flag);
unlink('./cache/work_flag');

?>