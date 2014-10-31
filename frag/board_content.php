<?php
    //preproc: fetch data from files
    require_once('board_preproc.php');
    require_once('../func/prob_link.php');
	require_once('../func/chal_link.php');
    if($_POST['forceupdate']){
        $status_string = 'force update request sent.';
    }
    
    $sort_rule = $_POST['sortby'];
    //echo 'SORT: '.$sort_rule;
?>

<script>
	var halt = <?php echo ($halt ? 'true' : 'false'); ?> ;
	var forceupdate = <?php echo ($_POST['forceupdate'] ? 'true' : 'false'); ?> ;
	if(forceupdate) halt = false;
	
	//console.log(halt);	
	
	if(!halt){ $.get('proc.php'); }
</script>


<?php
	foreach($groups as $group){
?>
        <div id = "table-<?=(int)$group['index']?>" style = "display: none; position: relative; margin-left: 80px">
        
    	<br>
    	<h2><?php echo $group['index'].' : '.$group['label']; ?></h2>
    	<br>
	
    	<div class = 'table-wrapper'>
        <table>
    	<?php
    		echo '<tr><td class = "name_tag"></td><td class = "sol_tag"></td>';
    		foreach($group['probs'] as $p){
    			echo '<td>'.getProbLink($prob_data[$p]['judge'],$prob_data[$p]['index']).'</td>';
    		}
    		echo '</tr>';
    		
    		
    		//................SORT
    		
    		//Calculate AC count
    		$sol = array();
    		foreach($group['names'] as $n){
    		    $summation = 0;
    		    foreach($group['probs'] as $p){
    		        if($name_data[$name_map[(int)$n]]['stats'][$p] == '9') ++$summation;
    		    }
    		    $sol[$n] = $summation;
    		}
    		
    		$rank = array();
            //$sort_rule = 'rank';
            
            if($sort_rule == 'none'){
                
        		//sort: no sorting
        		$i = 0;
        		foreach($group['names'] as $n){
        		    ++$i;
        		    $rank[$n] = $i;
        		}
        		
            } else if ($sort_rule == 'name'){
                
        		//sort: sort by name
        		foreach($group['names'] as $n){
        		    $rank[$n] = $name_data[$name_map[(int)$n]]['name'];
        		}
    		
        		asort($rank);                
                
            } else if ($sort_rule == 'rank'){
                
        		//sort: sort by rank
        		$rank = $sol;
    		
        		arsort($rank);
        		
            }
    		//................END SORT
    		
    		
			
			
    		
    		//foreach($group['names'] as $n){
    		foreach($rank as $n => $s){
    		    $n = (int)$n;
    		    //echo $n.' '.$s.';';
    			echo '<tr><td class = "name_tag">'.$name_data[$name_map[$n]]['name'].'</td>';
    			echo '<td class = "sol_tag">'.' ('.$sol[$n].'/'.round($sol[$n]/count($group['probs'])*100,0).'%)</td>';	
    			if ($name_data[$name_map[$n]]['stats'] == -1){
    				echo '<td class = "pend">pending...</td>';
    			} else {
    				foreach($group['probs'] as $p){
    					//$res = $stats_data[$n][$p];
    					$res = $name_data[$name_map[$n]]['stats'][$p];
    					if($res == 9){
    						//AC
    						echo '<td class = "AC">'.getChalLink($prob_data[$p]['judge'],$prob_data[$p]['index'],$name_data[$name_map[$n]],9).'</td>';	
							//'.$prob_data[$p]['judge'].$prob_data[$p]['index'].$n.'
    					} else if ($res == 8){
    						//tried
    						echo '<td class = "WA">'.getChalLink($prob_data[$p]['judge'],$prob_data[$p]['index'],$name_data[$name_map[$n]],8).'</td>';
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
		<pre style="font-size: 20px;">
<?=$group["inform"]?>
		</pre>
	
    </div>
<?php
	}
?>

<div style = "color: #666666; padding-left: 80px">
    <br>
    <?php echo $status_string; ?>
    <br>
</div>
