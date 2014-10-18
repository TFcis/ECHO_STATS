<!DOCTYPE HTML>
<html>
<head>
    <meta charset = 'utf-8'>
    <link href = './res/theme.css' rel = 'stylesheet' type = 'text/css'>
<head>
<body style = "padding-left: 16px;">
	<pre style = "margin: 0;">
    <?php
    	
    	echo '<br>: STATS PROCESSOR OPERATION LOG<br><br>';
    	
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
		$raw_names = str_replace(array("\r\n","\r","\n"),PHP_EOL,$raw_names);

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
					'ZJid'	=>	$tmp_name[3]
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
		$raw_probs = str_replace(array("\r\n","\r","\n"),PHP_EOL,$raw_probs);

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
			echo '...updating stats for '.$name['name'].'(Tid '.$name['TOJid'].')...<br>';
			
			$returned = array();
			$returned['UVa'] = 0;
			$returned['TOJ'] = 0;
			$returned['ZJ'] = 0;
			
			//fetch data
			echo '...fetching data from: UVa Online Judge...';
			$returned['UVa'] = getUVAstats($sortedProbset['UVa'], $name['UVAid']);
			if($returned['UVa'] === false){
				echo '<br>ERROR: Invalid query or request timed out. Updates for UVa prblemset aborted for '.$name['name'].'.<br><br>';
			} else {
				//echo $UVAreturn;
				echo '...done!<br>';
			}
			
			echo '...fetching data from: TNFSH Online Judge...';
			$returned['TOJ'] = getTOJstats($sortedProbset['TOJ'], $name['TOJid']);
			if($returned['TOJ'] === false){
				echo '<br>ERROR: Invalid query or request timed out. Updates for TOJ prblemset aborted for '.$name['name'].'.<br><br>';
			} else {
				//echo $TOJreturn;
				echo '...done!<br>';
			}
			
			echo '...fetching data from: ZeroJudge...';
			$returned['ZJ'] = getZJstats($sortedProbset['ZJ'], $name['ZJid']);
			if($returned['ZJ'] === false){
				echo '<br>ERROR: Invalid query or request timed out. Updates for ZJ prblemset aborted for '.$name['name'].'.<br><br>';
			} else {
				echo $ZJreturn;
				echo '...done!<br>';
			}
			echo '...data fetching complete!<br>';
			
			echo '...organizing returned data...<br>';
			$tmp_prog = array();
			$tmp_prog['UVa'] = 0;
			$tmp_prog['TOJ'] = 0;
			$tmp_prog['ZJ'] = 0;
			
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
				echo 'ERROR: failed to write to file. Please manually grant read/write authorization to ./cache.<br><br>';
			} else {
				echo '...data for '.$name['name'].' up to date.<br><br>';
			}
			
			fclose($file);


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
    	
    	echo '<br>: END STATS PROCESSOR OPERATION LOG<br><br>';
    
    ?>
    </pre>
</body>
</html>
