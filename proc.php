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
  		
		$search=$_SERVER['QUERY_STRING'];
		$ignore_work_flag=false;
		echo '...Update for';
		if($search=='if'){
			echo ' all...Ignore work_flag';
			$ignore_work_flag='if';
		} else if($search){
			$update_array=explode(",",$search);
			foreach($update_array as $id){
				if(is_numeric($id)){
					echo ' '.$id;
					$ignore_work_flag='num';
				}
			}
		}
		if($ignore_work_flag==false){
			if(file_exists('./cache/work_flag')){
				echo ' all...other update tasks pending. Abort.';
				exit();
			} else {
				$work_flag = fopen('./cache/work_flag', 'w');
				echo ' all';
				if(!$work_flag){
					echo 'FATAL ERROR: failed to create flag file. Check existence of ./config and grant write permission.<br><br>';
					exit();
				}
				if(!fwrite($work_flag, time())){
					echo 'ERROR: failed to write to file. Please manually grant read/write authorization to ./cache';
				} else {
					echo '...work_flag up to date';
				}
			}
		}
		echo '.<br>...done!<br><br>';


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
					'TZJid'	=>	$tmp_name[6],
					'POJid'	=>	$tmp_name[7],
					'HOJid'	=>	$tmp_name[8]
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
		$sortedProbset['POJ'] = array();
        $sortedProbset['HOJ'] = array();
        
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
        echo '...done!<br><br>';

		
        $update_ok = array("UVa"=>0,"TOJ"=>0,"ZJ"=>0,"GJ"=>0,"TIOJ"=>0,"TZJ"=>0,"POJ"=>0,"HOJ"=>0);
		$update_fail = array("UVa"=>0,"TOJ"=>0,"ZJ"=>0,"GJ"=>0,"TIOJ"=>0,"TZJ"=>0,"POJ"=>0,"HOJ"=>0);
        //loop through namelist and update caches
        foreach($name_data as $name){
			if($ignore_work_flag=='num'&&!in_array($name['TOJid'],$update_array))continue;
			$personstart=microtime(true);
		
			echo '...updating stats for '.$name['name'].'(Tid '.$name['TOJid'].')...<br>';
			
			$returned = array();
			$returned['UVa'] = 0;
			$returned['TOJ'] = 0;
			$returned['ZJ'] = 0;
			$returned['GJ'] = 0;
			$returned['TIOJ'] = 0;
			$returned['TZJ'] = 0;
			$returned['POJ'] = 0;
			$returned['HOJ'] = 0;
			echo '<table class=MsoTableGrid border=1 cellpadding=3 style="border-collapse:collapse;border:none"><tr><td>Judge</td><td>Read file</td><td>Process HTML</td><td>Create table</td><td>Create result</td><td>Total</td></tr>';
			//fetch data
			echo '<tr><td>UVa('.$name['UVAid'].')</td>';
			$judgestart=microtime(true);
			if($name['UVAid']=="NULL"){
				echo '<td colspan="4">No UVa account.</td>';
			} else if(count($sortedProbset['UVa'])==0){
			    echo '<td colspan="4">There is no UVa problem.</td>';
			} else {
				$returned['UVa'] = getUVAstats($sortedProbset['UVa'], $name['UVAid']);
				if($returned['UVa'] === false){
					$update_fail['UVa']++;
					echo '<td colspan="4">ERROR: Invalid query or request timed out.</td>';
				} else {
					$update_ok['UVa']++;
				}
			}
			echo '<td>'.(1000*(microtime(true)-$judgestart)).'</td></tr>';
			
			echo '<tr><td>TOJ('.$name['TOJid'].')</td>';
			$judgestart=microtime(true);
			if($name['TOJid']=="NULL"){
				echo '<td colspan="4">No TOJ account.</td>';
			} else if(count($sortedProbset['TOJ'])==0){
			    echo '<td colspan="4">There is no TOJ problem.</td>';
			} else {
				$returned['TOJ'] = getTOJstats($sortedProbset['TOJ'], $name['TOJid']);
				if($returned['TOJ'] === false){
					$update_fail['TOJ']++;
					echo '<td colspan="4">ERROR: Invalid query or request timed out.</td>';
				} else {
					$update_ok['TOJ']++;
				}
			}
			echo '<td>'.(1000*(microtime(true)-$judgestart)).'</td></tr>';
			
			$judgestart=microtime(true);
			echo '<tr><td>ZJ('.$name['ZJid'].')</td>';
			if($name['ZJid']=="NULL"){
				echo '<td colspan="4">No ZJ account.</td>';
			} else if(count($sortedProbset['ZJ'])==0){
			    echo '<td colspan="4">There is no ZJ problem.</td>';
			} else {
				$returned['ZJ'] = getZJstats($sortedProbset['ZJ'], $name['ZJid']);
				if($returned['ZJ'] === false){
					$update_fail['ZJ']++;
					echo '<td colspan="4">ERROR: Invalid query or request timed out.</td>';
				} else {
					$update_ok['ZJ']++;
				}
			}
			echo '<td>'.(1000*(microtime(true)-$judgestart)).'</td></tr>';
			
			$judgestart=microtime(true);
			echo '<tr><td>GJ('.$name['GJid'].')</td>';
			if($name['GJid']=="NULL"){
				echo '<td colspan="4">No GJ account.</td>';
			} else if(count($sortedProbset['GJ'])==0){
			    echo '<td colspan="4">There is no GJ problem.</td>';
			} else {
				$returned['GJ'] = getGJstats($sortedProbset['GJ'], $name['GJid']);
				if($returned['GJ'] === false){
					$update_fail['GJ']++;
					echo '<td colspan="4">ERROR: Invalid query or request timed out.</td>';
				} else {
					$update_ok['GJ']++;
				}
			}
			echo '<td>'.(1000*(microtime(true)-$judgestart)).'</td></tr>';
			
			$judgestart=microtime(true);
			echo '<tr><td>TIOJ('.$name['TIOJid'].')</td>';
			if($name['TIOJid']=="NULL"){
				echo '<td colspan="4">No TIOJ account.</td>';
			} else if(count($sortedProbset['TIOJ'])==0){
				echo '<td colspan="4">There is no TIOJ problem.</td>';
			} else {
				$returned['TIOJ'] = getTIOJstats($sortedProbset['TIOJ'], $name['TIOJid']);
				if($returned['TIOJ'] === false){
					$update_fail['TIOJ']++;
					echo '<td colspan="4">ERROR: Invalid query or request timed out.</td>';
				} else {
					$update_ok['TIOJ']++;
				}
			}
			echo '<td>'.(1000*(microtime(true)-$judgestart)).'</td></tr>';
			
			$judgestart=microtime(true);
			echo '<tr><td>TZJ('.$name['TZJid'].')</td>';
			if($name['TZJid']=="NULL"){
				echo '<td colspan="4">No TZJ account.</td>';
			} else if(count($sortedProbset['TZJ'])==0){
			    echo '<td colspan="4">There is no TZJ problem.</td>';
			} else {
				$returned['TZJ'] = getTZJstats($sortedProbset['TZJ'], $name['TZJid']);
				if($returned['TZJ'] === false){
					$update_fail['TZJ']++;
					echo '<td colspan="4">ERROR: Invalid query or request timed out.</td>';
				} else {
					$update_ok['TZJ']++;
				}
			}
			echo '<td>'.(1000*(microtime(true)-$judgestart)).'</td></tr>';
			
			$judgestart=microtime(true);
			echo '<tr><td>POJ('.$name['POJid'].')</td>';
			if($name['POJid']=="NULL"){
				echo '<td colspan="4">No POJ account.</td>';
			} else if(count($sortedProbset['POJ'])==0){
			    echo '<td colspan="4">There is no POJ problem.</td>';
			} else {
				$returned['POJ'] = getPOJstats($sortedProbset['POJ'], $name['POJid']);
				if($returned['POJ'] === false){
					$update_fail['POJ']++;
					echo '<td colspan="4">ERROR: Invalid query or request timed out.</td>';
				} else {
					$update_ok['POJ']++;
				}
			}
			echo '<td>'.(1000*(microtime(true)-$judgestart)).'</td></tr>';
			
			$judgestart=microtime(true);
			echo '<tr><td>HOJ('.$name['HOJid'].')</td>';
			if($name['HOJid']=="NULL"){
				echo '<td colspan="4">No HOJ account.</td>';
			} else if(count($sortedProbset['HOJ'])==0){
			    echo '<td colspan="4">There is no HOJ problem.</td>';
			} else {
				$returned['HOJ'] = getHOJstats($sortedProbset['HOJ'], $name['HOJid']);
				if($returned['HOJ'] === false){
					$update_fail['HOJ']++;
					echo '<td colspan="4">ERROR: Invalid query or request timed out.</td>';
				} else {
					$update_ok['HOJ']++;
				}
			}
			echo '<td>'.(1000*(microtime(true)-$judgestart)).'</td></tr>';
			
			echo '</table>';
			
			echo '...data fetching complete!<br>';
			
			echo '...organizing returned data...<br>';
			$tmp_prog = array();
			$tmp_prog['UVa'] = 0;
			$tmp_prog['TOJ'] = 0;
			$tmp_prog['ZJ'] = 0;
			$tmp_prog['GJ'] = 0;
			$tmp_prog['TIOJ'] = 0;
			$tmp_prog['TZJ'] = 0;
			$tmp_prog['POJ'] = 0;
			$tmp_prog['HOJ'] = 0;
			
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
			echo '...done!<br>';
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
		
		if($ignore_work_flag==false||$ignore_work_flag=='if'){
			$update_result='';
			function updateresult($ok,$fail){
				if($ok==0&&$fail==0)return "0";
				else if($ok==0&&$fail>0)return "1";
				else if($ok>0&&$fail>0)return "2";
				else return "3";
			}
			$update_result.=updateresult($update_ok['TOJ'],$update_fail['TOJ']);
			$update_result.=updateresult($update_ok['ZJ'],$update_fail['ZJ']);
			$update_result.=updateresult($update_ok['TZJ'],$update_fail['TZJ']);
			$update_result.=updateresult($update_ok['GJ'],$update_fail['GJ']);
			$update_result.=updateresult($update_ok['TIOJ'],$update_fail['TIOJ']);
			$update_result.=updateresult($update_ok['UVa'],$update_fail['UVa']);
			$update_result.=updateresult($update_ok['POJ'],$update_fail['POJ']);
			$update_result.=updateresult($update_ok['HOJ'],$update_fail['HOJ']);
			
			$filename = './cache/judge_available';
			if(!file_exists($filename)){
				echo 'NOTICE: no '.$filename.'.<br>';
				$file = fopen($filename, 'w');
				if($file){
					echo '...created '.$filename.'<br>';
				} else {
					echo 'FATAL ERROR: failed to create file. Please manually grant read/write authorization to ./cache.<br><br>';
				}
			} else {
				$file = fopen($filename, 'w');
			}
			if(!fwrite($file, $update_result)){
				echo 'ERROR: failed to write to file. Please manually grant read/write authorization to ./cache.<br>';
			} else {
				echo '...update_result up to date.<br>';
			}
			fclose($file);
		}
		
		if(!is_numeric($search)){
			echo '...logging update time & details...<br>';
			$prev_updt = fopen('./cache/prev_updt', 'w');
			if(!$prev_updt){
				echo 'NOTICE: failed to record update time. Next page request will trigger an update regardless of the interval limit.<br>';
				exit();
			} else {
				fwrite($prev_updt, time());
			}
			echo '...done!<br><br>';
			fclose($prev_updt);
  		}
  		
		if($ignore_work_flag==false){
			echo '...cleaning up...<br>';
			fclose($work_flag);
			unlink('./cache/work_flag');
			echo '...done!<br><br>';
		}
		
		echo 'It takes '.(microtime(true)-$starttime).' seconds.<br><br>';
		
    	echo '<br>: END STATS PROCESSOR OPERATION LOG<br><br>';
    
    ?>
    </pre>
</body>
</html>
