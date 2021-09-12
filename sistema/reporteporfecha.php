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
include "../conexion.php";	
$where = '';
$busqueda = '';
$fecha_de = '';
$fecha_a = '';

if(isset($_REQUEST['busqueda']) && $_REQUEST['busqueda']==''){
    header("location: buscarreporte.php");
}
if( isset($_REQUEST['fecha_de']) || isset($_REQUEST['fecha_a']))
{
    if($_REQUEST['fecha_de'] == '' || $_REQUEST['fecha_a'] == '')
    {
        header("location:buscarreporte.php");
    }
}

if(!empty($_REQUEST['busqueda'])){
    if(!is_numeric($_REQUEST['busqueda'])){
        header("location: buscarreporte.php");
    }
    $busqueda = strtolower($_REQUEST['busqueda']);
    $where ="nofactura = $busqueda";
    $buscar ="busqueda = $busqueda";
}
if(!empty($_REQUEST['fecha_de']) && !empty($_REQUEST['fecha_a'])){
    $fecha_de = $_REQUEST['fecha_de'];
    $fecha_a = $_REQUEST['fecha_a'];

    $buscar = '';

    if($fecha_de > $fecha_a){
        header("location: buscarreporte.php");
    }else if($fecha_de == $fecha_a){
        $where = "fecha LIKE '$fecha_de%'";
        $buscar = "fecha_de=$fecha_de&fecha_a=$fecha_a";
    }else{
        $f_de= $fecha_de.' 00:00:00';
        $f_a= $fecha_a.' 23:59:59';
        $where= "fecha BETWEEN '$f_de' AND '$f_a'";
        $buscar="fecha_de=$fecha_de&fecha_a=$fecha_a";
    }
}

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
WHERE $where AND  f.estatus!=10 
ORDER BY f.fecha DESC";

$pdf = new PDF();
$pdf->SetTitle('Reportes Ventas');
$pdf->AliasNbPages();
$pdf->AddPage('LANSCAPE', 'Letter');
$pdf->SetFont('Arial','B',10);

$resultado = mysqli_num_rows($conexion, $consulta);
			if($result > 0){

				while ($row = mysqli_fetch_array($resultado)) {
                   if($data["estatus"]==1){
                       $estado = '<span class="pagada" >Pagada</span>';
                   }else{
                    $estado = '<span class="anulada" >Anulada</span>';
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