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
		if(empty($_POST['cedula']) ||empty($_POST['proveedor']) ||empty($_POST['contacto']) || empty($_POST['telefono']) || empty($_POST['direccion']))
		{
		$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
		}else{
			$cedula    = $_POST['cedula'];
			$proveedor    = $_POST['proveedor'];
			$contacto = $_POST['contacto'];
			$telefono  = $_POST['telefono'];
			$direccion   = $_POST['direccion'];
			$usuario_id  = $_SESSION['idUser'];


				$query_insert = mysqli_query($conection,"INSERT INTO proveedor(cedula,proveedor,contacto,telefono,direccion,usuario_id)
					VALUES('$cedula','$proveedor','$contacto','$telefono','$direccion','$usuario_id')");
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
	<title>Registro Proveedor</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		
		<div class="form_register">
			<h1>Registro de proveedor</h1>
			<hr>
			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

			<form action="" method="post">
			<label for="proveedor" style="background-color:#2a479e; font-size: 11pt;
    background: #333;
    padding: 10px;
    color: #FFF;
    letter-spacing: 1px;
    border: 0;
    cursor: pointer;
    margin: 7px auto;">RUC o Cedúla</label>
				<input type="text" name="cedula" id="cedula" placeholder="Ingresar datos solicitado">

				<label for="proveedor">Proveedor</label>
				<input type="text" name="proveedor" id="proveedor" placeholder="Nombre del proveedor">

				<label for="contacto">Contacto</label>
				<input type="text" name="contacto" id="contacto" placeholder="Nombre del contacto">
			
				<label for="telefono">Teléfono</label>
				<input type="tel" name="telefono" id="telefono" pattern="[0-9]{8}" placeholder="Teléfono">
				<label for="direccion">Dirección</label>
				<input type="text" name="direccion" id="direccion" placeholder="Dirección ">
				
				<input type="submit" value="Guardar proveedor" class="btn_save">

			</form>


		</div>


	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>