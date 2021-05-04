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
		if(empty($_POST['descripcion']) ||empty($_POST['proveedor']) || empty($_POST['precio']) || empty($_POST['existencia'])|| empty($_POST['estatus']))
		{
			$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
		}else{

			$codproducto = $_POST['codproducto'];
			$descripcion  = $_POST['descripcion'];
			$proveedor = $_POST['proveedor'];
			$precio  = $_POST['precio'];
			$existencia   = $_POST['existencia'];
			$estatus   = $_POST['estatus'];

			
	
					$sql_update = mysqli_query($conection,"UPDATE producto
															SET descripcion = '$descripcion', proveedor='$proveedor',precio='$precio',existencia='$existencia', estatus='$estatus'
															WHERE codproducto= $codproducto ");
		

				if($sql_update){
					$alert='<p class="msg_save">proveedor actualizado correctamente.</p>';
				}else{
					$alert='<p class="msg_error">Error al actualizar el proveedor.</p>';
				

			}


		}

	}

	//Mostrar Datos
	if(empty($_REQUEST['id']))
	{
		header('Location: listaprodutos.php');
		mysqli_close($conection);
	}
	$codproducto = $_REQUEST['id'];

	$sql= mysqli_query($conection,"SELECT *
									FROM producto
									WHERE codproducto= $codproducto and estatus =1");
	mysqli_close($conection);
	$result_sql = mysqli_num_rows($sql);

	if($result_sql == 0){
		header('Location:listaprodutos.php');
	}else{
	
		while ($data = mysqli_fetch_array($sql)) {
			# code...
			$codproducto  = $data['codproducto'];
			$descripcion  = $data['proveedor'];
			$proveedor  = $data['proveedor'];
			$precio  = $data['precio'];
			$existencia = $data['exitencia'];
			$estatus = $data['estatus'];

		}
	}

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Actualizar Producto </title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		
		<div class="form_register">
			<h1>Actualizar Producto <?php echo $descripcion ?> </h1>
			<hr>
			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

			<form action="" method="post">
				<input type="Hidden" name="id"value = "<?php echo $codproducto ?>" >
				<label for="proveedor">Nombre Producto</label>
				<input type="text" name="descripcion" id="descripcion" placeholder="Descripcion del proveedor" value = "<?php echo $descripcion ?>" >

				<label for="contacto">Contacto</label>
				<input type="text" name="contacto" id="contacto" placeholder="Nombre completo de contacto" value = "<?php echo $contacto ?>" >
			
				<label for="telefono">Telefono</label>
				<input type="number" name="telefono" id="telefono" placeholder="Telefono" value = "<?php echo $telefono ?>" >
				<label for="direccion">Direccion</label>
				<input type="text" name="direccion" id="direccion" placeholder="direccion completa" value = "<?php echo $direccion ?>" >
				
				<input type="submit" value="Actualizar proveedor" class="btn_save">

			</form>

		</div>


	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>