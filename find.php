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

$m = new Memcached();
if ($m->addServer('localhost',11211))
{
 $out = _filter($filter,$m,$count);
	$stats = fixStats($m);
	echo json_encode(array('code'=>$count,'hits'=>$out,'stats'=>$stats));
} else echo(json_encode(array('code'=>-1)));
?>
