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
		if(empty($_POST['proveedor']) ||empty($_POST['producto']) ||empty($_POST['categoria']))
		{
		$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
		}else{

			$codproducto = $_POST['id'];
			$proveedor    = $_POST['proveedor'];
			$categoria    = $_POST['categoria'];
			$producto = $_POST['producto'];
			$usuario_id  = $_SESSION['idUser'];

			//valida si ya existe el mismo nombre del producto			
			$query = mysqli_query($conection,"SELECT * FROM producto 
			                               WHERE descripcion ='$producto' ");
			$result = mysqli_fetch_array($query);
			
			if($result > 0){
				$alert='<p class="msg_error">El nombre asigando ya existe en el sistema.</p>';
			}
			else{  
				$query_update = mysqli_query($conection,"UPDATE producto
				                    SET descripcion= '$producto',
				                        proveedor=$proveedor,
										categoria=$categoria,
										usuario_id=$usuario_id
										WHERE codproducto = $codproducto
					                   ");
					if($query_update){
					$alert='<p class="msg_save">Producto actulizado correctamente.</p>';
				}else{
					$alert='<p class="msg_error">Error al actualizar el producto.</p>';
				}
			}
			}
		}
//validar producto
if(empty($_REQUEST['id'])){
header("location: listaproductos.php");
}else
{
	$id_producto = $_REQUEST['id'];

	if(!is_numeric($id_producto)){
		header("location: listaproductos.php");
	}
	$query_producto = mysqli_query($conection,"SELECT p.codproducto,p.descripcion,pr.codproveedor,pr.proveedor,ca.cod_categoria,ca.nombre,p.precio,p.existencia,p.date_add 
	                                            FROM producto p
	                                            INNER JOIN proveedor pr ON pr.codproveedor=p.proveedor
	                                            INNER JOIN categoria ca ON ca.cod_categoria = p.categoria 
	                                            WHERE p.codproducto = $id_producto AND p.estatus = 1");
	$result_producto = mysqli_num_rows($query_producto);

	if($result_producto > 0){
		$data_producto = mysqli_fetch_assoc($query_producto);
		//print_r($data_producto);
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
	<title>Registro Productos</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">

		<div class="form_register">

	<h1>Editar Productos</h1>
			<hr>
			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

			<form action="" method="post" enctype="multipart/form-data">
			<input type="hidden" name="id" value="<?php echo $data_producto['codproducto']?>">
				<label for="proveedor">Proveedor</label>
				
				<?php
				$query_proveedor = mysqli_query($conection,"SELECT codproveedor,proveedor FROM proveedor WHERE 
				estatus = 1 ORDER BY proveedor ASC");
                $result_proveedor = mysqli_num_rows($query_proveedor);
			
				 ?>
				 <select name="proveedor" id="proveedor" class="notItemOne">
				 <option value="<?php  echo $data_producto['codproveedor']?>" selected><?php echo $data_producto['proveedor']?></option>
				 <?php 
				 if($result_proveedor > 0){
					 while($proveedor = mysqli_fetch_array($query_proveedor)){

				 ?>
				 	<option value="<?php echo $proveedor['codproveedor']; ?>" ><?php echo $proveedor['proveedor']; ?></option>
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
				 <select name="categoria" id="categoria" class= "notitemOne">
				 <option value="<?php  echo $data_producto['cod_categoria']?>" selected><?php echo $data_producto['nombre']?></option>
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
				<input type="text" name="producto" id="producto" placeholder="Nombre del Producto" value="<?php echo $data_producto['descripcion']?>">	
				<label for="precio">Existencia en uds.</label>
				<input type="number" name="cantidad"id="cantidad" placeholder="cantidad de Producto" value="<?php echo $data_producto['existencia']?>" disabled>		
				<label for="precio">Precio C$</label>
				<input type="number" name="precio"id="precio" placeholder="Precio C$ del Producto" value="<?php echo $data_producto['precio']?>" disabled>

				<button type="submit" value="Guardar Producto" class="btn_save">Actulizar Datos de Producto</button>

			</form>
		</div>
	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>