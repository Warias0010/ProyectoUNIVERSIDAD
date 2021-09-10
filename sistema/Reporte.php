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
        <form action="buscarreporte.php" method="get" class="form_search_date">
        <label >De: </label>
        <input type="date" name="fecha_de" id="fecha_de" required>
        <label >A</label>
        <input type="date" name="fecha_a" id="fecha_a" required>
        <button type="submit" class="btn_view">Bucar</button>
        </form>
        <form >
        <button href="reportegeneral.php" class="btn_view ">Generar reporte General</button>
        <button type="submit" class="btn_view">Imprimir reporte de facturas anuladas</button>
        </form>
        <br>
        </div>
		<table>
			<tr>
				<th>No.Factura</th>
				<th>Pago</th>
				<th>Fecha / Hora</th>
				<th>Cliente</th>
				<th>Vendedor</th>
				<th>Estado</th>
				<th class="textright">Total Factura</th>
			</tr>
		<?php 
			//Paginador
			$sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM factura WHERE estatus != 10 ");
			$result_register = mysqli_fetch_array($sql_registe);
			$total_registro = $result_register['total_registro'];

			$por_pagina = 8;

			if(empty($_GET['pagina']))
			{
				$pagina = 1;
			}else{
				$pagina = $_GET['pagina'];
			}

			$desde = ($pagina-1) * $por_pagina;
			$total_paginas = ceil($total_registro / $por_pagina);

			$query = mysqli_query($conection,"SELECT f.nofactura,f.fecha,f.totalfactura,f.codcliente,f.estatus,
                                             u.nombre as vendedor,
                                            cl.nombre as cliente,
											p.nombre as pago
                                            FROM factura f
											INNER JOIN tipopago p
                                            ON f.metodopago= p.Cod_Tipo_pago
                                            INNER JOIN usuario u 
                                            ON f.usuario = u.idusuario
                                            INNER JOIN cliente cl
                                            ON f.codcliente= cl.idcliente
                                            WHERE f.estatus !=10 
                                            ORDER BY f.fecha DESC LIMIT $desde,$por_pagina");
			mysqli_close($conection);

			$result = mysqli_num_rows($query);
			
		 ?>


		</table> 
        
		<div class="paginador">
			<ul>
			<?php 
				if($pagina != 1)
				{
			 ?>
				<li><a href="?pagina=<?php echo 1; ?>">|<</a></li>
				<li><a href="?pagina=<?php echo $pagina-1; ?>"><<</a></li>
			<?php 
				}
				for ($i=1; $i <= $total_paginas; $i++) { 
					# code...
					if($i == $pagina)
					{
						echo '<li class="pageSelected">'.$i.'</li>';
					}else{
						echo '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';
					}
				}

				if($pagina != $total_paginas)
				{
			 ?>
				<li><a href="?pagina=<?php echo $pagina + 1; ?>">>></a></li>
				<li><a href="?pagina=<?php echo $total_paginas; ?> ">>|</a></li>
			<?php } ?>
			</ul>
		</div>


	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>