<?php

namespace Lib\Util;

 /*
	MÉTODOS DISPONÍVEIS

	toIniFile()


 */

class Dir
	extends \Base{
	
	//Cria uma lista (divs) de arquivos e diretórios ... 
	static function listDir($dir){
		$dir = rtrim(str_replace(array('\\','|'), '/',$dir), '/ ').'/';
//		if(strpos(strtolower($dir), strtolower(\_cfg::this()->sync['path'])) === false 
//				|| !is_dir($dir)) $dir = \_cfg::this()->sync['path'];
		if(strpos(strtolower($dir), strtolower(\_cfg::this()->sync['path'])) === false) $dir = \_cfg::this()->sync['path'];
		$d = dir($dir);
		$id = 1;
		$o = '';
		 
		$afile = $adir = array();
		
		while (false !== ($f = $d->read())) {
			$id ++;
			if($f == '.' || $f == '..') continue;
			
			if(is_dir($dir.$f)) $adir[] = $f;
			else $afile[] = $f;
		}
			sort($adir);
			sort($afile);
			
			foreach($adir as $k=>$f){
				$o .= '<table class="diretorios" id="dir'.$k.'" sdir="'.$dir.'" sname="'.$f.'">
						<tr><td class="tseletor"></td>
								<td class="detalhes">
										<h3>'.$f.'</h3>
										<p class="fperm">'.substr(sprintf('%o', fileperms($dir.$f)), -4).'</p>					
										<p class="fdate">'.date ('d/m/Y H:i:s', filemtime($dir.$f)).'</p>
										<p class="fsize">&nbsp;</p>
								</td>
								<td class="tgoDir" onClick="goDir(\''.$dir.$f.'\')"></td>
							</tr>
						</table>			
				';
			}
			foreach($afile as $k=>$f){
				$ext = explode('.', $f); //pegando a extensão do arquivo	
				$o .= '<table class="arquivos '.end($ext).'" id="file'.$k.'" sdir="'.$dir.'" sname="'.$f.'">
						<tr><td class="tseletor"></td>
								<td class="detalhes">
										<h3>'.$f.'</h3>
										<p class="fperm">'.substr(sprintf('%o', fileperms($dir.$f)), -4).'</p>					
										<p class="fdate">'.date ('d/m/Y H:i:s', filemtime($dir.$f)).'</p>
										<p class="fsize">'.(filesize($dir.$f)/1000).'&nbsp;Kb.</p>
								</td>
								<td class="tgoEdit" onClick="viewFile(\'file'.$k.'\',\''.$dir.$f.'\')"></td>
							</tr>
						</table>			
				';
			}
		return ($o == '')?'<div class="fmList block"><h3>Nenhum arquivo ou diretório encontrado.</h3></div>':$o;		
	}

	//Lista um determinado diretório retornando em JSON	
	static function jList($dir = RPATH, $preview = false){		
		$dir = ($preview == 1) 
			? dirname(rtrim(str_replace('\\', '/', $dir), '/ ')).'/' 
			: rtrim(str_replace('\\', '/', $dir), '/ ').'/';
			
		if(strpos(strtolower($dir), strtolower(\_cfg::this()->sync['path'])) === false 
				|| !is_dir($dir)) $dir = \_cfg::this()->sync['path'];

		$d = dir($dir); 
//		$exdir = (strlen($dir) > 25) 
//									? substr($dir, 0, 11).'...'.substr($dir,(strlen($dir) - 11),11)
//									: $dir;
		$id = 0;		 
		$afile = $adir = array();
		
		while (false !== ($f = $d->read())) {
			$id ++;
			if($f == '.' || $f == '..') continue;
			
			if(is_dir($dir.$f)) {//para diretórios
				$adir[$id]['name'] = $f;
				$adir[$id]['perm'] = substr(sprintf('%o', fileperms($dir.$f)), -4);
				$adir[$id]['date'] = date ('d/m/Y H:i:s', filemtime($dir.$f));				
			}	else { //para arquivos
				$x = explode('.', $f); 
				$afile[$id]['name'] = $f;
				$afile[$id]['ext'] = end($x);
				$afile[$id]['perm'] = substr(sprintf('%o', fileperms($dir.$f)), -4);
				$afile[$id]['date'] = date ('d/m/Y H:i:s', filemtime($dir.$f));
				$sz = filesize($dir.$f);
				if($sz < 1000) $z = $sz.' b';
				if($sz > 1000 && $sz < 1000000) $z = intval($sz/1000).' K';
				if($sz > 1000000) $z = intval($sz/1000000).' M';
				$afile[$id]['size'] = $z;				
			}
		}
		sort($adir);
		sort($afile);
		$diretorio = explode('/', trim($dir, '/'));
		$diretorio = end($diretorio);
		return json_encode(array('base'=>$dir, 'diretorio'=>$diretorio, 'dir'=>$adir, 'file'=>$afile));			
	}
}