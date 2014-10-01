<?php
ini_set("display_errors", "Off");
$url=urldecode($_SERVER['QUERY_STRING']);
$db=new PDO("mysql:host=localhost;dbname=xiplus_problem;charset=utf8", "xiplus_problem", "problemlist");
?>
<!DOCTYPE>
<html>
<head>
	<title>題單</title>
	<meta charset="UTF-8" name="viewport" content="width=device-width,user-scalable=yes">
</head>
<body>
	<center>
	<h2>題單</h2>
	<table class=MsoTableGrid border=1 cellpadding=3 style='border-collapse:collapse;border:none'>
	<tr>
	<td style="border-bottom-width: 2px; border-right-width: 2px">姓名</td>
	<td style="border-bottom-width:2px">TOJ id</td>
	<td style="border-bottom-width:2px">UVA account</td>
	<td style="border-bottom-width:2px ; border-right-width: 2px">ZJ id</td>
<?php
$query="SELECT * FROM `".$url."_problem` ORDER BY `name`,`id`";
$n=0;
foreach ($db->query($query) as $row)
{
	$problemlist[$n]=$row;
	$n++;
	print '<td style="border-bottom-width:2px ">'.$row['name'].' '.$row['id'].'</td>';
}
?>
	</tr>
<?php
function zj($prom,$ZJID) {
	$response = file("http://zerojudge.tw/UserStatistic?account=".$ZJID);
	foreach ($prom as $row){
		if($row['name']=='zj'){
			$start=strpos($response,"?problemid=".$row['id']);
			$end=strpos($response,">".$row['id']."</a>");
			$html=substr($response,$start,$end-$start);
			print '<td>';
			if(strpos($html,'class="acstyle"')>=0)print "AC";
			else if(strpos($html,'color: #666666; font-weight: bold;')>=0)print "Tried";
			else if(strpos($html,'color: #666666;')>=0)print "";
			else print "ERR!!";
		}
		else print '<td>N/A</td>';
		print '</td>';
	}
}
$query="SELECT * FROM `".$url."_account`";
foreach ($db->query($query) as $row)
{
	print '<tr>';
	print '<td style="border-right-width:2px">'.$row['name'].'</td>';
	print '<td>'.$row['tojid'].'</td>';
	print '<td>'.$row['uvaid'].'</td>';
	print '<td style="border-right-width:2px">'.$row['zjacct'].'</td>';
	zj($problemlist,$row['zjacct']);
	print '</tr>';
}
?>
</table>
	<br>
<font color="#666666" style="font-size: 12px">
Develop by xiplus, domen111, John.</font>
	</center>
</body>
</html>
