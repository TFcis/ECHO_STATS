<!DOCTYPE HTML>
<html>
<head>
    <meta charset = 'utf-8'>
    <link href = './res/theme.css' rel = 'stylesheet' type = 'text/css'>
<head>
<body style = "padding-left: 16px;">
	<pre style = "margin: 0;">
    <?php
		require_once("./func/EOL.php");
    	
    	echo '<br>: STATS PROCESSOR OPERATION LOG<br><br>';
		$starttime=microtime(true);
    	
    	echo '...setting up update environment & flags...<br>';
  		if(!file_exists('./config/')){
  			echo 'FATAL ERROR: config folder absent. Please create ./config/ manually.<br><br>';
  			exit();
  		}
  		
    	if(!file_exists('./cache/')){
  			echo 'FATAL ERROR: cache folder absent. Please create ./cache/ manually.<br><br>';
  			exit();
  		}
  		
  		if(file_exists('./cache/work_flag')){
  			echo '...other update tasks pending. Abort.<br><br>';
  			exit();
  		}
  		
  		$work_flag = fopen('./cache/work_flag', 'w');
  		if(!$work_flag){
  			echo 'FATAL ERROR: failed to create flag file. Check existence of ./config and grant write permission.<br><br>';
  			exit();
  		}
  		
		echo '...done!<br><br>';


        echo '...initializing config files...<br>';
        //initialize files: problemlist
        if(!file_exists('./config/probs.dat')){
            echo 'NOTICE: probs.dat absent from config folder.<br><br>';
            $file = fopen('./config/probs.dat', 'w');
            if($file){
                echo '...empty probs.dat file created.<br>';
                fclose($file);
            } else {
                echo 'FATAL ERROR: failed to initialize probs.dat. Check ./config and add file manually.<br><br>';
                exit();
            }
        } else {
            echo '	./config/probs.dat<br>';
        }
        
        //initialize files: namelist
        if(!file_exists('./config/names.dat')){
            echo 'NOTICE: names.dat absent from config folder.<br><br>';
            $file = fopen('./config/names.dat', 'w');
            if($file){
                echo '...empty names.dat file created.<br>';
                fclose($file);
            } else {
                echo 'FATAL ERROR: failed to initialize names.dat. Check ./config and add file manually.<br><br>';
                exit();
            }
        } else {
            echo '	./config/names.dat<br>';
        }
        echo '...done!<br><br>';
        
        
        
        
        
        
        echo '...fetching names...<br>';
        
        //load tracked accounts
        $raw_names = file_get_contents('./config/names.dat');
		$raw_names = handleEOL($raw_names);

        if($raw_names === false){
            echo 'FATAL ERROR: failed to fetch data from names.dat<br><br>';
            exit();
            
        } else {
            if ($raw_names == ''){ echo 'NOTICE: empty names.dat.<br><br>'; }
            $name_data = explode(PHP_EOL, $raw_names);
            
            for($i = 0; $i < count($name_data); ++$i){
            	$tmp_name = explode("\t", $name_data[$i]);
            	
				$name_data[$i] = array(
					'name'	=>	$tmp_name[0],
					'TOJid'	=>	$tmp_name[1],
					'UVAid'	=>	$tmp_name[2],
					'ZJid'	=>	$tmp_name[3],
					'GJid'	=>	$tmp_name[4],
					'TIOJid'	=>	$tmp_name[5],
					'TZJid'	=>	$tmp_name[6]
				);
            }
            //unset($tmp_name);
            
            /*
            foreach($name_data as $name){
                echo '<br>NAME: '.$name['name'].'<br>TOJ: '.$name['TOJid'].'<br>UVA: '.$name['UVAid'].'<br>ZJ: '.$name['ZJid'].'<br>';
            }
            */
        }
        echo '...done!<br><br>';
        
        
        
        
        
        
        echo '...fetching tasks...<br>';
        
        //load task data
        $raw_probs = file_get_contents('./config/probs.dat');
		$raw_probs = handleEOL($raw_probs);

        if($raw_probs === false){
            echo 'FATAL ERROR: failed to fetch data from probs.dat<br><br>';
            exit();
            
        } else {
            if ($raw_probs == ''){ echo 'NOTICE: empty names.dat.<br><br>'; }

            $prob_data = explode(PHP_EOL, $raw_probs);
            
            for($i = 0; $i < count($prob_data); ++$i){
				$tmp_prob = explode("\t", $prob_data[$i]);
				$prob_data[$i] = array(
					'judge'	=> $tmp_prob[0],
					'index'	=> $tmp_prob[1],	
				);
				if($prob_data[$i]['judge'] == 'UVa'){ $prob_data[$i]['pid'] = $tmp_prob[1/*2*/]; }
            }
        }
        /*
        foreach($prob_data as $prob){
            echo '	'.$prob['judge'].' '.$prob['index'].'<br>';
        }
        */
        echo '...done!<br><br>';



        echo '...starting update...<br>';
        
		require_once('./func/fetch.php');


		//Organize problemsets
        $sortedProbset = array();
        $sortedProbset['TOJ'] = array();
        $sortedProbset['UVa'] = array();
        $sortedProbset['ZJ'] = array();
		$sortedProbset['GJ'] = array();
		$sortedProbset['TIOJ'] = array();
		$sortedProbset['TZJ'] = array();
        
		echo '...organizing problemset...<br>';
        foreach($prob_data as $p){
        	if(!array_key_exists($p['judge'], $sortedProbset)){
        		echo 'ERROR: unrecognized judge '.$p['judge'].'. Problemset entry ignored.<br><br>';
	        } else {
	        	if($p['judge'] == 'UVa'){
        			$sortedProbset['UVa'][] = $p['pid'];
	        	} else {
	        		$sortedProbset[$p['judge']][] = $p['index'];
	        	}
        	}
        }
        
        /*
        echo '<br>';
        echo 'TOJ problemset:<br>';
        foreach($sortedProbset['TOJ'] as $prob){
            echo '	TOJ '.$prob.'<br>';
        }
        echo '<br>';
        echo 'UVa problemset:<br>';
        foreach($sortedProbset['UVa'] as $prob){
            echo '	UVa (pid)'.$prob.'<br>';
        }
        echo '<br>';
        echo 'ZJ problemset:<br>';
        foreach($sortedProbset['ZJ'] as $prob){
            echo '	ZJ '.$prob.'<br>';
        }
        echo '<br>';
        */
        echo '...done!<br><br>';

        
        
        //loop through namelist and update caches
        foreach($name_data as $name){
			$personstart=microtime(true);
		
			echo '...updating stats for '.$name['name'].'(Tid '.$name['TOJid'].')...<br>';
			
			$returned = array();
			$returned['UVa'] = 0;
			$returned['TOJ'] = 0;
			$returned['ZJ'] = 0;
			$returned['GJ'] = 0;
			$returned['TIOJ'] = 0;
			$returned['TZJ'] = 0;
			
			//fetch data
			$judgestart=microtime(true);
			echo '...fetching data from: UVa Online Judge...';
			if($name['UVAid']!="NULL")
			    $returned['UVa'] = getUVAstats($sortedProbset['UVa'], $name['UVAid']);
			else
			    $returned['UVa'] = 0;
			if($returned['UVa'] === false){
				echo 'ERROR: Invalid query or request timed out. Updates for UVa prblemset aborted for '.$name['name'].'.';
			} else if($returned['UVa'] === 0) {
			    echo $name['name'].' have no UVa account.';
			} else {
				//echo $UVAreturn;
				echo '...done!';
			}
			echo 'It takes '.(1000*(microtime(true)-$judgestart)).' milliseconds.<br>';
			
			$judgestart=microtime(true);
			echo '...fetching data from: TNFSH Online Judge...';
			if($name['TOJid']!="NULL")
			    $returned['TOJ'] = getTOJstats($sortedProbset['TOJ'], $name['TOJid']);
			else
			    $returned['UVa'] = 0;
			if($returned['TOJ'] === false){
				echo 'ERROR: Invalid query or request timed out. Updates for TOJ prblemset aborted for '.$name['name'].'.';
			} else if($returned['TOJ'] === 0) {
			    echo $name['name'].' have no TOJ account.';
			} else {
				//echo $TOJreturn;
				echo '...done!';
			}
			echo 'It takes '.(1000*(microtime(true)-$judgestart)).' milliseconds.<br>';
			
			$judgestart=microtime(true);
			echo '...fetching data from: ZeroJudge...';
			if($name['ZJid']!="NULL")
			    $returned['ZJ'] = getZJstats($sortedProbset['ZJ'], $name['ZJid']);
			else
			    $returned['ZJ'] = 0;
			if($returned['ZJ'] === false){
				echo 'ERROR: Invalid query or request timed out. Updates for ZJ prblemset aborted for '.$name['name'].'.';
			} else if($returned['ZJ'] === 0) {
			    echo $name['name'].' have no ZJ account.';
			} else {
				echo $ZJreturn;
				echo '...done!';
			}
			echo 'It takes '.(1000*(microtime(true)-$judgestart)).' milliseconds.<br>';
			
			$judgestart=microtime(true);
			echo '...fetching data from: Green Judge...';
			if($name['GJid']!="NULL")
			    $returned['GJ'] = getGJstats($sortedProbset['GJ'], $name['GJid']);
			else
			    $returned['GJ'] = 0;
			if($returned['GJ'] === false){
				echo 'ERROR: Invalid query or request timed out. Updates for GJ prblemset aborted for '.$name['name'].'.';
			} else if($returned['GJ'] === 0) {
			    echo $name['name'].' have no GJ account.';
			} else {
				echo $GJreturn;
				echo '...done!';
			}
			echo 'It takes '.(1000*(microtime(true)-$judgestart)).' milliseconds.<br>';
			
			$judgestart=microtime(true);
			echo '...fetching data from: TIOJ Infor Online Judge...';
			if($name['TIOJid']!="NULL")
			    $returned['TIOJ'] = getTIOJstats($sortedProbset['TIOJ'], $name['TIOJid']);
			else
			    $returned['TIOJ'] = 0;
			if($returned['TIOJ'] === false){
				echo 'ERROR: Invalid query or request timed out. Updates for TIOJ prblemset aborted for '.$name['name'].'.';
			} else if($returned['TIOJ'] === 0) {
			    echo $name['name'].' have no TIOJ account.';
			} else {
				echo $TIOJreturn;
				echo '...done!';
			}
			echo 'It takes '.(1000*(microtime(true)-$judgestart)).' milliseconds.<br>';
			
			$judgestart=microtime(true);
			echo '...fetching data from: Tnfsh online Judge System...';
			if($name['TZJid']!="NULL")
			    $returned['TZJ'] = getTZJstats($sortedProbset['TZJ'], $name['TZJid']);
			else
			    $returned['TZJ'] = 0;
			if($returned['TZJ'] === false){
				echo 'ERROR: Invalid query or request timed out. Updates for TZJ prblemset aborted for '.$name['name'].'.';
			} else if($returned['TZJ'] === 0) {
			    echo $name['name'].' have no TZJ account.';
			} else {
				echo $TZJreturn;
				echo '...done!';
			}
			echo 'It takes '.(1000*(microtime(true)-$judgestart)).' milliseconds.<br>';
			
			echo '...data fetching complete!<br>';
			
			echo '...organizing returned data...<br>';
			$tmp_prog = array();
			$tmp_prog['UVa'] = 0;
			$tmp_prog['TOJ'] = 0;
			$tmp_prog['ZJ'] = 0;
			$tmp_prog['GJ'] = 0;
			$tmp_prog['TIOJ'] = 0;
			$tmp_prog['TZJ'] = 0;
			
			$finalstats = '';
			foreach($prob_data as $p){
				$res = $returned[$p['judge']][$tmp_prog[$p['judge']]];
				if(!$res){
					$finalstats .= '0';
				} else {
					$finalstats .= $res;
				}
				++$tmp_prog[$p['judge']];
			}
			echo '...done!<br><br>';
			//echo $finalstats;
			
			//check for cache

        	echo '...checking for cache: <br>';
        	$filename = './cache/'.$name['TOJid'].'.dat';
			if(!file_exists($filename)){
				echo 'NOTICE: no pre-existing cached data for '.$name['name'].'(Tid '.$name['TOJid'].').<br>';
				$file = fopen($filename, 'w');
				if($file){
					echo '...cache created for '.$name['name'].' as '.$filename.'<br>';
				} else {
					echo 'FATAL ERROR: failed to create file. Please manually grant read/write authorization to ./cache.<br><br>';
				}
			} else {
				echo '	'.$filename.'<br>';
				$file = fopen($filename, 'w');
			}
			
			echo '...logging data to cache...<br>';
			if(!fwrite($file, $finalstats)){
				echo 'ERROR: failed to write to file. Please manually grant read/write authorization to ./cache.<br>';
			} else {
				echo '...data for '.$name['name'].' up to date.<br>';
			}
			
			fclose($file);
			
			echo 'It takes '.(microtime(true)-$personstart).' seconds.<br><br>';
        }
		echo '...update complete!<br><br>';

		echo '...logging update time & details...<br>';
        
        $prev_updt = fopen('./cache/prev_updt', 'w');
  		if(!$prev_updt){
  			echo 'NOTICE: failed to record update time. Next page request will trigger an update regardless of the interval limit.<br>';
  			exit();
  			
  		} else {
  			fwrite($prev_updt, time());
  			
  		}
  		echo '...done!<br><br>';
  		
  		
  		echo '...cleaning up...<br>';
  		
  		fclose($prev_updt);
        
        fclose($work_flag);
        unlink('./cache/work_flag');
    
    	echo '...done!<br><br>';
		
		echo 'It takes '.(microtime(true)-$starttime).' seconds.<br><br>';
		
    	echo '<br>: END STATS PROCESSOR OPERATION LOG<br><br>';
    
    ?>
    </pre>
</body>
</html>
