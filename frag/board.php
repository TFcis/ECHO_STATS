<script>
    var current_Table = 0;
    var sort_by = 'rank';

    $(document).ready(function(){ loadPage(0); });

    function loadPage(force_update){
    
        $('#frame').load(
            'frag/board_content.php',
            
            {
                forceupdate: force_update,
                sortby: sort_by
            },
            
            function(){
                showTable(current_Table);
            }
        );
        
    }
    
    function showTable(i){
        //alert(i);
        $('#table-' + current_Table).hide();
        $('#table-' + i).fadeIn();
        current_Table = i;
    }    
</script>

<div style = "width: 100%">
    <?php
        //preprocess board - get info for group navigation (TODO)
        require_once('board_preproc.php');
    ?>
    
    <!-- LEFT NAVIGATION BAR -->
    <div id = "tools" style = "float: left;">
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

        <div>SORT BY:</div>
        <div style =  "text-align: right">
            <hr>
			<div><a onclick = "sort_by = 'rank'; loadPage(0)">rank</a></div>
            <div><a onclick = "sort_by = 'name'; loadPage(0)">name</a></div>
        </div>
        
        <br>
        
        <div>TOOLS:</div>
        <div style =  "text-align: right">
            <hr>
            <div><a onclick = "loadPage(0)">refresh</a></div>
            <div><a onclick = "loadPage(-1)">force update</a></div>
        </div>
		
        <br>
		
		<?php
			$judge_available=file_get_contents('../cache/judge_available');
			echo $judge_available[0];
			function availablecolor($n){
				if($n=="0")echo "#77777";
				else if($n=="1")echo "#cc0000";
				else if($n=="2")echo "#ffff00";
				else if($n=="3")echo "#00cc00";
			}
		?>
		<div>AVAILABLE:</div>
        <div style =  "text-align: right">
            <hr>
			<div>
			TOJ<a id="able_TOJ" href="http://toj.tfcis.org/oj/info/" target="_blank" style="color:<?php availablecolor($judge_available[0])?>;">&#x25cf;</a>
			ZJ<a id="able_ZJ" href="http://zerojudge.tw/" target="_blank" style="color:<?php availablecolor($judge_available[1])?>;">&#x25cf;</a>
			</div>
			<div>
			TZJ<a id="able_TZJ" href="http://judge.tnfsh.tn.edu.tw:8080/" target="_blank" style="color:<?php availablecolor($judge_available[2])?>;">&#x25cf;</a>
			GJ<a id="able_GJ" href="http://www.tcgs.tc.edu.tw:1218/" target="_blank" style="color:<?php availablecolor($judge_available[3])?>;">&#x25cf;</a>
			</div>
			<div>
			TIOJ<a id="able_TIOJ" href="http://tioj.ck.tp.edu.tw/" target="_blank" style="color:<?php availablecolor($judge_available[4])?>;">&#x25cf;</a>
			UVA<a id="able_UVA" href="http://uva.onlinejudge.org/" target="_blank" style="color:<?php availablecolor($judge_available[5])?>;">&#x25cf;</a>
			</div>
			<div>
			POJ<a id="able_POJ" href="http://poj.org/" target="_blank" style="color:<?php availablecolor($judge_available[6])?>;">&#x25cf;</a>
			HOJ<a id="able_HOJ" href="http://hoj.twbbs.org/judge/" target="_blank" style="color:<?php availablecolor($judge_available[7])?>;">&#x25cf;</a>
			</div>
        </div>
        
    </div>
    
    <!-- BOARDS -->
    <div id = "frame" style = "float: left">
    
    </div>
    
</div>