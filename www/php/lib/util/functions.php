<?php //*********************** FUNCTIONS ***************************
function _p($a,$b=false){$c='<pre>'.print_r($a,true).'</pre>';echo ($b)?exit($c):$c;}
function _cfg($var = null){return _cfg::get($var);}
function _go($uri = '', $metodo = '', $cod = 302) {
		if(strpos($uri, 'http://') === false || strpos($uri, 'https://') === false) $uri = BASE.$uri; //se tiver 'http' na uri então será externo.
		if (strtolower($metodo) == 'refresh') {header('Refresh:0;url='.$uri);}
		else {header('Location: '.$uri, TRUE, $cod);}
		exit;}