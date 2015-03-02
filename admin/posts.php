<?php

//iniciamos sesiones
session_start();

//archivos necesarios
require_once '../config.php';
require_once '../conexion.php';
require_once '../isUser.php';

//variable de coneccion con db
$dbConect = conect();

//funcion para retornar el tipo de imagen
function getImageExtension($imagetype){
    if(empty($imagetype))
    	return false;
    switch($imagetype)
    {
        case 'image/bmp': return '.bmp';
        case 'image/gif': return '.gif';
        case 'image/jpeg': return '.jpg';
        case 'image/png': return '.png';
        default: return false;
    }
}

//verificamos que no este conectado el usuario
if (!empty($_SESSION['user']) && !empty($_SESSION['password'])) {
	$arrayUser = isUser($_SESSION['user'], $_SESSION['password'],$dbConect);
}

//verificamos que sea un daministrador
if (empty($arrayUser) || $arrayUser['type'] != 'admin') {
	header('Location: ../index.php');
	die();
}

//borramos un post
if (!empty($_GET['del'])) {
	$query = "DELETE FROM post WHERE id_post = {$_GET['del']}";
	$result = mysql_query($query, $dbConect);
	
	header('Location: posts.php?dele=true');
	die();
}

//agregamos un post a la bd
//si se envio el formulario
if (!empty($_POST['submit'])) {
	
	//definimos variables
	if (!empty($_POST['titulo']))
		$titulo = $_POST['titulo'];
	if (!empty($_POST['copete']))
		$copete = $_POST['copete'];
	if (!empty($_POST['cuerpo']))
		$cuerpo = $_POST['cuerpo'];
	if (!empty($_POST['idCategoria']))
		$idCategoria = $_POST['idCategoria'];
	if (!empty($_POST['fPublicacion']))
		$fPublicacion = $_POST['fPublicacion'];

	//llenamos la variable error si es necesario
	if (empty($titulo))
		$error['titulo'] = 'Es obligatorio completar el campo titulo';
	if (empty($copete))
		$error['copete'] = 'Es obligatorio completar el campo encabezado';
	if (empty($cuerpo))
		$error['cuerpo'] = 'Es obligatorio completar el campo del post';
	if (empty($idCategoria))
		$error['idCategoria'] = 'Es obligatorio seleccionar una categoria';

	//subida de imagen
	if (!empty($_FILES["uploadedimage"]["name"])) {

		$fileName = $_FILES["uploadedimage"]["name"];
		$tempName = $_FILES["uploadedimage"]["tmp_name"];
		$imgType = $_FILES["uploadedimage"]["type"];
		$ext = getImageExtension($imgType);
		$imageName = $_FILES["uploadedimage"]["name"];
		$targetPath = "../img/posts/".$imageName;
		$targetPathBD = "img/posts/".$imageName;
		if (move_uploaded_file($tempName, $targetPath)){
			echo "imagen subida";
		}else{
			$error['image'] = 'Falta imagen principal';
		}
			
		
	}

	//si no hay error registramos el post
	if (empty($error)) {

		$fCreacion = date("Y-m-d H:i:s");
		$fModificacion = date("Y-m-d H:i:s");
		if (empty($fPublicacion))
			$fPublicacion = date("Y-m-d H:i:s");

		$idUsuario = $arrayUser['id_user'];
		$query = "INSERT INTO post (title, header,image,body,id_user,id_category,publish,creation,changes) VALUES ('$titulo','$copete','$targetPathBD','$cuerpo','$idUsuario','$idCategoria','$fPublicacion','$fCreacion','$fModificacion')";
		$result = mysql_query($query,$dbConect);
		header('Location: posts.php?add=true');
		die();
	}
}

