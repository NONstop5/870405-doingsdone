<?php
// Функция подсчета количества задач в проекте
function calcTasksQuantity ($tasks, $project) {
    $tasksQuantity = 0;
    foreach ($tasks as $task) {
        if ($project === $task['project']) {
            $tasksQuantity++;
        }
    }
    return $tasksQuantity;
}

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
?>
