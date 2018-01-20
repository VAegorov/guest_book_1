<?php

function db_connect()
{
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $db_name = 'test';
    $link = mysqli_connect($host, $user, $password, $db_name);
    mysqli_set_charset($link, "UTF8") or die(mysqli_error($link));
    return $link;
}

//выбирает из БД статьи
//$limit кол-во статей на одной странице
//$start номер первой выбираемой статьи
function get_articles($link, $start, $limit)
{
    $query = sprintf("SELECT id, date, name, article FROM guest_book_1 LIMIT %d,%d", $start, $limit);
    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    for ($articles = []; $article = mysqli_fetch_assoc($result); $articles[] = $article);
    return $articles;
}

// возвращает 1 если статья добавлена, 0 если нет+
function add_article($name, $article, $link)
{
    $name = mysqli_real_escape_string($link, $name);
    $article = mysqli_real_escape_string($link, $article);
    $query = sprintf("INSERT INTO guest_book_1 (name, article) VALUES ('%s', '%s')", $name, $article);
    mysqli_query($link, $query) or die(mysqli_error($link));
    $result_add_article = 0;
    if (mysqli_affected_rows($link) == 1) $result_add_article = 1;
    return $result_add_article;
}

//узнать в каком чанке находится страница (int) $page_get-номер страницы из GET+
function numChunk($chunk_arr, $page_get) {
    foreach ($chunk_arr as $key=>$chunk) {
        foreach ($chunk as $page=>$article) {
            if ($page == $page_get) {
                return $key;
            }
        }
    }
}

//узнать тип чанка int(1,2,3)+
function typeChunk($chunk_arr, $chunk) {
    $key_final = count($chunk_arr) - 1;//номер ключа последнего чанка
    if ($chunk == 0) $sost = 1;
    elseif (($chunk + 1) > $key_final) $sost = 3;
    else $sost = 2;
    return $sost;
}

//получаем номер первой выбираемой статьи $start+
function startArticle($chunk_arr, $chunk, $page_get) {
    $start_arr = $chunk_arr[$chunk];
    foreach ($start_arr as $key=>$elem) {
        foreach ($elem as $start)
        if ($key == $page_get) return $start;
    }
}

//разбивает статьи на chunk и страницы, возвр массив: chunk=>[страница]=>[номера выводимых статей],
//последнее значение=количество статей+
function setPage ($link, $limit, $limit_page)
{
    $query = "SELECT COUNT(*) AS count FROM guest_book_1";
    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    $count_arr = mysqli_fetch_assoc($result);
    $count = $count_arr['count'];
    for ($i = 0; $i < $count; $i++) $articles_num[] = $i;
    $page_arr = array_chunk($articles_num, $limit, true);
    array_unshift($page_arr,1);
    unset($page_arr[0]);
    $chunk_arr = array_chunk($page_arr, $limit_page, true);
    $chunk_arr[] = $count;
    return $chunk_arr;
}

//возвращает массив выводимых чанком страниц
function outNumPage($chunk_arr, $chunk) {
    $start_arr = $chunk_arr[$chunk];
    $pages_num_arr = [];
    foreach ($start_arr as $key=>$elem) {
        $pages_num_arr[] = $key;
    }
    return $pages_num_arr;
}
?>