<?php 
	session_start();

	include "../conexion.php";	

 ?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Lista de clientes</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<?php 

			$busqueda = strtolower($_REQUEST['busqueda']);
			if(empty($busqueda))
			{
				header("location: listaproducto.php");
				mysqli_close($conection);
			}


		 ?>
		
		<h1>Inventario Producto</h1>
		<?php if($_SESSION['rol']==1|| $_SESSION['rol']==2){?>
		<a href="registro_producto.php" class="btn_new">Ingresar Nuevo Producto</a>
		<?php } ?>
		<a href="nueva_venta.php" class="btn_new">Realizar Factura</a>
		
		<form action="buscarproducto.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Buscar" value="<?php echo $busqueda; ?>">
			<input type="submit" value="Buscar" class="btn_search">
			<a href="listaproductos.php" class="link_delete">Eliminar búsqueda</a>
		</form>

		<table>
			<tr>
            <th>Cod_Producto</th>
				<th>Producto</th>
				<th>Proveedor</th>
				<th>Categoría</th>
				<th>Precio</th>
				<th>Existencia</th>
				<th>Fecha Entrada</th>
				<th>Acciones</th>
				<th>Alerta</th>
			</tr>
		<?php 
			//Paginador
		
			$sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro  FROM producto p
                                                                INNER JOIN proveedor pr ON pr.codproveedor=p.proveedor
			                                                    INNER JOIN categoria ca ON ca.cod_categoria = p.categoria
																WHERE (p.codproducto LIKE '%$busqueda%' OR 
																		p.descripcion LIKE '%$busqueda%' OR 
																		pr.proveedor LIKE '%$busqueda%' OR 
																		ca.nombre LIKE '%$busqueda%'  ) AND p.estatus = 1");
			$result_register = mysqli_fetch_array($sql_registe);
			$total_registro = $result_register['total_registro'];

			$por_pagina = 5;

			if(empty($_GET['pagina']))
			{
				$pagina = 1;
			}else{
				$pagina = $_GET['pagina'];
			}

			$desde = ($pagina-1) * $por_pagina;
			$total_paginas = ceil($total_registro / $por_pagina);

			$query = mysqli_query($conection,"SELECT p.codproducto,p.descripcion,pr.proveedor,ca.nombre,p.precio,p.existencia,p.date_add
                                               FROM producto p
                                               INNER JOIN proveedor pr ON pr.codproveedor=p.proveedor
                                               INNER JOIN categoria ca ON ca.cod_categoria = p.categoria
                                               WHERE (p.codproducto LIKE '%$busqueda%' OR 
                                               p.descripcion LIKE '%$busqueda%' OR 
                                               pr.proveedor LIKE '%$busqueda%' OR 
                                               ca.nombre LIKE '%$busqueda%') 
            AND p.estatus = 1  ORDER BY p.codproducto ASC LIMIT $desde,$por_pagina 
				");
			mysqli_close($conection);
			$result = mysqli_num_rows($query);
			if($result > 0){

				while ($data = mysqli_fetch_array($query)) {
					
                    ?>
                    <tr class= "row"<?php echo $data["codproducto"]; ?> >
                        <td><?php echo $data["codproducto"]; ?></td>
                        <td><?php echo $data["descripcion"]; ?></td>
                        <td><?php echo $data["proveedor"]; ?></td>
                        <td><?php echo $data["nombre"]; ?></td>
                        <td class= "celPrecio">C$ <?php echo $data["precio"]; ?></td>
                        <td class= "celExistencia"> <?php echo $data["existencia"]; ?> uds.</td>
                        <td><?php echo $data["date_add"]; ?></td>
    
                        <?php if($_SESSION['rol']==1|| $_SESSION['rol']==2){?>
                        <td>
                            <a class="link_add add_product" product="<?php echo $data["codproducto"]; ?>" href="#" >Actulizar Stock</a>
                            |
                            <a class="link_edit" href="editar_productos.php?id=<?php echo $data["codproducto"]; ?>">Editar</a>
                             |
                             <a class="link_delete del_product" href="eliminar_producto.php?id=<?php echo $data["codproducto"]; ?>">Eliminar</a>
                                            
                        </td>
                        <?php 
                        if ( $data['existencia'] <= 5 ): echo 
                        "<td class=\"text-center\" style=\"background-color: #333; color: #ffff;\">Nivel Bajo</td>"; else: echo "<td></td>"; endif; ?>
                        <?php } ?>
                    </tr>
                
            <?php 
                    }
    
                }
             ?>
    
    
            </table>
            <div class="paginador">
                <ul>
                <?php 
                    if($pagina != 1)
                    {
                 ?>
                    <li><a href="?pagina=<?php echo 1; ?>">|<</a></li>
                    <li><a href="?pagina=<?php echo $pagina-1; ?>"><<</a></li>
                <?php 
                    }
                    for ($i=1; $i <= $total_paginas; $i++) { 
                        # code...
                        if($i == $pagina)
                        {
                            echo '<li class="pageSelected">'.$i.'</li>';
                        }else{
                            echo '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';
                        }
                    }
    
                    if($pagina != $total_paginas)
                    {
                 ?>
                    <li><a href="?pagina=<?php echo $pagina + 1; ?>">>></a></li>
                    <li><a href="?pagina=<?php echo $total_paginas; ?> ">>|</a></li>
                <?php } ?>
                </ul>
            </div>
    
    
        </section>
        <?php include "includes/footer.php"; ?>
    </body>
    </html>