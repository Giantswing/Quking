<?php
	include_once("../config/site_version.php");
	
	if(isset($_POST['install'])){
		$host = $_POST['host'];
		$port = $_POST['port'];
		$db = $_POST['db'];
		$userDB = $_POST['userDB'];
		$passwordDB = $_POST['passwordDB'];		
		
		$linkDB = mysqli_connect($host , $userDB, $passwordDB, $db, $port);
		
		if($linkDB == null){
			$errormsj = "Error en las credenciales, comprueba los datos";
		} else {
			$fp = fopen("../config/config.php", "w");
			$txt = "<?php\n".'$host='."'$host';\n".'$port='."$port;\n".'$userDB='.
			"'$userDB';\n".'$passwordDB='."'$passwordDB';\n".'$db='."'$db';\n?>";
			fwrite($fp, $txt);
			fclose($fp);
			
			$sql[]="create table if not exists verify_users (
					login VARCHAR(50) primary key,
					password VARCHAR(512) not null,
					role enum('admin','user'),
					code varchar(10) not null
					);";
					
			$sql[]="create table if not exists users (
					login VARCHAR(50) primary key,
					password VARCHAR(512) not null,
					role enum('admin','user')
					);";
						
			foreach($sql as $query){
				//mysqli_query($linkDB, $query) or die("Error en consulta $consulta ".mysqli_error($linkDB));
				mysqli_query($linkDB, $query);
			}
					
			mysqli_close($linkDB);
			header("Location:install.php?step=2");
		}
	}
	
	if(isset($_POST['install_admin'])){
			include_once("../config/config.php");
		
			$linkDB = mysqli_connect($host , $userDB, $passwordDB, $db, $port) or die();
			$ok=true;
			$admin_login = $_POST['admin_login'];
			$admin_pass1 = $_POST['admin_pass1'];
			$admin_pass2 = $_POST['admin_pass2'];
			
			if(!preg_match('/[A-Z,a-z,0-9,.]{4,16}$/', $admin_login)){
				$ok=false;
				$errormsj="El login debe tener entre 4 y 16 caracteres";
			}
			
			
			if(strlen($admin_pass1)<5){
				$ok=false;
				$errormsj = "La contraseña debe ser mayor que 5 caracteres";
			}
			
			
			if($admin_pass1 != $admin_pass2){
				$ok=false;
				$errormsj = "Las contraseñas no coinciden";
			}
			
			if($ok){
				$insert_query = "insert into users values('$admin_login', password('$admin_pass1'), 'admin');";
				mysqli_query($linkDB, $insert_query);
				mysqli_close();
				header("Location:install.php?step=3");
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
	
	<?php if(!isset($_GET['step'])){ ?>
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
	
	<?php if(isset($_GET['step']) && $_GET['step']==2){ ?>
	<div class="form_data">
		<h3>Credenciales del administrador</h3>
		<form method="POST">
			Login admin: <input type="text" name="admin_login" value="admin"/>
			Password admin: <input type="password" name="admin_pass1"/>
			Repetir pass: <input type="password" name="admin_pass2"/>
			<br>
			<input id="form_button" type="submit" name="install_admin" value="INSTALAR (PASO 2)"/>
		</form>
	</div>
	<?php } ?>
	
	<?php if(isset($_GET['step']) && $_GET['step']==3){ 
		$okmsj="Usuario administrador creado con éxito, borre la carpeta /Instalar y comience a usar la aplicación";
	} ?>
	
	
	<br><br>
	<?php if(isset($errormsj)) echo "<div class='errormsj'>$errormsj</div>"; ?>
	<?php if(isset($okmsj)) echo "<div class='okmsj'>$okmsj</div>"; ?>
	<?php if(isset($site_version)) echo "<div id='site_version'>$site_version</div>"; ?>
</body>
</html>