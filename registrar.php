<?php 

require_once 'config.php';
require_once 'conexion.php';

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


//si se envio el formulario
if (!empty($_POST['submit'])) {

	//cargamos las variables con los datos del form
	if (!empty($_POST['usuario']))
		$usuario = $_POST['usuario'];
	if (!empty($_POST['password']))
		$password = $_POST['password'];
	if (!empty($_POST['re-password']))
		$repassword = $_POST['re-password'];
	if (!empty($_POST['email']))
		$email = $_POST['email'];

	//si hay errores se obtienen
	if (empty($_POST['usuario']))
		$error['usuario'] = 'Es obligatorio completar el nombre del usuario';
	if (empty($_POST['password']))
		$error['password'] = 'Es obligatorio completar la contraseña';
	if (empty($_POST['email']))
		$error['email'] = 'Es obligatorio completar el email';
	if ($_POST['password'] != $_POST['re-password'])
		$error['re-password'] = 'La contraseña no coincide';

	//verifica que no exista usuario
	$busca_usuario = "SELECT user FROM user WHERE user='$usuario'";
	$busca_email = "SELECT email FROM user WHERE email='$email'";
	$resul_usuario = mysql_query($busca_usuario, $dbConect);
	$resul_email = mysql_query($busca_email, $dbConect);
	if (mysql_num_rows($resul_usuario) != 0) 
		$error['rep_usuario'] = 'Usuario existente';
	if (mysql_num_rows($resul_email) != 0) 
		$error['rep_email'] = 'Correo existente';

	//subida de imagen
	if (!empty($_FILES["uploadedimage"]["name"])) {
		$fileName = $_FILES["uploadedimage"]["name"];
		$tempName = $_FILES["uploadedimage"]["tmp_name"];
		$imgType = $_FILES["uploadedimage"]["type"];
		$ext = getImageExtension($imgType);
		$imageName = $usuario.$ext;
		$targetPath = "img/users/".$imageName;
		
	}

	//si no hay error procede con el registro
	if (empty($error)) {
		if (move_uploaded_file($tempName, $targetPath)) 
			$query = "INSERT INTO user (user,password,email,image) VALUES ('$usuario','".md5($password)."','$email','$targetPath')";	
		else
			$query = "INSERT INTO user (user,password,email) VALUES ('$usuario','".md5($password)."','$email')";	

		$resut = mysql_query($query,$dbConect);
		header('Location: index.php?registro=true');
		die();
	}

}

?>

<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Blog Personal</title>
</head>
 
<body>
 
<h1>Registro de usuario</h1>
 
 <?php if (!empty($error)) {
 	echo '<ul>';
 	foreach ($error as $message) {
 		echo '<li>'.$message.'</li>';
 	}
 	echo '</ul>';
 } ?>

<form action="registrar.php" method="post" enctype="multipart/form-data">
	<p>
		<label for="usuario">Nombre del Usuario</label>
		<br>
		<input name="usuario" type="text" value="<?php if(!empty($usuario)) echo $usuario; ?>" />
	</p>
	<p>
		<label for="password">Contraseña</label>
		<br>
		<input name="password" type="password" value="<?php if(!empty($password)) echo $password; ?>" />
	</p>
	<p>
		<label for="re-password">Repetir Contraseña</label>
		<br>
		<input name="re-password" type="password" value="<?php if(!empty($repassword)) echo $repassword; ?>" />
	</p>
	<p>
		<label for="email">E-mail</label>
		<br>
		<input name="email" type="text" value="<?php if(!empty($email)) echo $email; ?>" />
	</p>
	<p>
		<label for="uploadedimage">Imagen</label>
		<br>
		<input name="uploadedimage" type="file" />
	</p>
	<p>
		<input name="submit" type="submit" value="Registrarse" />
	</p>
</form>
 
</body>
</html>