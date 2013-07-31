<?php
namespace Lib\Util;

class Compact {
	
	private $data = '';
	private $handle = '';	
	
	function __construct(){
		$this->handle = new	ZipArchive();
		if(!is_object($this->handle)) trigger_error('Não foi possível instanciar a classe ZipArchive!.') ;		
	}

	// Compacta um diretorio!
	// ex.: $z = new Compact; $z->zip('/var/www/sync', '/var/www/file.zip', '/');
	function zip($zipFile, $dir, $newDir = ''){		
		// abre o arquivo .zip
		if ($this->handle->open($zipFile, ZIPARCHIVE::CREATE) !== true) 
			return trigger_error('Não foi possível abrir o arquivo de DESTINO ('.$zipFile.')!');
		
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
		
		// itera cada pasta/arquivo contido no diretório especificado
		foreach ($iterator as $key=>$value) {
			// adiciona o arquivo ao .zip
			if(!$this->handle->addFile(realpath($key), $newDir.str_replace('\\', '/', str_replace($dir,'',realpath($key)))))
				return trigger_error('Não é possível adicionar o arquivo "'.$key.'"!');
		}
		// fecha e salva o arquivo .zip gerado
		return $this->handle->close();
	}
	
	//Adicionando um ARQUIVO ao zip.file
	// ex.: $z = new Compact; $z->insert('/var/www/sync/index.php', '/var/www/file.zip', '/index.php');
	function insert($zipFile, $file, $asFile = ''){
		// abre o arquivo .zip
		if ($this->handle->open($zipFile, ZIPARCHIVE::CREATE) !== true) 
			return trigger_error('Não foi possível abrir o arquivo "'.$zipfile.')"!');
     
    	$this->handle->addFile($file, (($asFile == '') ? $file : $asFile));
		return $this->handle->close();
	}
	
	
	//lista os arquivos compactados. 
	//ex.: $z = new Compact; $listagem = $z->view('/var/www/file.zip');
	function view($zipFile){
		if($this->handle->open($zipFile) !== true) 
			return trigger_error('Não foi possível abrir o arquivo "'.$zipFile.'"!');
			
		$temp = array();
		for ($i = 0; $i < $this->handle->numFiles; $i++) { $temp[] = $this->handle->getNameIndex($i);}
		return $temp;		
	}
	
	//Descompatando um diretório/arquivo
	//ex.: $z = Compact; $z->unZip('/var/www/file.zip', '/var/www/destino/');
	function unZip($zipFile, $dir = ''){
		if($dir == '') $dir = dirname(__FILE__);
		if($this->handle->open($zipFile) !== true) 
			trigger_error('Não foi possível abrir o arquivo '.$zipFile.'!');
		$this->handle->extractTo($dir);
		return $this->handle->close();	
	}	
	
}
