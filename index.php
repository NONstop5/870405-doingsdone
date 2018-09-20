<?php
require_once ('functions.php');

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);
$projects = ['Входящие', 'Учеба', 'Работа', 'Домашние дела', 'Авто'];
$tasks = [
    [
        'taskName' => 'Собеседование в IT <b>компании</b>',
        'taskDate' => '01.12.2018',
        'project' => $projects[2],
        'completed' => false
    ],
    [
        'taskName' => 'Выполнить тестовое задание',
        'taskDate' => '25.12.2018',
        'project' => $projects[2],
        'completed' => false
    ],
    [
        'taskName' => 'Сделать задание первого раздела',
        'taskDate' => '21.12.2018',
        'project' => $projects[1],
        'completed' => true
    ],
    [
        'taskName' => 'Встреча с другом',
        'taskDate' => '22.12.2018',
        'project' => $projects[0],
        'completed' => false
    ],
    [
        'taskName' => 'Купить корм для кота',
        'taskDate' => 'Нет',
        'project' => $projects[3],
        'completed' => false
    ],
    [
        'taskName' => 'Заказать пиццу',
        'taskDate' => 'Нет',
        'project' => $projects[3],
        'completed' => false
    ]
];
$pageTitle = "Дела в порядке";
$content = include_template('index.php', ["tasks" => $tasks, "show_complete_tasks" => $show_complete_tasks]);
$htmlData = include_template('layout.php', ["tasks" => $tasks, "projects" => $projects, "pageTitle" => $pageTitle, "content" => $content]);
print($htmlData);
?>

