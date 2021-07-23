$(document).ready(function(){
    //validar cedula
    
    //ver factura pdf
    $('.btn_factura').click(function(e){
      e.preventDefault();
      var codCliente = $(this).attr('cl');
      var nofactura = $(this).attr('f');  
      generarPDF(codCliente,nofactura);
    });

    //modal forms anular factura
    $('.anular_factura').click(function(e){
        e.preventDefault();
        var nofactura = $(this).attr('fac');
        var action = 'infoFactura';
        $.ajax({
            url : 'ajax.php',
             type : "POST",
             async: true,
             data:{action:action,nofactura:nofactura},
             success : function(response){
                 if(response !='error'){
                    var info = JSON.parse(response);
                   // console.log(response);
                      $('.bodymodal').html('<form action="" method="post" name="form_anular_factura" id="form_anular_factura" onsubmit="event.preventDefault(); anularFactura(); ">'+
                      '<h1><br> Anular Factura</h1><br>'+
                      '<p>¿Realmente desea anular la factura?</p>'+

                      
                      '<div class="alert alertAddProduct"></div>'+
                      '<p><strong>No.Factura: '+info.nofactura+'</strong></>'+
                      '<p><strong>Monto C$: ' +info.totalfactura+'</strong></>'+
                      '<p><strong>Fecha/Hora Emitida  : '+info.fecha+'</strong></>'+
                      '<input type="hidden"  name="action" value="anularFactura" required>'+
                      '<input id="no_factura" type="hidden"  name="no_factura" value="'+info.nofactura+'" required>'+

                      
                      '<button type="submit" class="btn_ok">Anular</button>'+
                      '<a href="#" class="btn_new"  onclick="closeModal();">Cerrar</a>'+
                      '</form>')
                 }
             },
             error: function(error){
             console.log(error);
             }
        });
        $('.modal').fadeIn();
    });

     //facturar venta
     $('#btn_facturar_venta').click(function(e){
        e.preventDefault(); 
           var rows = $('#detalle_venta tr').length;
            if(rows > 0){
                var action = 'procesarVenta';
                var codcliente = $('#idcliente').val();
     
                $.ajax({
                     url : 'ajax.php',
                     type : "POST",
                     async: true,
                     data:{action:action,codcliente:codcliente},
     
                     success: function(response)
                     {   
                     if(response != 'error'){
                        var info = JSON.parse(response);
                        //console.log(info);

                        generarPDF(info.codcliente,info.nofactura);
                       location.reload();
                     }
            },
             error: function(error){
            }
            });
          } else {
            console.log('no data');
          }
        
    });

    //Anular venta
    $('#btn_anular_venta').click(function(e){
    e.preventDefault(); 
       var rows = $('#detalle_venta tr').length;
       if (confirm("Confirmar Accion")) {
        if(rows > 0){
            var action= 'anularVenta';
 
            $.ajax({
                 url : 'ajax.php',
                 type : "POST",
                 async: true,
                 data:{action:action},
 
                 success: function(response)
                 {   
                 if(response != 'error'){
                   location.reload();
                 }
                 window.alert("Operacion Realizada con Exito!");
        },
         error: function(error){
        }
        });
      } else {
        console.log('no data');
      }
    }
});
    //agregar producto al detalle temporal
    $('#add_product_venta').click(function(e){
        e.preventDefault();
        if($('#txt_cant_producto').val() > 0){
            var codproducto = $('#txt_cod_producto').val();
            var cantidad = $('#txt_cant_producto').val();
            var action = 'addProductoDetalle';
            $.ajax({
                url : 'ajax.php',
                type : "POST",
                async: true,
                data: {action:action,producto:codproducto,cantidad:cantidad},

                success: function(response){
                   if(response!= 'error'){
                    var info = JSON.parse(response);
                  //  console.log(info);
                     $('#detalle_venta').html(info.detalle);
                     $('#detalle_totales').html(info.totales);
 
                     $('#txt_cod_producto').val('');
                     $('#txt_descripcion').html('-');
                     $('#txt_existencia').html('-');
                     $('#txt_cant_producto').val('0');
                     $('#txt_precio').html('0.00');
                     $('#txt_precio_total').html('0.00');
 
                     //bloque de campos
                     $('#txt_cant_producto').attr('disabled','disabled');
 
                     //bloque de funcion agregar 
                     $('#add_product_venta').slideUp();
                      }else{
                      console.log('no data');
                       window.alert("Favor Ingresar el Codigo del Producto!");
                    }  
                },
                error: function(error){

                }

            });
        }
          
    });

    //Buscar producto
    $('#txt_cod_producto').keyup(function(e){
        e.preventDefault();
        var producto = $(this).val();
        var action = 'infoProducto';
        if(producto != '') {
        $.ajax({
            url: 'ajax.php',
            type:"POST",
            async: true,
            data: {action:action,producto:producto},
            success:function(response)
            {
             if(response != 'error')
            {
            var info = JSON.parse(response);
             $('#txt_descripcion').html(info.descripcion);
             $('#txt_existencia').html(info.existencia);
             $('#txt_cant_producto').val('1');
             $('#txt_precio').html(info.precio);
             $('#txt_precio_total').html(info.precio);

             // activar campo la cantidad 
             $('#txt_cant_producto').removeAttr('disabled');
             //mostrar boton de  agregar 
             $('#add_product_venta').slideDown();  
            }else {
                $('#txt_descripcion').html('-');
                $('#txt_existencia').html('-');
                $('#txt_cant_producto').val('0')
                $('#txt_precio').html('0.00');
                $('#txt_precio_total').html('0.00');
                

                //bloquear cantidad
                $('#txt_cant_producto').attr('disabled','disabled');

                //ocultar boton agregar 
                $('#add_product_venta').slideUp(); 
            }    

            },
            error: function(error){
                window.alert("Favor Ingresar el cantidad  del Producto!");
            }
        });
      }
     });
     //agregar producto al detalle temporal 
     $('#add_product_venta').click(function(e){
         e.preventDefault();
         if($('#txt_cant_productos').val() > 0){
             var codproducto = $('#txt_cod_producto').val();
            var cantidad = $('#txt_cant_producto').val();
            var action = 'addProductoDetalle';

            $.ajax({
                url: 'ajax.php',
                type:"POST",
                async: true,
                data: {action:action,producto:codproducto,cantidad:cantidad},
                success: function(response){

                    console.log(response);
                },
                error: function(error){
                    
                }
               
            });

         }
     });

     //validar producto antes de agregar 
     $('#txt_cant_producto').keyup(function(e){
         e.preventDefault();
         var precio_total = $(this).val() * $('#txt_precio').html();
         var existencia= parseInt($('#txt_existencia').html());
         $('#txt_precio_total').html(precio_total);
         // validamos la cantida d de productos si es menos que 1
         if( ($(this).val()<1 || isNaN($(this).val())) || ($(this).val() > existencia))  {
             $('#add_product_venta').slideUp();   
         }else{
             $('#add_product_venta').slideDown();
         }
     });

    //crear clientes 
    $('#form_new_cliente_venta').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: 'ajax.php',
            type:"POST",
            async: true,
            data: $('#form_new_cliente_venta').serialize(),
            success:function(response)
            {
                if(response != 'error'){
                    $('#idcliente').val(response);
                    //bloqueos de campos ... si se retorna la variable
                    $('#nom_cliente').attr('disabled','disabled');
                    $('#tel_cliente').attr('disabled','disabled');
                    $('#dir_cliente').attr('disabled','disabled');

                    //ocultar botones
                    $('btn_new_cliente').slideUp();

                    //ocultar agregar
                    $('#div_registro_cliente').slideUp();
                }
        
            },
            error: function(error){
               
            }
        });
    });
     //crear clientes -Modulo venta 
    $('#form_new_cliente_venta').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: 'ajax.php',
            type:"POST",
            async: true,
            data: $('#form_new_cliente_venta').serialize(),
            success:function(response)
            {
                if(response != 'error'){
                    $('#idcliente').val(response);
                    //bloqueos de campos ... si se retorna la variable
                    $('#nom_cliente').attr('disabled','disabled');
                    $('#tel_cliente').attr('disabled','disabled');
                    $('#dir_cliente').attr('disabled','disabled');

                    //ocultar botones
                    $('btn_new_cliente').slideUp();

                    //ocultar agregar
                    $('#div_registro_cliente').slideUp();
                }
        
            },
            error: function(error){
        
            }
        });
    });
    //evento Bucar cliente
    $('#nit_cliente').keyup(function(e){
     e.preventDefault();

     var cl =$(this).val();
     var action = 'searchCliente'
     $.ajax({
         url: 'ajax.php',
         type:"POST",
         async: true,
         data: {action:action,cliente:cl},

         success:function(response)
         {
             if(response == 0) { 
             $('#idcliente').val('');
             $('#nom_cliente').val('');
             $('#tel_cliente').val('');
             $('#dir_cliente').val('');

             //Mostrar Boton agregar 
             $('.btn_new_cliente').slideDown();     
            }else{
            var data = $.parseJSON(response);
             $('#idcliente').val(data.idcliente);
             $('#nom_cliente').val(data.nombre);
             $('#tel_cliente').val(data.telefono);
             $('#dir_cliente').val(data.direccion);

             //btn ocultar 
             $('.btn_new_cliente').slideUp(); 
            } 
            // Bloquear campos ya con datos de la base de datos 
            $('#nom_cliente').attr('disabled','disabled');
             $('#tel_cliente').attr('disabled','disabled');
             $('#dir_cliente').attr('disabled','disabled');

             //ocultar el btn guardar
             $('#div_registro_cliente').slideUp();
         },
         error: function(error){
         }
     });
    });
 //activa campo para registrar nuevos clientes
 $('.btn_new_cliente').click(function(e){
    e.preventDefault();
    $('#nom_cliente').removeAttr('disabled');
    $('#tel_cliente').removeAttr('disabled');
    $('#dir_cliente').removeAttr('disabled');

    $('#div_registro_cliente').slideDown();
  });

  //modal para el form add product
