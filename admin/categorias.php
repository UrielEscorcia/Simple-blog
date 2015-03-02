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

//borramos una categoria
if (!empty($_GET['del'])) {
	$query = "DELETE FROM category WHERE id_category = {$_GET['del']}";
	$result = mysql_query($query, $dbConect);
	
	header('Location: categorias.php?dele=true');
	die();
}

//agregar categorias a la base de datos
//si se envio el formulario
if (!empty($_POST['submit'])) {
	
	//definimos variables
	if (!empty($_POST['nombre'])) 
		$nombre = $_POST['nombre'];
	
	//completamos variable de error si es necesario
	if (empty($_POST['nombre'])) 
		$error['nombre'] = 'Es obligatorio el nombre de la categoria';

	//si no ha error registramos categoria
	if (empty($error)) {
		$query = "INSERT INTO category (value) VALUES ('$nombre')";
		$result = mysql_query($query, $dbConect);
		header('Location: categorias.php?add=true');
		die();
	}
}

//actualizamos una categoria
if (!empty($_POST['submitEdit'])) {

	//definimos variables
	if (!empty($_POST['nombre'])) 
		$nombre = $_POST['nombre'];
	if (!empty($_POST['idCategoria'])) 
		$idCategoria = $_POST['idCategoria'];

	//completamos variable de error si es necesario
	if (empty($nombre)) 
		$error['nombre'] = 'Es obligatorio el nombre de la categoria';
	if (empty($idCategoria)) 
		$error['idCategoria'] = 'Falta ID de categoria';
	//si no ha error actualizamos
	if (empty($error)) {
		$query = "UPDATE category SET value = '$nombre' WHERE id_category = $idCategoria";
		$result = mysql_query($query, $dbConect);
		header('Location: categorias.php?edit=true');
		die();
	}
	
}

//obtenemos listado de categorias
$arrayCategories = array();
$query = "SELECT * FROM category ORDER BY value ASC";
$result = mysql_query($query, $dbConect);
while ($row = mysql_fetch_assoc($result)) {
	array_push($arrayCategories, $row);
}

//si tenemos una categoria a editar
if (!empty($_GET['id'])) {
	//obtenemos datos de la ategoria
	$query = "SELECT * FROM category WHERE id_category={$_GET['id']}";
	$result = mysql_query($query, $dbConect);
	$rowCategory = mysql_fetch_assoc($result);
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
	<p>Bienvenido - <?php echo $arrayUser['user']; ?> -<a href="index.php">Panel de Control</a> - <a href="../index.php?salir=true">Salir</a></p>	

	<h2>Categorias</h2>
	<?php
	if (!empty($_GET['add'])) {
		echo '<div>La categoria se agrego con exito.</div>';
	}elseif (!empty($_GET['dele'])) {
		echo '<div>La categoria fue eliminada con exito.</div>';
	}elseif (!empty($_GET['edit'])) {
		echo '<div>La categoria se actualizó con exito.</div>';
	}
	?>

	<?php if (!empty($arrayCategories)) { ?>
		<div>
			<h3>Listado de categorias</h3>
			<table>
				<tr>
					<th>id</th>
					<th>Categoria</th>
					<th></th>
				</tr>
				<?php
				foreach ($arrayCategories as $cat) {
					echo '<tr>';
						echo '<th>'.$cat['id_category'].'</th>';
						echo '<th>'.$cat['value'].'</th>';
						echo '<th><a href="categorias.php?id='.$cat['id_category'].'">Editar</a> - <a href="categorias.php?del='.$cat['id_category'].'">Borrar</a></th>';
					echo '</tr>';
				}
				
				?>
				
			</table>
		</div>
	<?php }	?>

	<?php if (empty($_GET['id'])) { ?>
		<div>
			<h3>Agragar Nueva Categoria</h3>
			<?php 
				if (!empty($error)) {
				 	echo '<ul>';
				 	foreach ($error as $message) {
				 		echo '<li>'.$message.'</li>';
				 	}
				 	echo '</ul>';
				} 
			?>
			<form action="categorias.php" method="post">
				<p>
					<label for="nombre">Nombre de la categoria</label>
					<br>
					<input name="nombre" type="text" value="" />
				</p>
				<p>
					<input name="submit" type="submit" value="Agregar" />
				</p>
			</form>
		</div>
	<?php }	?>

	<?php if (!empty($_GET['id'])) { ?>
		<div>
			<h3>Editar Categoria</h3>
			<?php 
				if (!empty($error)) {
				 	echo '<ul>';
				 	foreach ($error as $message) {
				 		echo '<li>'.$message.'</li>';
				 	}
				 	echo '</ul>';
				} 
			?>
			<form action="categorias.php" method="post">
				<p>
					<label for="nombre">Nombre de la categoria</label>
					<br>
					<input name="nombre" type="text" value="<?php echo $rowCategory['value']; ?>" />
				</p>
				<p>
					<input name="idCategoria" type="hidden" value="<?php echo $rowCategory['id_category']; ?>" />
					<input name="submitEdit" type="submit" value="Actualizar" />
				</p>
			</form>
			<a href="categorias.php">Agregar nueva</a>
		</div>
	<?php }	?>

</body>
</html>

