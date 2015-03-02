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

//obtenemos listado de posts
$arrayPosts = array();
$query = "SELECT id_post, title, header,image FROM post WHERE publish < '".date('Y-m-d H:i:s')."' ORDER BY publish DESC";
$result = mysql_query($query, $dbConect);
while ($row = mysql_fetch_assoc($result)) {
	array_push($arrayPosts, $row);
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

				<?php if ( !empty($_GET['registro']) ) { ?>
					<div>El registro ha sido exitoso.</div>
				<?php } ?>

				<!-- One -->
					<section id="one">
						<header class="major">
							<h2>Imagine and do it... everything... just develop it </h2>
						</header>
						<p>Accumsan orci faucibus id eu lorem semper. Eu ac iaculis ac nunc nisi lorem vulputate lorem neque cubilia ac in adipiscing in curae lobortis tortor primis integer massa adipiscing id nisi accumsan pellentesque commodo blandit enim arcu non at amet id arcu magna. Accumsan orci faucibus id eu lorem semper nunc nisi lorem vulputate lorem neque cubilia.</p>
						<ul class="actions">
							<li><a href="#" class="button">See More</a></li>
						</ul>
					</section>

				<!-- Two -->
				<?php if(!empty($arrayPosts)) { ?>
					<section id="two">
						<h2>Posts</h2>
						<div class="row">
							<?php  foreach ($arrayPosts as $post) { ?>
								<article class="6u 12u$(3) work-item">
									<a href="onePost.php?idPost=<?php echo $post['id_post']; ?>" class="image fit thumb"><img src="<?php echo $post['image']; ?>" alt="" /></a>
									<h3><a href="onePost.php?idPost=<?php echo $post['id_post']; ?>"><?php echo $post['title']; ?></a></h3>
									<p><?php echo $post['header']; ?></p>
								</article>
							<?php  } ?>
						</div>
					</section>
				<?php  } ?>

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