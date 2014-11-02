<!DOCTYPE HTML>

<html>
<head>
    <meta charset = 'utf-8'>
	<title>EchoStats</title>
	<link href='http://fonts.googleapis.com/css?family=Lato:400,700,900' rel='stylesheet' type='text/css'>
	<link href = './res/theme.css' rel = 'stylesheet' type = 'text/css'>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	
	<script>
		var CONT;
		
		$(document).ready(function(){
		    //$('#content').load('frag/board.php', function(){/*complete*/});

            CONT = document.getElementById('content');
			
			if(location.hash=="")
			    location.hash = "board";
			else
			    loadTemplate(location.hash.slice(1));
		});
		
		function loadTemplate(template){
			$("title").text(template+" - EchoStats");
			location.hash = template;
	    	$(CONT).load("frag/"+template+".php");
		}
		
        $(window).on('hashchange',function(){ 
            loadTemplate(location.hash.slice(1));
        });
	</script>
	
</head>
<body>
	<div id = "title" style = "position: relative; padding: 40px 0 16px 0">
	<center>
		<div style = "">
		
			<h1><span style = "color: #999999">ECHO</span> STATS <span style = "color: #999999">;</h1>
			
			<div style = "color: #666666;"><br>
                A <i>JUDGEMENTAL</i> STATISTICS ENGINE
			<br></div>
			
			<div style = "color: #AAAAAA;">
                <?php
                	$inform = @file_get_contents('./config/inform.dat');
					$inform = explode('<======>', $inform);
					echo $inform[0];
                ?>
			</div>
			
			<br>
			
			<div style = "color: #999999">
			
				<a id = "nav-board" title = "STATS" class = "icon" href="#board">
				<span>&#xe800;</span>
				</a> ⋅ <!-- BOARD -->
				
				<a id = "nav-konfigurator" title = "KONFIGURATOR" class = "icon" href="#konfigurator">
				<span>&#xe84d;</span>
				</a> ⋅ <!-- FUNCTIONS -->
				
				<!--<a id = "nav-credits" title = "CREDITS" class = "icon">
				<span>&#xe805;</span>
				</a> ⋅  CREDITS -->
				
				<a id = "nav-develop" title = "DEVELOP" class = "icon" href="#develop">
				<span>&#xe813;</span>
				</a> ⋅ <!-- DEVELOP -->
				
				<a id = "nav-bug" title = "BUG REPORTS" class = "icon" href="#bug">
				<span>&#xe80e;</span>
				</a>  <!-- BUG REPORTS -->
			
			</div>
			
		</div>
	</center>
	</div>
	
	
	<div id = "content">
        <!-- LOAD CONTENTS VIA jQ HERE -->
	</div>
</body>
</html>