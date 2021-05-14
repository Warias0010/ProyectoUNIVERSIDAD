<?php
session_start();
include "../conexion.php";	
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