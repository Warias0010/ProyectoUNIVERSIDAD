<?php
session_start();
if($_SESSION['rol']!=1 and $_SESSION['rol']!=2){
    header("Location: ./");
}

include "../conexion.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include "includes/scripts.php"; ?>
    <title>Salida de productos vendidos</title>
</head>
<body>
    <?php include "includes/header.php"; ?>

    <section id="container">

    <h1>Salida de productos</h1>

    <table>
        <tr>
            <th>Código de salida</th>
            <th>Número de factura</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio de venta</th>
        </tr>
        <?php
        $sdl=mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM detallefactura");
        $result_resgister=mysqli_fetch_array($sdl);
        $total_registro=$result_resgister['total_registro'];

        $por_pagina=5;

        if(empty($_GET['pagina'])){
            $pagina=1;
        }else{
            $pagina=$_GET['pagina'];
        }

        $desde=($pagina-1) * $por_pagina;
        $total_paginas= ceil($total_registro / $por_pagina);

        $query= mysqli_query($conection,"SELECT d.correlativo, d.nofactura, p.descripcion
         AS Descripcion_producto, d.cantidad, d.precio_venta FROM detallefactura d
          INNER JOIN producto p ON d.codproducto = p.codproducto ORDER BY d.correlativo
           DESC LIMIT $desde,$por_pagina ");

          mysqli_close($conection);

          $result=mysqli_num_rows($query);
          if($result >0){
              while($data=mysqli_fetch_array($query)){
                  ?>
                    <tr>
                        <td><?php echo $data["correlativo"]; ?></td>
                        <td><?php echo $data["nofactura"]; ?></td>
                        <td><?php echo $data["Descripcion_producto"]; ?></td>
                        <td><?php echo $data["cantidad"]; ?></td>
                        <td><?php echo $data["precio_venta"]; ?></td>
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
</body>
</html>