<?php 

function isUser($user, $pass, $con){
	//virificar que las variables tengan algo
	if ( $user == '' || $pass == '' ) 
		return false;

	//buscamos datos en la bd
	$query = "SELECT id_user, user, password, type, image FROM user WHERE user = '$user'";
	$result = mysql_query($query,$con);
	$row = mysql_fetch_array($result);
	$pass_from_db = $row['password'];
	unset($query);

	//comprobamos que las contraseñas coincidan
	if ($pass_from_db == $pass) 
		return $row;
	else
		return false;
	
}

?>