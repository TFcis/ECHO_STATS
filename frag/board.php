    <script>
        var current_Table = 0;
        showTable(0);
        
        function showTable(i){
            //alert(i);
            $('#table-' + current_Table).hide();
            $('#table-' + i).fadeIn();
            current_Table = i;
        }    
    </script>

<div style = "width: 100%">
    <?php
        //preprocess board.
        require_once('board_preproc.php');
        
    ?>
    
    <!-- LEFT NAVIGATION BAR -->
    <div style = "float: left;">
        <div>
            <br>
            <?php echo $status_string; ?>
            <br>
        </div>

        <br>

        <div>BOARDS:</div>
        <div style =  "text-align: right">
        <?php
            foreach($groups as $group){
        ?>
        
            <hr>
            <a id = "<?php echo $group['index']?>" onclick = "showTable(<?php echo $group['index'] ?>)">
            <?php echo $group['label']; ?>
            </a>
            
        <?php } ?>
        </div>
        
        <br>
        <br>
        
        <div>TOOLS:</div>
        <div style =  "text-align: right">
            <hr>
            <a>force update</a>
        </div>
        
    </div>
    
    <!-- BOARDS -->
    <div style = "float: left">
    <?php
        
    	foreach($groups as $group){
    ?>

        <div id = "table-<?php echo $group['index']?>" style = "display: none; position: relative; margin-left: 80px">
    
        	<br>
        	<h2><?php echo $group['index'].' : '.$group['label']; ?></h2>
        	<br>
    	
        	<div class = 'table-wrapper'>
            <table>
        	<?php
        		echo '<tr><td class = "name_tag"></td>';
        		foreach($group['probs'] as $p){
        			echo '<td>'.$prob_data[$p]['judge'].' '.$prob_data[$p]['index'].'</td>';
        		}
        		echo '</tr>';
        		
        		//track correct answer counts and save them
        		$rank = array();
        		foreach($group['names'] as $n){
        		    $summation = 0;
        		    foreach($group['probs'] as $p){
        		        if($name_data[$name_map[$n]]['stats'][$p] == '9') ++$summation;
        		    }
        		    $rank[$n] = $summation;
        		    //echo $n.' '.$rank[$n].';';
        		}
        		//echo '<br>';
        		
        		arsort($rank);
        		
        		/*
        		foreach($rank as $n => $s){
        		    echo $n.' '.$s.';';
        		}
        		*/
        		
        		//foreach($group['names'] as $n){
        		foreach($rank as $n => $s){
        		    //echo $n.' '.$s.';';
        			echo '<tr><td class = "name_tag">'/*.$name_map[$n]*/.$name_data[$name_map[$n]]['name'].$name_data[$name_map[$n]]['rank'].'</td>';	
        			if ($name_data[$name_map[$n]]['stats'] == -1){
        				echo '<td class = "pend">pending...</td>';
        			} else {
        				foreach($group['probs'] as $p){
        					//$res = $stats_data[$n][$p];
        					$res = $name_data[$name_map[$n]]['stats'][$p];
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
    	
        </div>
    <?php
    	}
    ?>
    </div>
    
</div>