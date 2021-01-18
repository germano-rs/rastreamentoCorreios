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

// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

require_once "config.php";
include_once "geraPdf.php";

if (isset($_POST['objeto'])) {
    $objeto = $_POST['objeto'];
    $objeto = explode(";", $objeto);

    foreach ($objeto as $obj) {
        $res = file_get_contents(APISERVICE . $obj);
        $decode = json_decode($res, true);

        $corpoDoEmail = file_get_contents(RAIZ . 'track.php?objeto=' . $obj);

        foreach ($decode as $key => $value) {
            switch ($value[0]['action']) {
                case "Objeto entregue ao destinatário":
                    $status = "Entregue";
                    $statusBar = 'entregue';
                    break;
                case "Objeto aguardando retirada no endereço indicado":
                    $status =  "Erro";
                    $statusBar = 'erro';
                    break;
                case "Carteiro não atendido - Entrega não realizada":
                    $status =  "Erro";
                    $statusBar = 'erro';
                    break;
                case "Objeto saiu para entrega ao destinatário":
                    $status =  "Encaminhado";
                    $statusBar = 'encaminhado';
                    break;
                case "Objeto em trânsito - por favor aguarde":
                    $status =  "Encaminhado";
                    $statusBar = 'transito';
                    break;
                case "Objeto postado após o horário limite da unidade":
                    $status =  "Postado";
                    $statusBar = 'postado';
                    break;
            }
            // Instantiation and passing `true` enables exceptions


            try {
                $mail = new PHPMailer();
                // Método de envio
                $mail->IsSMTP();
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
                // Configurações de compatibilidade para autenticação em TLS
                $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));
                // Você pode habilitar esta opção caso tenha problemas. Assim pode identificar mensagens de erro.
                // $mail->SMTPDebug = 2;
                // Define o remetente
                // Seu e-mail
                $mail->CharSet = 'UTF-8';
                $mail->IsHTML(true);
                $mail->From = EMAIL;
                // Seu nome
                $mail->FromName = "";

                $mail->addAddress(DESTINATARIO); // Add a recipient

                $trackDir = geraPDF($key);
                // Content
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = 'Encomenda: ' . $key . ' - Status: ' . $status;
                $mail->Body = $corpoDoEmail;
                $mail->AddAttachment($trackDir);
                //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                $mail->send();
                $result .= "E-mail da entrega $key foi enviado com sucesso! <br>";
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
            unlink($trackDir);
        }
    }
    $_SESSION['emailStatus'] =  $result;
    header('Location: ' . RAIZ . 'index.php');
}
