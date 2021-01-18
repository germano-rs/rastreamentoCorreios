<?php
//Iniciando a sessão:
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
} ?>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="Germano" content="">
    <link rel="icon" href="">

    <title>Tracking - Elastic .</title>

    <!-- Principal CSS do Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <!-- Estilos customizados para esse template -->
    <link href="" rel="stylesheet">
    <style type="text/css">
        .gray {
            background-color: gray;
            color: white;
        }
    </style>
</head>
<?php
require_once "config.php";

function status($msg)
{
    switch ($msg) {
        case "Objeto entregue ao destinatário":
            return "Entregue";
            break;
        case "Objeto aguardando retirada no endereço indicado":
            return "Erro";
            break;
        case "Carteiro não atendido - Entrega não realizada":
            return "Erro";
            break;
        case "Objeto saiu para entrega ao destinatário":
            return "Encaminhado";
            break;
        case "Objeto em trânsito - por favor aguarde":
            return "Encaminhado";
            break;
        case "Objeto postado após o horário limite da unidade":
            return "Postado";
            break;
    }
}

?>

<body class="bg-light">
    <main role="main" class="container mt-4">
        <div class="jumbotron jumbotron-fluid">
            <div class="container">
                <h1 class="display-4">Rastreio de entregas</h1>
                <p class="lead">Consulte a situação de seus objetos nos Correios.<br> Digite quantos códigos preicsar, de 13 dígitos cada, separando-os apenas com ponto e vírgula. <br>Ex: AA123456789BR;AA987654321BR;AA100833276BR.</p>
            </div>
        </div>
        <form class="mb-4" method="get" action="<?php echo RAIZ ?>index.php">
            <div class="form-group">
                <input onkeyup="trimString()" placeholder="Digite aqui o(s) código(s)." class="form-control form-control-sm mb-2" id="trackingCode" name="objeto" type="text">
            </div>
            <button type="submit" id="" class="btn btn-primary">Buscar</button>
        </form>
        <div id="trackingForm">
            <?php
            if (isset($_SESSION['emailStatus'])) {
                echo '<div class="alert alert-info" role="alert">' . $_SESSION['emailStatus'] . '</b></div>';
                unset($_SESSION['emailStatus']);
            }
            if (isset($_GET['objeto'])) {
                $obj = $_GET["objeto"];

                $res = file_get_contents(APISERVICE . $obj);
                $decode = json_decode($res, TRUE);

                if (isset($decode['erro'])) {
                    echo '<div class="alert alert-danger" role="alert">Não encontramos o código <b>' . $decode['obj'] . '</b> buscado</div>';
                    exit;
                }

                if (isset($decode['msg'])) {
                    echo '<div class="alert alert-info" role="alert">' . $decode['msg'] . '</b></div>';
                    exit;
                }

            ?>
                <div class="my-3 p-3 bg-white rounded shadow-sm">
                    <div class="row">
                        <table class="table table-hover">

                            <?php

                            foreach ($decode as $key => $value) { ?>
                                <thead class="gray">
                                    <tr>
                                        <th scope="col"><?php echo $key; ?></th>
                                        <th scope="col"><?php echo status($value[0]['action']); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    foreach ($value as $data) { ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo $data['date'] . "<br>" . $data['hour'] . "<br>" . $data['location'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo "<strong>" . $data['action'] . "</strong><br>" . $data['message'];
                                                ?>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                }  ?>
                        </table>
                    </div>
                    <form class="mb-4" method="post" action="<?php echo RAIZ ?>send.php">
                        <input type="hidden" name="objeto" value="<?php echo $obj ?>">
                        <button type="submit" id="" class="btn btn-primary">Enviar Email</button>
                    </form>
                <?php }
                ?>

                </div>

        </div>
    </main>
    <!-- Principal JavaScript do Bootstrap
    ================================================== -->
    <!-- Foi colocado no final para a página carregar mais rápido -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

</body>

<script>
    function trimString() {
        var x = document.getElementById("trackingCode");
        x.value = x.value.trim()
    }
</script>

</html>