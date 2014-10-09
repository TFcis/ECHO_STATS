    <?php
        //preprocess board.
        require_once('board_preproc.php');
        
    ?><br><?php echo $status_string; ?><br><?php
        
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