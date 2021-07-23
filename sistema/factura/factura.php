<?php
	$subtotal 	= 0;
	$iva 	 	= 0;
	$impuesto 	= 0;
	$tl_sniva   = 0;
	$total 		= 0;
 //print_r($configuracion); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Factura</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php echo $anulada; ?>
<div id="page_pdf">
	<table id="factura_head">
		<tr>
			<td class="logo_factura">
			</td>
			<td class="info_empresa">
				<div>
				<span class="h2">SUPER GANGAS "CONNY"</span>
					<p>Les Ofresco de todo para su hogar, calidad y siempre con los precios más bajos.</p>
					<p>Managua,NIC Gancho de camino 1 1/2 C. arriba</p>
					<p>Cel: +(505) 8834-6162 Tel. +(505) 2248-4285 </p>
					<p>Email: franchezco@gmail.com</p>
					<p>Conny Robleto- Propietaria</p>
					<p>"Contamos con taller Propio"</p>
				</div>
			</td>
			<td class="info_factura">
				<div class="round">
				    <p>RUC: 6030812630001R</p>
					<span class="h3">Factura</span>
					<p>No. Factura: <strong><?php echo $factura['nofactura']; ?></strong></p>
					<p>Fecha: <?php echo $factura['fecha']; ?></p>
					<p>Hora: <?php echo $factura['hora']; ?></p>
					<p>Vendedor: <?php echo $factura['vendedor']; ?></p>
					<p>Tipo pago: Efectivo </p>
				</div>
			</td>
		</tr>
	</table>
	<table id="factura_cliente">
		<tr>
			<td class="info_cliente">
				<div class="round">
					<span class="h3">Cliente</span>
					<table class="datos_cliente">
						<tr>
						<td><label>Cliente:</label> <p><?php echo $factura['nombre']; ?></p></td>
						<td><label>Dirección:</label> <p><?php echo $factura['direccion']; ?></p></td>
							
						</tr>
						<tr>
							
						</tr>
					</table>
				</div>
			</td>

		</tr>
	</table>

	<table id="factura_detalle">
			<thead>
				<tr>
					<th width="50px">Cant.</th>
					<th class="textleft">Descripción</th>
					<th class="textright" width="150px">Precio Unitario</th>
					<th class="textright" width="150px"> Precio Total</th>
				</tr>
			</thead>
			<tbody id="detalle_productos">

			<?php

				if($result_detalle > 0){

					while ($row = mysqli_fetch_assoc($query_productos)){
			 ?>
				<tr>
					<td class="textcenter"><?php echo $row['cantidad']; ?></td>
					<td><?php echo $row['descripcion']; ?></td>
					<td class="textright"><?php echo $row['precio_venta']; ?></td>
					<td class="textright"><?php echo $row['precio_total']; ?></td>
				</tr>
			<?php
						$precio_total = $row['precio_total'];
						$subtotal = round($subtotal + $precio_total, 2);
					}
				}

				$impuesto 	= round($subtotal * ($iva / 100), 2);
				$tl_sniva 	= round($subtotal - $impuesto,2 );
				$total 		= round($tl_sniva + $impuesto,2);
			?>
			</tbody>
			<tfoot id="detalle_totales">
				<tr>
					<td colspan="3" class="textright"><span>SUBTOTAL C$.</span></td>
					<td class="textright"><span><?php echo $tl_sniva; ?></span></td>
				</tr>
				<tr>
					<td colspan="3" class="textright"><span>IVA %</span></td>
					<td class="textright">N/A</td>
				</tr>
				<tr>
					<td colspan="3" class="textright"><span>TOTAL C$.</span></td>
					<td class="textright"><span><?php echo $total; ?></span></td>
				</tr>
		</tfoot>
	</table>
	<div>
	<p class="nota" >REVISE SU MERCADERIA NO SE ACEPTAN DEVOLUCIONES</p>
		<h4 class="label_gracias">¡Gracias por su compra!</h4>
	</div>
	<p >_________________</p>
		<p >Recibi Conforme</p>
		<p class="textright">_________________</p>
		<p class="textright" >Entregue Conforme</p>
	</div>
</div>

</body>
</html>