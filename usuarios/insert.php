<?php
// SET HEADER
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// INCLUDING DATABASE AND MAKING OBJECT
require '../database.php';
$db_connection = new Database();
$conn = $db_connection->dbConnection();
$random_salt_length = 32;

// GET DATA FORM REQUEST
$data = json_decode(file_get_contents("php://input"));

//CREATE MESSAGE ARRAY AND SET EMPTY
$msg['message'] = '';

// CHECK IF RECEIVED DATA FROM THE REQUEST
if(isset($data->fullname) && isset($data->usuario) && isset($data->contrasena) && isset($data->correo)){
    // CHECK DATA VALUE IS EMPTY OR NOT
    if(!empty($data->usuario) && !empty($data->usuario) && !empty($data->contrasena) && !empty($data->correo)){
        $query = "SELECT * FROM `usuarios` WHERE usuario = '$data->usuario' OR correo='$data->correo'";
        $stmt = $conn->prepare($query);
        $stmt->execute();

        if($stmt->rowCount() > 0){
          $userExists = false;
        }else{
            $userExists= true;
        }
       

        if($userExists == true){
            
          $salt = getSalt();
       
      		//Generate a unique password Hash
      		$passwordHash = password_hash(concatPasswordWithSalt($data->contrasena,$salt),PASSWORD_DEFAULT);

          $insert_query = "INSERT INTO `usuarios`(usuario, correo, contrasena, salt, fullname) VALUES(:usuario,:correo,:contrasena,:salt,:fullname)";

          $insert_stmt = $conn->prepare($insert_query);
          // DATA BINDING
          $insert_stmt->bindValue(':usuario', htmlspecialchars(strip_tags($data->usuario)),PDO::PARAM_STR);
          $insert_stmt->bindValue(':contrasena', htmlspecialchars(strip_tags($passwordHash)),PDO::PARAM_STR);
          $insert_stmt->bindValue(':correo', htmlspecialchars(strip_tags($data->correo)),PDO::PARAM_STR);
          $insert_stmt->bindValue(':salt', htmlspecialchars(strip_tags($salt)),PDO::PARAM_STR);
          $insert_stmt->bindValue(':fullname', htmlspecialchars(strip_tags($data->fullname)),PDO::PARAM_STR);

          if($insert_stmt->execute()){
              $msg["status"] = 0;
			$msg["message"] = "Usuario creado con exito";
          }else{
              $msg['message'] = 'Data not Inserted';
          }
        }else{
          $msg["status"] = 1;
      		$msg["message"] = "Este usuario ya existe";
        }

    }else{
        $msg["status"] = 2;
        $msg['message'] = 'Por favor llene todos los campos';
    }
}
else{
    $msg["status"] = 2;
    $msg['message'] = 'Por favor llene todos los campos';
}
//ECHO DATA IN JSON FORMAT
echo  json_encode($msg);

/**
* Creates a unique Salt for hashing the password
*
* @return
*/
function getSalt(){
	global $random_salt_length;
	return bin2hex(openssl_random_pseudo_bytes($random_salt_length));
}
/**
* Creates password hash using the Salt and the password
*
* @param $password
* @param $salt
*
* @return
*/
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