$('.add_product').click(function(e){
    e.preventDefault();
    var producto1 = $(this).attr('product');
    var action = 'infoProducto1';
    
        $.ajax({
            url: 'ajax.php',
            type:"POST",
            async: true,
            data: {action:action,producto1:producto1},
            success: function(response){
               // console.log(response);
                if(response != 'error'){
                    var info = JSON.parse(response);
                  //  console.log(info);
                    $('#producto_id').val(info.codproducto);
                    $('.nameProducto').html(info.descripcion);
                }else
                window.alert("Ocurrio un error en el proceso");
            },
            error: function(error){
                console.log(error);     
            }
        });

       $('.modal').fadeIn();
    });

});//termina el and ready

//funcion anular la factura
function anularFactura(){
    var noFactura = $('#no_factura').val();
    var action = 'anularFactura';
    $.ajax({
        url: 'ajax.php',
        type:"POST",
        async: true,
        data: {action:action,noFactura:noFactura},
        success: function(response){
            if(response=='error'){
                $('.alertAddProduct').html('<p style="color:red;"> Error en el proceso de anular la factura.</p>');
            }else{
                $('#row_'+noFactura+' .estado').html('<span class="anulada">Anulada</span>');
                $('#form_anular_factura .btn_ok').remove();
                $('#row_'+noFactura+' .div_factura').html('<button type="button" class="btn_anular inactive">Anulada</button>');
                $('.alertAddProduct').html('<p>Factura Anulada</p>');
            }

        },
        error: function(error){

        }
    });
}
function sendDataProduct(){
    $('.alertAddProduct').html('');

    $.ajax({
        url: 'ajax.php',
        type:"POST",
        async: true,
        data: $('#form_add_product').serialize(),
        success: function(response){
            console.log(response);
           if(response == 'error'){
              $('.alertAddProduct').html('<p style="color:red;">Eror al ejecutar la acción</p>');
           }else
           {
            var info = JSON.parse(response);
            $('.row'+info.producto_id+'.celExistencia').html(info.nueva_existencia);
            $('.row'+info.producto_id+'.celPrecio').html(info.nuevo_precio);
            
            $('#txtprecio').val('');
            $('#txtcantidad').val('');
            $('.alertAddProduct').html('<p>Registro Agregado Correctamente. </p>');
           }
        },
        error: function(error){
            console.log(error);     
        }
    });
}

