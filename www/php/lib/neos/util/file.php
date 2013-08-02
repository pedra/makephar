<?php

namespace Lib\Util;

/*
  MÉTODOS DISPONÍVEIS

  toIniFile()


 */

class File extends \Lib\Base {

    /**
     * Cria um arquivo ".ini"
     *
     * @param Array $ini		Array contendo os dados a serem convertidos
     * @param String $file	Caminho e nome do arquivo ".ini"
     *
     * @return Bool|String	Se $file for indicado retorna o status da criação/grvação do arquivo
     * Se $file não for indicado retorna uma string com os dados convertidos
     */
    static
            function toIniFile($ini, $file = null){
        if(!(is_array($ini) || is_object($ini)))
            return false;
        $o = '';
        foreach($ini as $k => $v){
            $o .= '[' . $k . "]\r\n";
            //segundo nó
            if(is_array($v)){
                foreach($v as $_k => $_v){
                    //terceiro nó
                    if(is_array($_v)){
                        foreach($_v as $__k => $__v){
                            if(is_array($__v))
                                $__v = print_r($__v, true);
                            $o .= "\t" . $_k . '[' . $__k . '] = ' . (is_numeric($__v) ? $__v : '"' . $__v . '"') . "\r\n";
                        }
                    }else
                        $o .= "\t" . $_k . ' = ' . (is_numeric($_v) ? $_v : '"' . $_v . '"') . "\r\n";
                }
            }
        }
        if($file != null)
            return file_put_contents($file, $o);
        else
            return $o;
    }
    /* Conversor de DIRETORIO em arquivo PHAR
     * $dir				- diretório (caminho completo);
     * $file			- arquivo de saída - indiqeu o caminho completo, nome e extensão '.phar'(obrigatório).
     * $compress	- true/false para compactação do arquivo.
     * $signature	- arquivo da chave de segurança para o phar (opcional).
     * nesta versão somente use uma chave MD5.
     */

    static function makePhar($dir, $file, $stub = '', $compress = true, $type = 'ex'){

        //conferindo os dados...
        if(!is_dir($dir)) return 'Diretório "' . $dir . '" inexistente!';
        $u = true;
        if(is_file($file)) $u = unlink($file);
        if(!$u) return 'O arquivo de destino ["' . $file . '"] não pode ser manipulado. Talvez algum problema de acesso ou permissão.';

        //aumentando a memoria e o tempo de execução
        //pode ser muito significante em sistemas lentos e diretórios muito grandes
        ini_set('memory_limit', '30M');
        ini_set('max_execution_time', 180);

        //pegando o diretório (e sub-diretórios) e arquivos contidos
        $phar = new \Phar($file);
        $phar->buildFromDirectory($dir);

        //Convertendo para DATA e retornando
        if($type == 'dt'){
            $ret = $phar->convertToData(\Phar::TAR, ((\Phar::canCompress(\Phar::GZ)) ? \Phar::GZ : \Phar::NONE), '.dphar');
            $phar = null;
            unlink($file);
            return $ret;
        }

        //criando o cabeçalho Stub
        if($type == 'ex') $stub = '<?php include(\'phar://\' . __FILE__ . \'/' . $stub . '\');__HALT_COMPILER();';
        if($type == 'wp') $stub = '<?php Phar::webPhar(\'\',\''.$stub.'\');Phar::mapPhar();Phar::interceptFileFuncs();Phar::mungServer(array(\'REQUEST_URI\', \'PHP_SELF\', \'SCRIPT_NAME\', \'SCRIPT_FILENAME\'));__HALT_COMPILER();';
        $phar->setStub($stub);

        //Comprimindo
        if($compress && \Phar::canCompress(\Phar::GZ))
            return $phar->compressFiles(\Phar::GZ);
    }

    static function download($file){
        //gerando header apropriado
        include_once LIB . 'mimes.php';
        $ext = explode('.', $file);
        $name = explode('/', $file);
        if(isset($_mimes[end($ext)])) header('Content-type: ' . ((is_array($_mimes[end($ext)])) ? $_mimes[end($ext)][0] : $_mimes[end($ext)]));
        header('Content-Disposition: attachment; filename="' . end($name) . '"');
        header("Content-Transfer-Encoding: binary");
        //enviando o arquivo solicitado
        exit(file_get_contents($file));
    }

    static function getFile($file){
        if(file_exists($file)) return file_get_contents($file);
    }

    static function saveFile($file, $conteudo){
        return file_put_contents($file, ((get_magic_quotes_gpc() == 1) ? stripslashes($conteudo) : $conteudo));
    }
}