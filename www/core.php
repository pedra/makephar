<?php
define('PATH', __DIR__ . '/');
Phar::interceptFileFuncs();
//ob_start('ob_gzhandler'); 
set_include_path( get_include_path() . PATH_SEPARATOR . PATH);

//pegando o PATH físico do ARQUIVO PHAR
$x = explode('/', $_SERVER['SCRIPT_FILENAME']);
array_pop($x);
$x = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, implode('/', $x));
define('RPATH', $x . DIRECTORY_SEPARATOR);

//obtendo o que foi requerido depois do script (http://site/scpt.phar/PATH_INFO)
$x = isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:'';

$x = explode('/', trim($x, '/ '));

//PATH_DIR	= diretório do arquivo solicitado
//NAME_FILE	= nome do arquivo solicitado
//URL_BASE	= base para os arquivos html => <base href="<?php echo URL_BASE;? >" /> 
define('PATH_DIR', PATH . implode('/', $x));
$x = end($x);
if($x == 'core.php' || $x == 'core' || $x == '') $x = 'index';
define('NAME_FILE', $x);


$x = (isset($_SERVER['SCRIPT_NAME'])) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['PHP_SELF'];
define('URL_BASE', ((_detectSSL()) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . '/' . trim($x, '/ ') . '/');


if(PATH . NAME_FILE != PATH_DIR && is_file(PATH_DIR)) {
      //procurando o mime type                                             
		include PATH . 'lib/mimes.php';
		$ext = explode('.', NAME_FILE);
		$ext = end($ext);
		if(!isset($_mimes[$ext])){$mime = 'text/plain';}
		else{$mime = (is_array($_mimes[$ext])) ? $_mimes[$ext][0] : $_mimes[$ext];}
		//pegando o arquivo
		$dt = file_get_contents(PATH_DIR);
		//enviando
		ob_start('ob_gzhandler');
		header('Content-Type: ' . $mime );
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime(PATH_DIR)) . ' GMT');
		header('Cache-Control: max-age=31536000');
		header('X-Powered-By: NEOS PHP Framework');
		//header('Etag: "' . md5(microtime(true)) . '"');
		header('Content-Length: ' . strlen($dt));
		//saindo...
		exit($dt);		
}

//Carregando arquivo da raíz
header('X-Powered-By: NEOS PHP Framework');
$inc = PATH . NAME_FILE;
if(is_file($inc)) include($inc);
elseif(is_file($inc . '.php')) include($inc . '.php');
elseif(is_file($inc . '.html')) include($inc . '.html');

//comprimindo html
$x = ob_get_contents();
ob_end_clean();
$x = str_replace("\n", '', $x);
$x = @ereg_replace('[[:space:]]+', ' ', $x);
ob_start('ob_gzhandler');
echo $x;


//------- funções ------
	/*
	* detecta se o acesso está sendo feito por SSL (https)
	*/
	function _detectSSL(){
		if (!isset($_SERVER["HTTPS"]))		return false;
		if ($_SERVER["HTTPS"] == "on")		return true;
		if ($_SERVER["HTTPS"] == 1)			return true;
		if ($_SERVER['SERVER_PORT'] == 443) return true;
		return false;
	}
	function _pt($a, $ex = true){
		$pt = '<pre>' . print_r( $a , true) . '</pre>';
		if($ex) exit($pt);	
		echo $pt;
	}
?>