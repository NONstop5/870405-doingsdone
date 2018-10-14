<?php

// Функция шаблонизатор
function include_template($name, $data)
{
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
function connectDb($host, $userName, $userPassw, $dbName)
{
    $result = mysqli_connect($host, $userName, $userPassw, $dbName);
    if ($result == false) {
        print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
        exit();
    }
    mysqli_set_charset($result, "utf8");
    return $result;
}

// Функция обработки запроса к БД
function execSql($conn, $sql)
{
    $result = mysqli_query($conn, $sql);
    if ($result == false) {
        print("Ошибка при выполнении запроса:" . mysqli_error($conn) . "<br>");
        print($sql);
        exit();
    }
    return $result;
}

// Создание ассоциативного массива из запроса к БД
function getAssocArrayFromSQL($dbConn, $sql)
{
    $result = mysqli_fetch_all(execSql($dbConn, $sql), MYSQLI_ASSOC);
    return $result;
}

// Создание ассоциативного массива из запроса к БД
function startSession($userId, $userName)
{
    session_start();
    $_SESSION['userId'] = $userId;
    $_SESSION['userName'] = $userName;
}

// Функция создает SQL запрос на добавление нового пользователя
function getNewUserInsertSql($userEmail, $userPassword, $userName)
{
    $sql = 'INSERT INTO users
            (
                user_name,
                user_email,
                user_password
            ) VALUES (
                \'' . $userName . '\',
                \'' . $userEmail . '\',
                \'' . password_hash($userPassword, PASSWORD_DEFAULT) . '\'
            )';
    return $sql;
}

// Функция создает SQL запрос на добавление новой задачи
function getProjectInsertSql($currentUserId, $projectName)
{
    $sql = 'INSERT INTO projects
            (
                user_id,
                project_name
            ) VALUES (
                ' . $currentUserId . ',
                \'' . $projectName . '\'
            )';
    return $sql;
}

// Функция создает SQL запрос на добавление новой задачи
function getTaskInsertSql($currentUserId, $projectId, $taskName, $taskCompleteDate, $taskFile)
{
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
function convertDateToTimestampSqlFormat($dateStr)
{
    return date('Y-m-d H:i:s', strtotime($dateStr));
}

// Функция проверки корректности формата даты
function checkDateFormat($dateStr)
{
    $result = false;
    $pattern = '/^([0-9]{2})\.([0-9]{2})\.([0-9]{4})$/';
    if (preg_match($pattern, $dateStr, $matches)) {
        $result = checkdate($matches[2], $matches[1], $matches[3]);
    }
    return $result;
}

// Функция удаления спецсимволов и пробелов
function clearUserInputStr($dataStr)
{
    return substr(htmlspecialchars(trim($dataStr), ENT_QUOTES), 0, 49);
}

// Функция заполнения массива ошибок значениями
function setErrorsValues ($fieldValuesArray, $fieldName, $errorMessage)
{
    $result = $fieldValuesArray;
    $result['errors']['errorFlag'] = 1;
    $result['errors']['errorGeneralMessage'] = '<p class="error-message">Пожалуйста, исправьте ошибки в форме</p>';
    $result['errors'][$fieldName]['errorClass'] = ' form__input--error';
    $result['errors'][$fieldName]['errorMessage'] = '<p class="form__message"><span class="form__message error-message">' . $errorMessage . '</span></p>';

    return $result;
}

// Функция создания пустого массива значений полей нового пользователя
function createEmptyNewUserFieldValuesArray()
{
    $errorClassAndMessage = [
        'errorClass' => '',
        'errorMessage' => ''
    ];

    $result = [
        'fieldValues' => [
            'email' => '',
            'password' => '',
            'name' => ''
        ],
        'errors' => [
            'errorFlag' => 0,
            'errorGeneralMessage' => '',
            'email' => $errorClassAndMessage,
            'password' => $errorClassAndMessage,
            'name' => $errorClassAndMessage
        ]
    ];
    return $result;
}

// Функция создания пустого массива значений полей существующего пользователя
function createEmptyExistingUserFieldValuesArray()
{
    $errorClassAndMessage = [
        'errorClass' => '',
        'errorMessage' => ''
    ];

    $result = [
        'fieldValues' => [
            'email' => '',
            'password' => ''
        ],
        'errors' => [
            'errorFlag' => 0,
            'errorGeneralMessage' => '',
            'email' => $errorClassAndMessage,
            'password' => $errorClassAndMessage
        ]
    ];
    return $result;
}

// Функция создания пустого массива значений полей проекта
function createEmptyProjectFieldValuesArray()
{
    $errorClassAndMessage = [
        'errorClass' => '',
        'errorMessage' => ''
    ];

    $result = [
        'fieldValues' => [
            'name' => ''
        ],
        'errors' => [
            'errorFlag' => 0,
            'errorGeneralMessage' => '',
            'name' => $errorClassAndMessage,
        ]
    ];
    return $result;
}

// Функция создания пустого массива значений полей задачи
function createEmptyTaskFieldValuesArray()
{
    $errorClassAndMessage = [
        'errorClass' => '',
        'errorMessage' => ''
    ];

    $result = [
        'fieldValues' => [
            'name' => '',
            'project' => 0,
            'date' => '',
            'file' => ''
        ],
        'errors' => [
            'errorFlag' => 0,
            'errorGeneralMessage' => '',
            'name' => $errorClassAndMessage,
            'project' => $errorClassAndMessage,
            'date' => $errorClassAndMessage
        ]
    ];
    return $result;
}

// Функция проверки полей формы нового пользователя
function checkNewUserFields($dbConn, $postArray)
{
    $result = createEmptyNewUserFieldValuesArray();

    $postEmailStr = clearUserInputStr($postArray['email']);
    $postPasswordStr = clearUserInputStr($postArray['password']);
    $postNameStr = clearUserInputStr($postArray['name']);

    $isCorrectEmail = filter_var($postEmailStr, FILTER_VALIDATE_EMAIL);

    if (!empty($postEmailStr) && $isCorrectEmail) {
        $result['fieldValues']['email'] = $postEmailStr;

        $sql = 'SELECT user_id FROM users WHERE user_email = \'' . $postEmailStr . '\'';
        $users = execSql($dbConn, $sql);

        if (mysqli_num_rows($users)) {
            $result = setErrorsValues ($result, 'email', 'Пользователь с таким e-mail уже существует');
        }
    } else {
        $result = setErrorsValues ($result, 'email', 'E-mail введён некорректно');
    }

    if (!empty($postPasswordStr)) {
        $result['fieldValues']['password'] = $postPasswordStr;
    } else {
        $result = setErrorsValues ($result, 'password', 'Введите корректное значение');
    }

    if (!empty($postNameStr)) {
        $result['fieldValues']['name'] = $postNameStr;
    } else {
        $result = setErrorsValues ($result, 'name', 'Введите корректное значение');
    }

    return $result;
}

// Функция проверки полей формы нового пользователя
function checkExistingUserFields($dbConn, $postArray)
{
    $result = createEmptyExistingUserFieldValuesArray();

    $postEmailStr = clearUserInputStr($postArray['email']);
    $postPasswordStr = clearUserInputStr($postArray['password']);

    $isCorrectEmail = filter_var($postEmailStr, FILTER_VALIDATE_EMAIL);

    $result['fieldValues']['email'] = $postEmailStr;
    $result['fieldValues']['password'] = $postPasswordStr;

    if (empty($postEmailStr) && !$isCorrectEmail) {
        $result = setErrorsValues ($result, 'email', 'E-mail введён некорректно');
    }

    if (!empty($postPasswordStr)) {
        $sql = 'SELECT user_id, user_name, user_password FROM users WHERE user_email = \'' . $postEmailStr . '\'';
        $users = getAssocArrayFromSQL($dbConn, $sql);

        if (count($users)) {
            if (!password_verify($postPasswordStr, $users[0]['user_password'])) {
                $result['errors']['errorFlag'] = 1;
                $result['errors']['errorGeneralMessage'] = '<p class="error-message">Пользователь с такими e-mail и паролем не найден</p>';
            } else {
                startSession($users[0]['user_id'], $users[0]['user_name']);
            }
        } else {
            $result['errors']['errorFlag'] = 1;
            $result['errors']['errorGeneralMessage'] = '<p class="error-message">Пользователь с такими e-mail и паролем не найден</p>';
        }
    } else {
        $result = setErrorsValues ($result, 'password', 'Введите корректное значение');
    }

    return $result;
}

// Функция проверки полей формы нового проекта
function checkProjectFields($dbConn, $postArray)
{
    $result = createEmptyProjectFieldValuesArray();

    $postNameStr = clearUserInputStr($postArray['name']);

    if (!empty($postNameStr)) {
        $result['fieldValues']['name'] = $postNameStr;
    } else {
        $result = setErrorsValues ($result, 'name', 'Введите корректное значение');
    }

    return $result;
}

// Функция проверки полей формы новой задачи
function checkTaskFields($dbConn, $currentUserId, $postArray, $filesArray)
{
    $result = createEmptyTaskFieldValuesArray();

    $postNameStr = clearUserInputStr($postArray['name']);
    $postProjectStr = clearUserInputStr($postArray['project']);
    $postDateStr = clearUserInputStr($postArray['date']);

    if (!empty($postNameStr)) {
        $result['fieldValues']['name'] = $postNameStr;
    } else {
        $result = setErrorsValues ($result, 'name', 'Введите корректное значение');
    }

    if (!empty($postProjectStr)) {
        $sql = 'SELECT project_id FROM projects WHERE user_id = ' . $currentUserId . ' AND project_id = ' . $postProjectStr;
        $projects = execSql($dbConn, $sql);

        if (mysqli_num_rows($projects)) {
            $result['fieldValues']['project'] = $postProjectStr;
        }
    } else {
        $result = setErrorsValues ($result, 'project', 'Введите корректное значение');
    }

    if (!empty($postDateStr)) {
        if (checkDateFormat($postDateStr)) {
            $result['fieldValues']['date'] = $postDateStr;
        } else {
            $result['fieldValues']['date'] = $postDateStr;
            $result = setErrorsValues ($result, 'date', 'Введите корректное значение даты');
        }
    }

    if (!empty($filesArray)) {
        if (!$filesArray['preview']['error']) {
            $filePath = '/' . basename($filesArray['preview']['name']);
            if (move_uploaded_file($filesArray['preview']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $filePath)) {
                $result['fieldValues']['file'] = $filePath;
            }
        }
    }

    return $result;
}
