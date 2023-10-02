<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1.php</title>
</head>
<body>
<?php
    // データベース接続情報
    $dsn = 'mysql:dbname=*****;host=localhost';
    $db_user = '*****';
    $db_password = '*****';
    
    try {
        // データベース接続（エラーレポートを表示する）
        $pdo = new PDO($dsn, $db_user, $db_password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        
        // テーブル作成（datetimeカラムを追加）
        $sql = "CREATE TABLE IF NOT EXISTS m5 (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name CHAR(32),
            comment TEXT,
            datetime DATETIME NOT NULL,
            password VARCHAR(255) NOT NULL
        );";
        $pdo->exec($sql); // クエリを実行
        
        // テーブルのスキーマ変更が反映されるように、新しいPDOインスタンスを作成
        $pdo = new PDO($dsn, $db_user, $db_password);
    
        // 削除機能
        if (!empty($_POST["削除番号"]) && !empty($_POST["pwdel"])) {
            $deleteNumber = $_POST["削除番号"];
            $deletePassword = $_POST["pwdel"];
            
            // データベースから該当の投稿を取得
            $sql = "SELECT * FROM m5 WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id',$id, PDO::PARAM_INT);
            $stmt->execute([$deleteNumber]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && $result['password'] === $deletePassword) {
                // パスワード一致時に削除
                $sql = "DELETE FROM m5 WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$deleteNumber]);
            } else {
                echo "削除失敗：削除番号またはパスワードが間違っています";
            }
        }
        
        // 編集機能＋新規投稿（datetimeを追加）
        elseif (!empty($_POST["name"]) && !empty($_POST["str"]) && !empty($_POST["pw"])) {
            $PW = $_POST["pw"];
            $name = $_POST["name"];
            $str = $_POST["str"];
            $datetime = date("Y-m-d H:i:s");
            
            if (!empty($_POST["editNumber"])) {
                // 編集
                $editNumber = $_POST["editNumber"];
                $sql = "UPDATE m5 SET name = ?, comment = ?, datetime = ?, password = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':str', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':ts', $datetime, PDO::PARAM_STR);
                $stmt->bindParam(':pass', $password, PDO::PARAM_STR);
                $stmt->bindParam(':id',$editnumber, PDO::PARAM_STR);
                $stmt->execute([$name, $str, $datetime, $PW, $editNumber]);
            } else {
                // 新規投稿
                $sql = "INSERT INTO m5 (name, comment, datetime, password) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':str', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':ts', $datetime, PDO::PARAM_STR);
                $stmt->bindParam(':pass', $PW, PDO::PARAM_STR);
                $stmt->execute([$name, $str, $datetime, $PW]);
            }
        }
        
        // 編集番号取得
        $editName = "";
        $editStr = "";
        $editNumber = "";
        $editpw = "";
        
        if (!empty($_POST["edit"]) && !empty($_POST["pwedit"])) {
            $edit = $_POST["edit"];
            $pe = $_POST["pwedit"];
            
            // データベースから編集対象の情報を取得
            $sql = "SELECT * FROM m5 WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id',$id,PDO::PARAM_INT);
            $stmt->execute([$edit]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && $result['password'] === $pe) {
                $editName = $result["name"];
                $editStr = $result["comment"];
                $editNumber = $result["id"];
                $editpw = $result["password"];
                echo "成功";
            } else {
                echo "編集失敗：編集番号またはパスワードが間違っています";
            }
        }
    } catch (PDOException $e) {
        echo 'データベースエラー: ' . $e->getMessage();
    }
    ?>
    <form method="post" action="">
    <input type="text" name="name" placeholder="名前" value="<?php echo $editName; ?>">
    <br>
    <input type="text" name="str" placeholder="コメント" value="<?php echo $editStr; ?>">
    <br>
    <input type="text" name="pw" placeholder="暗証番号" value="<?php echo $editpw; ?>">
    <input type="submit" name="submit" value="送信">
    <br>
    <input type="number" name="editNumber" placeholder="投稿番号" value="<?php echo $editNumber; ?>">
    <br>
    <br>
    <form method="post" action="">
    <input type="number" name="削除番号" placeholder="削除対象番号">
    <br>
    <input type="text" name="pwdel" placeholder="暗証番号del">
    <input type="submit" name="delConduct" value="削除">
    <br>
    <br>
    <form method="post" action="">
    <input type="number" name="edit" placeholder="編集対象番号">
    <br>
    <input type="text" name="pwedit" placeholder="暗証番号edit">
    <input type="submit" name="editConduct" value="編集">
    </form> 
    <br>
    
    <?php
    
    echo "<hr>";
    // 投稿を表示
    try {
        $sql = "SELECT * FROM m5";
        $stmt = $pdo->query($sql);
        
        foreach ($stmt as $row) {
            echo $row["id"] . " ";
            echo $row["name"] . " ";
            echo $row["comment"] . " ";
            echo $row["datetime"] . " "; // 日時を表示
            echo $row["password"]   . " ";
            echo "<br>";
        }
    } catch (PDOException $e) {
        echo 'データベースエラー: ' . $e->getMessage();
    }
    ?>
</body>
</html>
</body>
</html>