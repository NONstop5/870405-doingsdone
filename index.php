<?php
require_once ('constants.php');
require_once ('functions.php');

$dbConn = connectDb($host, $dbUserName, $dbUserPassw, $dbName);

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$currentUserId = 1;
$projectFilterQuery = '';
$activeProject = ['id' => '', 'getStr' => ''];

if (isset($_GET['project_id'])) {
    $activeProject['id'] = intval($_GET['project_id']);
    $projectFilterQuery = ' AND `project_id` = ' . $activeProject['id'];

    $sql = 'SELECT project_id FROM projects WHERE user_id = ' . $currentUserId . ' AND project_id = ' . $activeProject['id'];
    $projects = execSql($dbConn, $sql);

    if (!mysqli_num_rows($projects)) {
        header('Location: ' . $_SERVER['REQUEST_URI'], true, 404);
        print('Страница не обнаружена!');
        exit();
    }

    $activeProject['getStr'] = '?project_id=' . $activeProject['id'];
}

$sql = 'SELECT projects.project_id, projects.project_name, COUNT(tasks.task_id) AS task_count
        FROM projects
        LEFT JOIN tasks
        ON projects.project_id = tasks.project_id
        WHERE projects.user_id = ' . $currentUserId . '
        GROUP BY projects.project_id';
$projects = mysqli_fetch_all(mysqli_query($dbConn, $sql), MYSQLI_ASSOC);

$sql = 'SELECT task_id, task_name, task_deadline, task_complete_status, task_file
        FROM tasks
        WHERE user_id = ' . $currentUserId . $projectFilterQuery . ' ORDER BY task_id DESC';
$tasks = mysqli_fetch_all(execSql ($dbConn, $sql), MYSQLI_ASSOC);

$pageTitle = "Дела в порядке";
$content = include_template('index.php', ["tasks" => $tasks, "show_complete_tasks" => $show_complete_tasks]);
$htmlData = include_template('layout.php', ["tasks" => $tasks, "projects" => $projects, "pageTitle" => $pageTitle, "content" => $content, "activeProject" => $activeProject]);
print($htmlData);
?>
