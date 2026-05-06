<?php
/**
 * API Endpoint: Edycja zajęć
 * Metoda: POST
 * Body: FormData z polami: id, nazwa, data, godzina_od, godzina_do, id_instruktora, id_sali, opis, limit_miejsc, status
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
    // Walidacja ID zajęć
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        echo json_encode([
            'sukces' => false,
            'komunikat' => 'Brak ID zajęć'
        ]);
        exit;
    }
    
    // Przygotowanie danych do aktualizacji
    $noweDane = [];
    $dozwolone = ['nazwa', 'opis', 'data', 'godzina_od', 'godzina_do', 'limit_miejsc', 'id_instruktora', 'id_sali', 'status'];
    
    foreach ($dozwolone as $pole) {
        if (isset($_POST[$pole])) {
            if ($pole === 'id_sali' && empty($_POST[$pole])) {
                $noweDane[$pole] = null;
            } elseif (in_array($pole, ['limit_miejsc', 'id_instruktora', 'id_sali'])) {
                $noweDane[$pole] = (int)$_POST[$pole];
            } else {
                $noweDane[$pole] = $_POST[$pole];
            }
        }
    }
    
    // Walidacja: sprawdź czy limit miejsc nie przekracza pojemności sali
    if (isset($noweDane['limit_miejsc']) && isset($noweDane['id_sali']) && $noweDane['id_sali'] !== null) {
        require_once __DIR__ . '/../../../src/models/Database.php';
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT pojemnosc FROM sale WHERE id = ?");
        $stmt->execute([$noweDane['id_sali']]);
        $sala = $stmt->fetch();
        
        if ($sala && $noweDane['limit_miejsc'] > $sala['pojemnosc']) {
            echo json_encode([
                'sukces' => false,
                'komunikat' => "Limit miejsc ({$noweDane['limit_miejsc']}) przekracza pojemność sali ({$sala['pojemnosc']})"
            ]);
            exit;
        }
    }
    
    // Utworzenie obiektu Koordynator i edycja zajęć
    $koordynator = new Koordynator(['id' => $_SESSION['user_id']]);
    $wynik = $koordynator->edytujZajecia((int)$_POST['id'], $noweDane);
    
    echo json_encode($wynik);
    
} catch (Exception $e) {
    echo json_encode([
        'sukces' => false,
        'komunikat' => 'Błąd serwera: ' . $e->getMessage()
    ]);
}
?>
