<?php

require_once 'constants.php';
require_once 'functions.php';

clearSession();

$pageTitle = "Дела в порядке - Добро пожаловать";
$htmlData = include_template('guest.php', ['pageTitle' => $pageTitle]);
print($htmlData);
