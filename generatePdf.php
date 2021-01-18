<?php

use Dompdf\Dompdf;

function geraPDF($track)
{
    try {
        require 'vendor/autoload.php';
        require_once "config.php";

        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        //ob_start();
        //include_once("track.php?objeto=" . $track);
        $htmlPage = file_get_contents(RAIZ . "track.php?objeto=" . $track);
        // carregamos o código HTML no nosso arquivo PDF
        $dompdf->loadHtml($htmlPage);

        // (Opcional) Defina o tamanho (A4, A3, A2, etc) e a oritenação do papel, que pode ser 'portrait' (em pé) ou 'landscape' (deitado)
        $dompdf->setPaper('A4', 'portrait');

        // Renderizar o documento
        $dompdf->render();

        // pega o código fonte do novo arquivo PDF gerado
        $output = $dompdf->output();

        $dirname = __DIR__ . '/pdf';
        if (!is_dir($dirname)) {
            mkdir($dirname);
        }

        $fileName = 'Tracking-' . $track . '.pdf';
        $dirFile =  __DIR__ . '/pdf/' . $fileName;

        // defina aqui o nome do arquivo que você quer que seja salvo
        file_put_contents($dirFile, $output);
        return $dirFile;
    } catch (\Throwable $th) {
        echo 'Erro ao gerar pdf';
    }
}
