<?php
	require_once("curl_get_contents.php");
	$reload_times_limit=3;
	$load_time_limit=1000;
	
	//fetch API for UVa
	function getUVAstats($probs, $uid){
		$UVAstats = '';
		
		$funstart=microtime(true);
		$data = file_get_contents("http://uhunt.felix-halim.net/api/subs-nums/$uid/".implode(',', $probs)."/0");
		if($data === false){ return false; }
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td><td>N/A</td><td>N/A</td>';
		
		$funstart=microtime(true);
		
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
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td>';
		
		return $UVAstats;
	}
	$UVAdata = file_get_contents("http://uhunt.felix-halim.net/api/p/");
	$UVAdata = json_decode($UVAdata, true);
	function getUVaProbNum($probID){
		global $UVAdata;
		foreach($UVAdata as $prob){
			if($prob[0]==$probID)
				return $prob[1];
		}
	}
	
	
    //fetch API for TOJ
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
		$funstart=microtime(true);
		$response = file_get_contents('http://210.70.137.215/oj/be/api', false, stream_context_create($context));
		if($response === false) return false;
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td><td>N/A</td><td>N/A</td>';
		
		//Get NA list
		$funstart=microtime(true);
		$AClist = json_decode($response)->ac;
		$data = array(
				'reqtype' => 'NA',
				'acct_id' => $uid
			);
		$context['http'] = array (
			'timeout'   => 60,
			'method'	=> 'POST',
			'content'   => http_build_query($data, '', '&'),
		);
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td><td>N/A</td></tr>';
		echo '<tr><td></td>';
		$funstart=microtime(true);
		$response = file_get_contents('http://210.70.137.215/oj/be/api', false, stream_context_create($context));
		if($response === false) return false;
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td><td>N/A</td><td>N/A</td>';
		
		$funstart=microtime(true);
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
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td>';
		return $TOJstats;
	}
	
	
    //fetch API for ZJ
	function getZJstats($prom,$ZJID){
		global $reload_times_limit;
		global $load_time_limit;
		$ZJ_stats = '';
		$response=false;
		$reloadtimes=0;
		$loadtime=array();
		while($response==false&&$reloadtimes<$reload_times_limit){
			$funstart=microtime(true);
			//$response=curl_get_contents("http://zerojudge.tw/UserStatistic?account=".$ZJID,$load_time_limit);
			$response=curl_get_contents('http://zerojudge.tw/Login',10000,array('account' => 'tester123123' , 'passwd' => '123123' ,'returnPage' => 'http://zerojudge.tw/UserStatistic?account='.$ZJID ));
			$loadtime[$reloadtimes]=number_format(1000*(microtime(true)-$funstart),2);
			$reloadtimes++;
		}
		echo '<!--'.$response.'-->';
		if(!$response) return false;
		if(!(strrpos($response,"DataException")===false)) return false;
		echo '<td>'.$loadtime[0];
		for($i=1;$i<$reloadtimes;$i++)echo '/'.$loadtime[0];
		echo '</td>';
		
		//處理HTML
		$funstart=microtime(true);
		$response=str_replace(array("\r\n","\t"," ","\"","</a>",">","&account=".$ZJID),"",$response);
		$response=str_replace("style=color:#666666;font-weight:bold;title=","8",$response);//WA
		$response=str_replace("style=color:#666666title=","0",$response);//NA
		$response=str_replace("id=acstyleclass=acstyletitle=","9",$response);//AC
		$response=str_replace(array("<ahref=./Submissions?problemid=","<ahref=./ShowProblem?problemid=",),"\\",$response);
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td>';
		
		//建立表格
		$funstart=microtime(true);
		$Stats_array=array();
		$start=strpos($response,"\\");
		if($start===false)return -1;
		$length=strlen($response);
		for($i=$start;$i<$length;$i++){
			if($response[$i]=="\\"){
				$Stats_array[substr($response,$i+1,4)]=substr($response,$i+5,1);//$response[$i+5];
				$i+=9;
			}
		}
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td>';
		
		$funstart=microtime(true);
		foreach ($prom as $q){
			$ZJ_stats .= $Stats_array[$q];
		}
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td>';
		
		return $ZJ_stats;
	}

	
	//fetch API for TCGSJ
	function getGJstats($prom,$GJID){
		global $reload_times_limit;
		global $load_time_limit;
		$TCGSJ_stats = '';
		$response=false;
		$reloadtimes=0;
		$loadtime=array();
		while($response==false&&$reloadtimes<$reload_times_limit){
			$funstart=microtime(true);
			$response=curl_get_contents("http://www.tcgs.tc.edu.tw:1218/ShowUserStatistic?account=".$GJID,$load_time_limit);
			$loadtime[$reloadtimes]=number_format(1000*(microtime(true)-$funstart),2);
			$reloadtimes++;
		}
		if(!$response) return false;
		if(!(strrpos($response,"DataException")===false)) return false;
		echo '<td>'.$loadtime[0];
		for($i=1;$i<$reloadtimes;$i++)echo '/'.$loadtime[0];
		echo '</td>';
		
		//處理HTML
		$funstart=microtime(true);
		$response=str_replace(array("\r\n","\t"," ","\"","</a>",">","&account=".$GJID),"",$response);
		$response=str_replace("style=color:#666666;font-weight:bold;title=","8",$response);//WA
		$response=str_replace("style=color:#666666title=","0",$response);//NA
		$response=str_replace("id=acstyletitle=","9",$response);//AC
		$response=str_replace(array("<ahref=./RealtimeStatus?problemid=","<ahref=./ShowProblem?problemid="),"\\",$response);
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td>';
		
		//建立表格
		$funstart=microtime(true);
		$Stats_array=array();
		$start=strpos($response,"\\");
		if($start===false)return -1;
		$length=strlen($response);
		for($i=$start;$i<$length;$i++){
			if($response[$i]=="\\"){
				$Stats_array[substr($response,$i+1,4)]=substr($response,$i+5,1);//$response[$i+5];
				$i+=9;
			}
		}
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td>';
		
		$funstart=microtime(true);
		foreach ($prom as $q){
			$GJ_stats .= $Stats_array[$q];
		}
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td>';

		return $GJ_stats;
	}
	
	
	//fetch API for TIOJ
	function getTIOJstats($prom,$TIOJID){
		global $reload_times_limit;
		global $load_time_limit;
		$TIOJ_stats = '';
		$response=false;
		$reloadtimes=0;
		$loadtime=array();
		while($response==false&&$reloadtimes<$reload_times_limit){
			$funstart=microtime(true);
			$response=curl_get_contents("http://tioj.ck.tp.edu.tw/users/".$TIOJID,$load_time_limit);
			$loadtime[$reloadtimes]=number_format(1000*(microtime(true)-$funstart),2);
			$reloadtimes++;
		}
		if(!$response) return false;
		if(!(strrpos($response,"DataException")===false)) return false;
		echo '<td>'.$loadtime[0];
		for($i=1;$i<$reloadtimes;$i++)echo '/'.$loadtime[0];
		echo '</td>';
		
		//處理HTML
		$funstart=microtime(true);
		$response=str_replace(array("\r\n","\t"," ","\"","<td>","</td>","<tr>","</tr>","</a>",">","href=/problems/","/submissions?filter_user_id="),"",$response);
		$response=str_replace("text-warning","8",$response);//WA
		$response=str_replace("text-muted","0",$response);//NA
		$response=str_replace("text-success","9",$response);//AC
		$response=str_replace("<aclass=","*",$response);
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td>';

		//建立表格
		$funstart=microtime(true);
		$Stats_array=array();
		$start=strpos($response,"*");
		if($start===false)return -1;
		$length=strlen($response);
		for($i=$start;$i<$length;$i++){
			if($response[$i]=="*"){
				$Stats_array[substr($response,$i+2,4)]=substr($response,$i+1,1);//$response[$i+5];
				$i+=10;
			}
		}
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td>';
		
		$funstart=microtime(true);
		foreach ($prom as $q){
			$TIOJ_stats .= $Stats_array[$q];
		}
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td>';
		
		return $TIOJ_stats;
	}
	
	
	//fetch API for TZJ
	function getTZJstats($prom,$TZJID){
		global $reload_times_limit;
		global $load_time_limit;
		$TZJ_stats = '';
		$response=false;
		$reloadtimes=0;
		$loadtime=array();
		while($response==false&&$reloadtimes<$reload_times_limit){
			$funstart=microtime(true);
			$response=curl_get_contents("http://judge.tnfsh.tn.edu.tw:8080/ShowUserStatistic?account=".$TZJID,$load_time_limit);
			$loadtime[$reloadtimes]=number_format(1000*(microtime(true)-$funstart),2);
			$reloadtimes++;
		}
		if(!$response) return false;
		if(!(strrpos($response,"DataException")===false)) return false;
		echo '<td>'.$loadtime[0];
		for($i=1;$i<$reloadtimes;$i++)echo '/'.$loadtime[0];
		echo '</td>';
		
		//處理HTML
		$funstart=microtime(true);
		$response=str_replace(array("\r\n","\t"," ","\"","</a>",">","&account=".$TZJID),"",$response);
		$response=str_replace("style=color:#666666;font-weight:bold;title=","8",$response);//WA
		$response=str_replace("style=color:#666666title=","0",$response);//NA
		$response=str_replace("id=acstyletitle=","9",$response);//AC
		$response=str_replace(array("<ahref=./RealtimeStatus?problemid=","<ahref=./ShowProblem?problemid="),"\\",$response);
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td>';

		//建立表格
		$funstart=microtime(true);
		$Stats_array=array();
		$start=strpos($response,"\\");
		if($start===false)return -1;
		$length=strlen($response);
		for($i=$start;$i<$length;$i++){
			if($response[$i]=="\\"){
				$Stats_array[substr($response,$i+1,4)]=substr($response,$i+5,1);//$response[$i+5];
				$i+=9;
			}
		}
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td>';
		
		$funstart=microtime(true);
		foreach ($prom as $q){
			$TZJ_stats .= $Stats_array[$q];
		}
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td>';

		return $TZJ_stats;
	}
	
	
	//fetch API for POJ
	function getPOJstats($prom,$POJID){
		global $reload_times_limit;
		global $load_time_limit;
		$POJ_stats = '';
		$response=false;
		$reloadtimes=0;
		$loadtime=array();
		while($response==false&&$reloadtimes<$reload_times_limit){
			$funstart=microtime(true);
			$response=curl_get_contents("http://poj.org/usercmp?uid1=".$POJID."&uid2=".$POJID,$load_time_limit);
			$loadtime[$reloadtimes]=number_format(1000*(microtime(true)-$funstart),2);
			$reloadtimes++;
		}
		if(!$response) return false;
		if(!(strrpos($response,"DataException")===false)) return false;
		echo '<td>'.$loadtime[0];
		for($i=1;$i<$reloadtimes;$i++)echo '/'.$loadtime[0];
		echo '</td>';
		
		//處理HTML
		$funstart=microtime(true);
		$response=str_replace(array("\r\n","\t"," ","\"","</a>",">",$POJID,"<ahref=userstatus?user_id="),"",$response);
		$response=str_replace(array("<ahref=problem?id="),"\\",$response);
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td>';

		//建立表格
		$funstart=microtime(true);
		$Stats_array=array();
		//AC
		$start=strpos($response,"Problemsbothandaccepted");
		$end=strpos($response,"Problemsonlytriedbutfailed");
		$response_temp=substr($response,$start,$end-$start);
		$length=strlen($response_temp);
		for($i=0;$i<$length;$i++){
			if($response_temp[$i]=="\\"){
				$Stats_array[substr($response_temp,$i+1,4)]=9;
				$i+=8;
			}
		}
		//WA
		$start=strpos($response,"Problemsbothandtriedbutfailed");
		$end=strpos($response,"imgheight");
		$response_temp=substr($response,$start,$end-$start);
		$length=strlen($response_temp);
		for($i=0;$i<$length;$i++){
			if($response_temp[$i]=="\\"){
				$Stats_array[substr($response_temp,$i+1,4)]=8;
				$i+=8;
			}
		}
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td>';
		
		$funstart=microtime(true);
		foreach ($prom as $q){
			if($Stats_array[$q]==8||$Stats_array[$q]==9)$POJ_stats .= $Stats_array[$q];
			else $POJ_stats .= 0;
		}
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td>';

		return $POJ_stats;
	}
	
	
	//fetch API for HOJ
	function getHOJstats($prom,$HOJID){
		global $reload_times_limit;
		global $load_time_limit;
		$HOJ_stats = '';
		$response=false;
		$reloadtimes=0;
		$loadtime=array();
		while($response==false&&$reloadtimes<$reload_times_limit){
			$funstart=microtime(true);
			$response=curl_get_contents("http://hoj.twbbs.org/judge/user/view/".$HOJID,$load_time_limit);
			$loadtime[$reloadtimes]=number_format(1000*(microtime(true)-$funstart),2);
			$reloadtimes++;
		}
		if(!$response) return false;
		if(!(strrpos($response,"DataException")===false)) return false;
		echo '<td>'.$loadtime[0];
		for($i=1;$i<$reloadtimes;$i++)echo '/'.$loadtime[0];
		echo '</td>';
		
		//處理HTML
		$funstart=microtime(true);
		$response=str_replace(array("\r\n","\t"," ","\"","</a>","<ahref=http://hoj.twbbs.org/judge/problem/view/","</strong></span>","/","<td>","<th>"),"",$response);
		$response=str_replace("><spanclass=red><strong>","8",$response);//WA
		$response=str_replace("><spanclass=blue><strong>","9",$response);//AC
		$response=str_replace(">","0",$response);//NA
		$response=str_replace(array("&nbsp;&nbsp;&nbsp;","<br0"),",",$response);
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td>';
		
		//建立表格
		$funstart=microtime(true);
		$start=strpos($response,"Problems,")+9;
		$end=strpos($response,"<tr0<table0");
		$response_temp=substr($response,$start,$end-$start);
		$response_array=explode(",",$response_temp);
		$Stats_array=array();
		foreach($response_array as $temp){
			$length=(strlen($temp)-1)/2;
			$Stats_array[substr($temp,0,$length)]=$temp[$length];
		}
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td>';
		
		$funstart=microtime(true);
		foreach ($prom as $q){
			$HOJ_stats .= $Stats_array[$q];
		}
		echo '<td>'.(1000*(microtime(true)-$funstart)).'</td>';

		return $HOJ_stats;
	}
?>
