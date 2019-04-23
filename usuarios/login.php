<?php
$response = array();
require '../database.php';
$db_connection = new Database();
$conn = $db_connection->dbConnection();

$random_salt_length = 32;

//Get the input request parameters
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE); //convert JSON into array

//Check for Mandatory parameters
if(isset($input['correo']) && isset($input['contrasena'])){
	$correo = $input['correo'];
	$password = $input['contrasena'];
	$query    = "SELECT * FROM `usuarios` WHERE correo='$correo'";

	if($stmt = $conn->prepare($query)){

		$stmt->execute();
       
    if($stmt->rowCount() > 0){
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $salt = $row['salt'];
            $fullName = $row['fullname'];
            $contrasena = $row['contrasena'];
            
        }
    
      if(isset($contrasena)){
         
        
  			//Validate the password
  			if(password_verify(concatPasswordWithSalt($password,$salt),$contrasena)){
  				$response["status"] = 0;
  				$response["message"] = "Login successful";
  				$response["full_name"] = $fullName;
  			}
  			else{
  				$response["status"] = 1;
  				$response["message"] = "Correo o clave invalida.";
  			}
  		}
  		else{
  			$response["status"] = 1;
  			$response["message"] = "Correo o clave invalida.";
  		}
    }else{
      $response["status"] = 1;
      $response["message"] = "El usuario no existe";
    }
	}
}
else{
	$response["status"] = 2;
	$response["message"] = "Missing mandatory parameters";
}
//Display the JSON response
echo json_encode($response);

function concatPasswordWithSalt($password,$salt){
	global $random_salt_length;
	if($random_salt_length % 2 == 0){
		$mid = $random_salt_length / 2;
	}
	else{
		$mid = ($random_salt_length - 1) / 2;
	}

	return
	substr($salt,0,$mid - 1).$password.substr($salt,$mid,$random_salt_length - 1);
}
?>
