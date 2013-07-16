<?php 
$msg = '';
if(isset($_POST['origem']) && isset($_POST['destino']) && isset($_POST['init'])) include 'lib/makephar.php';
$executar=true;
if(version_compare(PHP_VERSION, '5.3.0', '<')){
	$msg .= '<b>Versão do PHP incompatível!</b> - sua versão: ' . phpversion() . ' <br/>Versão requerida: 5.3.0 (ou mais).<br/>';
	$executar = false;
}
//checando phar.readonly
if(function_exists('ini_get')){
	if(ini_get('phar.readonly')!=''){
		$msg .= 'A diretiva <b>"phar.readonly"</b> no arquivo "php.ini" deve ser " = <b>Off</b>"<br/>';
		$executar = false;
	}
}else{
	$msg .= '<b>A diretiva "phar.readonly" no arquivo "php.ini" <ins>não pode ser checada</ins></b><br/>O valor deve ser <b>"= Off"</b> para usar arquivos PHAR<br />A conversão <ins>pode</ins> não funcionar!<br/>';}

$origem		= isset($_POST['origem'])	? $_POST['origem']	: RPATH;
$destino	= isset($_POST['destino'])	? $_POST['destino']	: dirname(RPATH) . DIRECTORY_SEPARATOR . 'newfile.phar';
$init		= isset($_POST['init'])		? $_POST['init']	: 'index.php';
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>MAKEPHAR</title>
<base href="<?php echo URL_BASE;?>" >
<link rel="shortcut icon" href="style/favicon.ico">
<link href="style/screen.css" rel="stylesheet" type="text/css" >
<script language="javascript">
	function offMsg(){ document.getElementsByClassName('msg').item('p').style.display = 'none'}
 	var t=setTimeout("offMsg()",14000)
</script>
</head>
<body>
	<div class="container">
		<input type="button" class="links" onclick="location.href='<?php echo URL_BASE;?>help.php'" value="Ajuda"/>
		<h1>Conversor PHAR</h1>
		<?php if($msg != '') echo '<div class="msg error">'.$msg.'</div>';?>
			
    <form action="<?php echo URL_BASE;?>index.php" method="post">
			<div class="inputs">
				<label>Origem (diretório):</label>			
				<input name="origem" type="text" value="<?php echo $origem;?>" title="O diretório que contém a sua aplicação em PHP a ser convertida."/>
				
				<label>Destino (arquivo):</label>
				<input name="destino" type="text" value="<?php echo $destino;?>" title="O arquivo (com extensão '.phar') que conterá a aplicação."/>
              
        <label>Default (executar este arquivo como default):</label>
				<input name="init" type="text" value="<?php echo $init;?>" title="Um arquivo da sua aplicação que deve ser executado como default. Ex.: index.php"/>
                
        <p title="Compactar o arquivo pode te dar maior segurança. Consulte a documentação do PHP para mais detalhes.">
          <label class="chkbox"><input name="compactar" type="checkbox" value="1" checked/>Compactar (gz/bz2)!</label>
        </p>		
			</div>			
			<?php if($executar){?><p><input name="" type="submit" value="Criar Arquivo PHAR" /></p><?php ;}?>		
    </form>
	</div>
  <div class="license">&copy; Paulo R. B. Rocha - <a href="mailto: prbr@ymail.com">prbr@ymail.com</a> - (+55 21) 3630-5920
      <p>É PROIBIDA A COMERCIALIZAÇÃO, DISTRIBUIÇÃO POR QUALQUER MEIO, MODIFICAÇÃO, ALTERAÇÃO E SER USADO COMO BASE OU PARTE DE OUTRO SISTEMA OU SOFTWARE SEM A DIVULGAÇÃO DESTE TEXTO TITULADO COMO LIÇENCA E NOME E DADOS DE CONTATO DO AUTOR NO MESMO SUPORTE OU MEDIA, LEGÍVEL PARA O USUÁRIO.</p>
  </div>
</body>
</html>