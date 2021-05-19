<?php
session_start();
include "../conexion.php";

//extrae datos del detalle temp
if($_POST['action'] == 'searchForDetalle')
{
   // print_r($_POST); esto comprueba los valores del metodo post
   if(empty($_POST['user'])){
       echo 'error';
   }
   else{
      
       $token = md5($_SESSION['idUser']);

       $query = mysqli_query($conection,"Select tmp.correlativo,tmp.token_user,tmp.cantidad,tmp.precio_venta,p.codproducto,p.descripcion FROM  detalle_temp tmp 
                                         INNER JOIN producto p ON tmp.codproducto = p.codproducto WHERE token_user='$token'");

       $result =mysqli_num_rows($query);
      

       $detalleTabla = '';
       $sub_total = 0;
       $total =0;
       $iva =0;
       $arrayData= array();
       if($result > 0){  
         while($data=mysqli_fetch_assoc($query)) {
             $precioTotal = round($data['cantidad']* $data['precio_venta'],2);
             $sub_total = round($sub_total + $precioTotal,2);
             $total = round($total+ $precioTotal,2);
             $detalleTabla .= '
             <tr>
             <td>'.$data['codproducto'].'</td>
             <td colspan="2">'.$data['descripcion'].'</td>
             <td class="textright">'.$data['cantidad'].'</td>
             <td class="textright">'.$data['precio_venta'].'</td>
             <td class="textright">'.$precioTotal.'</td>
             <td class="">
             <a class="link_delete" href="#" onclick="event.preventDefault(); del_producto_detalle('.$data['codproducto'].');"><i class="far fa-trash-alt"></i>Eliminar</a> 
             </td>
             </tr>
             ';
       }

       $impuesto = round($sub_total*($iva/100),2);
       $tl_sniva= round($sub_total+$impuesto);
        $total = round($tl_sniva+$impuesto);
         $detalleTotales = '
        <tr>
        <td colspan="5" class="textright">SubTotal C$.</td>
        <td class="textright">'. $tl_sniva.'</td>
        </tr>
        <tr>
        <td colspan="5" class="textright">IVA C$.</td>
        <td class="textright">N/A</td>
        </tr>
        <tr>
        <td colspan="5" class="textright"> Total C$.</td>
        <td class="textright">'.$total.'</td>
        </tr>
        
         ';

         $arrayData['detalle'] = $detalleTabla;
         $arrayData['totales']= $detalleTotales;

         echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
        }else
        {
           echo 'error';
        }
        mysqli_close($conection);
   }
   exit;
}

//agreggar producto al detalle de factura
if($_POST['action'] == 'addProductoDetalle')
{
   // print_r($_POST); esto comprueba los valores del metodo post
   if(empty($_POST['producto']) || empty($_POST['cantidad'])){
       echo 'error';
   }
   else{
       $codproducto = $_POST['producto'];
       $cantidad = $_POST['cantidad'];
       $token = md5($_SESSION['idUser']);

       $query_detalle_temp= mysqli_query($conection,"CALL add_detalle_temp($codproducto,$cantidad,'$token')");
       $result =mysqli_num_rows($query_detalle_temp);
      

       $detalleTabla = '';
       $sub_total = 0;
       $total =0;
       $arrayData= array();
       if($result > 0){  
         while($data=mysqli_fetch_assoc($query_detalle_temp)) {
             $precioTotal = round($data['cantidad']* $data['precio_venta'],2);
             $sub_total = round($sub_total + $precioTotal,2);
             $total = round($total+ $precioTotal,2);
             $detalleTabla .= '
             <tr>
             <td>'.$data['codproducto'].'</td>
             <td colspan="2">'.$data['descripcion'].'</td>
             <td class="textright">'.$data['cantidad'].'</td>
             <td class="textright">'.$data['precio_venta'].'</td>
             <td class="textright">'.$precioTotal.'</td>
             <td class="">
             <a class="link_delete" href="#" onclick="event.preventDefault(); del_producto_detalle('.$data['codproducto'].');"><i class="far fa-trash-alt"></i>Eliminar</a> 
             </td>
             </tr>
             ';
       }
         $tl_s= round($tl_s,2);
         $total = round($total+ $tl_s,2);
         $detalleTotales = '
        <tr>
        <td colspan="5" class="textright">SubTotal C$.</td>
        <td class="textright">'.$tl_s.'</td>
        </tr>
        <tr>
        <td colspan="5" class="textright"> Total C$.</td>
        <td class="textright">'.$total.'</td>
        </tr>
         ';

         $arrayData['detalle'] = $detalleTabla;
         $arrayData['totales']= $detalleTotales;

         echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
        }else
        {
           echo 'error';
        }
        mysqli_close($conection);
   }
   exit;
}

// Informacion Productos
if($_POST['action'] == 'infoProducto')
{
$producto_id = $_POST['producto'];
 $query = mysqli_query($conection," SELECT codproducto, descripcion, existencia, precio FROM producto 
                                            WHERE codproducto = $producto_id AND estatus= 1");
mysqli_close($conection);
$result= mysqli_num_rows($query);
 if($result>0){
     $data = mysqli_fetch_assoc($query);
     echo json_encode($data,JSON_UNESCAPED_UNICODE);
     exit;
 }
 echo 'error';
 exit;
 }


//registro cliente - Modulo Venta
if($_POST['action'] == 'addCliente')
{
 $nit = $_POST['nit_cliente'];
 $nombre = $_POST['nom_cliente'];
 $telefono = $_POST['tel_cliente'];
 $direccion = $_POST['dir_cliente'];
 $usuario_id = $_SESSION['idUser'];

 $query_insert = mysqli_query($conection,"INSERT INTO cliente(nit,nombre,telefono,direccion,usuario_id)
                   VALUES('$nit','$nombre','$telefono','$direccion','$usuario_id')");


if($query_insert){
    $codCliente = mysqli_insert_id($conection);
    $msg= $codCliente;
    $alert='<p class="msg>El cliente agregado sastifacriamente.</p>';
}else{
    $msg ='error';
}
mysqli_close($conection);
echo $msg;
exit;
}
//buscar cliente
if($_POST['action'] == 'searchCliente')
{// echo "buscar cliente"; Aqui se realiza la consulta ala base de datos 
    if(!empty($_POST['cliente'])){
        $nit = $_POST['cliente'];
        $query = mysqli_query($conection,"SELECT * FROM cliente WHERE nit LIKE '$nit' and estatus= 1 "
     );
        mysqli_close($conection);
        $result = mysqli_num_rows($query);

        $data = '';
        if($result> 0){
            $data = mysqli_fetch_assoc($query);
        }else {
            $data = 0;
        }
       echo json_encode($data,JSON_UNESCAPED_UNICODE);
      //registrar cliente desde modulo venta 
       
    }
    exit;  
}
?>