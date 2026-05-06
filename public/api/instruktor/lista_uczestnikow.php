<?php
/**
 * API Endpoint: Pobierz listę uczestników zajęć
 * Metoda: GET
 * Query: ?id_zajec=number
 */
session_start();
header('Content-Type: application/json');

// Sprawdzenie sesji i roli
if (!isset($_SESSION['user_id']) || $_SESSION['user_rola'] !== 'instruktor') {
    echo json_encode([
        'sukces' => false,
        'komunikat' => 'Brak uprawnień'
    ]);
    exit;
}

require_once __DIR__ . '/../../../src/models/Instruktor.php';

try {
    if (!isset($_GET['id_zajec'])) {
        echo json_encode([
            'sukces' => false,
            'komunikat' => 'Brak ID zajęć'
        ]);
        exit;
    }
    
    // Utworzenie obiektu Instruktor i pobranie listy uczestników
    $instruktor = new Instruktor(['id' => $_SESSION['user_id']]);
    $wynik = $instruktor->pobierzListeUczestnikow($_GET['id_zajec']);
    
    if ($wynik['sukces']) {
        echo json_encode([
            'sukces' => true,
            'uczestnicy' => $wynik['uczestnicy']
        ]);
    } else {
        echo json_encode($wynik);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'sukces' => false,
        'komunikat' => 'Błąd serwera: ' . $e->getMessage()
    ]);
}
?>
