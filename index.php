<?php
require_once("conexion/conexion.php");
$db = new Database();
$conectar = $db->conectar();

require 'vendor/autoload.php';
use Picqer\Barcode\BarcodeGeneratorPNG;

$asigna = [];

$usua = $conectar->prepare("SELECT * FROM personas");
$usua->execute();
$asigna = $usua->fetchAll(PDO::FETCH_ASSOC);

if ((isset($_POST["registro"])) && ($_POST["registro"] == "formu")) {
    $nombre = $_POST['nombre'];
    $cedula = $_POST['cedula'];
    $email = $_POST['email'];
    $codigo_barras = uniqid() . rand(1000, 9999);

    $generator = new BarcodeGeneratorPNG();

    $codigo_barras_imagen = $generator->getBarcode($codigo_barras, $generator::TYPE_CODE_128);

    file_put_contents(__DIR__ . '/images/' . $codigo_barras . '.png', $codigo_barras_imagen);

    $insertsql = $conectar->prepare("INSERT INTO personas(ced,nombre,cod_bar,email) VALUES (?,?,?,?)");
    $insertsql->execute([$cedula, $nombre, $codigo_barras, $email]);

    $usua->execute();
    $asigna = $usua->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form {
            border: 2px solid black;
            border-radius: 5px;
            padding: 20px;
        }
        h2{
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <h2 class="mb-4">Registrar Personas</h2>
        <form class="form" method="POST" enctype="multipart/form-data">
            <div class="mb-2">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-2">
                <label for="cedula" class="form-label">Cédula</label>
                <input type="text" class="form-control" id="cedula" name="cedula">
            </div>
            <div class="mb-2">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email">
            </div>
            <input type="hidden" name="registro" value="formu">
            <br/>
            <button type="submit" class="btn btn-primary">Registrar</button>
        </form>
    </div>
        <br/>
    <div class="container mt-3">
        <table class="table table-striped table-bordered table-hover">
            <thead class="thead-dark">
                <tr style="text-transform: uppercase;">
                    <th>Nombre</th>
                    <th>Cedula</th>
                    <th>Codigo de barras</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($asigna as $persona) { ?>
                    <tr>
                        <td>
                            <?= $persona["nombre"] ?>
                        </td>
                        <td>
                            <?= $persona["ced"] ?>
                        </td>
                        <td>
                            <img src="images/<?= $persona["cod_bar"] ?>.png" style="max-width: 400px;">
                        </td>
                        <td>
                            <?= $persona["email"] ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>

</html>