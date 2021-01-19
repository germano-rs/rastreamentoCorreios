<?php

use Dompdf\Dompdf;

function geraPDF($order)
{
    try {
        require 'vendor/autoload.php';
        require_once "config.php";

        //instancia o Dompdf
        $dompdf = new Dompdf();

        //busca o html que será usado para gerar o pdf
        $htmlPage = file_get_contents(RAIZ . "order.php?objeto=" . $order);

        //carregamos o código HTML no nosso arquivo PDF
        $dompdf->loadHtml($htmlPage);

        //(Opcional) Defina o tamanho (A4, A3, A2, etc) e a oritenação do papel, que pode ser 'portrait' (em pé) ou 'landscape' (deitado)
        $dompdf->setPaper('A4', 'portrait');

        //Renderizar o documento
        $dompdf->render();

        //pega o código fonte do novo arquivo PDF gerado e armazena na $output
        $output = $dompdf->output();

        //cria a pasta que armazena o .pdf criado (e que será apagado) caso não exista
        $dirname = __DIR__ . '/pdf';
        if (!is_dir($dirname)) {
            mkdir($dirname);
        }

        //define o nome do .pdf
        $fileName = 'Tracking-' . $order . '.pdf';
        $dirFile =  __DIR__ . '/pdf/' . $fileName;

        //cria o arquivo com o nome da $dirFile com o código do output.
        file_put_contents($dirFile, $output);

        //retorna o caminho para o pdf criado. O nome será usado para que o pdf seja anexado ao e-mail
        return $dirFile;
    } catch (\Throwable $th) {

        //em caso de erro
        echo 'Erro ao gerar pdf';
    }
}
