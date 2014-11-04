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
        <br>

        <div>SORT BY:</div>
        <div style =  "text-align: right">
            <hr>
			<div><a onclick = "sort_by = 'rank'; loadPage(0)">rank</a></div>
            <div><a onclick = "sort_by = 'name'; loadPage(0)">name</a></div>
        </div>
        
        <br>
        <br>
        
        <div>TOOLS:</div>
        <div style =  "text-align: right">
            <hr>
            <div><a onclick = "loadPage(0)">refresh</a></div>
            <div><a onclick = "loadPage(-1)">force update</a></div>
        </div>
        
    </div>
    
    <!-- BOARDS -->
    <div id = "frame" style = "float: left">
    
    </div>
    
</div>