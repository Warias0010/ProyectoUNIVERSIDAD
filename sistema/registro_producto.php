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
		if(empty($_POST['proveedor']) ||empty($_POST['contacto']) || empty($_POST['telefono']) || empty($_POST['direccion']))
		{
		$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
		}else{

			$proveedor    = $_POST['proveedor'];
			$contacto = $_POST['contacto'];
			$telefono  = $_POST['telefono'];
			$direccion   = $_POST['direccion'];
			$usuario_id  = $_SESSION['idUser'];


				$query_insert = mysqli_query($conection,"INSERT INTO proveedor(proveedor,contacto,telefono,direccion,usuario_id)
					VALUES('$proveedor','$contacto','$telefono','$direccion','$usuario_id')");
					if($query_insert){
					$alert='<p class="msg_save">proveedor guardado correctamente.</p>';
				}else{
					$alert='<p class="msg_error">Error al guardar el proveedor.</p>';
				}
			
			
			}

			mysqli_close($conection);
			
		}



 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Registro Productos</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">








		
		<div class="form_register">
		



	<h1>Registro de Productos</h1>
			<hr>
			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

			<form action="" method="post" enctype="multipart/form-data">
				<label for="proveedor">Proveedor</label>
				<select name="proveedor" id="proveedor">
					<option value="1">Sin Porveedor</option>
				</select>
			
				<label for="producto">Producto</label>
				<input type="text" name="producto" id="producto" placeholder="Nombre completo del Producto">
			
				<label for="precio">Precio</label>
				<input type="number" name="precio"id="precio" placeholder="Precio del Producto">
				<label for="cantidad">Cantidad</label>
				<input type="number" name="cantidad" id="cantidad" placeholder="Cantidad del Producto">				
				<input type="submit" value="Guardar Producto" class="btn_save">

			</form>


		</div>


	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>