		<nav>
			<ul>
				<li><a href="index.php">Inicio</a></li>
			<?php 
				if($_SESSION['rol'] == 1){
			 ?>
				<li class="principal">

					<a href="#">Usuarios</a>
					<ul>
						<li><a href="registro_usuario.php">Nuevo Usuario</a></li>
						<li><a href="lista_usuarios.php">Lista de Usuarios</a></li>
					</ul>
				</li>
			<?php } ?>
				<li class="principal">
					<a href="#">Clientes</a>
					<ul>
						<li><a href="registro_cliente.php">Nuevo Cliente</a></li>
						<li><a href="lista_clientes.php">Lista de Clientes</a></li>
					</ul>
				</li>
				<?php 
				if($_SESSION['rol'] == 1||$_SESSION['rol'] == 2){
			 ?>
				<li class="principal">
					<a href="#">Proveedores</a>
					<ul>
						<li><a href="registro_proveedor.php">Nuevo Proveedor</a></li>
						<li><a href="lista_proveedor.php">Lista de Proveedores</a></li>
					</ul>
				</li>
				<?php } ?>
				<li class="principal">
					<a href="#">Productos</a>
					<ul>
							<?php 
						if($_SESSION['rol'] == 1||$_SESSION['rol'] == 2){
						 ?>
						<li><a href="registro_producto.php">Nuevo Producto</a></li>
						<li><a href="listaproductos.php">Lista de Productos</a></li>
						<li><a href="pro.php">Actulizar Inventario</a></li>
						<?php } ?>
						
					</ul>
				</li>
				<li class="principal">
					<a href="#">Facturas</a>
					<ul>
						<li><a href="#">Nuevo Factura</a></li>
						<li><a href="factura.php">Facturas</a></li>
					</ul>
				</li>
			</ul>
		</nav>