<?php
//iniciamos sesiones
session_start();

//archivos necesarios
require_once '../config.php';
require_once '../conexion.php';
require_once '../isUser.php';

//variable de coneccion con db
$dbConect = conect();


//verificamos que no este conectado el usuario
if (!empty($_SESSION['user']) && !empty($_SESSION['password'])) {
	$arrayUser = isUser($_SESSION['user'], $_SESSION['password'],$dbConect);
}

//verificamos que sea un daministrador
if (empty($arrayUser) || $arrayUser['type'] != 'admin') {
	header('Location: ../index.php');
	die();
}

?>

<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Blog Personal</title>
</head>
 
<body>
 
	<h1>Blog Personal</h1>
	
	<p>Bienvenido - <?php echo $arrayUser['user']; ?> - <a href="../index.php?salir=true">Salir</a> - <a href="../index.php">Home</a></p>	
	<ul>
	    <li><a href="categorias.php">Administrar categorias</a></li>
	    <li><a href="posts.php">Administrar posts</a></li>
	    <li><a href="comentarios.php">Administrar comentarios por post.</a></li>
	</ul>
</body>
</html>