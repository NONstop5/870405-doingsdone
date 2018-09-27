<?php

// Функция подсчета количества задач в проекте
function calcTasksQuantity ($tasks, $project) {
  $tasksQuantity = 0;
  foreach ($tasks as $task) {
    if ($project['project_id'] === $task['project_id']) {
      $tasksQuantity++;
    }
  }
  return $tasksQuantity;
}

// Функция шаблонизатор
function include_template ($name, $data) {
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

// Функция обработки запроса к БД
function execSql ($conn, $sql) {
  $result = mysqli_query($conn, $sql);
  if ($result == false) {
    print("Ошибка при выполнении запроса:" . mysqli_error($conn));
  }
  return $result;
}
?>
