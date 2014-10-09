<!DOCTYPE HTML>

<html>
<head>
    <meta charset = 'utf-8'>
	<link href='http://fonts.googleapis.com/css?family=Lato:400,700,900' rel='stylesheet' type='text/css'>
	<link href = './res/theme.css' rel = 'stylesheet' type = 'text/css'>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	
	<script>
		var halt = <?php echo ($halt ? 'true' : 'false'); ?> ;
		if(!halt){ $.get('proc.php'); }
		
		$(document).ready(function(){
		    $('#content').load('frag/board.php', function(){/*complete*/});
		});
	</script>
	
</head>
<body>
	<div id = "title" style = "position: relative">
	<center>
		<div style = "margin: 40px 0 16px 0">
		
			<h1><span style = "color: #999999">ECHO</span> STATS <span style = "color: #999999">;</h1>
			
			<div style = "color: #666666;"><br>
                A JUDGEMENTAL STATISTICS ENGINE
			<br></div>
			
			<br>
			
			<div style = "color: #999999; font-size: 18px">
				<div id = "nav-functions" class = "icon"><a href = 'http://google.com'>&#xe82c;</a></div> ⋅ <!-- FUNCTIONS -->
				<div id = "nav-credits" class = "icon"><a href = 'http://google.com'>&#xe823;</a></div> ⋅   <!-- CREDITS -->
				<div id = "nav-board" class = "icon"><a href = 'http://google.com'>&#xe808;</a></div> ⋅     <!-- BOARD -->
				<div id = "nav-develop" class = "icon"><a href = 'http://google.com'>&#xe843;</a></div> ⋅   <!-- DEVELOP -->
				<div id = "nav-bug" class = "icon"><a href = 'http://google.com'>&#xe82e;</a></div>         <!-- BUG REPORTS -->
			</div>
			
		</div>
	</center>
	</div>
	
	
	<div id = "content">
        <!-- LOAD CONTENTS VIA jQ HERE -->
	</div>
</body>
</html>