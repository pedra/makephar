<?php
$msg = '';
$executar = true;
if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    $msg .= '<b>Versão do PHP incompatível!</b> - sua versão: ' . phpversion() . ' <br/>Versão requerida: 5.3.0 (ou mais).<br/>';
    $executar = false;
}
//checando phar.readonly
if (function_exists('ini_get')) {
    if (ini_get('phar.readonly') != '') {
        $msg .= 'A diretiva <b>"phar.readonly"</b> no arquivo "php.ini" deve ser " = <b>Off</b>"<br/>';
        $executar = false;
    }
} else {
    $msg .= '<b>A diretiva "phar.readonly" no arquivo "php.ini" <ins>não pode ser checada</ins></b><br/>O valor deve ser <b>"= Off"</b> para usar arquivos PHAR<br />A conversão <ins>pode</ins> não funcionar!<br/>';
}

//Processando a conversão PHAR
if (isset($_POST['origem']) && isset($_POST['destino']) && isset($_POST['init'])){
    //include LIB.'makephar.php';
    $origem = $_POST['origem'];
    $destino = $_POST['destino'];
    $init = $_POST['init'];
    $tipo = $_POST['tipo'];
    //$msg .= makephar($origem, $destino, $init, isset($_POST['compactar']));
    $mp = Lib\Util\File::makePhar($origem, $destino, $init, true, $tipo);
    if(is_string($mp)) $msg.= $mp;
    else $msg.= $mp === false ? 'Erro na compilação!':'Arquivo PHAR criado com sucesso!';
    $msg.= '<br>';
} else {
    $origem = RPATH;
    $destino = dirname(RPATH).DIRECTORY_SEPARATOR.'newfile.phar';
    $init = 'index.php';
    $tipo = 'ex';
}


//DEBUGS ->
//    echo 'path_dir: '.PATH_DIR.'<br>';
//    echo 'name_file: '.NAME_FILE.'<br>';
//    echo 'path_php: '.PATH_PHP.'<br>';
//    $view = new View;
//    $factory = Doc\Factory::teste();
//DEBUGS <-

?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <title>MAKEPHAR</title>
        <base href="<?php echo URL_BASE; ?>" >
        <link rel="shortcut icon" href="img/favicon.ico">
        <link href="style/main.css" rel="stylesheet" type="text/css" >
    </head>
    <body>
        <div class="container">
            <h1>MakePhar</h1>
            <p class="subtitulo">Conversor de aplicação para PHAR <a href="<?php echo URL_BASE; ?>help"> | precisa de ajuda?</a></p>
<?php if ($msg != '') echo '<div class="msg error">' . $msg . '</div>'; ?>

            <form action="<?php echo URL_BASE; ?>index.php" method="post">
                <div class="inputs">
                    <label>Origem (diretório):</label>
                    <input name="origem" type="text" value="<?php echo $origem; ?>" title="O diretório que contém a sua aplicação em PHP a ser convertida."/>

                    <label>Destino (arquivo):</label>
                    <input name="destino" type="text" value="<?php echo $destino; ?>" title="O arquivo (com extensão '.phar') que conterá a aplicação."/>

                    <label>Default (executar este arquivo como default):</label>
                    <input name="init" type="text" value="<?php echo $init; ?>" title="Um arquivo da sua aplicação que deve ser executado como default. Ex.: index.php"/>

                    <label>Tipo de Arquivo PHAR:</label>
                    <select name="tipo">
                        <option value="ex" <?php if($tipo == 'ex') echo 'selected'?>>Executável (depende de framework ePhar)</option>
                        <option value="wp" <?php if($tipo == 'wp') echo 'selected'?>>Aplicação transparente</option>
                        <option value="dt" <?php if($tipo == 'dt') echo 'selected'?>>Data PHAR (para usar como libPhar)</option>
                    </select>

                    <p title="Compactar o arquivo pode te dar maior segurança. Consulte a documentação do PHP para mais detalhes.">
                        <label class="chkbox"><input name="compactar" type="checkbox" value="1" checked/>Compactar (gz/bz2)!</label>
                    </p>
                </div>
                <?php if ($executar) echo '<button name="" type="submit">Criar Arquivo PHAR</button>'; ?>
            </form>
            <div class="license">&copy; Paulo R. B. Rocha - <a href="mailto: prbr@ymail.com">prbr@ymail.com</a> - (+55 21) 3630-5920
                <p>É PROIBIDA A COMERCIALIZAÇÃO, DISTRIBUIÇÃO POR QUALQUER MEIO, MODIFICAÇÃO, ALTERAÇÃO E SER USADO COMO BASE OU PARTE DE OUTRO SISTEMA OU SOFTWARE SEM A DIVULGAÇÃO DESTE TEXTO TITULADO COMO LICENÇA E NOME E DADOS DE CONTATO DO AUTOR NO MESMO SUPORTE OU MEDIA, LEGÍVEL PARA O USUÁRIO.</p>
            </div>
        </div>
        <script type="text/javascript" src="script/main.js"></script>
    </body>
</html>