<?php
/**
 * Wylogowanie użytkownika
 */
session_start();
require_once __DIR__ . '/../src/models/User.php';

User::wyloguj();

header('Location: index.php?wylogowano=1');
exit;
