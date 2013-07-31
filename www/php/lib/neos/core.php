<?php
/* TODO
 * Problemas
 * 1 - Somente arquivos simples podem ser solicitados (views).
 *      Não é possível solicitar com path (ex.: site.com/ctrl/sub/index == index e ignora o resto)
 * 2 -
 */
Phar::interceptFileFuncs();
if(!isset($_SERVER['PATH_INFO'])) $_SERVER['PATH_INFO'] = '';
header('X-Powered-By: www.itbras.com');

//Defines
define('NEOS_PHAR', ((strpos(__DIR__, 'phar:') === false)?false:true));
defined('PATH') || define('PATH', dirname(dirname(dirname(__DIR__))).'/'); 
defined('PPHP') || define('PPHP', PATH.'php/');
defined('VIEW') || define('VIEW', PPHP.'view/');
defined('LIB')  || define('LIB', PPHP.'lib/');
//URL_BASE = base para os arquivos html => <base href="<?php echo URL_BASE;? >" />
$tp = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/'
                    .trim((isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['PHP_SELF']), '/ ').'/';
define('URL_BASE', ((NEOS_PHAR) ? $tp : dirname($tp).'/'));
define('RPATH', inPhar());//RealPath - quando estiver em modo PHAR

//carregador automático de classes
loader();

//incluindo a VIEW de execução solicitada
$inc = VIEW . NAME_FILE;
if (is_file($inc)) include($inc);
elseif (is_file($inc . '.php')) include($inc . '.php');
elseif (is_file($inc . '.html')) include($inc . '.html');
elseif (is_file(VIEW.'404.php')) include VIEW.'404.php';

//terminando e retornando para o Browser
outPut(ob_get_contents());



//------- funções ------------------------------------------------
//Finaliza e envia para o Browser
function outPut($content){
    ob_end_clean();
    //tirando espaços do HTML
    $x = str_replace("\n", '', $content);
    $x = @ereg_replace('[[:space:]]+', ' ', $x);
    ob_start('ob_gzhandler');
    exit($x);    
}

//Resolve solicitações de download de arquivos armazenados no PHAR
function inPhar(){
    //pegando o PATH fisico do ARQUIVO PHAR
    $x = explode('/', $_SERVER['SCRIPT_FILENAME']);
    array_pop($x);
    $x = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, implode('/', $x));
    $rpath = $x . DIRECTORY_SEPARATOR;

    //obtendo o que foi requerido depois do script (http://site/scpt.phar/PATH_INFO)
    $x = explode('/', trim($_SERVER['PATH_INFO'], '/ '));
    //PATH_DIR = diretório do arquivo solicitado
    define('PATH_DIR', PATH . implode('/', $x));
    //NAME_FILE = nome do arquivo solicitado
    $x = end($x);
    if ($x == 'core.php' || $x == 'core' || $x == '') $x = 'index';
    define('NAME_FILE', $x);

    if (NEOS_PHAR 
        && strpos(PATH_DIR, PPHP) === false
        && PATH . NAME_FILE != PATH_DIR
        && is_file(PATH_DIR)) download(NAME_FILE, PATH_DIR);
    else return $rpath;
}

//Faz download de arquivos ...
function download($file, $path){
    //procurando o mime type
    include LIB.'neos/util/mimes.php';
    $ext = explode('.', $file);
    $ext = end($ext);
    if (!isset($_mimes[$ext])) $mime = 'text/plain';
    else $mime = (is_array($_mimes[$ext])) ? $_mimes[$ext][0] : $_mimes[$ext];
    //pegando o arquivo
    if($ext == 'css') $dt = preg_replace("#/\*[^*]*\*+(?:[^/*][^*]*\*+)*/#","",preg_replace('<\s*([@{}:;,]|\)\s|\s\()\s*>S','\1',str_replace(array("\n","\r","\t"),'',file_get_contents($path))));
    elseif($ext == 'js') $dt = preg_replace("/^\s/m",'',str_replace("\t",'',file_get_contents($path)));
    else $dt = file_get_contents($path);
    //enviando
    ob_end_clean();
    ob_start('ob_gzhandler');
    header('Content-Type: ' . $mime);
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
    header('Cache-Control: max-age=31536000');
    header('Content-Length: ' . strlen($dt));
    //saindo...
    exit($dt);
}

//Simples função para "debug" print.
function p($a, $ex = true) {
    $pt = '<pre>' . print_r($a, true) . '</pre>';
    if ($ex) exit($pt);
    echo $pt;
}


//------------------------------ DELETE
function px(){
    echo 'Dir: '.__DIR__;
    echo '<br>Phar: '.NEOS_PHAR;
    echo '<br>URL_BASE: '.URL_BASE; 
    echo '<br>PATH: '.PATH;
    echo '<br>RPATH: '.RPATH;
    echo '<br>PHP: '.PPHP;
    echo '<br>VIEW: '.VIEW;
    echo '<br>LIB: '.LIB;

    p($_SERVER);
}
//------------------------------ DELETE

function loader(){
    //iniciando o carregador automático de classes (autoLoader)
    set_include_path('.'.PATH_SEPARATOR.str_replace('phar:', 'phar|', LIB)
                    .PATH_SEPARATOR.str_replace('phar:', 'phar|', PPHP).trim(get_include_path(), ' .'));

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

    include_once LIB.'autoload.php';
}