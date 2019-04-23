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
    $reporte_id = $data->id;

    //GET POST BY ID FROM DATABASE
    $get_post = "SELECT * FROM `reportes` WHERE id=:reporte_id";
    $get_stmt = $conn->prepare($get_post);
    $get_stmt->bindValue(':reporte_id', $reporte_id,PDO::PARAM_INT);
    $get_stmt->execute();


    //CHECK WHETHER THERE IS ANY POST IN OUR DATABASE
    if($get_stmt->rowCount() > 0){

        // FETCH POST FROM DATBASE
        $row = $get_stmt->fetch(PDO::FETCH_ASSOC);

        // CHECK, IF NEW UPDATE REQUEST DATA IS AVAILABLE THEN SET IT OTHERWISE SET OLD DATA
        $reporte_idusuario = isset($data->idusuario) ? $data->idusuario : $row['idusuario'];
        $reporte_foto = isset($data->foto) ? $data->foto : $row['foto'];
        $reporte_fecha = isset($data->fecha) ? $data->fecha : $row['fecha'];
        $reporte_direccion = isset($data->direccion) ? $data->direccion : $row['direccion'];
        $reporte_latitud = isset($data->latitud) ? $data->latitud : $row['latitud'];
        $reporte_longitud = isset($data->longitud) ? $data->longitud : $row['longitud'];


        $update_query = "UPDATE `reportes` SET idusuario = :idusuario, foto = :foto, fecha = :fecha, direccion = :direccion, latitud = :latitud, longitud = :longitud WHERE id = :id";

        $update_stmt = $conn->prepare($update_query);

        // DATA BINDING AND REMOVE SPECIAL CHARS AND REMOVE TAGS
        $update_stmt->bindValue(':idusuario', htmlspecialchars(strip_tags($data->idusuario)),PDO::PARAM_INT);
        $update_stmt->bindValue(':foto', htmlspecialchars(strip_tags($data->foto)),PDO::PARAM_STR);
        $update_stmt->bindValue(':fecha', htmlspecialchars(strip_tags($data->fecha)),PDO::PARAM_STR);
        $update_stmt->bindValue(':direccion', htmlspecialchars(strip_tags($data->direccion)),PDO::PARAM_STR);
        $update_stmt->bindValue(':latitud', htmlspecialchars(strip_tags($data->latitud)),PDO::PARAM_STR);
        $update_stmt->bindValue(':longitud', htmlspecialchars(strip_tags($data->longitud)),PDO::PARAM_STR);

        $update_stmt->bindValue(':id', $reporte_id,PDO::PARAM_INT);


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
