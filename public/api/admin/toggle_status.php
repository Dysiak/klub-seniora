<?php
/**
 * API Endpoint: Zmiana statusu użytkownika (aktywny/nieaktywny)
 * Metoda: POST
 * Body: {id: number}
 */
session_start();
header('Content-Type: application/json');

// Sprawdzenie sesji i roli
if (!isset($_SESSION['user_id']) || $_SESSION['user_rola'] !== 'administrator') {
    echo json_encode([
        'sukces' => false,
        'komunikat' => 'Brak uprawnień'
    ]);
    exit;
}

require_once __DIR__ . '/../../../src/models/Database.php';

try {
    // Odczytanie danych JSON
    $dane = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($dane['id'])) {
        echo json_encode([
            'sukces' => false,
            'komunikat' => 'Brak ID użytkownika'
        ]);
        exit;
    }
    
    $userId = (int)$dane['id'];
    
    // Zabezpieczenie - nie można zablokować samego siebie
    if ($userId === $_SESSION['user_id']) {
        echo json_encode([
            'sukces' => false,
            'komunikat' => 'Nie możesz zablokować swojego konta'
        ]);
        exit;
    }
    
    $db = Database::getInstance()->getConnection();
    
    // Odczytanie obecnego statusu
    $sql = "SELECT czy_aktywny FROM users WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode([
            'sukces' => false,
            'komunikat' => 'Użytkownik nie istnieje'
        ]);
        exit;
    }
    
    // Zmiana statusu na przeciwny
    $nowyStatus = !$user['czy_aktywny'];
    $sql = "UPDATE users SET czy_aktywny = ? WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$nowyStatus ? 1 : 0, $userId]);
    
    echo json_encode([
        'sukces' => true,
        'komunikat' => $nowyStatus ? 'Użytkownik został odblokowany' : 'Użytkownik został zablokowany'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'sukces' => false,
        'komunikat' => 'Błąd serwera: ' . $e->getMessage()
    ]);
}
?>
