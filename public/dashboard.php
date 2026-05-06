<?php
/**
 * Dashboard - panel główny (routing do odpowiedniego panelu w zależności od roli)
 */
session_start();

// Sprawdzenie czy użytkownik jest zalogowany
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Routing w zależności od roli
$rola = $_SESSION['user_rola'];

switch ($rola) {
    case 'senior':
        include 'dashboards/senior_dashboard.php';
        break;
    case 'instruktor':
        include 'dashboards/instruktor_dashboard.php';
        break;
    case 'koordynator':
        include 'dashboards/koordynator_dashboard.php';
        break;
    case 'administrator':
        include 'dashboards/admin_dashboard.php';
        break;
    default:
        session_destroy();
        header('Location: login.php');
        exit;
}
