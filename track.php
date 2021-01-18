<style>
    th,
    td {
        border-bottom: 1px solid #ddd;
    }



    th {
        background-color: gray;
        color: white;
    }
</style>
<?php
require_once "config.php";

if (isset($_GET['objeto'])) {
    $obj = $_GET["objeto"];
    $res = file_get_contents(APISERVICE . $obj);
    $decode = json_decode($res, TRUE);

?>
    <table cellpadding="0" cellspacing="0" width="500">
        <?php
        foreach ($decode as $key => $value) {
            switch ($value[0]['action']) {
                case "Objeto entregue ao destinatário":
                    $status = "Entregue";
                    $statusBar = ' <td style="background-color:green; width: 100%;height: 10px;padding: 5px; ;"></td>';
                    break;
                case "Objeto aguardando retirada no endereço indicado":
                    $status =  "Erro";
                    $statusBar = ' <td style="background-color:red;width: 75%;height: 10px;padding: 5px; ;"></td>';
                    break;
                case "Carteiro não atendido - Entrega não realizada":
                    $status =  "Erro";
                    $statusBar = ' <td style="background-color:red;width: 75%;height: 10px;padding: 5px; ;"></td>';
                    break;
                case "Objeto saiu para entrega ao destinatário":
                    $status =  "Encaminhado";
                    $statusBar = ' <td style="background-color:blue;width: 75%;height: 10px;padding: 5px; ;"></td>';
                    break;
                case "Objeto em trânsito - por favor aguarde":
                    $status =  "Encaminhado";
                    $statusBar = ' <td style="background-color:yellow;width: 50%;height: 10px;padding: 5px; ;"></td>';
                    break;
                case "Objeto postado após o horário limite da unidade":
                    $status =  "Postado";
                    $statusBar = ' <td style="background-color:orangered;width: 25%;height: 10px;padding: 5px; ;"></td>';
                    break;
            } ?>
            <thead class="gray">
                <tr>
                    <td colspan="2" style="padding: 20px 0 20px 0;color: #153643;font-weight: bold; font-family: Arial, sans-serif; border:none; font-size: 24px;">
                        <?php echo $status; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 2px 0 12px 0;color: light-gray ; font-weight: bold; font-family: Arial, sans-serif;border:none; font-size: 12px;">
                        <?php
                        echo $value[0]['action']; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 2px 0 12px 0;color: light-gray ; font-family: Arial, sans-serif;border:none; font-size: 12px;">
                        Você pode acompanhar o envio com o código de rastreamento
                        <a href="<?php echo RAIZ ?>index.php?objeto=<?php echo $key; ?>"> <?php echo $key; ?></a>

                    </td>
                </tr>
            </thead>
            <thead class=" gray">
                <tr>
                    <th scope="col"><?php echo $key; ?></th>
                    <th scope="col"><?php echo $status; ?></th>
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
                ?>
                <tr>
                    <td><strong><?php echo $status; ?></strong> </td>
                    <?php echo $statusBar; ?>

                </tr>
            </tbody>
            <tfoot>
                <?php if ($status != "Entregue") {
                ?>
                    <tr>
                        <td colspan="2" style="padding: 20px 0 12px 0;color: gray ; font-family: Arial, sans-serif;border:none; font-size: 12px;">
                            Falta pouco!
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="2" style="padding: 25px 0 5px 0;color: gray ; font-weight: bold; font-family: Arial, sans-serif;border:none; font-size: 18px;">
                        Dados do envio
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 2px 0 12px 0;color: gray ; font-weight: bold; font-family: Arial, sans-serif;border:none; font-size: 15px;">
                        Germano Raimar Silva <br>
                        Telefone: (38) 99918-8899 <br>
                        Rua Uruguaiana, 78, Centro - Curvelo/MG <br>
                        Email: german.o@outlook.com.br / comitereg2@gmail.com <br>
                    </td>
                </tr>

            </tfoot>
    <?php
        }
    }
    ?>
    </table>