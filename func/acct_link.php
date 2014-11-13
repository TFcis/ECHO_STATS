<?php
function getAcctLink($judge,$acctid){
    if($judge=="UVA")
		$url = getUVaAcctUrl($acctid["UVAid"]);
    else if($judge=="ZJ")
        $url = getZJAcctUrl($acctid["ZJid"]);
    else if($judge=="TOJ")
        $url = getTOJAcctUrl($acctid["TOJid"]);
	else if($judge=="GJ")
        $url = getGJAcctUrl($acctid["GJid"]);
	else if($judge=="TIOJ")
        $url = getTIOJAcctUrl($acctid["TIOJid"]);
	else if($judge=="TZJ")
        $url = getTZJAcctUrl($acctid["TZJid"]);
	else if($judge=="POJ")
        $url = getPOJAcctUrl($acctid["POJid"]);
	else if($judge=="HOJ")
        $url = getHOJAcctUrl($acctid["HOJid"]);
    return "<a href='".$url."' target='_blank'>".$judge."(".$acctid[$judge."id"].")</a>";
}
function getUVaAcctUrl($acctid){
    return "http://uhunt.felix-halim.net/id/".$acctid;
}
function getZJAcctUrl($acctid){
    return "http://zerojudge.tw/UserStatistic?account=".$acctid;
}
function getTOJAcctUrl($acctid){
    return "http://toj.tfcis.org/oj/acct/".$acctid."/";
}
function getGJAcctUrl($acctid){
    return "http://www.tcgs.tc.edu.tw:1218/ShowUserStatistic?account=".$acctid;
}
function getTIOJAcctUrl($acctid){
    return "http://tioj.ck.tp.edu.tw/users/".$acctid;
}
function getTZJAcctUrl($acctid){
    return "http://judge.tnfsh.tn.edu.tw:8080/ShowUserStatistic?account=".$acctid;
}
function getPOJAcctUrl($acctid){
    return "http://poj.org/userstatus?user_id=".$acctid;
}
function getHOJAcctUrl($acctid){
    return "http://hoj.twbbs.org/judge/user/view/".$acctid;
}
?>