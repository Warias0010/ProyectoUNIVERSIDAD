<?php
require('fpdf/fpdf.php');
session_start();
if(!empty($_SESSION['active']))
{
}else{

	if(!empty($_POST))
	{
		if(empty($_POST['usuario']) || empty($_POST['clave']))
		{
			$alert = 'Ingrese su usuario y su calve';
		}else{

			require_once "conexion.php";

			$user = mysqli_real_escape_string($conection,$_POST['usuario']);
			$pass = md5(mysqli_real_escape_string($conection,$_POST['clave']));

			$query = mysqli_query($conection,"SELECT * FROM usuario WHERE usuario= '$user' AND clave = '$pass' AND estatus = 1 " );
			mysqli_close($conection);
			$result = mysqli_num_rows($query);

			if($result > 0)
			{
				$data = mysqli_fetch_array($query);
				$_SESSION['active'] = true;
				$_SESSION['idUser'] = $data['idusuario'];
				$_SESSION['nombre'] = $data['nombre'];
				$_SESSION['email']  = $data['correo'];
				$_SESSION['user']   = $data['usuario'];
				$_SESSION['rol']    = $data['rol'];
			}else{
				$alert = 'El usuario o la clave son incorrectos';
				session_destroy();
			}


		}

	}
}

class PDF extends FPDF
{
// Cabecera de página
function Header()
{	
    // Arial bold 15
    $this->SetFont('Arial','B',16);
    // Movernos a la derecha
    $this->Cell(90);
    // Título
    $this->Cell(245,10, '',0,1,'C',0);
    $this->Cell(110,10, 'Usuario: ',0,0,'R',0);
    $this->Cell(50,10,utf8_decode($_SESSION['nombre']),0,1,'R',0);

    $this->Cell(245,10,'Reporte de Facturas ',0,1,'C',0);
    $this->Cell(245,10, '*******Uso Interno*******',0,1,'C',0);
    $this->Cell(100,10, 'Fecha Consulta: ',0,0,'R',0);
    $this->Cell(70,10,date('d/m/Y'),0,1,'R',0);
			
				
    // Salto de línea
    $this->Ln(20);

    $this->Cell(30,10,'Factura',1,0,'C',0);
	$this->Cell(20,10,'Pago',1,0,'C',0);
	$this->Cell(50,10,utf8_decode('Fecha Emisión'),1,0,'C',0);
    $this->Cell(50,10,utf8_decode('Cliente'),1,0,'C',0);
	$this->Cell(40,10,utf8_encode('Vendedor'),1,0,'C',0);
    $this->Cell(30,10,utf8_encode('Estado'),1,0,'C',0);
    $this->Cell(40,10,'Total Factura',1,1,'C',0);
  
}

// Pie de página
function Footer()
{
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Número de página
    $this->Cell(0,10, utf8_decode('Página') .$this->PageNo().'/{nb}',0,0,'C');
   
}
}

require ("cn.php");

$consulta = "SELECT f.nofactura,f.fecha,f.totalfactura,f.codcliente,f.estatus,
u.nombre as vendedor,
cl.nombre as cliente,
p.nombre as pago
FROM factura f
INNER JOIN tipopago p
ON f.metodopago= p.Cod_Tipo_pago
INNER JOIN usuario u 
ON f.usuario = u.idusuario
INNER JOIN cliente cl
ON f.codcliente= cl.idcliente
WHERE f.estatus = 2   ORDER BY f.fecha DESC";
$resultado = mysqli_query($conexion, $consulta);

$pdf = new PDF();
$pdf->SetTitle('Reportes Ventas');
$pdf->AliasNbPages();
$pdf->AddPage('LANSCAPE', 'Letter');
$pdf->SetFont('Arial','B',10);

while ($row=$resultado->fetch_assoc()) {
        if($row["estatus"]==1){
            $estado = 'Pagada';
        }else{
         $estado = 'Anulada';
        }
	$pdf->Cell(30,10,$row['nofactura'],1,0,'C',0);
	$pdf->Cell(20,10,$row['pago'],1,0,'C',0);
	$pdf->Cell(50,10,$row['fecha'],1,0,'C',0);
    $pdf->Cell(50,10,utf8_decode($row['cliente']),1,0,'C',0);
	$pdf->Cell(40,10, utf8_decode($row['vendedor']) ,1,0,'C',0);
    $pdf->Cell(30,10, $estado,1,0,'C',0);
   $pdf->Cell(40,10,$row['totalfactura'],1,1,'C',0);
} 


	$pdf->Output();
?>