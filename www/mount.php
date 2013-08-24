<?php
$ret = copy_dir(__DIR__, dirname($_SERVER['SCRIPT_FILENAME']).'/site');
echo '

Esqueleto criado com sucesso!
Neos PHP Framework - version 1.0

help: prbr@ymail.com
';

//function
function copy_dir($de, $para){ 
	$msg = array();
	$para = rtrim($para, '/\\ ');
    if (!is_dir($para)){
    	$msg[] = 'Criando diretorio '.$para."\n";
        mkdir($para, 0755);
    }
        
    $folder = opendir($de);
        
    while ($item = readdir($folder)){
    	if ($item == '.' || $item == '..') continue;
        $item = '/'.$item;
        if (is_dir($de.$item)) $msg[] = copy_dir($de.$item, $para.$item);
        else { 
        	$msg[] = 'Copiando '.$de.$item.' para '.$para.$item."\n"; 
            copy($de.$item, $para.$item);
        }
    }
    return $msg;
}