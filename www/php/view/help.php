<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <title>MAKEPHAR | Administrador</title>
        <base href="<?php echo URL_BASE;?>" />
        <link rel="shortcut icon" href="img/favicon.ico"/>
        <link href="style/main.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <div class="container helper">
            <h1>MakePhar</h1>
            <p class="subtitulo">Conversor de aplicação para PHAR <a href="<?php echo URL_BASE; ?>"> | voltar a página inicial.</a></p>
            <h2>Pré-requisitos</h2>
            <p>Sua versão do PHP deve ser a <b>5.3</b> ou superior, rodando em &copy;Windows ou Linux e com suporte a Zlib somente se for compactar o arquivo gerado.</p>
            <p>O arquivo <b>php.ini</b> deve ter a seguinte linha:<code title="Copie & cole!">phar.readonly = <b>off</b></code></p>
            <p>O arquivo de configurações do Apache (<b>httpd.conf</b>) deve ter a linha:<code title="Copie & cole!">AddType application/x-httpd-php <b>.phar</b></code></p>
            <h2>Usando</h2>
            <p>Copie este arquivo para o host do seu servidor web <i><b>(ex.: /var/www/make.phar)</b></i>. Usando o seu navegador, acesse o arquivo digitando algo como:<code title="Copie & cole!">localhost/make.phar</code></p>
            <p>No formulário que será mostrado, preencha os campos conforme descrito abaixo:</p>
            <ul>
                <li><b>ORIGEM</b> - diretório a ser convertido para Phar <span class="quiet">( ex.: /var/www/site )</span>;</li>
                <li><b>DESTINO</b> - caminho, nome e extensão do arquivo final <span class="quiet">( ex.: /var/www/site.phar )</span>;</li>
                <li><b>DEFAULT</b> - indique o script que deve ser executado automaticamente <span class="quiet">( ex.: index.php )</span>;</li>
                <li><b>COMPACTAR</b> - compacta o arquivo final com gz/bz2!<br /> Isso pode dificultar a engenharia reversa em seus scripts.</li>
            </ul>
            <p>Ao clicar no botão <b>"Criar Arquivo PHAR"</b> os arquivos serão convertidos em PHAR e no final do processo uma mensagem de <b>"sucesso"</b> aparecerá por alguns segundos na parte superior da página.<br />Caso ocorram erros, estes serão mostrados nesta mesma posição.</p>
            [ <a href="<?php echo URL_BASE; ?>">voltar a página inicial</a> ]
            <div class="license">&copy; Paulo R. B. Rocha - <a href="mailto: prbr@ymail.com">prbr@ymail.com</a> - (+55 21) 3630-5920
                <p>É PROIBIDA A COMERCIALIZAÇÃO, DISTRIBUIÇÃO POR QUALQUER MEIO, MODIFICAÇÃO, ALTERAÇÃO E SER USADO COMO BASE OU PARTE DE OUTRO SISTEMA OU SOFTWARE SEM A DIVULGAÇÃO DESTE TEXTO TITULADO COMO LICENÇA E NOME E DADOS DE CONTATO DO AUTOR NO MESMO SUPORTE OU MEDIA, LEGÍVEL PARA O USUÁRIO.</p>
            </div>
        </div>

    </body>
</html>