//si se envio el formulario de edicion
if (!empty($_POST['submitEdit'])) {
	
	//definimos variables
	if (!empty($_POST['idPost']))
		$idPost = $_POST['idPost'];
	if (!empty($_POST['titulo']))
		$titulo = $_POST['titulo'];
	if (!empty($_POST['copete']))
		$copete = $_POST['copete'];
	if (!empty($_POST['cuerpo']))
		$cuerpo = $_POST['cuerpo'];
	if (!empty($_POST['idCategoria']))
		$idCategoria = $_POST['idCategoria'];
	if (!empty($_POST['fPublicacion']))
		$fPublicacion = $_POST['fPublicacion'];

	//llenamos la variable error si es necesario
	if (empty($idPost))
		$error['idPost'] = 'Es obligatorio completar el campo id';
	if (empty($titulo))
		$error['titulo'] = 'Es obligatorio completar el campo titulo';
	if (empty($copete))
		$error['copete'] = 'Es obligatorio completar el campo encabezado';
	if (empty($cuerpo))
		$error['cuerpo'] = 'Es obligatorio completar el campo del post';
	if (empty($idCategoria))
		$error['idCategoria'] = 'Es obligatorio seleccionar una categoria';

	//subida de imagen
	$updateImage = false;
	if (!empty($_FILES["uploadedimage"]["name"])) {
		$fileName = $_FILES["uploadedimage"]["name"];
		$tempName = $_FILES["uploadedimage"]["tmp_name"];
		$imgType = $_FILES["uploadedimage"]["type"];
		$ext = getImageExtension($imgType);
		$imageName = $_FILES["uploadedimage"]["name"];
		$targetPath = "../img/posts/".$imageName;
		$targetPathBD = "img/posts/".$imageName;
		if (move_uploaded_file($tempName, $targetPath)){
			echo "imagen subida";
		}else{
			$error['image'] = 'Falta imagen principal';

		$updateImage = true;
		}
		
	}


	//si no hay error registramos el post
	if (empty($error)) {
		//actualizamos fecha de modificacion
		$fModificacion = date("Y-m-d H:i:s");
		if (empty($fPublicacion))
			$fPublicacion = date("Y-m-d H:i:s");

		$idUsuario = $arrayUser['id_user'];
		if ($uploadedimage) 
			$query = "UPDATE post SET title='$titulo', header='$copete', image='$targetPathBD', body='$cuerpo',id_user='$idUsuario',id_category='$idCategoria',publish='$fPublicacion',changes='$fModificacion' WHERE id_post=$idPost";
		else
			$query = "UPDATE post SET title='$titulo', header='$copete', body='$cuerpo',id_user='$idUsuario',id_category='$idCategoria',publish='$fPublicacion',changes='$fModificacion' WHERE id_post=$idPost";
		$result = mysql_query($query,$dbConect);
		header('Location: posts.php?edit=true');
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

//obtenemos listado de posts
$arrayPosts = array();
$query = "SELECT id_post, title FROM post ORDER BY id_post ASC";
$result = mysql_query($query, $dbConect);
while ($row = mysql_fetch_assoc($result)) {
	array_push($arrayPosts, $row);
}

//si tenemos un post a editar
if (!empty($_GET['id'])) {
	//obtenemos datos del post
	$query = "SELECT id_post, title, header,body,id_category,publish FROM post WHERE id_post={$_GET['id']}";
	$result = mysql_query($query, $dbConect);
	$rowPost = mysql_fetch_assoc($result);
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

	<h2>Posts</h2>
	<?php
	if (!empty($_GET['add'])) {
		echo '<div>El Post se agrego con exito.</div>';
	}elseif (!empty($_GET['dele'])) {
		echo '<div>El Post fue eliminado con exito.</div>';
	}elseif (!empty($_GET['edit'])) {
		echo '<div>El Post se actualizó con exito.</div>';
	}
	?>

	<?php if (!empty($arrayPosts)) { ?>
		<div>
			<h3>Listado de Posts</h3>
			<table>
				<tr>
					<th>id</th>
					<th>Titulo</th>
					<th></th>
				</tr>
				<?php
				foreach ($arrayPosts as $noticia) {
					echo '<tr>';
						echo '<th>'.$noticia['id_post'].'</th>';
						echo '<th>'.$noticia['title'].'</th>';
						echo '<th><a href="posts.php?id='.$noticia['id_post'].'">Editar</a> - <a href="posts.php?del='.$noticia['id_post'].'">Borrar</a></th>';
					echo '</tr>';
				}
				
				?>
				
			</table>
		</div>
	<?php }	?>

	<?php if (empty($_GET['id'])) { ?>
		<div>
			<h3>Agragar Nuevo Post</h3>
			<?php 
				if (!empty($error)) {
				 	echo '<ul>';
				 	foreach ($error as $message) {
				 		echo '<li>'.$message.'</li>';
				 	}
				 	echo '</ul>';
				} 
			?>
			<form action="posts.php" method="post" enctype="multipart/form-data">
				<p>
					<label for="titulo">Titulo</label>
					<br>
					<input name="titulo" type="text" value="" />
				</p>
				<p>
					<label for="idCategoria">Categoria</label>
					<br>
					<select name="idCategoria">
						<option value ="">Seleccione una categoria</option>
						<?php
						foreach ($arrayCategories as $category) {
							echo '<option value ="'.$category['id_category'].'">'.$category['value'].'</option>';
						}
						?>
					</select>
				</p>
				<p>
					<label for="fPublicacion">Fecha de publicacion (aaaa-mm-dd hh:mm:ss) Ej: 2008-10-29 17:20:00 </label>
					<br>
					<input name="fPublicacion" type="text" value="" />
				</p>
				<p>
					<label for="uploadedimage">Imagen</label>
					<br>
					<input name="uploadedimage" type="file" />
				</p>
				<p>
                    <label for="copete">Copete</label>
                    <br>
                    <textarea rows="5" cols="50" name="copete"></textarea>
                </p>
                <p>
                    <label for="cuerpo">Cuerpo</label>
                    <br>
                    <textarea rows="10" cols="50" name="cuerpo"></textarea>
                </p>
				<p>
					<input name="submit" type="submit" value="Agregar" />
				</p>
			</form>
		</div>
	<?php }	?>

	<?php if (!empty($_GET['id'])) { ?>
		<div>
			<h3>Editar Post</h3>
			<?php 
				if (!empty($error)) {
				 	echo '<ul>';
				 	foreach ($error as $message) {
				 		echo '<li>'.$message.'</li>';
				 	}
				 	echo '</ul>';
				} 
			?>
			<form action="posts.php" method="post" enctype="multipart/form-data">
				<p>
					<label for="titulo">Titulo</label>
					<br>
					<input name="titulo" type="text" value="<?php echo $rowPost['title']; ?>" />
				</p>
				<p>
					<label for="idCategoria">Categoria</label>
					<br>
					<select name="idCategoria">
						<option value ="">Seleccione una categoria</option>
						<?php
						foreach ($arrayCategories as $category) {
							if($category['id_category']==$rowPost['id_category'])
								echo '<option value ="'.$category['id_category'].'" selected="selected>'.$category['value'].'</option>';	
							echo '<option value ="'.$category['id_category'].'">'.$category['value'].'</option>';
						}
						?>
					</select>
				</p>
				<p>
					<label for="fPublicacion">Fecha de publicacion (aaaa-mm-dd hh:mm:ss) Ej: 2008-10-29 17:20:00 </label>
					<br>
					<input name="fPublicacion" type="text" value="<?php echo $rowPost['publish']; ?>" />
				</p>
				<p>
					<label for="uploadedimage">Imagen</label>
					<br>
					<input name="uploadedimage" type="file" />
				</p>
				<p>
                    <label for="copete">Copete</label>
                    <br>
                    <textarea rows="5" cols="50" name="copete"><?php echo $rowPost['header']; ?></textarea>
                </p>
                <p>
                    <label for="cuerpo">Cuerpo</label>
                    <br>
                    <textarea rows="10" cols="50" name="cuerpo"><?php echo $rowPost['body']; ?></textarea>
                </p>
				<p>
					<input name="idPost" type="hidden" value="<?php echo $rowPost['id_post']; ?>" />
					<input name="submitEdit" type="submit" value="Actualizar" />
				</p>
			</form>
			<a href="posts.php">Agregar nueva</a>
		</div>
	<?php }	?>

</body>
</html>
