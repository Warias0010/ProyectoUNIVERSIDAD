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
            
                <div class="card-acerca" class="textright">
                    <h4>Nombre usuario:</h4>
                    <h2><?php echo $_SESSION['nombre'];?></h2>
                    <h4>Usuario</h4>
                    <h2><?php echo $_SESSION['user'];?></h2>
                </div>
            </div>
        </section>
	<?php include "includes/footer.php"; ?>
</body>
</html>