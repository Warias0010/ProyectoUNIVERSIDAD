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

			$cod_categoria = $_POST['id'];
			$nombre  = $_POST['nombre'];
			
					$sql_update = mysqli_query($conection,"UPDATE categoria
															SET nombre = '$nombre'
															WHERE cod_categoria= $cod_categoria ");
		

				if($sql_update){
					$alert='<p class="msg_save">Categoria actualizado correctamente.</p>';
                   
				}else{
					$alert='<p class="msg_error">Error al actualizar la categoría.</p>';
				

			}


		}

	}

	//Mostrar Datos
	if(empty($_REQUEST['id']))
	{
		header('Location: categoria.php');
		mysqli_close($conection);
	}
	$cod_categoria = $_REQUEST['id'];

	$sql= mysqli_query($conection,"SELECT *
									FROM categoria
									WHERE cod_categoria= $cod_categoria");
	mysqli_close($conection);
	$result_sql = mysqli_num_rows($sql);

	if($result_sql == 0){
		header('Location: categoria.php');
	}else{
	
		while ($data = mysqli_fetch_array($sql)) {
			# code...
			$cod_categoria  = $data['cod_categoria'];
			$nombre  = $data['nombre'];
		}
	}

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Actualizar Dato Categoría</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		
		<div class="form_register">
			<h1>Actualizar Datos Categoría</h1>
			<hr>
			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

			<form action="" method="post">
				<input type="Hidden" name="id"value = "<?php echo $cod_categoria ?>" >
				<label for="categoria">Nombre Categoría</label>
				<input type="text" name="nombre" id="nombre" placeholder="Nombre categoría" value = "<?php echo $nombre ?>" >

				<input type="submit" value="Actualizar datos" class="btn_save">
                <a class="link_delete" href="categoria.php">Verificar Cambios</a>
			</form>

		</div>


	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>