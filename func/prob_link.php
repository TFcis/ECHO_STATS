<?php
function getProbLink($judge,$probid){
    if($judge=="UVa")
        $url = getUVaUrl($probid);
    else if($judge=="ZJ")
        $url = getZJUrl($probid);
    else if($judge=="TOJ")
        $url = getTOJUrl($probid);
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