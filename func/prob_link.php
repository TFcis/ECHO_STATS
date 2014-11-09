<?php
function getProbLink($judge,$probid){
    if($judge=="UVa")
        $url = getUVaUrl($probid);
    else if($judge=="ZJ")
        $url = getZJUrl($probid);
    else if($judge=="TOJ")
        $url = getTOJUrl($probid);
	else if($judge=="GJ")
        $url = getGJUrl($probid);
	else if($judge=="TIOJ")
        $url = getTIOJUrl($probid);
	else if($judge=="TZJ")
        $url = getTZJUrl($probid);
	else if($judge=="POJ")
        $url = getPOJUrl($probid);
    return "<a href='$url' target='_blank'>$judge<br/>$probid</a>";
}
function getUVaUrl($probid){
    return "http://domen.heliohost.org/uva/?$probid";
}
function getZJUrl($probid){
    return "http://zerojudge.tw/ShowProblem?problemid=$probid";
}
function getTOJUrl($probid){
    return "http://toj.tfcis.org/oj/pro/$probid/";
}
function getGJUrl($probid){
    return "http://www.tcgs.tc.edu.tw:1218/ShowProblem?problemid=$probid";
}
function getTIOJUrl($probid){
    return "http://tioj.ck.tp.edu.tw/problems/$probid";
}
function getTZJUrl($probid){
    return "http://judge.tnfsh.tn.edu.tw:8080/ShowProblem?problemid=$probid";
}
function getPOJUrl($probid){
    return "http://poj.org/problem?id=$probid";
}
?>