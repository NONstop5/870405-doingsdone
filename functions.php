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

// Функция создает SQL запрос на добавление новой задачи
function getTaskInsertSql($currentUserId, $projectId, $taskName, $taskCompleteDate, $taskFile) {
    $quote = '\'';
    $taskCompleteDateSqlField = '';
    $taskCompleteDateSqlValue = '';
    $taskFileSqlField = '';
    $taskFileSqlValue = '';

    if (!empty($taskCompleteDate)) {
        $taskCompleteDateSqlField = ', task_deadline';
        $taskCompleteDateSqlValue = ', ' . $quote. convertDateToTimestampSqlFormat($taskCompleteDate) . $quote;
    }

    if (!empty($taskFile)) {
        $taskFileSqlField = ', task_file';
        $taskFileSqlValue = ', ' . $quote. $taskFile . $quote;
    }
    $sql = 'INSERT INTO tasks
            (
                user_id,
                project_id,
                task_name' .
                $taskCompleteDateSqlField .
                $taskFileSqlField . '
            ) VALUES (
                ' . $currentUserId . ',
                ' . $projectId . ',
                \'' . $taskName . '\'' .
                $taskCompleteDateSqlValue .
                $taskFileSqlValue . '
            )';
    return $sql;
}

// Функция конвертирует дату в формат даты Timestamp mySQL
function convertDateToTimestampSqlFormat($dateStr) {
    return date('Y-m-d H:i:s', strtotime($dateStr));
}

// Функция проверки корректности формата даты
function checkDateFormat($dateStr) {
    $result = false;
    $pattern = '/^([0-9]{2})\.([0-9]{2})\.([0-9]{4})$/';
    if (preg_match($pattern, $dateStr, $matches)) {
        $result = checkdate($matches[2], $matches[1], $matches[3]);
    }
    return $result;
}

function clearUserInputStr($dataStr) {
    return htmlspecialchars(trim($dataStr));
}

// Функция создания пустого массива значений полей задач
function createEmptyTaskFieldValuesArray() {
    $result = [
        'fieldValues' => [
            'name' => '',
            'project' => 0,
            'date' => '',
            'file' => ''

        ],
        'errors' => [
            'errorFlag' => 0,
            'name' => [
                'errorClass' => '',
                'errorMessage' => ''
            ],
            'project' => [
                'errorClass' => '',
                'errorMessage' => ''
            ],
            'date' => [
                'errorClass' => '',
                'errorMessage' => ''
            ]
        ]
    ];
    return $result;
}

// Функция проверки полей формы новой задачи
function checkTaskFields($dbConn, $currentUserId, $postArray, $filesArray) {
    $result = createEmptyTaskFieldValuesArray();
    if (!empty(clearUserInputStr($postArray['name']))) {
        $result['fieldValues']['name'] = clearUserInputStr($postArray['name']);
    } else {
        $result['errors']['errorFlag'] = 1;
        $result['errors']['name']['errorClass'] = ' form__input--error';
        $result['errors']['name']['errorMessage'] = '<p class="form__message"><span class="form__message error-message">Введите корректное значение</span></p>';
    }

    if (!empty(clearUserInputStr($postArray['project']))) {
        $sql = 'SELECT project_id FROM projects WHERE user_id = ' . $currentUserId . ' AND project_id = ' . $postArray['project'];
        $projects = execSql ($dbConn, $sql);

        if (mysqli_num_rows($projects)) {
            $result['fieldValues']['project'] = clearUserInputStr($postArray['project']);
        }
    } else {
        $result['errors']['errorFlag'] = 1;
        $result['errors']['project']['errorClass'] = ' form__input--error';
        $result['errors']['project']['errorMessage'] = '<p class="form__message"><span class="form__message error-message">Введите корректное значение/span></p>';

    }

    if (!empty(clearUserInputStr($postArray['date']))) {
        $dateStr = $postArray['date'];
        if (checkDateFormat($dateStr)) {
            $result['fieldValues']['date'] = clearUserInputStr($dateStr);
        } else {
            $result['fieldValues']['date'] = clearUserInputStr($dateStr);
            $result['errors']['errorFlag'] = 1;
            $result['errors']['date']['errorClass'] = ' form__input--error';
            $result['errors']['date']['errorMessage'] = '<p class="form__message"><span class="form__message error-message">Введите корректное значение</span></p>';
        }
    }

    if (!empty($filesArray)) {
        if (!$filesArray['preview']['error']) {
            $filePath = '/' . basename($filesArray['preview']['name']);
            if (move_uploaded_file($filesArray['preview']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $filePath)) {;
                $result['fieldValues']['file'] = $filePath;
            }
        }
    }

    return $result;
}
?>
