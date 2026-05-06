<?php
/**
 * API Endpoint: Usunięcie zajęć
 * Metoda: POST
 * Body: {id_zajec: number}
 */
session_start();
header('Content-Type: application/json');

// Sprawdzenie sesji i roli
if (!isset($_SESSION['user_id']) || $_SESSION['user_rola'] !== 'koordynator') {
    echo json_encode([
        'sukces' => false,
        'komunikat' => 'Brak uprawnień'
    ]);
    exit;
}

require_once __DIR__ . '/../../../src/models/Koordynator.php';

try {
    // Odczytanie danych JSON
    $dane = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($dane['id_zajec'])) {
        echo json_encode([
            'sukces' => false,
            'komunikat' => 'Brak ID zajęć'
        ]);
        exit;
    }
    
    // Utworzenie obiektu Koordynator i usunięcie zajęć
    $koordynator = new Koordynator(['id' => $_SESSION['user_id']]);
    $wynik = $koordynator->usunZajecia($dane['id_zajec']);
    
    echo json_encode($wynik);
    
} catch (Exception $e) {
    echo json_encode([
        'sukces' => false,
        'komunikat' => 'Błąd serwera: ' . $e->getMessage()
    ]);
}
?>
