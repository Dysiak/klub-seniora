<?php
/**
 * API Endpoint: Usunięcie użytkownika
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
    
    // Zabezpieczenie - nie można usunąć samego siebie
    if ($userId === $_SESSION['user_id']) {
        echo json_encode([
            'sukces' => false,
            'komunikat' => 'Nie możesz usunąć swojego konta'
        ]);
        exit;
    }
    
    $db = Database::getInstance()->getConnection();
    
    // Sprawdzenie czy użytkownik istnieje
    $sql = "SELECT id FROM users WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$userId]);
    
    if (!$stmt->fetch()) {
        echo json_encode([
            'sukces' => false,
            'komunikat' => 'Użytkownik nie istnieje'
        ]);
        exit;
    }
    
    // Usunięcie użytkownika (CASCADE usunie powiązane rekordy)
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$userId]);
    
    echo json_encode([
        'sukces' => true,
        'komunikat' => 'Użytkownik został usunięty'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'sukces' => false,
        'komunikat' => 'Błąd serwera: ' . $e->getMessage()
    ]);
}
?>
