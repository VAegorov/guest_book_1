<?php

require_once 'helper.php';
$link = db_connect();

$limit = 10;
$limit_page = 5;
$page_get = 1;
if (isset($_REQUEST['page'])) {
    $page_get = ($_REQUEST['page']);
}
$chunk_arr = setPage($link, $limit, $limit_page);
$count = array_pop($chunk_arr);//общее количество статей
$page_final = ceil($count / $limit);//номер последней страницы
$sl = false;
if ($page_final - $page_get > 0) $sl = true;//определяем тип первого chunka

/*echo "<pre>";
var_dump($chunk_arr);
echo "</pre>";*/
$chunk = numChunk($chunk_arr, $page_get);
$type_chunk = typeChunk($chunk_arr, $chunk);
$out_num_page = outNumPage($chunk_arr, $chunk);
$start = startArticle($chunk_arr, $chunk, $page_get);

if (isset($_REQUEST['success']) &&  $_REQUEST['success'] == 1) {
    $message = "Сообщение успешно добавлено!";
} elseif (isset($_REQUEST['success']) &&  $_REQUEST['success'] == 0) $message = "Сообщение не добавлено! Попробуйте снова.";

if (isset($_REQUEST['submit'])) {
    $name = trim($_REQUEST['name']);
    $article = trim($_REQUEST['article']);
    if (empty($name) || empty($article)) {
        echo "Вы не заполнили полностью все поля формы!";
    } else {
        $result = add_article($name, $article, $link);
        if ($result == 1) {
            header("location: {$_SERVER['$_SERVER_NAME']}{$_SERVER['SCRIPT_NAME']}?success=$result");
            exit;
        }
    }
}
?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>GUEST BOOK</title>
</head>
<body>
    <h1>Гостевая книга</h1>
    <p>
        <a href="index.php"><?php if ($type_chunk == 2 || $type_chunk == 3) echo "Начало"; ?></a>
        <a href="index.php?page=<?=$page_get - 1; ?>"><?php if ($type_chunk == 2 || $type_chunk == 3) echo "Предыдущая"; ?></a>
        <?php
            $pages_num_arr = outNumPage($chunk_arr, $chunk);
            foreach ($pages_num_arr as $page_num):
        ?>
        <a href="index.php?page=<?=$page_num; ?>"><?php if ($page_num == $page_get) echo "-"; ?><?=$page_num; ?><?php if ($page_num == $page_get) echo "-"; ?></a>
    <?php
    endforeach;
    ?>
        <a href="index.php?page=<?=$page_get + 1; ?>"><?php if ($type_chunk == 2 || ($type_chunk == 1 && $sl)) echo "Следующая"; ?></a>
        <a href="index.php?page=<?=$page_final; ?>"><?php if ($type_chunk == 2 || ($type_chunk == 1 && $sl)) echo "Конец"; ?></a>
    </p>
    <?php
        $articles = get_articles($link, $start, $limit);
        foreach ($articles as $elem):
    ?>
    <div>
        <p><?=htmlspecialchars($elem['date']) . " " . htmlspecialchars($elem['name']); ?></p>
        <p><?=nl2br(htmlspecialchars($elem['article'])); ?></p><hr>
    </div>
    <?php
        endforeach;
    ?>
    <div>
        <p>
            <a href="index.php"><?php if ($type_chunk == 2 || $type_chunk == 3) echo "Начало"; ?></a>
            <a href="index.php?page=<?=$page_get - 1; ?>"><?php if ($type_chunk == 2 || $type_chunk == 3) echo "Предыдущая"; ?></a>
            <?php
            $pages_num_arr = outNumPage($chunk_arr, $chunk);
            foreach ($pages_num_arr as $page_num):
                ?>
                <a href="index.php?page=<?=$page_num; ?>"><?php if ($page_num == $page_get) echo "-"; ?><?=$page_num; ?><?php if ($page_num == $page_get) echo "-"; ?></a>
                <?php
            endforeach;
            ?>
            <a href="index.php?page=<?=$page_get + 1; ?>"><?php if ($type_chunk == 2 || ($type_chunk == 1 && $sl)) echo "Следующая"; ?></a>
            <a href="index.php?page=<?=$page_final; ?>"><?php if ($type_chunk == 2 || ($type_chunk == 1 && $sl)) echo "Конец"; ?></a>
        </p>
        <p><?php if (isset($message)) {echo $message;} ?></p>
        <form action="" method="POST">
            <p><input type="text" placeholder="Ваше имя" name="name"></p>
            <p><textarea placeholder="Ваше сообщение" name="article"></textarea></p>
            <p><input type="submit" name="submit"></p>
        </form>
    </div>
</body>
</html>
