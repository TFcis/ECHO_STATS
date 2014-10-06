<!DOCTYPE HTML>
<?php
		//turn of error reporting (for the sake of user-end satisfaction?)
		error_reporting(0); 
		ini_set('display_errors', 0);


        $raw_names = file_get_contents('./config/names.dat');

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
        $raw_probs = file_get_contents('./config/probs.dat');
		
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
        	$filename = './cache/'.$name['TOJid'].'.dat';
        	$raw_stats = file_get_contents($filename);
			if(!$raw_stats){
				//pending...
				$stats_data[$name['TOJid']] = -1;
			} else {
				$file = fopen($filename, 'r');
				$stats_data[$name['TOJid']] = $raw_stats;
			}
			
        }


		//load group data
		$raw_groups = file_get_contents('./config/groups.dat');
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
			if(file_exists('./cache/prev_updt')){
				$prev_updt = file_get_contents('./cache/prev_updt');
			}
			
			$dt = time() - $prev_updt;
			$status_string .= "Last update: $dt cycles ago.<br>";
			
			if($dt < $interval_limit){
				$status_string .= "time interval limit($dt)<br>";
			} else {
				if (file_exists('./cache/work_flag')){
					$halt = true;
					$status_string .= 'update tasks pending...<br>';
				} else {
					$status_string .= 'update triggered.<br>';
				}
			}

		
	?>
<html>
<head>
    <meta charset = 'utf-8'>
	<link href='http://fonts.googleapis.com/css?family=Lato:400,700,900' rel='stylesheet' type='text/css'>
	<link href = 'theme.css' rel = 'stylesheet' type = 'text/css'>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	
	<script>
		var halt = <?php echo ($halt ? 'true' : 'false'); ?> ;
		if(!halt){ $.get('proc.php'); }
	</script>
	
</head>
<body>
	<div id = "title" style = "position: relative">
	<center>
		<!--<div style = "position: absolute; bottom: 0; left: 128px; text-align: right">-->
		<div style = "margin: 40px 0 0 0">
		
			<h1><span style = "color: #999999">ECHO</span> STATS <span style = "color: #999999">;</h1>
			
			<div style = "color: #666666;"><br>
			<?php
			echo $status_string;
			?>
			<br></div>
			
			<div style = "color: #999999; font-weight: 700">
			| LOGIN | CREDITS | BUG |
			</div>
		
		</div>
	</center>
	</div>
	
	
	
	<div id = "content">
		<?php
			foreach($groups as $group){
		?>
			
			<br>
			<h2><?php echo $group['index'].' : '.$group['label']; ?></h2>
			<div class = 'table-wrapper'>
			<table>
			<?php
				echo '<tr><td class = "name_tag"></td>';
				foreach($group['probs'] as $p){
					echo '<td>'.$prob_data[$p]['judge'].' '.$prob_data[$p]['index'].'</td>';
				}
				echo '</tr>';
				
				
				foreach($group['names'] as $n){
					echo '<tr><td class = "name_tag">'.$name_data[$name_map[$n]]['name'].'</td>';	
					if ($stats_data[$n] == -1){
						echo '<td class = "pend">pending...</td>';
					} else {
						foreach($group['probs'] as $p){
							$res = $stats_data[$n][$p];
							if($res == 9){
								//AC
								echo '<td class = "AC">&#x25cf;</td>';	
							} else if ($res == 8){
								//tried
								echo '<td class = "WA">&#x25cf;</td>';	
							} else if ($res == 0) {
								//N/A
								echo '<td class = "NA">&#x25cf;</td>';	
							}

						}
					}
					echo '</tr>';
				}
				
				
			?>
			</table>
			</div>
			<br>
			<hr>
		<?php
			}
		?>
	</div>
</body>
</html>