<?php
require_once ('functions.php');

$dbConn = mysqli_connect('localhost', 'root', '', 'doingsdone');
mysqli_set_charset($dbConn, "utf8");
if ($dbConn == false) {
  print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
}

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$sql = 'SELECT `project_id`, `project_name` FROM `projects` WHERE `user_id` = 2';
$projects = mysqli_fetch_all(mysqli_query($dbConn, $sql), MYSQLI_ASSOC);

$sql = 'SELECT `project_id`, `task_name`, `task_deadline`, `task_complete_status` FROM `tasks` WHERE `user_id` = 2';
$tasks = mysqli_fetch_all(execSql ($dbConn, $sql), MYSQLI_ASSOC);

$pageTitle = "Дела в порядке";
$content = include_template('index.php', ["tasks" => $tasks, "show_complete_tasks" => $show_complete_tasks]);
$htmlData = include_template('layout.php', ["tasks" => $tasks, "projects" => $projects, "pageTitle" => $pageTitle, "content" => $content]);
print($htmlData);
?>

