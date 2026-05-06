<?php
/**
 * Bootstrap file for PHPUnit tests
 */

// Autoloader dla klas testowych
spl_autoload_register(function ($class) {
    // Konwersja namespace na ścieżkę
    $prefix = 'App\\Models\\';
    $base_dir = __DIR__ . '/../src/models/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Definicje stałych dla środowiska testowego
define('TESTING', true);
define('DB_HOST', 'localhost');
define('DB_NAME', 'klub_seniora_test');
define('DB_USER', 'root');
define('DB_PASS', '');

// Wyłączenie wyświetlania błędów w testach (aby nie zaśmiecać outputu)
error_reporting(E_ALL);
ini_set('display_errors', '0');
