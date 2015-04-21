<?php
/***************************************************
Copyright:jReply LLC, 2015. https://jresponse.net
Demo:http://jresponse.co/readmin
Comments & suggestions:contact@jreply.com
Licensed MIT:http://choosealicense.com/licenses/mit/
****************************************************/
function dieWith($err){die(json_encode(array('code'=>$err)));}

function _filter($filter,$m,&$count)
{
 $keys = $m->getAllKeys();
	if (!(('*' == $filter) || (0 === strlen($filter)))) 
	{
	 if (false === @preg_match($filter,null)) dieWith(-999);
		$keys = preg_grep($filter,$keys);
	}
	sort($keys);
	if ($m->getDelayed($keys))
	{
	 $values = $m->fetchAll();
  if (false === $values) $values = array();
  $count = count($values);
		return $values;
	}
	$count = 0;
 return array();	
}
