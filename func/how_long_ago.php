<?php
function how_long_ago($time){
	$time_return='';
	if(floor($time/86400)>0){
		$time_return .=floor($time/86400)." day";
		if(floor($time/86400)>1)$time_return .="s";
		$time_return .=" ";
	}
	if(floor($time%86400/3600)>0){
		$time_return .=floor($time%86400/3600)." hour";
		if(floor($time%86400/3600)>1)$time_return .="s";
		$time_return .=" ";
	}
	if(floor($time%3600/60)>0){
		$time_return .=floor($time%3600/60)." minute";
		if(floor($time%3600/60)>1)$time_return .="s";
		$time_return .=" ";
	}
	if($time%60>0){
		$time_return .=($time%60)." second";
		if($time%60>1)$time_return .="s";
		$time_return .=" ";
	}
	return $time_return;
}
?>