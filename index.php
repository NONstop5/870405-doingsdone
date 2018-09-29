<?php
require_once ('functions.php');

$dbConn = mysqli_connect('localhost', 'root', '', 'doingsdone');
if ($dbConn == false) {
    print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
    exit();
}
mysqli_set_charset($dbConn, "utf8");

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$projectFilterQuery = '';
if (isset($_GET['project_id'])) {
    $project_id = intval($_GET['project_id']);
    $projectFilterQuery = ' AND `project_id` =' . $project_id;
}

$sql = 'SELECT projects.project_id, projects.project_name, COUNT(tasks.task_id) AS `task_count`
        FROM `projects`
        LEFT JOIN `tasks`
        ON projects.project_id = tasks.project_id
        WHERE projects.user_id = 1
        GROUP BY projects.project_id';
$projects = mysqli_fetch_all(mysqli_query($dbConn, $sql), MYSQLI_ASSOC);

$sql = 'SELECT `task_name`, `task_deadline`, `task_complete_status`
        FROM `tasks`
        WHERE `user_id` = 1' . $projectFilterQuery;
$tasks = mysqli_fetch_all(execSql ($dbConn, $sql), MYSQLI_ASSOC);

if (count($projects) && count($tasks)) {
    $pageTitle = "Дела в порядке";
    $content = include_template('index.php', ["tasks" => $tasks, "show_complete_tasks" => $show_complete_tasks]);
    $htmlData = include_template('layout.php', ["tasks" => $tasks, "projects" => $projects, "pageTitle" => $pageTitle, "content" => $content]);
    print($htmlData);
} else {
    exit(header('Location: /error404/'));
}
?>

