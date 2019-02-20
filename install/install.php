<?php
	include_once("../config/site_version.php");
	if(isset($_POST['install'])){
		$host = $_POST['host'];
		$port = $_POST['port'];
		$db = $_POST['db'];
		$userDB = $_POST['userDB'];
		$passwordDB = $_POST['passwordDB'];		
		
		$linkDB = mysqli_connect($host , $userDB, $passwordDB, $db, $port) or die();
		
		if($linkDB == null){
			$mensaje = "<div class='errormsj'>Error en las credenciales, comprueba los datos</div>";
		} else {
			$fp = fopen("../conf/config.php", "w");
			$texto = "<?php\n".'$host='."'$host';\n".'$port='."$port;\n".'$userDB='.
			"'$userDB';\n".'$passwordDB='."'$passwordDB';\n".'$db='."'$db';\n?>";
			fwrite($fp, $texto);
			fclose($fp);
			
			$sql[]="create table if not exists verify_user (
					login VARCHAR(50) primary key,
					password VARCHAR(512) not null,
					role enum('admin','user'),
					code varchar(10) not null
					);";
						
			foreach($sql as $query){
				mysqli_query($linkDB, $query) or die("Error en consulta $consulta ".mysqli_error($linkDB));
			}
					
			mysqli_close($linkDB);
			header("Location:install.php?step=2");
		}
	}
?>

<!DOCTYPE html>
<head>
	<title>Quking | Instalación</title>
	<meta charset="utf-8"/>
	<link rel = "stylesheet" href = "../css/style.css">
</head>
<body>
	<h1 id="banner">Instalación de Quking</h1>
	
	<?php if(!isset($step)){ ?>
	<div class="form_data">
		<h3>Credenciales de la base de datos</h3>
		<form method="POST">
			HOST: <input type="text" name="host"/>
			Puerto: <input type = "text" name="port"/>
			Nombre BD: <input type="text" name = "db"/>
			Usuario SGBD: <input type="text" name="userDB"/>
			Password SGBD: <input type = "password" name = "passwordDB"/>
			<br>
			<input id="form_button" type="submit" name="install" value="INSTALAR (PASO 1)"/>
		</form>
	</div>
	<?php } ?>
	
	<?php if(isset($step)){ ?>
	<div class="form_data">
		<h3>Credenciales del administrador</h3>
		<form method="POST">
			Login admin: <input type="text" name="login" value="admin"/>
			Password admin: <input type="password" name="pass1"/>
			Repetir pass: <input type="password" name="pass2"/>
			<br>
			<input id="form_button" type="submit" name="install" value="INSTALAR (PASO 2)"/>
		</form>
	</div>
	<?php } ?>
	
	<?php if(isset($site_version)) echo "<div id='site_version'>$site_version</div>"; ?>
</body>
</html>