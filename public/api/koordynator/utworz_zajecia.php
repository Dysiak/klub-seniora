<?php
/**
 * API Endpoint: Utworzenie nowych zajęć
 * Metoda: POST
 * Body: FormData z polami: nazwa, data, godzina_od, godzina_do, id_instruktora, id_sali, opis, limit_miejsc
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
    // Walidacja wymaganych pól
    $wymagane = ['nazwa', 'data', 'godzina_od', 'godzina_do', 'id_instruktora', 'limit_miejsc'];
    foreach ($wymagane as $pole) {
        if (!isset($_POST[$pole]) || empty($_POST[$pole])) {
            echo json_encode([
                'sukces' => false,
                'komunikat' => "Pole {$pole} jest wymagane"
            ]);
            exit;
        }
    }
    
    // Przygotowanie danych zajęć
    $daneZajec = [
        'nazwa' => $_POST['nazwa'],
        'opis' => $_POST['opis'] ?? '',
        'data' => $_POST['data'],
        'godzina_od' => $_POST['godzina_od'],
        'godzina_do' => $_POST['godzina_do'],
        'limit_miejsc' => (int)$_POST['limit_miejsc'],
        'id_instruktora' => (int)$_POST['id_instruktora'],
        'id_sali' => !empty($_POST['id_sali']) ? (int)$_POST['id_sali'] : null
    ];
    
    // Walidacja: sprawdź czy limit miejsc nie przekracza pojemności sali
    if ($daneZajec['id_sali'] !== null) {
        require_once __DIR__ . '/../../../src/models/Database.php';
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT pojemnosc FROM sale WHERE id = ?");
        $stmt->execute([$daneZajec['id_sali']]);
        $sala = $stmt->fetch();
        
        if ($sala && $daneZajec['limit_miejsc'] > $sala['pojemnosc']) {
            echo json_encode([
                'sukces' => false,
                'komunikat' => "Limit miejsc ({$daneZajec['limit_miejsc']}) przekracza pojemność sali ({$sala['pojemnosc']})"
            ]);
            exit;
        }
    }
    
    // Utworzenie obiektu Koordynator i utworzenie zajęć
    $koordynator = new Koordynator(['id' => $_SESSION['user_id']]);
    $wynik = $koordynator->utworzZajecia($daneZajec);
    
    echo json_encode($wynik);
    
} catch (Exception $e) {
    echo json_encode([
        'sukces' => false,
        'komunikat' => 'Błąd serwera: ' . $e->getMessage()
    ]);
}
?>
