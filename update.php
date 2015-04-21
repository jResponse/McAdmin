<?php
/***************************************************
Copyright:jReply LLC, 2015. https://jresponse.net
Demo:http://jresponse.co/readmin
Comments & suggestions:contact@jreply.com
Licensed MIT:http://choosealicense.com/licenses/mit/
****************************************************/
require 'filter.php';
require 'stats.php';

$key = (isset($_REQUEST['k']))?$_REQUEST['k']:null;
$value = (isset($_REQUEST['v']))?$_REQUEST['v']:null;
$filter = (isset($_REQUEST['f']))?$_REQUEST['f']:'*';
$ttl = (isset($_REQUEST['t']))?$_REQUEST['t']:'0';

//if (false !== strpos('string,set,list,zset,hash',$key)) dieWith(-999);
//the default keys may not be modified comment out line 23 for your own installation
if (null === $key) dieWith(-3);
if (null === $value) dieWith(-2);
$ttl = intval($ttl);
$ttl = (0 > $ttl)?0:$ttl;
$ttl = ((0 === $ttl) || (1200 < $ttl))?600:$ttl;
//For the demo we are forcing keys to expire. Comment out line 29 for your own installation
$key = addslashes($key);

$m = new Memcached();
if ($m->addServer('localhost',11211))
{
 $old = $m->get($key);
 if ($old) $m->delete($key);
	$m->set($key,$value,$ttl);
	$out = _filter($filter,$m,$count);
	$stats = fixStats($m);
	echo json_encode(array('code'=>1,'had'=>$old,'count'=>$count,'hits'=>$out,'stats'=>$stats));
} else dieWith(-1);
?>
