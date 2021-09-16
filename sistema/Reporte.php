<?php 
 
	session_start();
	include "../conexion.php";	

 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Lista de factura</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<br>
        <br>
		<h1>Reportes de Ventas</h1>
		<h1></h1>
		<br>
        <div >
        <h5>Buscar por fecha</h5>
        <form action="reporteporfecha.php" method="get" class="form_search_date" target="_blank">
        <label >De: </label>
        <input type="date" name="fecha_de" id="fecha_de" required>
        <label >A</label>
        <input type="date" name="fecha_a" id="fecha_a" required>
        <button  type="submit" class="btn_view" target="_blank">Generar Reporte por Fecha</button>
        </form>
        <form >
			<a href="reportegeneral.php" class="btn_view " target="_blank">Generar Reporte General </a>
            <a href="reportefacanulada.php" class="btn_view " target="_blank">Generar Reporte Facturas Anuladas </a>
            <a href="reportefacturaactiva.php" class="btn_view " target="_blank">Generar Reporte Facturas Activas </a>
        </form>
        <br>
        </div>
	


	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>