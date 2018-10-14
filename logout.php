<?php

require_once 'constants.php';
require_once 'functions.php';

session_start();

if (!empty($_SESSION['userName'])) {
    session_destroy();
}

$pageTitle = "Дела в порядке - Добро пожаловать";
$htmlData = include_template('guest.php', ['pageTitle' => $pageTitle]);
print($htmlData);
