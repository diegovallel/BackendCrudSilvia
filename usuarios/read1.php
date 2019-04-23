<?php
// SET HEADER
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

// INCLUDING DATABASE AND MAKING OBJECT
require '../database.php';
$db_connection = new Database();
$conn = $db_connection->dbConnection();
$data = json_decode(file_get_contents("php://input"));

// CHECK GET ID PARAMETER OR NOT
if($_GET['correo'])
{
    //IF HAS ID PARAMETER
    $post_correo = $_GET['correo'];
    $sql = "SELECT * FROM `usuarios` WHERE correo='$post_correo'";

    $stmt = $conn->prepare($sql);

    $stmt->execute();

    // MAKE SQL QUERY
    // IF GET POSTS ID, THEN SHOW POSTS BY ID OTHERWISE SHOW ALL POSTS


    //CHECK WHETHER THERE IS ANY POST IN OUR DATABASE
    if($stmt->rowCount() > 0){
        // CREATE POSTS ARRAY
        $posts_array = [];

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

            $post_data = [
                'id' => $row['id'],
                'usuario' => $row['usuario'],
                'contrasena' => $row['contrasena'],
                'correo' => $row['correo'],
                'genero' => $row['genero'],
                'fecha_nacimiento' => $row['fecha_nacimiento'],
                'escolaridad' => $row['escolaridad'],
                'etnia' => $row['etnia'],
                'reportes' => $row['reportes'],
                'fullname' => $row['fullname']
            ];
            // PUSH POST DATA IN OUR $posts_array ARRAY
            array_push($posts_array, $post_data);
        }
        $obj->employees = $posts_array;
        //SHOW POST/POSTS IN JSON FORMAT
        echo json_encode($obj);


    }
    else{
        //IF THER IS NO POST IN OUR DATABASE
        echo json_encode(['message'=>'No post found']);
    }
}else{
  echo json_encode(['message'=>'No post found']);
}


?>
