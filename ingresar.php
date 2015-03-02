<?php
//iniciamos sesiones
session_start();

//archivos necesarios
require_once 'config.php';
require_once 'conexion.php';
require_once 'isUser.php';

//variable de coneccion con db
$dbConect = conect();

//verificar q no este conectado el usuario
if (!empty($_SESSION['user']) && !empty($_SESSION['password'])) {
	if (isUser($_SESSION['user'], $_SESSION['password'],$dbConect)) {
		header('Location: index.php');
		die();
	}
}

//si el formulario se envio
if (!empty($_POST['submit'])) {

	//definimos variables
	if (!empty($_POST['usuario']))
		$usuario = $_POST['usuario'];
	if (!empty($_POST['password']))
		$password = $_POST['password'];

	//completamos variable de error
	if (empty($_POST['usuario']))
		$error['usuario'] = 'Es obligatorio completar el nombre del usuario';
	if (empty($_POST['password']))
		$error['password'] = 'Es obligatorio completar este campo';

	//si no hay error ingresamos al usuario
	if (empty($error)) {
		
		//verificamos datos ingresados con la base de datos
		if ($arrayUser = isUser($usuario,md5($password),$dbConect)) {
			
			//definimos sesiones
			$_SESSION['user'] = $arrayUser['user'];
			$_SESSION['password'] = $arrayUser['password'];
			header('Location: index.php');
			die();
		}
		else{
			$error['noExiste'] = 'El nombre de usuario o contraseña no coinciden';
		}
	}
}


?>

<h1>Inicio de sesión</h1>
<?php if (!empty($error)) {
 	echo '<ul>';
 	foreach ($error as $message) {
 		echo '<li>'.$message.'</li>';
 	}
 	echo '</ul>';
 } ?>
<form action="ingresar.php" method="post">
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
		<input name="submit" type="submit" value="Ingresar" />
	</p>
</form>