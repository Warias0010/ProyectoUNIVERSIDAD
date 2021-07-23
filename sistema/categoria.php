<?php 
	session_start();
		if($_SESSION['rol'] != 1 and $_SESSION['rol'] != 2)
	{
		header("location: ./");
	}
	

	include "../conexion.php";

	if(!empty($_POST))
	{
		$alert='';
		if(empty($_POST['nombre']))
		{
			$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
		}else{

			$nombre = $_POST['nombre'];
			
			$query = mysqli_query($conection,"SELECT * FROM categoria WHERE nombre = '$nombre' ");
			$result = mysqli_fetch_array($query);

			if($result > 0){
				$alert='<p class="msg_error">La categoria ya existe en el sistema.</p>';
			}else{

				$query_insert = mysqli_query($conection,"INSERT INTO categoria(nombre)
																	VALUES('$nombre')");
				if($query_insert){
					$alert='<p class="msg_save">Categoría creado correctamente.</p>';
				}else{
					$alert='<p class="msg_error">Error al crear la categoría.</p>';
				}

			}


		}

	}



 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Registro Categorías</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<div class="form_register">
			<h1>Registro de Categorías</h1>
			<hr>
			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

			<form action="" method="post">
				<label for="categoria">Categoría</label>
				<input type="text" name="nombre" id="nombre" placeholder="Nombre Categoria">
				
				<input type="submit" value="Guardar Categoria" class="btn_save">

			</form>


		</div>
		<div>
		<form action="buscarcategoria.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Buscar Categoría">
			<input type="submit" value="Buscar" class="btn_search">
		</form>
		</div>
		<br>
        <h1>Lista de Categorías</h1>
		<table>
			<tr>
				<th>Código Categoría</th>
				<th>Nombre</th>
				<th>Acciones</th>
			</tr>
		<?php 
			//Paginador
			$sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM categoria");
			$result_register = mysqli_fetch_array($sql_registe);
			$total_registro = $result_register['total_registro'];

			$por_pagina = 10;

			if(empty($_GET['pagina']))
			{
				$pagina = 1;
			}else{
				$pagina = $_GET['pagina'];
			}

			$desde = ($pagina-1) * $por_pagina;
			$total_paginas = ceil($total_registro / $por_pagina);

			$query = mysqli_query($conection,"SELECT * FROM categoria  ORDER BY cod_categoria ASC LIMIT $desde,$por_pagina 
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

		</div>
	</section>
    
	<?php include "includes/footer.php"; ?>
</body>
</html>