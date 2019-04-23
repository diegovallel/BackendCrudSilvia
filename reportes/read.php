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

// CHECK GET ID PARAMETER OR NOT
if(isset($_GET['id']))
{
    //IF HAS ID PARAMETER
    $post_id = filter_var($_GET['id'], FILTER_VALIDATE_INT,[
        'options' => [
            'default' => 'all_reportes',
            'min_range' => 1
        ]
    ]);
}
else{
    $post_id = 'all_reportes';
}

// MAKE SQL QUERY
// IF GET POSTS ID, THEN SHOW POSTS BY ID OTHERWISE SHOW ALL POSTS
$sql = is_numeric($post_id) ? "SELECT * FROM `reportes` WHERE id='$post_id'" : "SELECT * FROM `reportes`";

$stmt = $conn->prepare($sql);

$stmt->execute();

//CHECK WHETHER THERE IS ANY POST IN OUR DATABASE
if($stmt->rowCount() > 0){
    // CREATE POSTS ARRAY
    $posts_array = [];

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        $post_data = [
            'id' => $row['id'],
            'idusuario' => $row['idusuario'],
            'foto' => $row['foto'],
            'fecha' => $row['fecha'],
            'direccion' => $row['direccion'],
            'latitud' => $row['latitud'],
            'longitud' => $row['longitud']
        ];
        // PUSH POST DATA IN OUR $posts_array ARRAY
        array_push($posts_array, $post_data);
    }
    //SHOW POST/POSTS IN JSON FORMAT
    echo json_encode($posts_array);


}
else{
    //IF THER IS NO POST IN OUR DATABASE
    echo json_encode(['message'=>'No post found']);
}
?>
