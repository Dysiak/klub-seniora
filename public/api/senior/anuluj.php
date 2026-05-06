<?php
/**
 * API Endpoint: Anulowanie rezerwacji
 * Metoda: POST
 * Body: {id_rezerwacji: number}
 */
session_start();
header('Content-Type: application/json');

// Sprawdzenie sesji i roli
if (!isset($_SESSION['user_id']) || $_SESSION['user_rola'] !== 'senior') {
    echo json_encode([
        'sukces' => false,
        'komunikat' => 'Brak uprawnień'
    ]);
    exit;
}

require_once __DIR__ . '/../../../src/models/Senior.php';

try {
    // Odczytanie danych JSON
    $dane = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($dane['id_rezerwacji'])) {
        echo json_encode([
            'sukces' => false,
            'komunikat' => 'Brak ID rezerwacji'
        ]);
        exit;
    }
    
    // Utworzenie obiektu Senior i anulowanie rezerwacji
    $senior = new Senior(['id' => $_SESSION['user_id']]);
    $wynik = $senior->anulujRezerwacje($dane['id_rezerwacji']);
    
    echo json_encode($wynik);
    
} catch (Exception $e) {
    echo json_encode([
        'sukces' => false,
        'komunikat' => 'Błąd serwera: ' . $e->getMessage()
    ]);
}
?>
