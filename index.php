<!DOCTYPE HTML>
<?php

    //LOAD PROBLEM LIST DATA
    $problist = fopen('./dat/problemlist.dat', 'r');
    
    $type = [];
    $index = [];

    $probcount = 0;
    
    if($problist){
        while($n = fscanf($problist, "%s\t%s\n")){
            
            if(!$n[0] == 'ZJ'){
                $n[1] = (int)$n[1];
            }
            
            $type[$probcount] = $n[0];
            $index[$probcount] =$n[1];

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
    
    $namecount = 0;
    
    if($namelist){
        
        while($n = fscanf($namelist, "%s\t%d\t%d\t%s\n")){
            $names[$namecount] = $n[0];
            $TOJid[$namecount] = $n[1];
            $UVAid[$namecount] = $n[2];
            $ZJ_id[$namecount] = $n[3];
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


    if(file_exists('./cache/work_flag')){
        echo 'update tasks pending...';
        
    } else {
        $last_update_t = file_get_contents('./cache/prev_uptd');
        $dt = (time() - (int)$last_update_t);
        echo 'LAST UPDATE: '.$last_update_t.'<br>';
        echo 'TIME INTVRL: '.$dt.'<br>';
        
        if($dt < 60){
            echo 'time interval limit('.$dt.')<br>';   
        } else {
            echo 'update triggered.<br>';
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

    <div>
    <!--DATA DISPLY -->
    <div>
    <center>
    <table>



    <tr>
        <td>NAME</td><!--<td>TOJ</td><td>UVa</td><td>ZJ</td>-->
        <?php
            for($i = 0; $i < $probcount; ++$i){
                echo '<td>'.$type[$i].' '.$index[$i].'</td>';
            }
        ?>
    </tr>
    
    <?php
        for ($i = 0; $i < $namecount; ++$i){
        
                echo '<td>'.$names[$i].'</td>';
                
                $filename = './cache/'.$TOJid[$i].'.dat';
                $cache_raw = file_get_contents($filename);

                unset($cache);
                $cache = explode(',', $cache_raw);

                for($j = 0; $j < $probcount; ++$j){
                    if ($cache[$j] == 1){
                        echo '<td class = "AC">AC</td>';
                    } else if ($cache[$j] == 0) {
                        echo '<td class = "NA">N/A</td>';
                    }else if ($cache[$j] == -1) {
                        echo '<td class = "WA">WA</td>';
                    }
                }

                echo '</tr>';
        }
    
    ?>
    </table>

    </center>
    </div>
</body>
</html>