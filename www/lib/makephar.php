<?php
ini_set('memory_limit', '30M');
ini_set('max_execution_time', 180);

$dir = trim($_POST['origem'], ' \\/');	
$stub = trim(htmlspecialchars_decode('&lt;?php include(\'phar://\' . __FILE__ . \'/' . $_POST['init'] . '\');__HALT_COMPILER();'), ' ');

if(is_dir($dir)){ 		
	//criando arquivo PHAR
	$phar = new Phar(trim($_POST['destino'], ' \\/'));	
	
	//pegando o diretório (e sub-diretórios) e arquivos contidos
	$phar->buildFromIterator(
		new RecursiveIteratorIterator(
		 new RecursiveDirectoryIterator($dir)), $dir);
	
	//criando o cabeçalho Stub
	$phar->setStub($stub);
	
	//comprimindo os dados (exceto o Stub)
	if(isset($_POST['compactar'])){
		if(Phar::canCompress(Phar::GZ)) 		$phar->compressFiles(Phar::GZ);
		elseif (Phar::canCompress(Phar::BZ2))	$phar->compressFiles(Phar::BZ2);
	}
	
	$msg .= 'Arquivo PHAR criado com sucesso!<br/>';
} else { $msg .= 'A "ORIGEM" não é um diretório/arquivo válido.<br/>';}