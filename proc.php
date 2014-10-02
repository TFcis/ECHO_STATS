<?php
    //prevent overlapping processing tasks
    $flag = fopen('./cache/work_flag', 'w');

    $problist = fopen('./dat/problemlist.dat', 'r');
    
    $type = [];
    $index = [];
    
    $TOJproblist = [];
    $UVAproblist = [];
    $ZJ_problist = [];
    
    $probcount = 0;
    
    if($problist){
        while($n = fscanf($problist, "%s\t%s\n")){
            
            if(!$n[0] == 'ZJ'){
                $n[1] = (int)$n[1];
            }
            
            if($n[0] == 'UVa'){
                $UVAproblist[] = $n[1];
                
            } else if ($n[0] == 'TOJ') {
                $TOJproblist[] = $n[1];
                
            } else if ($n[0] == 'ZJ') { 
                $ZJ_problist[] = $n[1];
                
            }
            
            $type[$probcount] = $n[0];
            $index[$probcount] =$n[1];

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
        
        while($n = fscanf($namelist, "%s\t%d\t%d\t%s\n")){
            $names[$namecount] = $n[0];
            $TOJid[$namecount] = $n[1];
            $UVAid[$namecount] = $n[2];
            $ZJ_id[$namecount] = $n[3];
            
            $filename = './cache/'.$n[1].'.dat';
            if(!file_exists($file)){
                //cho 'create file';
                $file = fopen($filename, 'w');
                //if($file){echo 'ok';}
            }

            ++$namecount;
        }
        fclose($namelist);
        
    } else {
        //THROW ERROR
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
        
        if($raw_data == ''){            //........................N/A
            return 0;
        } else {
        
        $subs = explode('],[', $raw_data);
        
        for($i = 0; $i < count($subs); ++$i){
            $subs[$i] = explode(',', $subs[$i])[2];
            if ($subs[$i] == '90'){     //........................AC
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
    
    //echo '<tr>';
    //echo '<td>'.$names[$i].'</td>';//<td>'.$TOJid[$i].'</td><td>'.$UVAid[$i].'</td><td>'.$ZJ_id[$i].'</td>';
    
    unset($TOJstats);                
    unset($UVAstats);
    unset($ZJ_stats);

    getUVaStatus($UVAproblist, $UVAid[$i]);
    getZJstatus($ZJ_problist, $ZJ_id[$i]);
    
    
    /*
    echo 'ZJ: ';
    foreach($ZJ_stats as $i){
        echo ' '.$i;
    }
    echo '<br>';
    
    echo 'UVA: ';
    foreach($UVAstats as $i){
        echo ' '.$i;
    }
    echo '<br>';
    */
    
    
    
    
    $TOJp = 0;
    $UVAp = 0;
    $ZJ_p = 0;
    
    
    for($j = 0; $j < $probcount; ++$j){
        $res = 0;
        if($cache[$j] == 1) {
            //AC, do nothing
        } else if($type[$j] == 'TOJ'){
            $cache[$j] = 0;
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
    $file = fopen($filename, 'w');
    $update = implode(',', $cache);
    echo $update.'<br>';
    
    fwrite($file, $update);
    fclose($file);
            
}

$tlog = fopen('./cache/prev_uptd', 'w');
fwrite($tlog, time());
fclose($tlog);

fclose($flag);
unlink('./cache/work_flag');

?>