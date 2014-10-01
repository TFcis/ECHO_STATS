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
	$start=strpos($response,"?problemid=".$prom);
	$end=strpos($response,">".$prom."</a>");
	$html=substr($response,$start,$end-$start);
	if(strpos($html,'class="acstyle"')>=0)return "AC";
	else if(strpos($html,'color: #666666; font-weight: bold;')>=0)return "Tried";
	else if(strpos($html,'color: #666666;')>=0)return "";
	else return "ERR!!";
}
$query="SELECT * FROM `".$url."_account`";
foreach ($db->query($query) as $row)
{
	print '<tr>';
	print '<td style="border-right-width:2px">'.$row['name'].'</td>';
	print '<td>'.$row['tojid'].'</td>';
	print '<td>'.$row['uvaid'].'</td>';
	print '<td style="border-right-width:2px">'.$row['zjacct'].'</td>';
	foreach ($problemlist as $row2)
	{
		if($row2['name']=='zj')print '<td>'.zj($row2['id'],$row['zjacct']).'</td>';
		else print '<td>'.$row2['name'].' '.$row2['id'].'</td>';
	}
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
