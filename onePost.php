<?php
//iniciamos sesiones
session_start();

//archivos necesarios
require_once 'config.php';
require_once 'conexion.php';
require_once 'isUser.php';

//variable de coneccion con db
$dbConect = conect();

//si usuario quiere deslogear
if (!empty($_GET['salir'])) {
	//borramos y destruimos tdo tipo de sesion
	session_unset();
	session_destroy();

}

//verificamos que no este conectado el usuario
if (!empty($_SESSION['user']) && !empty($_SESSION['password'])) {
	$arrayUser = isUser($_SESSION['user'], $_SESSION['password'],$dbConect);
}

//agregamos comentarios a la base de datos
//si se envio el formulario
if (!empty($_POST['submit'])) {
	
	//completamos variables
	if (!empty($_POST['comentario'])) 
		$comentario = $_POST['comentario'];
	if (!empty($_GET['idPost'])) 
		$idPost = $_GET['idPost'];
	if (!empty($arrayUser['id_user'])) 
		$idUser = $arrayUser['id_user'];

	//completamos variable de error si es necesario
	if (empty($comentario))
		$error['comentario'] = true;
	if (empty($idPost))
		$error['idPost'] = true;
	if (empty($idUser))
		$error['idUser'] = true;


	//si no hay errores registramos el comentario
	if (empty($error)) {
		//insertar los datos en la bd

		$query = "INSERT INTO comment (comments,id_user,id_post) VALUES ('$comentario','$idUser','$idPost')";
		$result = mysql_query($query,$dbConect);
		header('Location: onePost.php?idPost='.$idPost);
		die();
	}

}

//traemos el contenido del post
$queryPost = "SELECT post.id_post, post.title, post.header,post.image, post.body, category.value as category, user.user FROM post INNER JOIN category ON category.id_category = post.id_category INNER JOIN user ON user.id_user = post.id_user WHERE post.id_post = ".$_GET['idPost']." LIMIT 1";
$resultPost = mysql_query($queryPost,$dbConect);
$noticia = mysql_fetch_assoc($resultPost);

//obtenemos los comentarios aprobados
$arrayComentarios = array();
$queryComent = "SELECT comment.id_comment, comment.comments,comment.creation, user.user, user.image
			FROM comment 
			INNER JOIN user ON comment.id_user = user.id_user 
			WHERE comment.state = 'ready' AND comment.id_post = " . $_GET['idPost'] . " 
			ORDER BY comment.id_comment DESC";
$resultComent = mysql_query($queryComent,$dbConect);
while ($row = mysql_fetch_assoc($resultComent)) {
	array_push($arrayComentarios, $row);
}

?>

<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Urisito</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<script src="js/jquery.min.js"></script>
		<script src="js/jquery.poptrox.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/init.js"></script>
		<noscript>
			<link rel="stylesheet" href="css/skel.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-xlarge.css" />
		</noscript>
	</head>
 
	<body>
	<!-- Header -->
			<header id="header">
				<?php if ( empty($arrayUser['user']) ) { ?>
					<h1 class="login">
						<div class="user">
							<a href="ingresar.php">Log in</a>
							<a href="registrar.php">Sign in</a>
						</div>
					</h1>
				<?php } else { ?>
					<h1 class="login">
						<div class="user">
						<?php 
						if (!empty($arrayUser['image'])) 
							echo '<img src="'.$arrayUser['image'].'" width="20px" height="20px" />';
						else
							echo '<img src="img/users/avatar.jpg" width="20px" height="20px" />';

						?>
						<strong><?php echo $arrayUser['user']; ?></strong><a href="index.php?salir=true">Salir</a>
					
						<?php if ( $arrayUser['type'] == 'admin') { ?>
							
						    <a href="admin/index.php">Admin</a>
							
						<?php } ?>
						</div>
					</h1>
				<?php } ?>
				<a href="index.php" class="image avatar"><img src="img/team-01.png" alt="" /></a>
				<h1><strong>Urisito</strong></h1>
				<h1>Software Developer - Mobile and Gaming Apps. Web, iOS and Android Development.</h1>
			</header>

	<!-- Main -->
			<div id="main">

				<!-- One -->
					<section id="one">
						<div class="posted">
							<header class="major">
								<h2><?php echo $noticia['title']; ?></h2>
							</header>
							<div class="row">
								<div class="8u 12u$(2)">
									<img class="imgPost" src="<?php echo $noticia['image']; ?>"  />
								</div>
								<div class="4u$ 12u$(2)">
									<p class="date">Posted by <b><?php echo $noticia['user']; ?></b> on <i><?php echo $noticia['category']; ?></i></p>
								</div>
							</div>
							<div class="bodyPost">
								<?php echo $noticia['body']; ?>
							</div>
						</div>
					</section>

				
				<!-- Three -->
				<?php if(!empty($arrayComentarios)) { ?>
					<section id="three">
						<div class="posted">
							<h3>Comments</h3>
							<?php  foreach ($arrayComentarios as $commentario) { ?>
									<div class="row comment">
										<div class="2u 12u$(2)">
											<?php 
											if (!empty($commentario['image'])) 
												echo '<img src="'.$commentario['image'].'" width="40px" height="40px" />';
											else
												echo '<img src="img/users/avatar.jpg" width="40px" height="40px" />';

											?>
										</div>
										<div class="10u$ 12u$(2)">
												<?php  echo '<h3>'.$commentario['user'].'<i>'.$commentario['creation'].'</i></h3>'; ?>
												<div class="bodyC">
													<p><?php  echo $commentario['comments']; ?></p>
												</div>
										</div>
									</div>
								
							<?php  } ?>
						</div>

					</section>
				<?php }  ?>

				<!-- Form comments -->
					<section id="three">
						<div class="posted">
							<h2>Leave a Reply</h2>
							<?php if (!empty($arrayUser)) { ?>
							<div class="formComment">
								<form action="onePost.php?idPost=<?php echo $_GET['idPost']; ?>" method="post">
									<p>
										<textarea rows="6" cols="50" name="comentario" placeholder="Message"></textarea>
									</p>
									<p>
										<input name="submit" type="submit" value="Post Comment" />
									</p>
								</form>
							</div>
							<?php } else { ?>
								<p>To comment you must be a registered user.</p>
								<ul class="actions small">
									<li><a href="ingresar.php" class="button special small">Log in</a></li>
									<li><a href="registrar.php" class="button small">Sign in</a></li>
								</ul>
								
								
							<?php } ?>	 
						</div>
							
					</section>

			</div>

			<!-- Footer -->
					<footer id="footer">
						<ul class="icons">
							<li><a href="#" class="icon fa-twitter"><span class="label">Twitter</span></a></li>
							<li><a href="#" class="icon fa-facebook"><span class="label">Facebook</span></a></li>
							<li><a href="#" class="icon fa-github"><span class="label">Github</span></a></li>
							<li><a href="#" class="icon fa-envelope-o"><span class="label">Email</span></a></li>
						</ul>
						
					</footer>

	</body>
</html>