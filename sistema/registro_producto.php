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
		if(empty($_POST['proveedor']) ||empty($_POST['producto']) ||empty($_POST['precio']) ||($_POST['precio'])<= 0 || empty($_POST['cantidad'])|| ($_POST['cantidad'])<= 0)
		{
		$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
		}else{

			$proveedor    = $_POST['proveedor'];
			$categoria    = $_POST['categoria'];
			$producto = $_POST['producto'];
			$precio  = $_POST['precio'];
			$cantidad   = $_POST['cantidad'];
			$usuario_id  = $_SESSION['idUser'];

			//valida si ya existe el producto
			$query = mysqli_query($conection,"SELECT * FROM producto WHERE descripcion = '$producto' ");
			$result = mysqli_fetch_array($query);

			if($result > 0){
				$alert='<span><p class="msg_error"> El producto ya existe en el sistema.</p></span><br>';
			}else{

				$query_insert = mysqli_query($conection,"INSERT INTO producto(proveedor,categoria,descripcion,precio,existencia,usuario_id)
					VALUES('$proveedor','$categoria','$producto','$precio','$cantidad','$usuario_id')");
					if($query_insert){
					$alert='<p class="msg_save">Producto guardado correctamente.</p>';
				}else{
					$alert='<p class="msg_error">Error al guardar el proveedor.</p>';
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
				<?php
				$query_proveedor = mysqli_query($conection,"SELECT codproveedor,proveedor FROM proveedor WHERE 
				estatus = 1 ORDER BY proveedor ASC");
                $result_proveedor = mysqli_num_rows($query_proveedor);
			
				 ?>
				 <select name="proveedor" id="proveedor">
				 <?php 
				 if($result_proveedor > 0){
					 while($proveedor = mysqli_fetch_array($query_proveedor)){

				 ?>
				 	<option value="<?php echo $proveedor['codproveedor']; ?>"><?php echo $proveedor['proveedor']; ?></option>
				 <?php 
				  }	 
				}
				 ?>
				
				</select>

				<label for="categoria">Categoria</label>
				<?php
				$query_categoria = mysqli_query($conection, "SELECT cod_categoria,nombre FROM categoria");
                $result_categoria = mysqli_num_rows($query_categoria);
				mysqli_close($conection);
				 ?>
				 <select name="categoria" id="categoria">
				 <?php 
				 if($result_categoria > 0){
					 while($categoria = mysqli_fetch_array($query_categoria)){

				 ?>
				 	<option value="<?php echo $categoria['cod_categoria']; ?>"><?php echo $categoria['nombre']; ?></option>
				 <?php 
				  }	 
				}
				 ?>
				</select>			
				<label for="producto">Producto</label>
				<input type="text" name="producto" id="producto" placeholder="Nombre del Producto">			
				<label for="precio">Precio C$</label>
				<input type="number" name="precio"id="precio" placeholder="Precio C$ del Producto">
				<label for="cantidad">Cantidad </label>
				<input type="number" name="cantidad" id="cantidad" placeholder="Cantidad del Producto">

				<button type="submit" value="Guardar Producto" class="btn_save">Guardar Producto</button>

			</form>
		</div>
	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>