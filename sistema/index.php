<?php 
session_start();
include "../conexion.php";
$usuarios = mysqli_query($conection, "SELECT * FROM usuario");
$totalU= mysqli_num_rows($usuarios);
$clientes = mysqli_query($conection, "SELECT * FROM cliente");
$totalC = mysqli_num_rows($clientes);
$productos = mysqli_query($conection, "SELECT * FROM producto");
$totalP = mysqli_num_rows($productos);
$ventas = mysqli_query($conection, "SELECT * FROM factura");
$totalV = mysqli_num_rows($ventas);
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
    <link rel="stylesheet" href="../css/style.css">
	<title>Sisteme de Facturación e Inventario</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
    <section id="Acerca" class="acerca-de">
            <div class="contenedor-acerca-de">
            <h3>Información General</h3>
                <div class="card-acerca">
                    <i class="fab fa-forumbee"></i>
                    <h4>Usuarios Activos</h4>
                </div>
                <div class="card-acerca">
                    <i class="fas fa-wave-square"></i>
                    <h4>Total Facturas</h4>
                </div>
                <div class="card-acerca">
                    <i class="fas fa-low-vision"></i>
                    <h4>Ultimas entradas</h4>
                </div>
            </div>
        </section>
	<?php include "includes/footer.php"; ?>
</body>
</html>