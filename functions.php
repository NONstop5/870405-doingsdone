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

// Функция подключния к БД
function connectDb($host, $userName, $userPassw, $dbName) {
    $result = mysqli_connect($host, $userName, $userPassw, $dbName);
    if ($result == false) {
        print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
        exit();
    }
    mysqli_set_charset($result, "utf8");
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

// Создание ассоциативного массива из запроса к БД
function getAssocArrayFromSQL($dbConn, $sql) {
    $result = mysqli_fetch_all(execSql ($dbConn, $sql), MYSQLI_ASSOC);
    return $result;
}
?>
