<?php
session_start();
include "../conexion.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php include "includes/scripts.php"; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Venta</title>
</head>

<body>
    <?php include "includes/header.php"; ?>
    <section id="container">
        <div class="title_page">
            <h1><i class="fas fa-cube"></i>Nueva venta</h1>
        </div>
        <div class="datos_cliente">
            <div class="action_cliente">
                <h4>Datos Del Cliente</h4>
                <a href="#" class="btn_new btn_new_cliente"><i class="fas fa-plus"></i>Nuevo Cliente</a>
            </div>
            <form id="form_new_cliente_venta" name="form_new_cliente_venta"  class="datos">
                <input type="hidden" name="action" value="addCliente">
                <input type="hidden" id="idcliente" name="idcliente" value="" required>
                <div class="wd30">
                    <label for="nit">Cédula</label>
                    <input type="text" name="nit_cliente" id="nit_cliente">
                </div>
                <div class="wd30">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nom_cliente" id="nom_cliente" disabled required>
                </div>
                <div class="wd30">
                    <label for="telefono">Telefono</label>
                    <input type="number" name="tel_cliente" id="tel_cliente" disabled required>
                </div>
                <div class="wd100">
                    <label for="direccion">Direccion</label>
                    <input type="text" name="dir_cliente" id="dir_cliente" disabled required>
                </div>
                <div id="div_registro_cliente" class="wd100">
                    <button type="submit" class="btn_save"><i class="far fa-save fa-lg"></i>Guardar</button>
                </div>
            </form>
        </div>
        <div class="datos_venta">
            <h4>Datos de ventas</h4>
            <div class="datos">
                <div class="wd50">
                    <label>Vendedor</label>
                    <p><?php echo $_SESSION['nombre'];?></p>
                </div>
                <div class="wd50">
                    <label>Acciones</label>
                    <div id="acciones_venta">
                        <a href="#" class="btn_ok textcenter" id="btn_anular_venta"><i class="fas fa-ban"></i>Anular</a>
                        <a href="#" class="btn_new textcenter" id="btn_facturar_venta"><i class="fas fa-ban"></i>Generar Factura</a>
                    </div>
                </div>
            </div>
        </div>
        <table class="tbl_venta">
        <thead>
        <tr>
        <th width="100px">Codigo</th>
        <th>Descripcion</th>
        <th>Existencia</th>
        <th width="100px">Cantidad</th>
        <th class="textright">Precio</th>
        <th class="textright">Precio Total</th>
        <th>Accion</th>
        </tr>
        <tr>
        <td><input type="text" name="txt_cod_producto" id="txt_cod_producto" ></td>
        <td id="txt_descripcion">-</td>
        <td id="txt_existencia">-</td>
        <td><input type="text" name="txt_cant_producto" id="txt_cant_prodcuto" value="0" min="1" disabled></td>
        <td id="txt_precio" class="textright">0.00</td>
        <td id="precio_total" class="textright">0.00</td>
        <td><a href="#" id="add_product_venta" class="link_add">Agregar</a></td>
        </tr>
        <tr>
        <th>Codigo</th>
        <th colspan="2">Descripcion</th>
        <th>Cantidad</th>
        <th class="textright">Precio</th>
        <th class="textright">Precio Total</th>
        <th>Accion</th>
        </tr>
        </thead>
        <tbody id="detalle_venta">
        <tr>
        <td>1</td>
        <td colspan="2">Ropero Tres Cuerpo</td>
        <td class="textright">1</td>
        <td class="textright">100.00</td>
        <td class="textright">100.00</td>
        <td class="">
        <a class="link_delete" href="#" onclick="event.preventDefault(); del_producto_detalle(1);"><i class="far fa-trash-alt"></i>Eliminar</a> 
        </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
        <td colspan="5" class="textright">SubTotol C$.</td>
        <td class="textright">100.00</td>
        </tr>
        <tr>
        <td colspan="5" class="textright"> Total C$.</td>
        <td class="textright">100.00</td>
        </tr>
        </tfoot>
        </table>
    </section>
    <?php include "includes/footer.php"; ?>
</body>

</html>