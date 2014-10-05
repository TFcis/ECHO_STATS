<?php
//header('Location: proc.php');
?>
<!DOCTYPE HTML>
<?php
	    //load tracked accounts
        $raw_names = file_get_contents('./config/names.dat');

        if($raw_names === false){
            exit();
            
        } else {
            if ($raw_names == ''){ echo 'NOTICE: empty names.dat.<br><br>'; }
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
        }
        
        
        
        //load task data
        $raw_probs = file_get_contents('./config/probs.dat');

        if($raw_probs === false){
            //echo 'FATAL ERROR: failed to fetch data from probs.dat<br><br>';
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

		if(file_exists('./cache/work_flag')){
			$status_string .= '<br>update in progress...';
	        $halt_flag = 1;
			
		} else {
			if(!file_exists('./cache/prev_uptd'))
				fclose(fopen('./cache/prev_uptd', 'w'));
				$last_update_t = file_get_contents('./cache/prev_uptd');
				$dt = (time() - (int)$last_update_t);
				$status_string .= 'LAST UPDATE: '.$dt.' CYCLES AGO<br>';
			
			if($dt < 200){
				$status_string .= 'time interval limit('.$dt.')<br>';
	            $halt_flag = 1;
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
<script>
		<?php if(!$halt_flag) {?>
			$.get('proc.php');
		<?php } ?>
</script>
</head>
<body>
	<div id = "title" style = "position: relative">
		<div style = "position: absolute; bottom: 24px; left: 96px; text-align: right">
		<h1><span style = "color: #888888">ECHO</span> STATS <span style = "color: #888888">;</h1>
		<span style = "color: #666666">
		<?php
		echo $status_string;
		?>
		</span>
		</div>
	</div>
	
	<div id = "content">
		<?php
			foreach($groups as $group){
		?>
			<hr>
			
			<br>
			<h2><?php echo $group['index'].' : '.$group['label']; ?></h2>
			<table>
			<?php
				echo '<tr><td>NAME</td>';
				foreach($group['probs'] as $p){
					echo '<td>'.$prob_data[$p]['judge'].' '.$prob_data[$p]['index'].'</td>';
				}
				echo '</tr>';
				
				
				foreach($group['names'] as $n){
					echo '<tr><td>'.$name_data[$name_map[$n]]['name'].'</td>';	
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
			<br>
		<?php
			}
		?>
	</div>

</body>
</html>