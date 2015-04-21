<?php
/***************************************************
Copyright:jReply LLC, 2015. https://jresponse.net
Demo:http://jresponse.co/readmin
Comments & suggestions:contact@jreply.com
Licensed MIT:http://choosealicense.com/licenses/mit/
****************************************************/
require 'filter.php';
require 'stats.php';

$filter = (isset($_GET['filter']))?$_GET['filter']:'*';
$key = (isset($_GET['key']))?$_GET['key']:null;
if (null === $key) dieWith(-2);
$m = new Memcached();
if ($m->addServer('localhost',11211))

{
 $rv = $m->get($key);
	if (false === $rv) dieWith(-1);
 $rv = $m->delete($key);
	if (false === $rv) dieWith(0);
	$out = _filter($filter,$m,$count);
	$stats = fixStats($m);
	echo json_encode(array('code'=>1,'hits'=>$out,'stats'=>$stats));
} else dieWith(-2);
?>
