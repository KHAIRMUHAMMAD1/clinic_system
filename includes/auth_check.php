<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /clinic_system/login.php');
    exit;
}
require_once __DIR__ . '/config.php';
?>
