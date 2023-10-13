<?php

/**
 * 両端の空白を除去する関数です。マルチバイトを含みます。
 * 参考 https://qiita.com/fallout/items/a13cebb07015d421fde3
 */
function mbTrim($pString)
{
    return preg_replace('/\A[\p{Cc}\p{Cf}\p{Z}]++|[\p{Cc}\p{Cf}\p{Z}]++\z/u', '', $pString);
}

    // Comment
    // 入力値を確認する（コメント者）
    $is_valid_reader_name = true;
    $input_reader_name = '';
    if (isset($_POST['reader_name'])){
        $input_reader_name = mbTrim(str_replace("\r\n", "\n", $_POST['reader_name']));
        $_SESSION['input_pre_reader_name'] = $_POST['reader_name'];
        
    } else {
        $is_valid_reader_name = false;
    }
    
    if($is_valid_reader_name && mb_strlen($input_reader_name) > 30){
        $is_valid_reader_name = false;
        $_SESSION['input_error_reader_name'] = 'ニックネームは 30 文字以内で入力してください。（現在 ' . mb_strlen($input_reader_name) . ' 文字）';
    }

    // 入力値を確認する（コメント内容）
    $is_valid_comment = true;
    $input_comment = '';
    if(isset($_POST['comment'])){
        $input_comment = mbTrim(str_replace("\r\n", "\n", $_POST['comment']));
        $_SESSION['input_pre_comment'] = $_POST['comment'];

    } else {
        $is_valid_comment = false;
    }

    if($is_valid_comment && $input_comment === ''){
        $is_valid_comment = false;
    $_SESSION['input_error_comment'] = 'コメントの入力は必須です。';
    }

    if($is_valid_comment && mb_strlen($input_comment) > 1000){
        $is_valid_comment = false;
        $_SESSION['input_error_comment'] = 'コメント内容は 1000 文字以下で入力してください。（現在 ' . mb_strlen($input_comment) . ' 文字）';

    }
    
    // 投稿IDを取得する
    $post_id = null;
    if (isset($_POST['post_id'])) {
        $post_id = $_POST['post_id']; 
    } else {
        $post_id = 1;
    }

    // 投稿をデータベースへ保存する処理
    if($is_valid_reader_name && $is_valid_comment){
        if($input_reader_name === ''){
            $input_reader_name = '匿名さん';
        }
        
        // INSERT クエリを作成する
        $query = 'INSERT INTO comments (post_id, reader_name, comment) VALUES (:post_id, :reader_name, :comment)';
        
        // SQL 実行の準備 (実行はされない)
        $stmt = $dbh->prepare($query);
        
        // プレースホルダに値をセットする
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->bindValue(':reader_name', $input_reader_name, PDO::PARAM_STR);
        $stmt->bindValue(':comment', $input_comment, PDO::PARAM_STR);
        
        // クエリを実行する
        $stmt->execute();
        $_SESSION['action_success_text'] = 'コメントしました';
        $_SESSION['input_error_reader_name'] = '';
        $_SESSION['input_error_comment'] = '';
        $_SESSION['input_pre_reader_name'] = '';
        $_SESSION['input_pre_comment'] = '';
        
    } else {
    $_SESSION['action_success_text'] = '';
    $_SESSION['action_error_text'] = 'コメントの入力内容を確認してください';
}

header('Location: /');
exit();