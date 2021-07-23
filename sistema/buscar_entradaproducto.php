<?php 
	session_start();
	if($_SESSION['rol'] != 1)
	{
		header("location: ./");
	}

	include "../conexion.php";	

 ?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de productos en inventario</title>
</head>
<body>
<?php include "includes/header.php"; ?>
<section id="container">
      <?php 

			$busqueda = strtolower($_REQUEST['busqueda']);
			if(empty($busqueda))
			{
				header("location: Listarentradas.php");
				mysqli_close($conection);
			}


	  ?>

        <h1 class="un">Últimas Entradas a Inventario</h1>
		<a href="listaproductos.php" class="btn_new">Agregar Nueva Entrada</a>

		<form action="buscar_entrada_inventario.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Buscar" value="<?php echo $busqueda; ?>">
			<input type="submit" value="Buscar" class="btn_search">
			<a href="Listarentradas.php" class="link_delete">Eliminar búsqueda</a>
		</form>

		<table>
			<tr>
			    <th>Cod_Entrada</th>
				<th>Producto</th>
				<th>Fecha</th>
				<th>Cantidad</th>
				<th>Precio</th>
				<th>Ingreso Producto</th>
			</tr>
		<?php 
			//Paginador

			$sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM entradas ");

			$result_register = mysqli_fetch_array($sql_registe);
			$total_registro = $result_register['total_registro'];

			$por_pagina = 5;

			if(empty($_GET['pagina']))
			{
				$pagina = 1;
			}else{
				$pagina = $_GET['pagina'];
			}

			$desde = ($pagina-1) * $por_pagina;
			$total_paginas = ceil($total_registro / $por_pagina);

			$query = mysqli_query($conection,"SELECT E.identrada,E.fecha,E.cantidad,E.precio, U.nombre as usuario, P.descripcion as producto
FROM entradas E INNER JOIN usuario U ON E.usuario_id = U.idusuario INNER JOIN producto P ON E.codproducto= P.codproducto  
										WHERE 
										( E.identrada LIKE '%$busqueda%' OR 
										P.descripcion LIKE '%$busqueda%' OR
										E.fecha LIKE '%$busqueda%' OR
										E.cantidad LIKE '%$busqueda%' OR
										E.precio LIKE '%$busqueda%' OR
										U.nombre LIKE '%$busqueda%' ) 
										ORDER BY E.identrada ASC LIMIT $desde,$por_pagina 
				");
			mysqli_close($conection);
			$result = mysqli_num_rows($query);
			if($result > 0){

				while ($data = mysqli_fetch_array($query)){
			?>
				<tr>
					<td><?php echo $data["identrada"]; ?></td>
					<td><?php echo $data["producto"]; ?></td>
					<td><?php echo $data["fecha"]; ?></td>
					<td><?php echo $data["cantidad"]; ?></td>
					<td><?php echo $data["precio"]; ?></td>
					<td><?php echo $data["usuario"]; ?></td>
				</tr>
			
		<?php 
				}
            }
		 ?>


		</table>
<?php 
	
	if($total_registro != 0)
	{
 ?>
		<div class="paginador">
			<ul>
			<?php 
				if($pagina != 1)
				{
			 ?>
				<li><a href="?pagina=<?php echo 1; ?>&busqueda=<?php echo $busqueda; ?>">|<</a></li>
				<li><a href="?pagina=<?php echo $pagina-1; ?>&busqueda=<?php echo $busqueda; ?>"><<</a></li>
			<?php 
				}
				for ($i=1; $i <= $total_paginas; $i++) { 
					# code...
					if($i == $pagina)
					{
						echo '<li class="pageSelected">'.$i.'</li>';
					}else{
						echo '<li><a href="?pagina='.$i.'&busqueda='.$busqueda.'">'.$i.'</a></li>';
					}
				}

				if($pagina != $total_paginas)
				{
			 ?>
				<li><a href="?pagina=<?php echo $pagina + 1; ?>&busqueda=<?php echo $busqueda; ?>">>></a></li>
				<li><a href="?pagina=<?php echo $total_paginas; ?>&busqueda=<?php echo $busqueda; ?> ">>|</a></li>
			<?php } ?>
			</ul>
		</div>
<?php } ?>


</section>

<?php include "includes/footer.php"; ?>

</body>
</html>