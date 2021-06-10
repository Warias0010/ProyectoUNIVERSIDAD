<?php
session_start();
include "../conexion.php";
//anular factura
if($_POST['action']=='anularFactura'){

    if(!empty($_POST['noFactura']))
    {
        $noFactura = $_POST['noFactura'];
        $query_anular = mysqli_query($conection,"CALL anular_factura($noFactura)");
        mysqli_close($conection);
        $result= mysqli_num_rows($query_anular);
        if($result>0){
            $data = mysqli_fetch_assoc($query_anular);
            echo json_encode($data,JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    echo "error";
    exit;
}
//info factura
if($_POST['action']=='infoFactura'){
    if(!empty($_POST['nofactura'])){
        

        $nofactura= $_POST['nofactura'];
        $query = mysqli_query($conection,"SELECT * FROM factura WHERE nofactura ='$nofactura' 
        AND estatus=1 ");
        mysqli_close($conection);
        
        $result = mysqli_num_rows($query);
        if($result >0){
            $data = mysqli_fetch_assoc($query);
            echo json_encode($data,JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    echo "error";
    exit;
}
//print_r($_POST);exit;
//actualiza stock
    //extrae datos del producto
    if($_POST['action'] == 'infoProducto1')
    {
       $producto_id = $_POST['producto1'];

       $query = mysqli_query($conection,"SELECT codproducto,descripcion FROM producto
                                          WHERE codproducto = $producto_id AND estatus = 1");
       mysqli_close($conection);
       $result = mysqli_num_rows($query);
       if($result > 0){
           $data = mysqli_fetch_assoc($query);
           echo json_encode($data,JSON_UNESCAPED_UNICODE);
           exit;
       }
       echo 'error';
       exit;
    }
    //agregar producto a entrada 
    if($_POST['action']== 'addProduct'){
        if(!empty($_POST['cantidad']) ||!empty($_POST['precio']) ||!empty($_POST['producto_id'])){

            $cantidad = $_POST['cantidad'];
            $precio = $_POST['precio'];
            $producto_id = $_POST['producto_id'];
            $usuario_id = $_SESSION['idUser'];

            $query_insert = mysqli_query($conection,"INSERT INTO entradas(codproducto,
                                                                         cantidad,precio,
                                                                         usuario_id) 
                                                                         VALUES ($producto_id,
                                                                         $cantidad,$precio,
                                                                         $usuario_id)");
            if($query_insert){
                //ejecuta el procdimeinto almacenado
                $query_upd = mysqli_query($conection,"CALL actualizar_precio_producto($cantidad,$precio,$producto_id)");
                $result_pro = mysqli_num_rows($query_upd);
                if($result_pro > 0){
                    $data = mysqli_fetch_assoc($query_upd);
                    $data['producto_id'] = $producto_id;
                    echo json_encode($data,JSON_UNESCAPED_UNICODE);
                    exit; 
                }
                else{
                    echo 'error';
                }
                mysqli_close($conection);
            }
            else{
                echo 'error';
            }
        }
        exit;
    }

//Procesar Venta
if($_POST['action'] == 'procesarVenta'){

    if(empty($_POST['codcliente'])){
     $codcliente = 1;
 }else{
    $codcliente = $_POST['codcliente'];
 }
 $token = md5($_SESSION['idUser']);
 $usuario = $_SESSION['idUser'];

 $query = mysqli_query($conection, "SELECT * FROM detalle_temp WHERE token_user ='$token' ");
 $result = mysqli_num_rows($query);

 if($result > 0){
     $query_procesar = mysqli_query($conection,"CALL procesar_venta($usuario,$codcliente,'$token')");
     $result_detalle = mysqli_num_rows($query_procesar);

     if($result_detalle > 0){
         $data = mysqli_fetch_assoc($query_procesar);
         echo json_encode($data,JSON_UNESCAPED_UNICODE);
     }else{
         echo "error";
     }
 }else{
     echo "error";
 }
 mysqli_close($conection);
 exit;
}



//anula la venta que esta en el detalle
if($_POST['action'] == 'anularVenta'){

    $token = md5($_SESSION['idUser']);
    
    $query_del= mysqli_query($conection, "DELETE FROM detalle_temp WHERE token_user ='$token' ");
    mysqli_close($conection);
    if($query_del){
        echo 'ok';
    }else{
        echo 'error';
    }
    exit;
}


//Esto es para elimarproducto de detalle
if($_POST['action'] == 'del_product_detalle'){
    if(empty($_POST['id_detalle'])){
        echo 'error';
    }
    else{
       $id_detalle= $_POST ['id_detalle'];
        $token = md5($_SESSION['idUser']);
 
        //llama al procemiento almacenado para elimanar del detalle
        $query_detalle_temp = mysqli_query($conection,"CALL del_detalle_temp($id_detalle,'$token')");
        $result= mysqli_num_rows($query_detalle_temp);


        $detalleTabla = '';
        $sub_total = 0;
        $total =0;
        $iva =0;
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
              <a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalle('.$data['correlativo'].');"><i class="far fa-trash-alt"></i>Eliminar</a> 
              </td>
              </tr>
              ';
        }
 
        $impuesto = round($sub_total*($iva/100),2);
        $tl_sniva= round($sub_total+$impuesto,2);
         $total = round($tl_sniva+$impuesto,2);
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

//extrae datos del detalle temp 
if($_POST['action'] == 'searchForDetalle')
{
   if(empty($_POST['user'])){
       echo 'error';
   }
   else{
      
       $token = md5($_SESSION['idUser']);

       $query = mysqli_query($conection,"SELECT tmp.correlativo,tmp.token_user,tmp.cantidad,tmp.precio_venta,p.codproducto,p.descripcion FROM  detalle_temp tmp 
                                         INNER JOIN producto p ON tmp.codproducto = p.codproducto WHERE token_user='$token' ");

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
             <a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalle('.$data['correlativo'].');"><i class="far fa-trash-alt"></i>Eliminar</a> 
             </td>
             </tr>
             ';
       }

       $impuesto = round($sub_total*($iva/100),2);
       $tl_sniva= round($sub_total+$impuesto,2);
        $total = round($tl_sniva+$impuesto,2);
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

//agreggar producto al detalle de temporal
if($_POST['action'] == 'addProductoDetalle')
{
   if(empty($_POST['producto']) || empty($_POST['cantidad'])){
       echo 'error';
   }
   else{
       $codproducto = $_POST['producto'];
       $cantidad = $_POST['cantidad'];
       $token = md5($_SESSION['idUser']);

       $query_detalle_temp = mysqli_query($conection,"CALL add_detalle_temp($codproducto,$cantidad,'$token')");
       $result =mysqli_num_rows($query_detalle_temp);
      

       $detalleTabla = '';
       $sub_total = 0;
       $total = 0;
       $iva= 0;
       $arrayData= array();
       if($result > 0){  
         while($data=mysqli_fetch_assoc($query_detalle_temp)) {
             $precioTotal = round($data['cantidad']* $data['precio_venta'], 2);
             $sub_total = round($sub_total + $precioTotal, 2);
             $total = round($total+ $precioTotal, 2);

             $detalleTabla .= '
             <tr>
             <td>'.$data['codproducto'].'</td>
             <td colspan="2">'.$data['descripcion'].'</td>
             <td class="textright">'.$data['cantidad'].'</td>
             <td class="textright">'.$data['precio_venta'].'</td>
             <td class="textright">'.$precioTotal.'</td>
             <td class="">
             <a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalle('.$data['correlativo'].');"></i>Eliminar</a> 
             </td>
             </tr>
             ';
       }
       
       $impuesto =0;
       $impuesto = round($sub_total*($iva/100),2);
       $tl_sniva= round($sub_total+$impuesto,2);
        $total = round($tl_sniva+$impuesto,2);
         $detalleTotales = '
        <tr>
        <td colspan="5" class="textright">SubTotal C$.</td>
        <td class="textright">'.$tl_sniva.'</td>
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

// extrae datos del producto
if($_POST['action'] == 'infoProducto')
{

 $producto_id = $_POST['producto'];
 $query = mysqli_query($conection," SELECT codproducto, descripcion, existencia, precio FROM producto 
                                            WHERE codproducto = $producto_id AND estatus= 1");
mysqli_close($conection);
$result= mysqli_num_rows($query);
 if($result > 0){
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
mysqli_close($conection);
if($query_insert){
    $codCliente = mysqli_insert_id($conection);
    $msg= $codCliente;
    $alert='<p class="msg>El cliente agregado sastifacriamente.</p>';
}else{
    $msg ='error';
}
//mysqli_close($conection);
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