<?php 

	if(empty($_SESSION['active']))
	{
		header('location: ../');
	}
 ?>
	<header>
		<div class="header">
			<h1> Facturación e Inventario</h1>
			<div class="optionsBar">
				<p>Nicaragua, <?php echo fechaC(); ?></p>
				<span>|</span>
				<span class="user"><?php echo $_SESSION['nombre'].' -'.$_SESSION['user']; ?></span>
				<img class="photouser" src="img/user.png" alt="Usuario">
				<a href="salir.php"><img class="close" src="img/salir.png" alt="Salir del sistema" title="Salir"></a>
			</div>
		</div>
		<?php include "nav.php"; ?>
	</header>
	<div class="modal">
		<div class="bodymodal">
			<form action="" method="post" name="form_add_product" id="form_add_product" onsubmit="event.preventDefault(); sendDataProduct();">
			<h1><br> Existencia y Precio De :</h1><br>
			<h2 name="nameProducto" class="nameProducto">Sofa</h2><br>
			<input type="number" name="cantidad" id="txtcantidad" placeholder="Cantidad Ingreso" required><br>
			<input type="float" name="precio" id="txtprecio" placeholder="Ingrese Nuevo precio en C$" required><br>
			<input type="hidden" name="producto_id" id="producto_id" required>
			<input type="hidden" name="action" value="addProduct" required>
			<div class="alert alertAddProduct"></div>
			<button type="submit" class="btn_new">Agregar</button>
			<a href="listaproductos.php" class="btn_ok  closeModal" onclick="closeModal();">Cerrar</a>
			</form>
		</div>
	</div>