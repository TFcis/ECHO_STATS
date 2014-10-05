<?php
//prevent overlapping processing tasks
$flag = fopen('./cache/work_flag', 'w');

$problist = fopen('./dat/problemlist.dat', 'r');

$type = Array();
$index = Array();

$TOJproblist = Array();
//$TOJgroups = Array();

$UVAproblist = Array();
//$UVAgroups = Array();

$ZJ_problist = Array();
//$ZJ_groups = Array();

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

$names = Array();
$TOJid = Array();
$UVAid = Array();
$ZJ_id = Array();

$namecount = 0;

if($namelist){
	
	while($n = fscanf($namelist, "%d\t%s\t%d\t%d\t%s\n")){
		//$group[$namecount] = (int)$n[0];
		$names[$namecount] = $n[1];
		$TOJid[$namecount] = (int)$n[2];
		$UVAid[$namecount] = (int)$n[3];
		$ZJ_id[$namecount] = $n[4];
		++$namecount;
	}
	fclose($namelist);
	
} else {
	//THROW ERROR
}


include 'fetchAPI.php';
$TOJstats = Array();
$UVAstats = Array();
$ZJ_stats = Array();


for ($i = 0; $i < $namecount; ++$i){
	echo '<br>LOG FOR '.$names[$i].'<br>';
	$filename = './cache/'.$TOJid[$i].'.dat';
	
	if(file_exists($filename))
		$cache_raw = file_get_contents($filename);
	else
		$cache_raw = '';
	
	unset($cache);
	$cache[] = explode(',', $cache_raw);
	
	unset($TOJstats);                
	unset($UVAstats);
	unset($ZJ_stats);

	$TOJstats = getTOJstatus($TOJproblist, $TOJid[$i]);
	$UVAstats = getUVaStatus($UVAproblist, $UVAid[$i]);
	$ZJ_stats = getZJstatus($ZJ_problist, $ZJ_id[$i]);
	
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