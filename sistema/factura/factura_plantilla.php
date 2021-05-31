<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Factura</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<img class="anulada" src="img/anulado.png" alt="Anulada">
<div id="page_pdf">
	<table id="factura_head">
		<tr>
			<td>
				
			</td>
			<td class="info_empresa">
				<div>
					<span class="h2">SUPER GANGAS "CONNY"</span>
					<p>Les Ofresco de todo para su hogar, calidad y siempre con los precios más bajos.</p>
					<p>Managua,NIC Gancho de camino 1 1/2 C. arriba</p>
					<p>Cel: +(505) 8834-6162 Tel. +(505) 2248-4285 </p>
					<p>Email: franchezco@gmail.com</p>
					<p>Conny Robleto- Propietaria</p>
					<p>RUC: 6030812630001R</p>
					<p>"Contamos con taller Propio"</p>
				</div>
			</td>
			<td class="info_factura">
				<div class="round">
					<span class="h3">FACTURA</span>
					<p>No. Factura: <strong>000001</strong></p>
					<p>Fecha: 20/01/2019</p>
					<p>Hora: 10:30am</p>
					<p>Vendedor: Jorge Pérez Hernández Cabrera</p>
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
							<td><label>Cedula:</label><p>54895468</p></td>
							<td><label>Teléfono:</label> <p>7854526</p></td>
						</tr>
						<tr>
							<td><label>Cliente:</label> <p>Angel Arana Cabrera</p></td>
							<td><label>Dirección:</label> <p>Calzada Buena Vista</p></td>
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
					<th class="textright" width="150px">Precio Unitario.</th>
					<th class="textright" width="150px"> Precio Total</th>
				</tr>
			</thead>
			<tbody id="detalle_productos">
				<tr>
					<td class="textcenter">1</td>
					<td>Plancha</td>
					<td class="textright">516.67</td>
					<td class="textright">516.67</td>
				</tr>
				<tr>
					<td class="textcenter">1</td>
					<td>Plancha</td>
					<td class="textright">516.67</td>
					<td class="textright">516.67</td>
				</tr>
				<tr>
					<td class="textcenter">1</td>
					<td>Plancha</td>
					<td class="textright">516.67</td>
					<td class="textright">516.67</td>
				</tr>
				<tr>
					<td class="textcenter">1</td>
					<td>Plancha</td>
					<td class="textright">516.67</td>
					<td class="textright">516.67</td>
				</tr>
				<tr>
					<td class="textcenter">1</td>
					<td>Plancha</td>
					<td class="textright">516.67</td>
					<td class="textright">516.67</td>
				</tr>
				<tr>
					<td class="textcenter">1</td>
					<td>Plancha</td>
					<td class="textright">516.67</td>
					<td class="textright">516.67</td>
				</tr>
			</tbody>
			<tfoot id="detalle_totales">
				<tr>
					<td colspan="3" class="textright"><span>SUBTOTAL C$.</span></td>
					<td class="textright"><span>516.67</span></td>
				</tr>
				<tr>
					<td colspan="3" class="textright"><span>IVA ()</span></td>
					<td class="textright"><span>N/A</span></td>
				</tr>
				<tr>
					<td colspan="3" class="textright"><span>TOTAL C$.</span></td>
					<td class="textright"><span>516.67</span></td>
				</tr>
		</tfoot>
	</table>
	<div>
		<p class="nota" >REVISE SU MERCADERIA NO SE ACEPTAN DEVOLUCIONES</p>
		<h4 class="label_gracias">¡Gracias por su compra!</h4>
	</div>
	<div>
	<p >_________________</p>
		<p >Recibi Conforme</p>
		<p class="textright">_________________</p>
		<p class="textright" >Entregue Conforme</p>
	</div>

</div>

</body>
</html>