<script>
    var current_Table = 0;


    $(document).ready(function(){
        loadPage();
    });

    function loadPage(){
        $('#frame').load('frag/board_content.php', function(){
            showTable(0);
        });
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
        
        <div>TOOLS:</div>
        <div style =  "text-align: right">
            <hr>
            <a onclick = "loadPage()">force update</a>
        </div>
        
    </div>
    
    <!-- BOARDS -->
    <div id = "frame" style = "float: left">
    
    </div>
    
</div>