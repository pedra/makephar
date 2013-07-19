<?php

function makephar($origem, $destino, $init = 'index.php', $compactar = true){
    ini_set('memory_limit', '30M');
    ini_set('max_execution_time', 180);

    $dir = rtrim($origem, ' \\/').'/';
    $stub = trim(htmlspecialchars_decode('&lt;?php include(\'phar://\' . __FILE__ . \'/' . $init . '\');__HALT_COMPILER();'), ' ');

    if(is_dir($dir)){
        //criando arquivo PHAR
        $phar = new Phar(rtrim($destino, ' \\/'));

        //pegando o diretório (e sub-diretórios) e arquivos contidos
        $phar->buildFromDirectory($dir);

        //criando o cabeçalho Stub
        $phar->setStub($stub);

        //comprimindo os dados (exceto o Stub)
        if($compactar){
            if (Phar::canCompress(Phar::GZ)) $phar->compressFiles(Phar::GZ);
            elseif (Phar::canCompress(Phar::BZ2)) $phar->compressFiles(Phar::BZ2);
        }
        return 'Arquivo PHAR criado com sucesso!<br/>';
    } else { return 'A "ORIGEM" não é um diretório/arquivo válido.<br/>';}
}