<?php

require_once 'constants.php';
require_once 'functions.php';

sessionCheck();

$dbConn = connectDb($host, $dbUserName, $dbUserPassw, $dbName);

$currentUserId = $_SESSION['userId'];

$activeProject = ['id' => ''];
$fieldsValues = createEmptyProjectFieldValuesArray();

if (isset($_POST['submit'])) {
    $fieldsValues = checkProjectFields($dbConn, $currentUserId, $_POST);
    if (!$fieldsValues['errors']['errorFlag']) {
        $sql = getProjectInsertSql($currentUserId, $fieldsValues['fieldValues']['name']);
        execSql($dbConn, $sql);
        header('Location: /');
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
