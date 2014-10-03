<!DOCTYPE HTML>
<?php

    //LOAD PROBLEM LIST DATA
    $problist = fopen('./dat/problemlist.dat', 'r');
    
    $type = [];
    $index = [];

    $probgroup = [];

    $probcount = 0;
    
    if($problist){
        while($n = fscanf($problist, "%d\t%s\t%s\n")){
            
            if(!$n[1] == 'ZJ'){
                $n[2] = (int)$n[2];
            }
            
            $probgroup[$probcount] = $n[0];
            $type[$probcount] = $n[1];
            $index[$probcount] = $n[2];

            ++$probcount;
        
        }
        fclose($problist);
    } else {
        //THROW ERROR
    }



    //LOAD USER DATA
    $namelist = fopen('./dat/namelist.dat', 'r');
    
    $names = [];
    $TOJid = [];
    $UVAid = [];
    $ZJ_id = [];
    
    $group = [];
    
    $namecount = 0;
    
    if($namelist){
        
        while($n = fscanf($namelist, "%d\t%s\t%d\t%d\t%s\n")){
            $group[$namecount] = (int)$n[0];
            $names[$namecount] = $n[1];
            $TOJid[$namecount] = (int)$n[2];
            $UVAid[$namecount] = (int)$n[3];
            $ZJ_id[$namecount] = $n[4];
            /*
            $filename = './cache/'.$n[1].'.dat';
            if(!file_exists($file)){
                //cho 'create file';
                $file = fopen($filename, 'w');
                //if($file){echo 'ok';}
            }
            */
            ++$namecount;
        }
        fclose($namelist);
        
    } else {
        //THROW ERROR
    }

    $status_string = '';

    if(file_exists('./cache/work_flag')){
        $status_string .= '<br>update tasks pending...';
        
    } else {
        $last_update_t = file_get_contents('./cache/prev_uptd');
        $dt = (time() - (int)$last_update_t);
        //echo 'LAST UPDATE: '.$last_update_t.'<br>';
        $status_string .= 'LAST UPDATE: '.$dt.' CYCLES AGO<br>';
        
        if($dt < 20){
            $status_string .= 'time interval limit('.$dt.')<br>';   
        } else {
            $status_string .= '<br>update triggered.<br>';
            exec("php proc.php > /dev/null &");
        }
    }

?>

<html>
<head>
    <meta charset = 'utf-8' />
    <title>SolStats fileIO ver.</title>
    
    <!-- Google Fonts: Lato -->
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,900' rel='stylesheet' type='text/css'>
    <!-- Theme -->
    <link href = 'theme.css' rel = 'stylesheet' type = 'text/css' />
    
</head>
<body>
    <!--DATA DISPLY -->
    <center style = "height: 100%">
    <div id = "container">
    <div id = "banner" style = "position: relative">
        <div style = "position: absolute; bottom: 4px; right: 4px">
            <h1>SOLSTATS V 0.1</h1>
            <div style = "color: #444444"><?php echo $status_string; ?></div>
        </div>
    </div>
    
    <div id = "page">
    <!--UNITS -->
    <?php
        $stats = array(array());
        for ($i = 0; $i < $namecount; ++$i){
        
                //echo '<tr><td>'.$names[$i].'</td>';
                
                $filename = './cache/'.$TOJid[$i].'.dat';
                if(!file_exists($filename)){
                    //no cached data
                    $stats[$i][0] = 999;
                    //echo '<td class = "update" colspan = "'.$probcount.'">pending...</td>';
                    
                } else {
                    
                    $cache_raw = file_get_contents($filename);
                    if($cache_raw === false){
                        //file probably in use, hold
                        $stats[$i][0] = 999;
                        //echo '<td class = "update" colspan = "'.$probcount.'">!fileReadError</td>';
                    } else {
                        
                        unset($cache);
                        $cache = explode(',', $cache_raw);
        
                        for($j = 0; $j < $probcount; ++$j){
                            if ($cache[$j] == 1){
                                $stats[$i][] = 1;
                                //echo '<td class = "AC">AC</td>';
                            } else if ($cache[$j] == 0) {
                                $stats[$i][] = 0;
                                //echo '<td class = "NA">N/A</td>';
                            }else if ($cache[$j] == -1) {
                                $stats[$i][] = -1;
                                //echo '<td class = "WA">WA</td>';
                            }
                        }
                    
                    }

                }
                //echo '</tr>';
        }
    
    ?>

    <?php
    $groupnum = 3;
    for($t = 0; $t < $groupnum; ++$t){
        
        echo '<table><tr><td>NAME</td>';
        
        for($i = 0; $i < $probcount; ++$i){
            if($probgroup[$i] == $t){ echo '<td>'.$type[$i].' '.$index[$i].'</td>'; }
        }
            
        echo '</tr>';
        
        
        for ($i = 0; $i < $namecount; ++$i){
        if($group[$i] == $t){        
            echo '<tr><td>'.$names[$i].'</td>';
            
            if($stats[$i][0] == 999){
                
                echo '<td class = "update">pending...</td>';
            
            } else {
                
                for($j = 0; $j < $probcount; ++$j){
                if($probgroup[$j] == $group[$i]){
                    if ($stats[$i][$j] == 1){
                        //echo '<td class = "AC">'.$stats[$i][$j].'1</td>';
                        echo '<td class = "AC">AC</td>';
                    } else if ($stats[$i][$j] == 0) {
                        //echo '<td class = "NA">'.$stats[$i][$j].'0</td>';
                        echo '<td class = "NA">N/A</td>';
                    } else if ($stats[$i][$j] == -1) {
                        //echo '<td class = "WA">'.$stats[$i][$j].'-</td>';
                        echo '<td class = "WA">WA</td>';
                    }
                }
                }
            
            }
                        
            echo '</tr>';
            
        }
        }
        
        echo '</table><hr>';
    
    } ?>
    
    <!--END UNITS -->
    </div>

    </div>
    </center>
</body>
</html>