//funcion para el modal de agregar inventario
function closeModal(){
    $('.alertAddProduct').html('');
    $('#txtcantidad').val('');
    $('#txtprecio').val('');
    $('.modal').fadeOut();

}
//genera PDF
function generarPDF(cliente,factura){
    var ancho = 1000;
    var alto =800;
    //calcular posicion x,y para centrar la pagina
    var x = parseInt((window.screen.width/2) - (ancho/2));
    var y = parseInt((window.screen.height/2) - (alto/2));

    $url = 'factura/generaFactura.php?cl='+cliente +'&f='+factura;
    window.open($url,"Factura","left="+x+",top="+y+",height="+alto+",width"+ancho+",scrollbar=si,location=no,resizable=si,menubar=no");
}

////---- Funciones diferente WJDEVELOPER
function del_product_detalle(correlativo){
    var action= 'del_product_detalle';
    var id_detalle= correlativo;

    $.ajax({
        url : 'ajax.php',
        type : "POST",
        async: true,
        data: {action:action,id_detalle:id_detalle},
        success: function(response){
         // console.log(response);
         if(response!= 'error'){

             var info = JSON.parse(response)
             {  
                    // console.log(info);
                     $('#detalle_venta').html(info.detalle);
                     $('#detalle_totales').html(info.totales)
 
                     $('#txt_cod_producto').val('');
                     $('#txt_descripcion').html('-');
                     $('#txt_existencia').html('-');
                     $('#txt_cant_producto').val('0')
                     $('#txt_precio').html('0.00');
                     $('#txt_precio_total').html('0.00');
 
                     //bloque de campos
                     $('#txt_cant_producto').attr('disabled','disabled');
 
                     //bloque de funcion agregar 
                     $('#add_product_venta').slideUp();
                    }
                    

        }else{
            $('#detalle_venta').html('');
            $('#detalle_totales').html('');
        }    
        },
        error: function(error){
        }
    });
}

//ocultar el boton procesar en factura
function viewProcesar(){
    if($('#detalle_venta tr').lenght > 0){
        $('#btn_facturar_venta').show();
    }else{
        $('#btn_facturar_venta').hide();

    }
}

//cuando se recargue la pagina buscar si tiene fac no realizada
function searchForDetalle(id){
    var action= 'searchForDetalle';
    var user= id;

    $.ajax({
        url : 'ajax.php',
        type : "POST",
        async: true,
        data: {action:action,user:user},
        success: function(response){
         // console.log(response);
         if(response!= 'error'){
            var info = JSON.parse(response);
            // console.log(info);
             $('#detalle_venta').html(info.detalle);
             $('#detalle_totales').html(info.totales);
             //bloque de campos
             $('#txt_cant_producto').attr('disabled','disabled');

        }else{
            console.log('contacta con Teams de desarrollador');
        } 
       
        },
        error: function(error){
        }
    });
    
}
