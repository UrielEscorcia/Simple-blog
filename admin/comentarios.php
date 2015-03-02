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

//borramos un comentario
if (!empty($_GET['del'])) {
	$query = "DELETE FROM comment WHERE id_comment = {$_GET['del']}";
	$result = mysql_query($query, $dbConect);
	
	header('Location: comentarios.php?dele=true');
	die();
}

//aprobamos un comentario
if (!empty($_GET['apr'])) {
	$query = "UPDATE comment SET state='ready' WHERE id_comment = {$_GET['apr']}";
	$result = mysql_query($query, $dbConect);
	
	header('Location: comentarios.php?aprobar=true');
	die();
}

//obtenemos listado de comentarios sin aprobar
$arrayComentarios = array();
$query = "SELECT comment.id_comment, comment.comments, comment.id_post, user.user, post.title  
		FROM comment 
		INNER JOIN user ON comment.id_user = user.id_user 
		INNER JOIN post ON comment.id_post = post.id_post 
		WHERE comment.state = 'review' 
		ORDER BY comment.id_comment ASC";
$result = mysql_query($query,$dbConect);
while ($row = mysql_fetch_assoc($result)) {
	array_push($arrayComentarios, $row);
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
	<h2>Comentarios sin aprobar</h2>
	<?php
	if (!empty($_GET['aprobar'])) {
		echo '<div>El comentario fue aprobado.</div>';
	}elseif (!empty($_GET['dele'])) {
		echo '<div>El comentario fue eliminado con exito.</div>';
	}
	?>

	<?php if (!empty($arrayComentarios)) { ?>
		<div>
			<h3>Listado de comentarios</h3>
			<table>
				<tr>
					<th>id</th>
					<th>Comentario</th>
					<th></th>
				</tr>
				<?php
				foreach ($arrayComentarios as $cat) {
					echo '<tr>';
						echo '<th>'.$cat['id_comment'].'</th>';
						echo '<th>'.$cat['comments'].'<br /><i>Dijo <b>'.$cat['user'].'</b> en <a href="../onePost.php?idPost='.$cat['id_post'].'">'.$cat['title'].'</a></i></th>';
						echo '<th><a href="comentarios.php?apr='.$cat['id_comment'].'">Aprobar</a> - <a href="comentarios.php?del='.$cat['id_comment'].'">Borrar</a></th>';
					echo '</tr>';
				}
				
				?>
				
			</table>
		</div>
	<?php }	?>
	

</body>
</html>

