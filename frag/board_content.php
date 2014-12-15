<?php
    //preproc: fetch data from files
    require_once('board_preproc.php');
    require_once('../func/prob_link.php');
	require_once('../func/chal_link.php');
	require_once('../func/acct_link.php');
    if($_POST['forceupdate']){
        $status_string = 'force update request sent.';
    }
    
    $sort_rule = $_POST['sortby'];
    //echo 'SORT: '.$sort_rule;
?>

<script>
	var autoupdate = <?php echo ($autoupdate ? 'true' : 'false'); ?> ;
	var forceupdate = <?php echo $_POST['forceupdate']; ?> ;
	
	if(autoupdate||forceupdate==-1){ $.get('proc.php'); }
	else if(forceupdate){ $.get('proc.php?'+forceupdate); }
	function show(group,tid){
		if(document.all["group"+group+"tid"+tid].style.display=="none"){
			$("#group"+group+"tid"+tid).show("slow");
		}else{
			$("#group"+group+"tid"+tid).hide("slow");
		}
	}
</script>


<?php
	foreach($groups as $group){
?>
        <div id = "table-<?=(int)$group['index']?>" style = "display: none; position: relative; margin-left: 80px">
        
    	<br>
    	<h2><?php echo $group['index'].' : '.$group['label']; ?></h2>
    	<br>
	
    	<div class = 'table-wrapper' style="overflow-x:auto;overflow-y:auto">
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
			$no=1;
    		foreach($rank as $n => $s){
    		    $n = (int)$n;
    		    //echo $n.' '.$s.';
    			echo '<tr><td class = "name_tag"><a onclick="show('.$group['index'].','.$name_data[$name_map[$n]]['TOJid'].')">'.$name_data[$name_map[$n]]['name'].'</a>';
				echo '<div id="group'.$group['index'].'tid'.$name_data[$name_map[$n]]['TOJid'].'" style="display:none; position: absolute; top: 25px; left: 20px; z-index: 1; text-align: left; border: 2px solid #CCC; background: #000;">';
				echo '<a onclick="loadPage('.$name_data[$name_map[$n]]['TOJid'].')">update</a><br>';
				if($name_data[$name_map[$n]]['TOJid']!="NULL")echo getAcctLink('TOJ',$name_data[$name_map[$n]]).'<br>';
				if($name_data[$name_map[$n]]['ZJid']!="NULL")echo getAcctLink('ZJ',$name_data[$name_map[$n]]).'<br>';
				if($name_data[$name_map[$n]]['TZJid']!="NULL")echo getAcctLink('TZJ',$name_data[$name_map[$n]]).'<br>';
				if($name_data[$name_map[$n]]['GJid']!="NULL")echo getAcctLink('GJ',$name_data[$name_map[$n]]).'<br>';
				if($name_data[$name_map[$n]]['TIOJid']!="NULL")echo getAcctLink('TIOJ',$name_data[$name_map[$n]]).'<br>';
				if($name_data[$name_map[$n]]['UVAid']!="NULL")echo getAcctLink('UVA',$name_data[$name_map[$n]]).'<br>';
				if($name_data[$name_map[$n]]['POJid']!="NULL")echo getAcctLink('POJ',$name_data[$name_map[$n]]).'<br>';
				if($name_data[$name_map[$n]]['HOJid']!="NULL")echo getAcctLink('HOJ',$name_data[$name_map[$n]]).'<br>';
				echo '</div></td>';
    			echo '<td class = "sol_tag">'.' ('.$no++.'/'.$sol[$n].'/'.round($sol[$n]/count($group['probs'])*100,0).'%)</td>';	
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
    					} else {
							echo '<td >ERR</td>';	
						}
    				}
    			}
    			echo '</tr>';
    		}
    		
    		
    	?>
    	</table>
    	</div>
		<pre id="inform-<?=(int)$group['index']?>" style="font-size: 20px;">
<?=$group["inform"]?>
		</pre>
		<script>
			$(window).resize(windowSizeChange);
			$("#table-<?=(int)$group['index']?>").width(800);
			windowSizeChange();
			function windowSizeChange(){
				$("#table-<?=(int)$group['index']?>").width( $(window).width()-$("#frame").position().left - 150 );
			}
		</script>
    </div>
<?php
	}
?>

<div style = "color: #666666; padding-left: 80px">
    <?php echo $status_string; ?>
    <br>
</div>
<script type='text/javascript' src='./func/edit_font_color.js'></script>
<script>dfs(document.all.frame);</script>