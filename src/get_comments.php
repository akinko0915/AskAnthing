<?php
    require_once(__DIR__ . '/../src/db_connect.php');

    $connect = new PDO("mysql:host=localhost;dbname=comments", "root", "");

    $received_data = json_decode(file_get_contents("php://input"));
    

    if($received_data->postRequest = 'post_id'){
        $sql = "SELECT * FROM comments WHERE post_id :post_id";
    }

    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    while($row = $statement->fetch(PDO::FETCH_ASSOC)){
        $data[] = $row;
    }
    
    echo json_encode($data);
?>