<?php
/***************************************************
Copyright:jReply LLC, 2015. https://jresponse.net
Demo:http://jresponse.co/readmin
Comments & suggestions:contact@jreply.com
Licensed MIT:http://choosealicense.com/licenses/mit/
****************************************************/
require 'stats.php';

$m = new Memcached();
if ($m->addServer('localhost',11211))
{
 $code = $m->flush();
	echo json_encode(array('code'=>intval($code)));
} else echo(json_encode(array('code'=>-1)));

?>
