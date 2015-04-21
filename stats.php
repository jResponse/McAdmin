<?php
/***************************************************
Copyright:jReply LLC, 2015. https://jresponse.net
Demo:http://jresponse.co/readmin
Comments & suggestions:contact@jreply.com
Licensed MIT:http://choosealicense.com/licenses/mit/
****************************************************/
function pctUsed($stats)
{
 $pct = $stats['bytes']/$stats['limit_maxbytes'];
	return round(100*$pct,1);
}

function fixStats($m)
{
 $mver = $m->getVersion()['localhost:11211'];
	$stats = $m->getStats()['localhost:11211'];
	
	$stats = array('v'=>$mver,'ut'=>$stats['uptime'],'i'=>$stats['curr_items'],'upc'=>pctUsed($stats));
	return $stats;
}
 
