<?php
	//turn of error reporting (for the sake of user-end satisfaction?)
	$raw_names = @file_get_contents('../config/names.dat');

    $name_data = explode(PHP_EOL, $raw_names);
    $name_map = array();
    
    for($i = 0; $i < count($name_data); ++$i){
    	$tmp_name = explode("\t", $name_data[$i]);
    	
		$name_data[$i] = array(
			'name'	=>	$tmp_name[0],
			'TOJid'	=>	$tmp_name[1],
			'UVAid'	=>	$tmp_name[2],
			'ZJid'	=>	$tmp_name[3]
		);
		
		$name_map[$tmp_name[1]] = $i;
    }

    
    
    //load task data
    $raw_probs = @file_get_contents('../config/probs.dat');
	
    $prob_data = explode(PHP_EOL, $raw_probs);
    
    for($i = 0; $i < count($prob_data); ++$i){
		$tmp_prob = explode("\t", $prob_data[$i]);
		$prob_data[$i] = array(
			'judge'	=> $tmp_prob[0],
			'index'	=> $tmp_prob[1],	
		);
		if($prob_data[$i]['judge'] == 'UVa'){ $prob_data[$i]['pid'] = $tmp_prob[1/*2*/]; }
    }


    //load stats from cache
    $stats_data = array();
    foreach($name_data as $name){
    	$filename = '../cache/'.$name['TOJid'].'.dat';
    	$raw_stats = @file_get_contents($filename);
		if(!$raw_stats){
			//pending...
			$stats_data[$name['TOJid']] = -1;
		} else {
			$file = fopen($filename, 'r');
			$stats_data[$name['TOJid']] = $raw_stats;
		}
		
    }


	//load group data
	$raw_groups = @file_get_contents('../config/groups.dat');
	if(substr($raw_groups, -1) == ';') $raw_groups = substr($raw_groups, 0, -1);
	$groups = explode(';'.PHP_EOL, $raw_groups);
	
	for($i = 0; $i < count($groups); ++$i){
		$tmp_g = explode(PHP_EOL, $groups[$i]);
		$groups[$i] = array(
			'index' => $tmp_g[0],
			'label' => $tmp_g[1],
			'names' => explode(',', $tmp_g[2]),
			'probs' => explode(',', $tmp_g[3]),
		);
	}


    //check for update triggers etc.
    $status_string = '';
	$interval_limit = 40;
	

		$prev_updt = 0;
		if(file_exists('../cache/prev_updt')){
			$prev_updt = @file_get_contents('../cache/prev_updt');
		}
		
		$dt = time() - $prev_updt;
		$status_string .= "Last update: $dt cycles ago.<br>";
		
		if($dt < $interval_limit){
			$status_string .= "time interval limit($dt)<br>";
		} else {
			if (file_exists('../cache/work_flag')){
				$halt = true;
				$status_string .= 'update tasks pending...<br>';
			} else {
				$status_string .= 'update triggered.<br>';
			}
		}
		
?>