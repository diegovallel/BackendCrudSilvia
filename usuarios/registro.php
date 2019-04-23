<?php
$response = array();
include '../database.php';
include 'funcionesusuarios.php';

//Get the input request parameters
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE); //convert JSON into array

//Check for Mandatory parameters
if(isset($input['fullname']) && isset($input['usuario']) && isset($input['contrasena']) && isset($input['correo'])){
	$username = $input['usuario'];
	$password = $input['contrasena'];
	$correo = $input['correo'];
  $fullname = $input['fullname'];

	//Check if user already exist
	if(!userExists($username)){

		//Get a unique Salt
		$salt = getSalt();

		//Generate a unique password Hash
		$passwordHash = password_hash(concatPasswordWithSalt($password,$salt),PASSWORD_DEFAULT);

		//Query to register new user
		$insertQuery  = "INSERT INTO usuarios(usuario, correo, contrasena, salt, fullname) VALUES (?,?,?,?,?)";
		if($stmt = $con->prepare($insertQuery)){
			$stmt->bind_param("ssss",$username,$correo,$passwordHash,$salt,$fullname);
			$stmt->execute();
			$response["status"] = 0;
			$response["message"] = "Usuario creado de manera exitosa.";
			$stmt->close();
		}
	}
	else{
		$response["status"] = 1;
		$response["message"] = "El usuario ya existe.";
	}
}
else{
	$response["status"] = 2;
	$response["message"] = "Missing mandatory parameters";
}
echo json_encode($response);
?>
