<?php
//Iniciando a sessão:
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

require_once "config.php";
include_once "generatePdf.php";
// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);



if (isset($_POST['objeto'])) {
    $objeto = $_POST['objeto'];
    //cria array com cada objeto separado por ";"
    $objeto = explode(";", $objeto);

    //executa rotina para cada objeto enviado
    foreach ($objeto as $obj) {

        //traz os dados JSON retornados pela API
        $res = file_get_contents(APISERVICE . $obj);
        //decodifica o JSON retornado
        $decode = json_decode($res, true);

        //cria ,através do order.php, o html que gera a estrutura da encomenda que é enviada no corpo 
        //do email e no pdf. 
        $corpoDoEmail = file_get_contents(RAIZ . 'order.php?objeto=' . $obj);

        //extrai as informações retornadas pela API para a encomenda.
        foreach ($decode as $key => $value) {

            //define o status da entrega
            switch ($value[0]['action']) {
                case "Objeto entregue ao destinatário":
                    $status = "Entregue";
                    break;
                case "Objeto aguardando retirada no endereço indicado":
                    $status =  "Erro";
                    break;
                case "Carteiro não atendido - Entrega não realizada":
                    $status =  "Erro";
                    break;
                case "Objeto saiu para entrega ao destinatário":
                    $status =  "Encaminhado";
                    break;
                case "Objeto em trânsito - por favor aguarde":
                    $status =  "Encaminhado";
                    break;
                case "Objeto postado após o horário limite da unidade":
                    $status =  "Postado";
                    break;
            }


            //envio do email
            try {

                $mail = new PHPMailer();

                // Método de envio
                $mail->IsSMTP();
                $mail->CharSet = 'UTF-8';
                $mail->IsHTML(true);

                // Enviar por SMTP
                $mail->Host = "smtp.gmail.com";

                // Você pode alterar este parametro para o endereço de SMTP do seu provedor
                $mail->Port = 587;

                // Usar autenticação SMTP (obrigatório)
                $mail->SMTPAuth = true;

                // Usuário do servidor SMTP (endereço de email)
                // obs: Use a mesma senha da sua conta de email
                $mail->Username = EMAIL;
                $mail->Password = SENHA;
                //configurações de compatibilidade para autenticação em TLS
                $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));

                $mail->From = EMAIL;

                //seu nome
                $mail->FromName = "";

                //destinatário
                $mail->addAddress(DESTINATARIO);

                //em cópia
                $mail->AddCC(COPIAEMAIL);


                //conteúdo
                //assunto
                $mail->Subject = 'Encomenda: ' . $generatePdf . ' - Status: ' . $status;

                //corpo do email
                $mail->Body = $corpoDoEmail;

                //gera o pdf através do arquivo generatePdf.php e retorna com o caminho do arquivo
                $orderDir = geraPDF($key);

                //anexa o pdf gerado
                $mail->AddAttachment($orderDir);

                //envia o email
                $mail->send();

                //concatena o result para que apareça mensagem informando todas as encomendas com status enviado
                $result .= "E-mail da entrega $key foi enviado com sucesso! <br>";
            } catch (Exception $e) {
                //em caso de erro
                echo "A mensagem não foi enviada. O erro do e-mail foi: {$mail->ErrorInfo}";
            }
            unlink($generatePdf);
        }
    }
    $_SESSION['emailStatus'] =  $result;
    header('Location: ' . RAIZ . 'index.php');
} else {
    $jsonObcject = new stdClass();
    $jsonObcject->msg = "Objeto não encontrado";
}
