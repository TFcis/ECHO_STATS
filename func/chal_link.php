<?php
function getChalLink($judge,$probid,$acctid,$res){
    /*if($judge=="UVa")
		$url = getUVaChalUrl($probid,$acctid["UVaid"]);
    else */if($judge=="ZJ")
        $url = getZJChalUrl($probid,$acctid["ZJid"]);
    else if($judge=="TOJ")
        $url = getTOJChalUrl($probid,$acctid["TOJid"]);
	else if($judge=="GJ")
        $url = getGJChalUrl($probid,$acctid["GJid"]);
	else if($judge=="TIOJ")
        $url = getTIOJChalUrl($probid,$acctid["TIOJid"]);
	else if($judge=="TZJ")
        $url = getTZJChalUrl($probid,$acctid["TZJid"]);
	else if($judge=="POJ")
        $url = getPOJChalUrl($probid,$acctid["POJid"]);
	/*else if($judge=="HOJ")
        $url = getHOJChalUrl($probid,$acctid["HOJid"]);*/
    if($judge=="UVa"||$judge=="HOJ")return "&#x25cf;";
	else if($res==9)return "<a href='$url' target='_blank' style='color:#00cc00'>&#x25cf;</a>";
	else if($res==8)return "<a href='$url' target='_blank' style='color:#cc0000'>&#x25cf;</a>";
}
/*function getUVaChalUrl($probid,$acctid){
    return "http://domen.heliohost.org/uva/?$probid";
}*/
function getZJChalUrl($probid,$acctid){
    return "http://zerojudge.tw/Submissions?problemid=".$probid."&account=".$acctid;
}
function getTOJChalUrl($probid,$acctid){
    return "http://toj.tfcis.org/oj/chal/?proid=".$probid."&acctid=".$acctid;
}
function getGJChalUrl($probid,$acctid){
    return "http://www.tcgs.tc.edu.tw:1218/RealtimeStatus?problemid=".$probid."&account=".$acctid;
}
function getTIOJChalUrl($probid,$acctid){
    return "http://tioj.ck.tp.edu.tw/problems/".$probid."/submissions?filter_username=".$acctid;
}
function getTZJChalUrl($probid,$acctid){
    return "http://judge.tnfsh.tn.edu.tw:8080/RealtimeStatus?problemid=".$probid."&account=".$acctid;
}
function getPOJChalUrl($probid,$acctid){
    return "http://poj.org/status?problem_id=".$probid."&user_id=".$acctid;
}
function getHOJChalUrl($probid,$acctid){
    return "http://hoj.twbbs.org/judge/judge/status?prob=".$probid."&user=".$acctid;
}
?>