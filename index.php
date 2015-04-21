<?php
/***************************************************
Copyright:jReply LLC, 2015. https://jresponse.net
Demo:http://jresponse.co/readmin
Comments & suggestions:contact@jreply.com
Licensed MIT:http://choosealicense.com/licenses/mit/
****************************************************/
if (!class_exists('memcached')) die('Please install php5-memcached, restart your webserver & try again');
if (false === strpos($_SERVER['HTTP_USER_AGENT'],'Chrome'))
die('This application only works in Chrome and Opera');

require 'stats.php';

$m = new Memcached();
if ($m->addServer('localhost',11211))
{
 $stats = json_encode(fixStats($m));
} else $stats = '';	
?>

<!doctype HTML>
<html>
<!--
Copyright:jReply LLC, 2015. https://jresponse.net
Demo:http://jresponse.co/readmin
Comments & suggestions:contact@jreply.com
Licensed MIT:http://choosealicense.com/licenses/mit/
-->
<head>
<title>Memcached Web Admin GUI</title>
<link rel="shortcut icon" href='https://jresponse.r.worldssl.net/ide/nimages/memcached.png'/>
<link rel="stylesheet" href="https://jresponse.r.worldssl.net/styles/darkness.css" />
<link rel='stylesheet' href='mcadmin.css'/>
<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js'></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<script>
<?php echo "_stats = '{$stats}';"; ?>
</script>
<script src='mcadmin.js'></script>
</head>
<body>
<h1>Memcached Administrator</h1>
<div class='lay'>
<span>Filter</span>
<input id='inpFilter' value='' maxlength='64' placeholder='Blank or a PHP Regex, e.g. /php/i'/>
<button class='dabtn' id='btnFilter'>Filter</button>
</div>
<div class='lay'>
<span>Hits</span>
<select id='selHits' size='8'>
</select>
</div>
<div class='lay'>
<span>Commands</span>
<div class='divBox'>
<div class='flx'>
<div>
<button class='dabtn' id='btnAdd'>Add</button>
</div>
<div>
<button class='dabtn' id='btnDelete' disabled>Delete</button>
<button class='dabtn' id='btnFlush'>Flush</button>
</div>
</div>

</div>
</div>
<div id='divInfo'>
</div>
<p id='pCopy'>Copyright &copy; <a href='https://jresponse.net' target='_blank'>jReply LLC, 2015</a></p>
<div id='diaStringEd' class='divDia'>
<div class='lay'>
<span>Key</span>
<input id='inpStrKey' maxlength='24'/>
</div>
<div class='lay'>
<span>Value</span>
<textarea id='txaStrValue' cols='40' rows='4' maxlength='512'></textarea>
</div>
<div class='lay'>
<span>TTL (s)</span>
<input id='inpStrTTL' type='number' min='0' step = 1' value='0' placeholder='Not retrievable'/>
</div>
<div class='divFootnote'>0 = No TTL</div>
</div>


</body>
</html> 
