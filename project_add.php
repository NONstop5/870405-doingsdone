<?php

require_once 'session_check.php';
require_once 'constants.php';
require_once 'functions.php';

$dbConn = connectDb($host, $dbUserName, $dbUserPassw, $dbName);

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$currentUserId = $_SESSION['userId'];
$activeProject = ['id' => '', 'getStr' => ''];
$fieldsValues = createEmptyProjectFieldValuesArray();

if (isset($_POST['submit'])) {
    $fieldsValues = checkProjectFields($dbConn, $_POST);
    if (!$fieldsValues['errors']['errorFlag']) {
        $sql = getProjectInsertSql($currentUserId, $fieldsValues['fieldValues']['name']);
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

$pageTitle = "Дела в порядке - Добавление проекта";
$content = include_template('project_add.php', ['fieldsValues'=> $fieldsValues]);
$htmlData = include_template('layout.php', ['pageTitle' => $pageTitle, 'projects' => $projects, 'content' => $content, 'activeProject' => $activeProject]);
print($htmlData);
?>
