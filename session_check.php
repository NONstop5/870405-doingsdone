<?php

session_start();

if (empty($_SESSION['userName'])) {
    header('Location: /logout.php');
    exit();
}
