<?php
require_once ('functions.php');

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);
$projects = ['Входящие', 'Учеба', 'Работа', 'Домашние дела', 'Авто'];
$tasks = [
    [
        'taskName' => 'Собеседование в IT компании',
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

$content = print(include_template('index.php', $tasks)); // что сюда передовать ? (списки категорий/лотов/задач) - что именно то ?
print(include_template('layout.php', $tasks)); // куда тут еще title передавать
?>

