<?php
require('Upload.class.php');

if ((isset($_POST["submit"])) && (! empty($_FILES['foto']))){
    $upload = new Upload($_FILES['foto'], 1000, 800, "imagens/");
       echo $upload->salvar('055.650.509-36','cpf');
}


?>

<html>
	<head>
	<link href="../bootstrap/css/bootstrap.css" rel="stylesheet">
	</head>
	<form method='post' enctype='multipart/form-data'><br>
		<input type='file' name='foto' value='Cadastrar foto'>
		<input type='submit' name='submit' data-toggle="modal" data-target="#exampleModal">
	</form>
	
	</body>
</html>