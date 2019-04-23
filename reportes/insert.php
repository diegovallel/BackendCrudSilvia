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

// GET DATA FORM REQUEST
$data = json_decode(file_get_contents("php://input"));

//CREATE MESSAGE ARRAY AND SET EMPTY
$msg['message'] = '';

// CHECK IF RECEIVED DATA FROM THE REQUEST
if(isset($data->idusuario) && isset($data->direccion)){
    // CHECK DATA VALUE IS EMPTY OR NOT
    if(is_numeric($data->idusuario) && !empty($data->direccion)){

        $hoy = getdate();
        $data->fecha = (string) $hoy[year] . "/" . (string) $hoy[mon] . "/" . (string) $hoy[mday] . "-" . (string)$hoy[hours] . ":" . (string)$hoy[minutes] .  ":" . (string)$hoy[seconds];
        $string_animales = $data->animales;
        $animal = explode(", ", $string_animales);
        $string_tipos = $data->tipo;
        $incidente = explode(", ", $string_tipos);

        $insert_query = "INSERT INTO `reportes`(idusuario, foto, fecha, direccion, latitud, longitud) VALUES(:idusuario,:foto,:fecha,:direccion,:latitud,:longitud)";

        $insert_stmt = $conn->prepare($insert_query);

        // DATA BINDING
        $insert_stmt->bindValue(':idusuario', htmlspecialchars(strip_tags($data->idusuario)),PDO::PARAM_INT);

        $insert_stmt->bindValue(':foto', htmlspecialchars(strip_tags($data->foto)),PDO::PARAM_STR);

        $insert_stmt->bindValue(':fecha', htmlspecialchars(strip_tags($data->fecha)),PDO::PARAM_STR);
        $insert_stmt->bindValue(':direccion', htmlspecialchars(strip_tags($data->direccion)),PDO::PARAM_STR);
        $insert_stmt->bindValue(':latitud', htmlspecialchars(strip_tags($data->latitud)),PDO::PARAM_STR);
        $insert_stmt->bindValue(':longitud', htmlspecialchars(strip_tags($data->longitud)),PDO::PARAM_STR);

        if($insert_stmt->execute()){
            $msg['message'] = 'Data Inserted Successfully';
            $msg['status'] = 'ok';
            $idreporte = $conn->lastInsertId();

            foreach($animal as $key){
                $data_animal[] = array(
              'idreporte' => $idreporte,
              'animal' => $key
          );
            }

            //$data_animal=  json_encode($data_animal);

            foreach($incidente as $key){
                $data_verbo[] = array(
              'idreporte' => $idreporte,
              'verbo' => $key
          );
            }

            if(($data->numeroH != "Numero de agresores" && !empty($data->numeroH)) || ($data->edadH != "Rango de edad" && !empty($data->edadH))){
              $data_agresores[] = array(
                'idreporte' => $idreporte,
                'genero' => 'H',
                'edad' => $data->edadH,
                'numero' => $data->numeroH
              );
            }

            if(($data->numeroM != "Numero de agresores" && !empty($data->numeroM)) || ($data->edadM != "Rango de edad" && !empty($data->numeroM))){
              $data_agresores[] = array(
                'idreporte' => $idreporte,
                'genero' => 'M',
                'edad' => $data->edadM,
                'numero' => $data->numeroM
              );
            }

            if(($data->numeroN != "Numero de agresores" && !empty($data->numeroN)) || ($data->edadN != "Rango de edad" && !empty($data->numeroN))){
              $data_agresores[] = array(
                'idreporte' => $idreporte,
                'genero' => 'N',
                'edad' => $data->edadN,
                'numero' => $data->numeroN
              );
            }

            $status = insertAnimal($data_animal, $conn);

            $status = insertVerbo($data_verbo, $conn);

            $status = insertAgresores($data_agresores, $conn);
        }else{
            $msg['message'] = 'Data not Inserted';
        }

    }else{
        $msg['message'] = 'Oops! empty field detected. Please fill all the fields';
    }
}
else{
    $msg['message'] = 'Please fill all the fields';
}
//ECHO DATA IN JSON FORMAT
echo  json_encode($msg);

function insertAnimal($data, $conn){

  foreach($data as $row){
    $insert_query = "INSERT INTO `animales_agredidos`(idreporte, animal) VALUES(:idreporte,:animal)";

    $insert_stmt = $conn->prepare($insert_query);

    $insert_stmt->bindValue(':idreporte', htmlspecialchars(strip_tags($row['idreporte'])),PDO::PARAM_INT);

    $insert_stmt->bindValue(':animal', htmlspecialchars(strip_tags($row['animal'])),PDO::PARAM_INT);

    if($insert_stmt->execute()){
        $msg['message'] = 'ok';
    }else{
        $msg['message'] = '400';
    }
  //  return $msg;
  }
}

function insertVerbo($data, $conn){

  foreach($data as $row){
    $insert_query = "INSERT INTO `agresiones`(idreporte, verbo) VALUES(:idreporte,:verbo)";

    $insert_stmt = $conn->prepare($insert_query);

    // DATA BINDING
    $insert_stmt->bindValue(':idreporte', htmlspecialchars(strip_tags($row['idreporte'])),PDO::PARAM_INT);

    $insert_stmt->bindValue(':verbo', htmlspecialchars(strip_tags($row['verbo'])),PDO::PARAM_STR);

    if($insert_stmt->execute()){
        $msg['message'] = 'ok';
    }else{
        $msg['message'] = '400';
    }
  }
}

function insertAgresores($data, $conn){
  foreach ($data as $row) {
    $insert_query = "INSERT INTO `agresores`(idreporte, genero, edad, numero) VALUES(:idreporte,:genero,:edad,:numero)";
    $insert_stmt = $conn->prepare($insert_query);

    // DATA BINDING
    $insert_stmt->bindValue(':idreporte', htmlspecialchars(strip_tags($row['idreporte'])),PDO::PARAM_INT);

    $insert_stmt->bindValue(':genero', htmlspecialchars(strip_tags($row['genero'])),PDO::PARAM_STR);

    $insert_stmt->bindValue(':edad', htmlspecialchars(strip_tags($row['edad'])),PDO::PARAM_STR);

    $insert_stmt->bindValue(':numero', htmlspecialchars(strip_tags($row['numero'])),PDO::PARAM_STR);

    if($insert_stmt->execute()){
        $msg['message'] = 'ok';
    }else{
        $msg['message'] = '400';
    }
  }
}
?>
