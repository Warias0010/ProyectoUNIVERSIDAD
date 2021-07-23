<?php 
	session_start();
	if($_SESSION['rol'] != 1 and $_SESSION['rol'] !=2)
	{
		header("location: ./");
	}
	include "../conexion.php";

	if(!empty($_POST))
	{
		if(empty($_POST['codproducto']))
		{
		header("location: listaproductos.php");
		mysqli_close($conection);	
		}
		
		$codproducto = $_POST['codproducto'];

		//$query_delete = mysqli_query($conection,"DELETE FROM usuario WHERE idusuario =$idusuario ");
		$query_delete = mysqli_query($conection,"UPDATE producto SET estatus = 0 WHERE codproducto = $codproducto ");
		mysqli_close($conection);
		if($query_delete){
			header("location: listaproductos.php");
		}else{
			echo "Error al eliminar el producto ";
		}

	}




	if(empty($_REQUEST['id']) )
	{
		header("location: listaproducto.php");
		mysqli_close($conection);
	}else{

		$codproducto = $_REQUEST['id'];

		$query = mysqli_query($conection,"SELECT *
												FROM producto
											    WHERE codproducto = $codproducto ");
		
		mysqli_close($conection);
		$result = mysqli_num_rows($query);

		if($result > 0){
			while ($data = mysqli_fetch_array($query)) {
				# code...
				$codproducto = $data['codproducto'];
				$descripcion = $data['descripcion'];
			
			}
		}else{
			header("location: listaproductos.php");
		}


	}


 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Eliminar Producto</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<div class="data_delete">
			<h2>¿Estás seguro de eliminar el siguiente producto?</h2>
			<p>Codigo del producto: <span><?php echo $codproducto; ?></span></p>
			<p>Nombre del producto: <span><?php echo $descripcion; ?></span></p>
			
			<form method="post" action="">
				<input type="hidden" name="codproducto" value="<?php echo $codproducto; ?>">
				<a href="listaproductos.php" class="btn_cancel">Cancelar</a>
				<input type="submit" value="Eliminar" class="btn_ok">
			</form>
		</div>


	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>