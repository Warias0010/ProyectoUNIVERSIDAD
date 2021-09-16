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
		if(empty($_POST['ruc']) ||empty($_POST['proveedor']) ||empty($_POST['contacto']) || empty($_POST['telefono']) || empty($_POST['direccion']))
		{
			$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
		}else{

			$idproveedor = $_POST['id'];
			$ruc  = $_POST['ruc'];
			$proveedor  = $_POST['proveedor'];
			$contacto = $_POST['contacto'];
			$telefono  = $_POST['telefono'];
			$direccion   = $_POST['direccion'];

			
	
					$sql_update = mysqli_query($conection,"UPDATE proveedor
															SET ruc = '$ruc' ,proveedor = '$proveedor', contacto='$contacto',telefono='$telefono',direccion='$direccion'
															WHERE codproveedor= $idproveedor ");
		

				if($sql_update){
					/*$alert='<p class="msg_save">Proveedor actualizado correctamente.</p>';*/
					$alert='<br>';
					$alert='<a href="lista_proveedor.php" class="link_delete"> Verificar Cambios </a>';
				}else{
					$alert='<p class="msg_error">Error al actualizar el proveedor.</p>';
				

			}


		}

	}

	//Mostrar Datos
	if(empty($_REQUEST['id']))
	{
		header('Location: lista_proveedor.php');
		mysqli_close($conection);
	}
	$idproveedor = $_REQUEST['id'];

	$sql= mysqli_query($conection,"SELECT *
									FROM proveedor
									WHERE codproveedor= $idproveedor and estatus =1");
	mysqli_close($conection);
	$result_sql = mysqli_num_rows($sql);

	if($result_sql == 0){
		header('Location: lista_proveedor.php');
	}else{
	
		while ($data = mysqli_fetch_array($sql)) {
			# code...
			
			$idproveedor  = $data['codproveedor'];
			$ruc  =     $data['ruc'];
			$proveedor  = $data['proveedor'];
			$contacto  = $data['contacto'];
			$telefono  = $data['telefono'];
			$direccion = $data['direccion'];
			
		}
	}

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Actualizar Proveedor</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		
		<div class="form_register">
			<br>
			<h2 style="Color:blue; text-align:center;">Actualizar Datos del Proveedor</h2>
			<hr>
			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

			<form action="" method="post">
				
				<input type="Hidden" name="id"value = "<?php echo $idproveedor ?>" >
				<label for="proveedor" style="background-color:#2a479e; font-size: 11pt;
    background: #333;
    padding: 10px;
    color: #FFF;
    letter-spacing: 1px;
    border: 0;
    cursor: pointer;
    margin: 7px auto;">RUC o Cedúla</label>
				<input type="text" name="ruc" id="ruc" placeholder="" value = "<?php echo $ruc ?>" >

				<label for="proveedor">Nombre del proveedor</label>
				<input type="text" name="proveedor" id="proveedor" placeholder="Nombre del proveedor" value = "<?php echo $proveedor ?>" >

				<label for="contacto">Contacto</label>
				<input type="text" name="contacto" id="contacto" placeholder="Nombre completo de contacto" value = "<?php echo $contacto ?>" >
			
				<label for="telefono">Teléfono</label>
				<input type="number" name="telefono" id="telefono" placeholder="Teléfono" value = "<?php echo $telefono ?>" >
				<label for="direccion">Dirección</label>
				<input type="text" name="direccion" id="direccion" placeholder="Dirección completa" value = "<?php echo $direccion ?>" >
				
				<input type="submit" value="Actualizar proveedor" class="btn_save">
				
			</form>

		</div>


	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>