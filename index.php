<?php

require_once 'constants.php';
require_once 'functions.php';

sessionCheck();

$dbConn = connectDb($host, $dbUserName, $dbUserPassw, $dbName);

$currentUserId = $_SESSION['userId'];
$activeUserName = getUserName($dbConn, $currentUserId);

$projectFilterQuery = '';
$taskFilterQuery = '';
$taskSearchQuery = '';
$activeProject = [
    'id' => '',
    'aloneGetStr' => '',
    'additionGetStr' => ''
];
$activeTaskFilter = ['', '', '', ''];

if (isset($_GET['project_id'])) {
    $activeProject['id'] = intval($_GET['project_id']);
    $projectFilterQuery = ' AND project_id = ' . $activeProject['id'];

    $sql = 'SELECT project_id FROM projects WHERE user_id = ' . $currentUserId . ' AND project_id = ' . $activeProject['id'];
    $projects = execSql($dbConn, $sql);

    if (!mysqli_num_rows($projects)) {
        header('Location: ' . $_SERVER['REQUEST_URI'], true, 404);
        print('Страница не обнаружена!');
        exit();
    }

    $activeProject['aloneGetStr'] = '?project_id=' . $activeProject['id'];
    $activeProject['additionGetStr'] = '&project_id=' . $activeProject['id'];
}

if (isset($_GET['show_completed'])) {
    $showCompleteTasks = intval($_GET['show_completed']);
} else {
    $showCompleteTasks = rand(0, 1);
}

if (isset($_GET['task_id']) && isset($_GET['check'])) {
    $sql = 'UPDATE tasks SET task_complete_status = ' . intval($_GET['check']) . ' WHERE task_id = ' . intval($_GET['task_id']);
    execSql($dbConn, $sql);
}

if (isset($_GET['task_filter'])) {
    $activeTaskFilter[intval($_GET['task_filter'])] = ' tasks-switch__item--active';
    $taskFilterQuery = getTaskFilterQuery($_GET);
} else {
    $activeTaskFilter[0] = ' tasks-switch__item--active';
}

if (isset($_POST['submit']) && isset($_POST['search'])) {
    $taskSearchStr = clearUserInputStr($_POST['search']);
    if (!empty($taskSearchStr)) {
        $taskSearchQuery = ' AND MATCH(task_name) AGAINST(\'' . $taskSearchStr . '\' IN NATURAL LANGUAGE MODE)';
    }
}

$sql = 'SELECT projects.project_id, projects.project_name, COUNT(tasks.task_id) AS task_count
        FROM projects
        LEFT JOIN tasks
        ON projects.project_id = tasks.project_id
        WHERE projects.user_id = ' . $currentUserId . '
        GROUP BY projects.project_id';
$projects = getAssocArrayFromSQL($dbConn, $sql);

$sql = 'SELECT task_id, task_name, task_deadline, task_complete_status, task_file
        FROM tasks
        WHERE user_id = ' . $currentUserId . $projectFilterQuery . $taskFilterQuery . $taskSearchQuery . ' ORDER BY task_id DESC';
$tasks = getAssocArrayFromSQL($dbConn, $sql);

$pageTitle = "Дела в порядке";
$content = include_template('index.php', ["tasks" => $tasks, "showCompleteTasks" => $showCompleteTasks, "activeProject" => $activeProject, 'activeTaskFilter' => $activeTaskFilter]);
$htmlData = include_template('layout.php', ["tasks" => $tasks, "projects" => $projects, "pageTitle" => $pageTitle, "content" => $content, "activeProject" => $activeProject, 'activeUserName' => $activeUserName]);
print($htmlData);
