<?php
/**
 * API Endpoint: Edycja użytkownika
 * Metoda: POST
 * Body: FormData z polami: id, email, rola, imie, nazwisko, telefon, haslo (opcjonalnie)
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
    // Walidacja ID użytkownika
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        echo json_encode([
            'sukces' => false,
            'komunikat' => 'Brak ID użytkownika'
        ]);
        exit;
    }
    
    $userId = (int)$_POST['id'];
    $db = Database::getInstance()->getConnection();
    
    // Przygotowanie zapytania UPDATE
    $pola = [];
    $wartosci = [];
    
    if (isset($_POST['email']) && !empty($_POST['email'])) {
        $pola[] = "email = ?";
        $wartosci[] = $_POST['email'];
    }
    
    if (isset($_POST['rola']) && !empty($_POST['rola'])) {
        $pola[] = "rola = ?";
        $wartosci[] = $_POST['rola'];
    }
    
    if (isset($_POST['imie']) && !empty($_POST['imie'])) {
        $pola[] = "imie = ?";
        $wartosci[] = $_POST['imie'];
    }
    
    if (isset($_POST['nazwisko']) && !empty($_POST['nazwisko'])) {
        $pola[] = "nazwisko = ?";
        $wartosci[] = $_POST['nazwisko'];
    }
    
    if (isset($_POST['telefon'])) {
        // Walidacja numeru telefonu - jeśli nie jest pusty, musi mieć 9 cyfr
        if (!empty($_POST['telefon']) && !preg_match('/^[0-9]{9}$/', $_POST['telefon'])) {
            echo json_encode([
                'sukces' => false,
                'komunikat' => 'Numer telefonu musi zawierać dokładnie 9 cyfr'
            ]);
            exit;
        }
        $pola[] = "telefon = ?";
        $wartosci[] = $_POST['telefon'];
    }
    
    // Jeśli podano nowe hasło, zaktualizuj je
    if (isset($_POST['haslo']) && !empty($_POST['haslo'])) {
        $pola[] = "haslo = ?";
        $wartosci[] = password_hash($_POST['haslo'], PASSWORD_DEFAULT);
    }
    
    if (empty($pola)) {
        echo json_encode([
            'sukces' => false,
            'komunikat' => 'Brak danych do aktualizacji'
        ]);
        exit;
    }
    
    $wartosci[] = $userId;
    $sql = "UPDATE users SET " . implode(", ", $pola) . " WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute($wartosci);
    
    echo json_encode([
        'sukces' => true,
        'komunikat' => 'Dane użytkownika zostały zaktualizowane'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'sukces' => false,
        'komunikat' => 'Błąd serwera: ' . $e->getMessage()
    ]);
}
?>
