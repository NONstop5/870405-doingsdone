<?php

// Функция шаблонизатор
function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!file_exists($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require_once $name;

    $result = ob_get_clean();

    return $result;
}

// Функция обработки запроса к БД
function execSql($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    if ($result == false) {
        print("Ошибка при выполнении запроса:" . mysqli_error($conn) . "<br>");
        print($sql);
    }
    return $result;
}
?>
