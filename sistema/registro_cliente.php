<?php 
	session_start();

	include "../conexion.php";

	if(!empty($_POST))
	{
		$alert='';
		if(empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['direccion']))
		{
		$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
		}else{

			$nit    = $_POST['nit'];
			$nombre = $_POST['nombre'];
			$telefono  = $_POST['telefono'];
			$direccion   = $_POST['direccion'];
			$usuario_id  = $_SESSION['idUser'];

				$query = mysqli_query($conection,"SELECT * FROM cliente WHERE nit = '$nit'");
				$result = mysqli_fetch_array($query);
	
			if ($result > 0 ) {
				$alert='<p class="msg_error">El numero de cédula  ya existe.</p>';
			}else{

				$query_insert = mysqli_query($conection,"INSERT INTO cliente(nit,nombre,telefono,direccion,usuario_id)
					VALUES('$nit','$nombre','$telefono','$direccion','$usuario_id')");
					if($query_insert){
					
					$alert='<p class="msg_save">Cliente guardado correctamente.</p>';
				}else{
					$alert='<p class="msg_error">Error al guardar el cliente.</p>';
				}
			}
			
			}

			mysqli_close($conection);
			
		}



 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Registro Cliente</title>

	<script type="text/javascript">
   function validar() {
	   var nit = document.getElementById("nit").value;
    if ($("#nit").val() != "") {
        if (validar_cedula($("#nit").val())) {

        } else {
            $("#nit").val("");
            alertError("Cedula incorrecta")
        }
    }
}

function validar_cedula() {
    if (num_ced.length == 16) {
        var letras = ["A", "B", "C", "D", "E", "F", "G", "H", "J", "K", "L", "M", "N", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y"];
        tfecha = String;
        partes = num_ced.split("-");
        tfecha = partes[1];
        dia = tfecha.substr(0, 2);
        mes = tfecha.substr(2, 2);
        anno = tfecha.substr(4, 2);
        conletra = partes[2];
        sinletras = conletra.substr(0, 4);
        num_ced = partes[0] + partes[1] + sinletras;
        letra = conletra.substr(4, 1);
        letra = letra.toUpperCase();
        p1 = num_ced / 23;
        temporal = parseInt(p1);
        digito = num_ced - (temporal) * 23;

        if (letras[digito] == letra) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
</script>
</head>
<body>

	<?php include "includes/header.php"; ?>
	<section id="container">
		<div class="form_register">
			<br>
			<h1>Registro de Clientes</h1>
			<hr>
			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

			<form action="" method="post">
				<label for="nit">Cédula</label>
				<input type="text" name="nit" id="nit" pattern="\d{3}-?\d{6}-?\d{4}\D" maxlength="16" required title="Formato: 021-210494-1000T"  placeholder="021-210494-1000T">
				<!--<input type="text" name="nit" id="nit" onsubmit="return validar()" placeholder="Cédula cliente">-->
				<label for="nombre">Nombre</label>
				<input type="text"  name="nombre" id="nombre" placeholder="Nombre completo" required>
			
				<label for="telefono">Teléfono</label>
				<input type="number" name="telefono" id="telefono" pattern="?\d{8}" minlength="8" maxlength="8" required title="Formato: 84860397" placeholder="Teléfono" maxlength="8" required >
				<label for="direccion">Dirección</label>
				<input type="text" name="direccion" id="ireccion" placeholder="Dirección">
				
				<input type="submit" value="Guardar Cliente" class="btn_save">

			</form>


		</div>


	</section>
	<?php include "includes/footer.php"; ?>
	
</body>
</html>