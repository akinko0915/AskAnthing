<?php
    session_start();
    require_once(__DIR__ . '/../src/db_connect.php');
    
    $post_id = $_GET['post_id'];

        $sql = "SELECT reader_name, comment FROM comments WHERE post_id = :post_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);

        $stmt->execute();

        $data = array();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
            
        }
        
    // json_encode をコンソールで出すとしても2回してしまったら、取得したデータがおかしくなる。一回だけ
    echo json_encode($data);
?>