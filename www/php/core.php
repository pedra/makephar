<?php
/* TODO
 * Problemas
 * 1 - Somente arquivos simples podem ser solicitados (views).
 *      Não é possível solicitar com path (ex.: site.com/ctrl/sub/index == index e ignora o resto)
 * 2 -
 */
Phar::interceptFileFuncs();

define('PATH', dirname(__DIR__) . '/');
define('PATH_PHP', PATH.'php/');
define('VIEW', PATH_PHP.'view/');
define('LIB', PATH_PHP.'lib/');

//pegando o PATH fisico do ARQUIVO PHAR
$x = explode('/', $_SERVER['SCRIPT_FILENAME']);
array_pop($x);
$x = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, implode('/', $x));
define('RPATH', $x . DIRECTORY_SEPARATOR);

//obtendo o que foi requerido depois do script (http://site/scpt.phar/PATH_INFO)
$x = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
$x = explode('/', trim($x, '/ '));

//PATH_DIR = diretório do arquivo solicitado
define('PATH_DIR', PATH . implode('/', $x));

//NAME_FILE = nome do arquivo solicitado
$x = end($x);
if ($x == 'core.php' || $x == 'core' || $x == '') $x = 'index';
define('NAME_FILE', $x);

//URL_BASE = base para os arquivos html => <base href="<?php echo URL_BASE;? >" />
$x = (isset($_SERVER['SCRIPT_NAME'])) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['PHP_SELF'];
define('URL_BASE', ((_detectSSL()) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . '/' . trim($x, '/ ') . '/');

//carregador automático de classes
loader();

if (strpos(PATH_DIR, PATH_PHP) === false
        && PATH . NAME_FILE != PATH_DIR
        && is_file(PATH_DIR)) {
    //procurando o mime type
    include LIB.'mimes.php';
    $ext = explode('.', NAME_FILE);
    $ext = end($ext);
    if (!isset($_mimes[$ext])) $mime = 'text/plain';
    else $mime = (is_array($_mimes[$ext])) ? $_mimes[$ext][0] : $_mimes[$ext];
    //pegando o arquivo
    $dt = file_get_contents(PATH_DIR);
    //enviando
    ob_start('ob_gzhandler');
    header('Content-Type: ' . $mime);
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime(PATH_DIR)) . ' GMT');
    header('Cache-Control: max-age=31536000');
    header('X-Powered-By: www.itbras.com');
    //header('Etag: "' . md5(microtime(true)) . '"');
    header('Content-Length: ' . strlen($dt));
    //saindo...
    exit($dt);
}

//Carregando arquivo da raiz
header('X-Powered-By: www.itbras.com');
$inc = VIEW . NAME_FILE;
if (is_file($inc)) include($inc);
elseif (is_file($inc . '.php')) include($inc . '.php');
elseif (is_file($inc . '.html')) include($inc . '.html');
elseif (is_file(VIEW.'404.php')) include VIEW.'404.php';

//comprimindo html
$x = ob_get_contents();
ob_end_clean();
$x = str_replace("\n", '', $x);
$x = @ereg_replace('[[:space:]]+', ' ', $x);
ob_start('ob_gzhandler');
exit($x);

//------- funções ------------------------------------------------
/*
 * Detecta se o acesso está sendo feito por SSL (https)
 */
function _detectSSL() {
    if (!isset($_SERVER["HTTPS"]))return false;
    if ($_SERVER["HTTPS"] == "on")return true;
    if ($_SERVER["HTTPS"] == 1)return true;
    if ($_SERVER['SERVER_PORT'] == 443)return true;
    return false;
}
/*
 * Simples função para "debug" print.
 */
function p($a, $ex = true) {
    $pt = '<pre>' . print_r($a, true) . '</pre>';
    if ($ex) exit($pt);
    echo $pt;
}

function loader(){
    //iniciando o carregador automático de classes (autoLoader)
    set_include_path('.'.PATH_SEPARATOR.str_replace('phar:', 'phar|', LIB)
                    .PATH_SEPARATOR.str_replace('phar:', 'phar|', PATH_PHP).trim(get_include_path(), ' .'));

    //setando o carregamento automático - autoLoader
    spl_autoload_register(
        function ($class){
            $class = ltrim('/'.strtolower(trim(strtr($class, '_\\', '//'), '/ ')),' /\\').'.php';
            $pth = explode(PATH_SEPARATOR, ltrim(get_include_path(), '.'));
            array_shift($pth);
            foreach($pth as $f){
                if(file_exists($f = str_replace('phar|', 'phar:', $f).$class)) return require_once $f;
            }
        }
    );
}