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
	<title>Sisteme de Facturacion e Inventario</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<h1>Muebleria Super Gangas Conny</h1>
		<div class="view">
		<div class="view1">Usuarios</div>
        <div class="link"><?php echo $totalU; ?></div>
         </div>
		
	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>