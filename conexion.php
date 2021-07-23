<?php 
	
	$host = 'localhost';
	$user = 'root';
	$password = '12345678';
	$db = 'facinvconny';

	$conection = @mysqli_connect($host,$user,$password,$db);

	if(!$conection){
		echo "Error en la conexión";
	}

?>