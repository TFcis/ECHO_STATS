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
		
		//Get AC list
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
	
		$AClist = json_decode($response)->ac;
		
		//Get NA list
		$data = array(
				'reqtype' => 'NA',
				'acct_id' => $uid
			);
			
		$context['http'] = array (
			'timeout'   => 60,
			'method'	=> 'POST',
			'content'   => http_build_query($data, '', '&'),
		);
		
		$response = file_get_contents('http://210.70.137.215/oj/be/api', false, stream_context_create($context));
		if($response === false) return false;
		//echo $uid.'TOJ: ';
	
		$NAlist = json_decode($response)->na;
		
		foreach($probs as $p){
			if (in_array($p, $AClist)){
				$TOJstats .= 9;
			} else if (in_array($p, $NAlist)) {
				$TOJstats .= 8;
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
		if(!(strrpos($response,"DataException")===false)) return false;
		
		//處理HTML
		$response=str_replace(array("\r\n","\t"," ","\"","</a>",">","&account=".$ZJID,),"",$response);
		$response=str_replace("style=color:#666666;font-weight:bold;title=","8",$response);//WA
		$response=str_replace("style=color:#666666title=","0",$response);//NA
		$response=str_replace("id=acstyleclass=acstyletitle=","9",$response);//AC
		$response=str_replace(array("<ahref=./Submissions?problemid=","<ahref=./ShowProblem?problemid=",),"\\",$response);

		//建立表格
		$Stats_array=array();
		for($i=strpos($response,"\\");$i<=strlen($response);$i++){
			if($response[$i]=="\\"){
				$Stats_array[substr($response,$i+1,4)]=substr($response,$i+5,1);//$response[$i+5];
				$i+=9;
			}
		}
		
		foreach ($prom as $q){
			$ZJ_stats .= $Stats_array[$q];
		
			/*$start=strpos($response,"?problemid=".$q);
			$end  =strpos($response,">".$q."</a>");
			$html =substr($response,$start,$end-$start);
			//print '<td>';
			
			if(strpos($html,'class="acstyle"')){
				$ZJ_stats .= 9;
			} else if(strpos($html,'color: #666666; font-weight: bold;')){
				$ZJ_stats .= 8;
			} else if(strpos($html,'color: #666666')) {
				$ZJ_stats .= 0;
			} else {
				//THROW ERROR
			}*/
		}

		return $ZJ_stats;
	}
	
	
	
	//fetch API for TCGSJ
	function getGJstats($prom,$GJID){
		$TCGSJ_stats = '';
		/*
		$response=false;
		$reloadtimes=0;
		while($response==false&&$reloadtimes<=3){
			$reloadtimes++;
			$response=file_get_contents("http://www.tcgs.tc.edu.tw:1218/ShowUserStatistic?account=".$GJID);
		}
		*/
		$response=file_get_contents("http://www.tcgs.tc.edu.tw:1218/ShowUserStatistic?account=".$GJID);
		if(!$response) return false;
		if(!(strrpos($response,"DataException")===false)) return false;
		
		//處理HTML
		$response=str_replace(array("\r\n","\t"," ","\"","</a>",">","&account=".$GJID,),"",$response);
		$response=str_replace("style=color:#666666;font-weight:bold;title=","8",$response);//WA
		$response=str_replace("style=color:#666666title=","0",$response);//NA
		$response=str_replace("id=acstyletitle=","9",$response);//AC
		$response=str_replace(array("<ahref=./RealtimeStatus?problemid=","<ahref=./ShowProblem?problemid="),"\\",$response);

		//建立表格
		$Stats_array=array();
		for($i=strpos($response,"\\");$i<=strlen($response);$i++){
			if($response[$i]=="\\"){
				$Stats_array[substr($response,$i+1,4)]=substr($response,$i+5,1);//$response[$i+5];
				$i+=9;
			}
		}
		
		foreach ($prom as $q){
			$GJ_stats .= $Stats_array[$q];
		
			/*$start=strpos($response,"?problemid=".$q);
			$end  =strpos($response,">".$q."</a>");
			$html =substr($response,$start,$end-$start);
			//print '<td>';
			
			if(strpos($html,'id="acstyle"')){
				$GJ_stats .= 9;
			} else if(strpos($html,'color: #666666; font-weight: bold;')){
				$GJ_stats .= 8;
			} else if(strpos($html,'color:#666666')) {
				$GJ_stats .= 0;
			} else {
				//THROW ERROR
			}*/
		}

		return $GJ_stats;
	}
	
	
	
	//fetch API for TIOJ
	function getTIOJstats($prom,$TIOJID){
		$TIOJ_stats = '';
		$response=file_get_contents("http://tioj.ck.tp.edu.tw/users/".$TIOJID);
		if(!$response) return false;
		if(!(strrpos($response,"DataException")===false)) return false;
		
		foreach ($prom as $q){
			$start=strpos($response,"/problems/".$q."/submissions")-25;
			$end  =strpos($response,"/problems/".$q."/submissions")-6;
			$html =substr($response,$start,$end-$start);
			//print '<td>';
			
			if(strpos($html,'text-success')){
				$TIOJ_stats .= 9;
			} else if(strpos($html,'text-warning')){
				$TIOJ_stats .= 8;
			} else if(strpos($html,'text-muted')) {
				$TIOJ_stats .= 0;
			} else {
				//THROW ERROR
			}
		}

		return $TIOJ_stats;
	}
	
	
	//fetch API for TZJ
	function getTZJstats($prom,$TZJID){
		$TZJ_stats = '';
		$response=file_get_contents("http://judge.tnfsh.tn.edu.tw:8080/ShowUserStatistic?account=".$TZJID);
		if(!$response) return false;
		if(!(strrpos($response,"DataException")===false)) return false;
		
		//處理HTML
		$response=str_replace(array("\r\n","\t"," ","\"","</a>",">","&account=".$TZJID,),"",$response);
		$response=str_replace("style=color:#666666;font-weight:bold;title=","8",$response);//WA
		$response=str_replace("style=color:#666666title=","0",$response);//NA
		$response=str_replace("id=acstyletitle=","9",$response);//AC
		$response=str_replace("<ahref=./RealtimeStatus?problemid=","\\",$response);
		$response=str_replace("<ahref=./ShowProblem?problemid=","\\",$response);

		//建立表格
		$Stats_array=array();
		for($i=strpos($response,"\\");$i<=strlen($response);$i++){
			if($response[$i]=="\\"){
				$Stats_array[substr($response,$i+1,4)]=substr($response,$i+5,1);//$response[$i+5];
				$i+=9;
			}
		}
		
		foreach ($prom as $q){
			$TZJ_stats .= $Stats_array[$q];
		
			/*$start=strpos($response,"?problemid=".$q);
			$end  =strpos($response,">".$q."</a>");
			$html =substr($response,$start,$end-$start);
			//print '<td>';
			
			if(strpos($html,'id="acstyle"')){
				$TZJ_stats .= 9;
			} else if(strpos($html,'color: #666666; font-weight: bold;')){
				$TZJ_stats .= 8;
			} else if(strpos($html,'color:#666666')) {
				$TZJ_stats .= 0;
			} else {
				//THROW ERROR
			}*/
		}

		return $TZJ_stats;
	}
?>
