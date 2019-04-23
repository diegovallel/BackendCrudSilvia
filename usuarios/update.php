<?php
// SET HEADER
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: PUT");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// INCLUDING DATABASE AND MAKING OBJECT
require '../database.php';
$db_connection = new Database();
$conn = $db_connection->dbConnection();

// GET DATA FORM REQUEST
$data = json_decode(file_get_contents("php://input"));

//CHECKING, IF ID AVAILABLE ON $data
if(isset($data->id)){

    $msg['message'] = '';
    $usuario_id = $data->id;

    //GET POST BY ID FROM DATABASE
    $get_post = "SELECT * FROM `usuarios` WHERE id=:usuario_id";
    $get_stmt = $conn->prepare($get_post);
    $get_stmt->bindValue(':usuario_id', $usuario_id,PDO::PARAM_INT);
    $get_stmt->execute();


    //CHECK WHETHER THERE IS ANY POST IN OUR DATABASE
    if($get_stmt->rowCount() > 0){

        // FETCH POST FROM DATBASE
        $row = $get_stmt->fetch(PDO::FETCH_ASSOC);

        // CHECK, IF NEW UPDATE REQUEST DATA IS AVAILABLE THEN SET IT OTHERWISE SET OLD DATA
        $usuarios_usuario = isset($data->usuario) ? $data->usuario : $row['usuario'];
        $usuarios_contrasena = isset($data->contrasena) ? $data->contrasena : $row['contrasena'];
        $usuarios_correo = isset($data->correo) ? $data->correo : $row['correo'];
        $usuarios_genero = isset($data->genero) ? $data->genero : $row['genero'];
        $usuarios_fecha_nacimiento = isset($data->fecha_nacimiento) ? $data->fecha_nacimiento : $row['fecha_nacimiento'];
        $usuarios_escolaridad = isset($data->escolaridad) ? $data->escolaridad : $row['escolaridad'];
        $usuarios_etnia = isset($data->etnia) ? $data->etnia : $row['etnia'];
        $usuarios_reportes = isset($data->reportes) ? $data->reportes : $row['reportes'];

        $update_query = "UPDATE `usuarios` SET usuario = :usuario, contrasena = :contrasena, correo = :correo, genero = :genero, fecha_nacimiento = :fecha_nacimiento, escolaridad = :escolaridad, etnia = :etnia, reportes = :reportes
        WHERE id = :id";

        $update_stmt = $conn->prepare($update_query);

        // DATA BINDING AND REMOVE SPECIAL CHARS AND REMOVE TAGS
        $update_stmt->bindValue(':usuario', htmlspecialchars(strip_tags($data->usuario)),PDO::PARAM_STR);
        $update_stmt->bindValue(':contrasena', htmlspecialchars(strip_tags($data->contrasena)),PDO::PARAM_STR);
        $update_stmt->bindValue(':correo', htmlspecialchars(strip_tags($data->correo)),PDO::PARAM_STR);
        $update_stmt->bindValue(':genero', htmlspecialchars(strip_tags($data->genero)),PDO::PARAM_STR);
        $update_stmt->bindValue(':fecha_nacimiento', htmlspecialchars(strip_tags($data->fecha_nacimiento)),PDO::PARAM_STR);
        $update_stmt->bindValue(':escolaridad', htmlspecialchars(strip_tags($data->escolaridad)),PDO::PARAM_STR);
        $update_stmt->bindValue(':etnia', htmlspecialchars(strip_tags($data->etnia)),PDO::PARAM_STR);
        $update_stmt->bindValue(':reportes', htmlspecialchars(strip_tags($data->reportes)),PDO::PARAM_INT);

        $update_stmt->bindValue(':id', $usuario_id,PDO::PARAM_INT);


        if($update_stmt->execute()){
            $msg['message'] = 'Data updated successfully';
        }else{
            $msg['message'] = 'data not updated';
        }

    }
    else{
        $msg['message'] = 'Invlid ID';
    }

    echo  json_encode($msg);

}
?>
