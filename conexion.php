<?php

function conect(){
	$db_con = mysql_pconnect(DB_SERVER,DB_USER,DB_PASS);
	if (!$db_con){
		echo "Error conectando a la base de datos."; 
		return false;
	}
	if (!mysql_select_db(DB_NAME, $db_con)) {
		echo  "Error seleccionando la base de datos.";
		return false;
	}

	return $db_con;
}

?>