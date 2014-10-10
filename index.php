<!DOCTYPE HTML>

<html>
<head>
    <meta charset = 'utf-8'>
	<link href='http://fonts.googleapis.com/css?family=Lato:400,700,900' rel='stylesheet' type='text/css'>
	<link href = './res/theme.css' rel = 'stylesheet' type = 'text/css'>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	
	<script>
		var CONT;
		
		$(document).ready(function(){
		    //$('#content').load('frag/board.php', function(){/*complete*/});

            CONT = document.getElementById('content');

            loadTemplate('frag/board.php');
            
		    $('#nav-konfigurator').click(function(){    loadTemplate('frag/konfigurator.php');  });
		    $('#nav-credits').click(function(){         loadTemplate('frag/credits.php');       });
		    $('#nav-board').click(function(){           loadTemplate('frag/board.php');         });
		    $('#nav-develop').click(function(){         loadTemplate('frag/develop.php');       });
			$('#nav-bug').click(function(){             loadTemplate('frag/bug.php');           });
		});
		
		function loadTemplate(path){
	    	$(CONT).load(path);
		}
	</script>
	
</head>
<body>
	<div id = "title" style = "position: relative; padding: 40px 0 16px 0">
	<center>
		<div style = "">
		
			<h1><span style = "color: #999999">ECHO</span> STATS <span style = "color: #999999">;</h1>
			
			<div style = "color: #666666;"><br>
                A JUDGEMENTAL STATISTICS ENGINE
			<br></div>
			
			<br>
			
			<div style = "color: #999999">

				<a id = "nav-konfigurator" title = "KONFIGURATOR" class = "icon">
				<span>&#xe82c;</span>
				</a> ⋅ <!-- FUNCTIONS -->
				
				<a id = "nav-credits" title = "CREDITS" class = "icon">
				<span>&#xe823;</span>
				</a> ⋅ <!-- CREDITS -->
				
				<a id = "nav-board" title = "STATS" class = "icon">
				<span>&#xe808;</span>
				</a> ⋅ <!-- BOARD -->
				
				<a id = "nav-develop" title = "DEVELOP" class = "icon">
				<span>&#xe843;</span>
				</a> ⋅ <!-- DEVELOP -->
				
				<a id = "nav-bug" title = "BUG REPORTS" class = "icon">
				<span>&#xe82e;</span>
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