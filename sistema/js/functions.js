$(document).ready(function(){

    //--------------------- SELECCIONAR FOTO PRODUCTO ---------------------
    $("#foto").on("change",function(){
    	var uploadFoto = document.getElementById("foto").value;
        var foto       = document.getElementById("foto").files;
        var nav = window.URL || window.webkitURL;
        var contactAlert = document.getElementById('form_alert');
        
            if(uploadFoto !='')
            {
                var type = foto[0].type;
                var name = foto[0].name;
                if(type != 'image/jpeg' && type != 'image/jpg' && type != 'image/png')
                {
                    contactAlert.innerHTML = '<p class="errorArchivo">El archivo no es válido.</p>';                        
                    $("#img").remove();
                    $(".delPhoto").addClass('notBlock');
                    $('#foto').val('');
                    return false;
                }else{  
                        contactAlert.innerHTML='';
                        $("#img").remove();
                        $(".delPhoto").removeClass('notBlock');
                        var objeto_url = nav.createObjectURL(this.files[0]);
                        $(".prevPhoto").append("<img id='img' src="+objeto_url+">");
                        $(".upimg label").remove();
                        
                    }
              }else{
              	alert("No selecciono foto");
                $("#img").remove();
              }              
    });

    $('.delPhoto').click(function(){
    	$('#foto').val('');
    	$(".delPhoto").addClass('notBlock');
    	$("#img").remove();

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
            if(response !='error')
            {
            var info = JSON.parse(response);
             $('#txt_descripcion').html(info.descripcion);
             $('#txt_existencia').html(info.existencia);
             $('#txt_cant_producto').val('1')
             $('#txt_precio').html(info.precio);
             $('#txt_precio_total').html(info.precio);

             // activar la cantidad 
             $('#txt_cant_producto').removeAttr('disabled')
             //mostrar boton de  agregar 
             $('#add_product_venta').slideDown();  
            }else {
                $('#txt_descripcion').html('-');
                $('#txt_existencia').html('-');
                $('#txt_cant_producto').val('0')
                $('#txt_precio').html('0.00');
                $('#txt_precio_total').html('0.00');
                

                //bloquear cantidad
                $('#txt_cant_producto').Attr('disabled','disabled')

                //ocultar boton agregar 
                $('#add_product_venta').slideup(); 
            }    

            },
            error: function(error){
            }
        });
      }
     });
     //agregar producto al detalle
     $('#add_product_venta').click(function(e){
         e.preventDefault();
         if($('#txt_cant_productos').val()>0){
             var codproducto = $('#txt_cod_producto').val();
            var cantidad = $('#txt_cant_producto').val();
            var codproducto = $('addProductoDetalle').val();

            $.ajax({
                url: 'ajax.php',
                type:"POST",
                async: true,
                data: {action:action,producto:producto,cantidad:cantidad},
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
         if( ($(this).val()<1 || isNaN($(this).val())) || ($(this).val() > existencia) ){
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
     //crear clientes -Modu
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
});
