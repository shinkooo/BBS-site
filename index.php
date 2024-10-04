<?php

date_default_timezone_set("Asia/Tokyo");
$comment_array = array();
$pdo = null;
$stmt = null;
$error_messages = array();

//DB接続をページを開いたタイミングで行う
try{
    $pdo = new PDO('mysql:host=localhost;dbname=bbs-yt', "root","");
}catch(PDOException $e){
    echo $e->getMessage();
}

//フォームの打ち込み
if(!empty($_POST["submitButton"])){
    //名前のチェック
    //emptyではなくtrimにすることで空白が入力された場合もエラーを表示
    if(!trim($_POST["username"])){
        $error_messages["username"] = "名前を入力してください";
    }
    //コメントのチェック
    //emptyではなくtrimにすることで空白が入力された場合もエラーを表示
    if(!trim($_POST["comment"])){
        $error_messages["comment"] = "コメントを入力してください";
    }
    if(empty($error_messages)){
        $postdate = date("Y-m-d H:i:s");
        try{
            $stmt = $pdo->prepare("INSERT INTO `bbs-table` (`username`,`comment`,`postDate`) VALUES (:username, :comment, :postDate)");
            $stmt->bindParam(':username', $_POST["username"], PDO::PARAM_STR);
            $stmt->bindParam(':comment', $_POST["comment"], PDO::PARAM_STR);
            $stmt->bindParam(':postDate', $postdate, PDO::PARAM_STR);
            $stmt->execute();
        } catch(PDOException $e){
            echo $e->getMessage();
        }
    }
}

//DBからコメントデータを取得する
$sql = "SELECT * FROM `bbs-table`;";
$comment_array = $pdo->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP掲示板</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1 class="title">PHPで掲示板アプリ</h1>
    <hr>
    <div class="boardWrapper">
        <section>
            <?php foreach ($comment_array as $comment): ?>
                <article>
                    <div class="wrapper">
                        <div class="nameArea">
                            <span>名前：</span>
                            <p class="username"><?php echo htmlspecialchars($comment["username"], ENT_QUOTES, 'UTF-8'); ?></p>
                            <time><?php echo htmlspecialchars($comment["postDate"], ENT_QUOTES, 'UTF-8'); ?></time>
                        </div>
                        <p class="comment"><?php echo htmlspecialchars($comment["comment"], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
        <form class="formWrapper" method="POST">
            <div>
                <input type="submit" value="書き込む" name="submitButton">
                <label for="">名前：</label>
                <input type="text" name="username" value="<?php echo isset($_POST["username"]) ? htmlspecialchars($_POST["username"], ENT_QUOTES, 'UTF-8') : ''; ?>">
                <?php if(isset($error_messages["username"])): ?>
                    <p class="error"><?php echo $error_messages["username"]; ?></p>
                <?php endif; ?>
            </div>
            <div>
                <textarea class="commentTextArea" name="comment"><?php echo isset($_POST["comment"]) ? htmlspecialchars($_POST["comment"], ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
                <?php if(isset($error_messages["comment"])): ?>
                    <p class="error"><?php echo $error_messages["comment"]; ?></p>
                <?php endif; ?>
            </div>
        </form>
    </div>
</body>
</html>
