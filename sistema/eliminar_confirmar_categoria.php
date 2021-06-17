<?php 
	session_start();
	if($_SESSION['rol'] != 1 and $_SESSION['rol'] !=2)
	{
		header("location: ./");
	}
	include "../conexion.php";

	if(!empty($_POST))
	{
		if(empty($_POST['cod_categoria']))
		{
		header("location: categoria.php");	
		mysqli_close($conection);
		}
		$cod_categoria= $_POST['cod_categoria'];

		$query_delete = mysqli_query($conection,"DELETE FROM categoria WHERE cod_categoria =$cod_categoria ");
		mysqli_close($conection);
		if($query_delete){
			header("location: categoria.php");
		}else{
			echo "Error al eliminar";
			$alert='<p class="msg_error">La categoria ya existe en el sistema.</p>';
            
		}

	}




	if(empty($_REQUEST['id']) )
	{
		header("location: categoria.php");
		mysqli_close($conection);
	}else{

		$cod_categoria = $_REQUEST['id'];

		$query = mysqli_query($conection,"SELECT *
												FROM categoria
											    WHERE cod_categoria = $cod_categoria ");
		
		mysqli_close($conection);
		$result = mysqli_num_rows($query);
       
		if($result > 0){
			while ($data = mysqli_fetch_array($query)) {
				# code...
				$cod_categoria = $data['cod_categoria'];
				$nombre = $data['nombre'];
			
			}
		}else{
			header("location: categoria.php");
		}


	}


 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Eliminar Categoria</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<div class="data_delete">
			<h2>¿Está seguro de eliminar el siguiente registro?</h2>
			<p>Cod. Categoría: <span><?php echo $cod_categoria; ?></span></p>
			<p>Nombre: <span><?php echo $nombre; ?></span></p>
			
			<form method="post" action="">
				<input type="hidden" name="cod_categoria" value="<?php echo $cod_categoria; ?>">
				<a href="categoria.php" class="btn_cancel">Cancelar</a>
				<input type="submit" value="Eliminar" class="btn_ok">
			</form>
		</div>


	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>