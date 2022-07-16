<?
$xml_data = simplexml_load_file("C:\OpenServer\domains\\testWork\\data_light.xml");
$file_path = readline("File's path: ");
if(trim($file_path) != ""){
    if(simplexml_load_file($file_path)){
        $xml_data = simplexml_load_file($file_path);
    }
}

$pdo = new PDO("mysql:host=localhost;dbname=testdb", "root", "");
$sql_select_id = "SELECT `id` FROM `offers`";
$result = $pdo->query($sql_select_id);
$db_ids = $result->fetchAll(PDO::FETCH_ASSOC);
$offers_ids = [];

foreach($xml_data->offers->offer as $off){
    if(isset($off->id)){
        $offers_ids[] = (int)$off->id;
        $values = "`id`=:id, `mark`=:mark, `model`=:model, `generation`=:generation, `year`=:year, `run`=:run, `color`=:color, `body-type`=:body_type, `engine-type`=:engine_type, `transmission`=:transmission, `gear-type`=:gear_type, `generation_id`=:generation_id";
        
        $sql = "INSERT INTO `offers` VALUES(:id, :mark, :model, :generation, :year_off, :run, :color, :body_type, :engine_type, :transmission, :gear_type, :generation_id)";
        foreach($db_ids as $id){
            if($off->id == $id['id']){
                $sql = "UPDATE `offers` SET `id`=:id, `mark`=:mark, `model`=:model, `generation`=:generation, `year`=:year_off, `run`=:run, `color`=:color, `body-type`=:body_type, `engine-type`=:engine_type, `transmission`=:transmission, `gear-type`=:gear_type, `generation_id`=:generation_id WHERE `id` = $off->id";
                break;
            }
        }

        $query_data = [
            'id' => $off->id,
            'mark' => $off->mark,
            'model' => $off->model,
            'generation' => $off->generation,
            'year_off' => $off->year,
            'run' => $off->run,
            'color' => $off->color,
            'body_type' => $off->{'body-type'},
            'engine_type' => $off->{'engine-type'},
            'transmission' => $off->transmission,
            'gear_type' => $off->{'gear-type'},
            'generation_id' => $off->generation_id
        ];
        $stmt = $pdo->prepare($sql);
        $stmt->execute($query_data);
    }
}

$sql_select_id = "SELECT `id` FROM `offers`";
$result = $pdo->query($sql_select_id);
$db_ids = $result->fetchAll(PDO::FETCH_ASSOC);
foreach($db_ids as $id){
    if(!in_array($id['id'], $offers_ids)){
        $delete_sql = "DELETE FROM `offers` WHERE `id` = ?";
        $stmt_delete = $pdo->prepare($delete_sql);
        $stmt_delete->execute([$id['id']]);
    }
}

?>