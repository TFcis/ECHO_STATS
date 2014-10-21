<html>
<head>
<meta charset="UTF-8">
<title>TCGS-Judge-API</title>
</head>
<body>

<?php
	$url = $_SERVER['QUERY_STRING'];
	$url2 = explode("&",$url);
	$pl = explode(",",$url2[1]);
    //fetch API for TCGSJ
	function getZJstats($prom,$TCGSJID){
		$TCGSJ_stats = '';
		/*
		$response=false;
		$reloadtimes=0;
		while($response==false&&$reloadtimes<=3){
			$reloadtimes++;
			$response=file_get_contents("http://www.tcgs.tc.edu.tw:1218/ShowUserStatistic?account=".$TCGSJID);
		}
		*/
		$response=file_get_contents("http://www.tcgs.tc.edu.tw:1218/ShowUserStatistic?account=".$TCGSJID);
		if(!$response) return false;
		if(!(strrpos($response,"DataException")===false)) return false;
		
		foreach ($prom as $q){
			$start=strpos($response,"?problemid=".$q);
			$end  =strpos($response,">".$q."</a>");
			$html =substr($response,$start,$end-$start);
			//print '<td>';
			
			if(strpos($html,'id="acstyle"')){
				$TCGSJ_stats .= $q." AC<br>";
				//$TCGSJ_stats .= 9;
			} else if(strpos($html,'color: #666666; font-weight: bold;')){
				$TCGSJ_stats .= $q." WA<br>";
				//$TCGSJ_stats .= 8;
			} else if(strpos($html,'color:#666666')) {
				$TCGSJ_stats .= $q." N/A<br>";
				//$TCGSJ_stats .= 0;
			} else {
				//THROW ERROR
			}
		}

		return $TCGSJ_stats;
	}
	print getZJstats($pl,$url2[0]);
?>

</body>
</html>