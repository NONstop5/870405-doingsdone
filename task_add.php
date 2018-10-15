<?php

require_once 'session_check.php';
require_once 'constants.php';
require_once 'functions.php';

$dbConn = connectDb($host, $dbUserName, $dbUserPassw, $dbName);

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$currentUserId = $_SESSION['userId'];
$activeProject = ['id' => '', 'aloneGetStr' => '', 'additionGetStr' => ''];
$fieldsValues = createEmptyTaskFieldValuesArray();

if (isset($_GET['project_id'])) {
    $activeProject['id'] = intval($_GET['project_id']);
    if ($activeProject['id']) {
        $activeProject['aloneGetStr'] = '?project_id=' . $activeProject['id'];
        $activeProject['additionGetStr'] = '&project_id=' . $activeProject['id'];
    }
}

if (isset($_POST['submit'])) {
    $activeProject['id'] = intval($_POST['project']);
    if ($activeProject['id']) {
        $activeProject['aloneGetStr'] = '?project_id=' . $activeProject['id'];
        $activeProject['additionGetStr'] = '&project_id=' . $activeProject['id'];
    }

    $fieldsValues = checkTaskFields($dbConn, $currentUserId, $_POST, $_FILES);
    if (!$fieldsValues['errors']['errorFlag']) {
        $sql = getTaskInsertSql($currentUserId, $activeProject['id'], $fieldsValues['fieldValues']['name'], $fieldsValues['fieldValues']['date'], $fieldsValues['fieldValues']['file']);
        execSql($dbConn, $sql);
        header('Location: /index.php');
    }
}

$sql = 'SELECT projects.project_id, projects.project_name, COUNT(tasks.task_id) AS task_count
        FROM projects
        LEFT JOIN tasks
        ON projects.project_id = tasks.project_id
        WHERE projects.user_id = ' . $currentUserId . '
        GROUP BY projects.project_id';
$projects = getAssocArrayFromSQL($dbConn, $sql);

$pageTitle = "Дела в порядке - Добавление задачи";
$content = include_template('task_add.php', ['projects' => $projects, 'activeProject' => $activeProject, 'fieldsValues'=> $fieldsValues]);
$htmlData = include_template('layout.php', ['projects' => $projects, 'pageTitle' => $pageTitle, 'content' => $content, 'activeProject' => $activeProject]);
print($htmlData);
?>

