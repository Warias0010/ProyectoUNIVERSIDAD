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
