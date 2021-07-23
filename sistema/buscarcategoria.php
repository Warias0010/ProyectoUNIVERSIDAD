<?php 
	session_start();
	if($_SESSION['rol'] != 1 and $_SESSION['rol'] != 2)
	{
		header("location: ./");
	}
	include "../conexion.php";	

 ?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Lista Categoría</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<?php 

			$busqueda = strtolower($_REQUEST['busqueda']);
			if(empty($busqueda))
			{
				header("location: lista_proveedor.php");
				mysqli_close($conection);
			}


		 ?>
		
		<h1>Resultado de categoría</h1>
		<a href="categoria.php" class="btn_new">Ir a Categoría</a>
		
		<form action="buscarcategoria.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Buscar" value="<?php echo $busqueda; ?>">
			<input type="submit" value="Buscar" class="btn_search">
			<a href="categoria.php" class="link_delete">Eliminar búsqueda</a>
		</form>

		<table>
			<tr>
				<th>Código Categoria</th>
				<th>Nombre</th>
				<th>Contacto</th>
				<th>Acciones</th>
			</tr>
		<?php 
			//Paginador
		
			$sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM categoria 
																WHERE ( cod_categoria LIKE '%$busqueda%' OR 
																		nombre LIKE '%$busqueda%' 
																		 ) 
																");

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

			$query = mysqli_query($conection,"SELECT * FROM categoria 
										WHERE 
										( cod_categoria LIKE '%$busqueda%' OR 
											nombre LIKE '%$busqueda%'  
											) 
										 ORDER BY cod_categoria ASC LIMIT $desde,$por_pagina 
				");
			mysqli_close($conection);
			$result = mysqli_num_rows($query);
			if($result > 0){

				while ($data = mysqli_fetch_array($query)) {
					
			?>
				<tr>
					<td><?php echo $data["cod_categoria"]; ?></td>
					<td><?php echo $data["nombre"]; ?></td>
					<td>
						<a class="link_edit" href="editar_categoria.php?id=<?php echo $data["cod_categoria"]; ?>">Editar</a>
							|
						<a class="link_delete" href="eliminar_confirmar_categoria.php?id=<?php echo $data["cod_categoria"]; ?>">Eliminar</a>
					
						
					</td>
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