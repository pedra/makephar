<?php 
namespace Lib;
	/* Simples Gerenciador de View - SGV
	 *
	 * Dependências 
	 *  Constantes: VIEW, EXTVW, INITIME;
	 *  Classes: Main com Loader, Lib;
	 *  Configuração: config.ini (geral) ou sgv.ini
	 *
	 */

class Sgv
	extends Base{
	
	private $vars = array(); //conjunto de variáveis (nome=>valor)
	private $views = array(); //views que devem ser manipuladas (em ordem)
	
	private $styles = array(); //arquivos de style a ser carregados
	private $scripts = array(); //scripts a serem carregados (js, dart, etc)
	
	private $stylePath = ''; //localização dos arquivos de style/css
	private $scriptPath = ''; //localização dos arquivos de script
	
	private $styleUrl = ''; //url dos arquivos de style/css
	private $scriptUrl = ''; //url dos arquivos de script
	

	//inserir um arquivo de view a renderização
	static function set($file, $vars = null){
		if(is_array($vars)) self::this()->val($vars); //carregando variaveis [if exists]
		if(file_exists(VIEW.$file.EXTVW)) return self::this()->views[] = VIEW.$file.EXTVW;
		else return false;
	}
	
	//carregando variáveis para a(s) view(s)
	static function val($name, $val = null){ return self::value($name, $val);}
	static function value($name, $val = null){
		if($name == '') return false;
		if(!is_array($name)) $name = array($name=>$val);		
		foreach($name as $k=>$v){ self::this()->vars[$k] = $v; }		
	}
	
	//carregando styles em views
	static function style($file){
		if(file_exists(self::this()->stylePath.$file)) return self::this()->styles[] = self::this()->stylePath.$file;
		return false;
	}
	
	//carregando scripts em views
	static function script($file){
		if(file_exists(self::this()->scriptPath.$file)) return self::this()->scripts[] = self::this()->scriptPath.$file;
		return false;
	}
	
	//construindo a classe...
	function __construct($ini = null){
		if($ini) \Loader::this()->loadConfig($ini.'.ini');
		$this->stylePath	= _cfg()->output->stylePath;
		$this->scriptPath	= _cfg()->output->scriptPath;
		$this->styleUrl		= _cfg()->output->styleUrl;
		$this->scriptUrl	= _cfg()->output->scriptUrl;
	}
		
	//Renderizando views...
	function produce(){ 
		//carregando e renderizando cada view indicada
		$o = '';
		foreach($this->views as $v){
			$o .= $this->decode(file_get_contents($v));			
		}
		
		if(_cfg('statusBar')) $o .= $this->statusBar(true);
		exit($o);
	}
	
	function decode($render){
		$t = strlen($render) - 1;	
		$ini = '';
		$o = '';
		
		for($i =0; $i <= $t; $i++){
					
			if($ini != '' && $ini < $i){
				if($render[$i] == '@' && ($i - $ini) < 2) {			
					$o .= '@';
					$ini = '';
					continue;
				}
				if(!preg_match("/[a-zA-Z0-9\.:\[\]\-_()\/'$+,\\\]/",$render[$i])){
					$out1 = substr($render, $ini+1, $i-$ini-1);
					$out = rtrim($out1, ',.:');
					$i += (strlen($out) - strlen($out1));
					
					if(isset($this->vars[$out])) $out = $this->vars[$out];				
					else {
						restore_error_handler();
						ob_start();
						$ret = eval('return '.$out.';');
						if(ob_get_clean() === '') $out = $ret;
						else $out = '';
					}
				$o .= $out;
				$ini = '';
				if($render[$i] != ' ') $i --;//retirando espaço em branco...
				}
			} elseif($ini == '' && $render[$i] == '@') $ini = $i;
			  else $o .= $render[$i];		
		}
		return $o;
	}
	
	function outBuffer($out,$md){
		return $out;	
	}
	
		/**
	* Gera a barra de status
	* TODO : Criar o carregamento e compressão de arquivos CSS/JS para incluir os da barra de status.
	*
	* @return string Html para a barra de status
	*/

function statusBar($extended = true){
		$sb = '<script type="text/javascript">var neos_=\'none\';function neostatus(){if(neos_==\'none\'){neos_=\'block\'}else{neos_=\'none\'};document.getElementById(\'neostatustable\').style.display=neos_}</script><style>#neostatus{position:fixed;bottom:10px;right:10px;z-index:200;background:#777;background-color:rgba(60,60,60,0.7);box-shadow:0 10px 60px #000 inset;cursor:pointer;font-size:10px;color:#FFF !important;font-family:Helvetica,Tahoma,monospace,\'Courier New\',Courier,serif;margin:0;padding:4px 8px;border:2px solid #777;border-radius:7px;text-align:right}#neostatustable{display:none;min-width:300px;color:#DDD;margin:0 0 20px 0}#neostatustable tr td{background:transparent !important;padding:2px 5px 0 0; color:#999}#neostatustable th{padding:5px 0;color:#FFF}#neostatustable tr th{font-size:12px;font-weight:bold}#neostatustable pre {white-space:pre-wrap}.neostatuslg td{border-bottom:1px dashed #999}.r{text-align:right !important;padding:2px 0 0 0 !important}</style><div id="neostatus" onClick="neostatus()">';
		
		if($extended){
			$sb .= '<table id="neostatustable" title="click para esconder!"><tr><th colspan="2">www.itbras.com/zumbi - ver. '.VERSION.'</th></tr>';
			$ct = $cf = 0;
			foreach(get_included_files() as $f){
					$fz = filesize($f);
					$sb .= '<tr><td>'.$f.'</td><td class="r">'.number_format($fz/1000,2,',','.').'&nbsp;kb</td></tr>';
					$ct += $fz;
					$cf++;
			}
			$sb .= '<tr><th>Total ('.$cf.' arquivos )</th><th class="r">'.number_format($ct/1000,2,',','.').'&nbsp;kb</th></tr>';
				
		}
		$t = explode(' ',microtime());
		return $sb.'</table>'.number_format(round(((memory_get_usage()+memory_get_peak_usage())/2000),0),0,',','.').' kb | '.number_format((($t[0] * 1000)-INITIME),1,',','.').' ms</div>';
	}


}