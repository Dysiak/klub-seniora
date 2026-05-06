<?php
/**
 * API Endpoint: Utworzenie nowego użytkownika
 * Metoda: POST
 * Body: FormData z polami: login, email, haslo, rola, imie, nazwisko, telefon, data_urodzenia, adres
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

require_once __DIR__ . '/../../../src/models/User.php';
require_once __DIR__ . '/../../../src/models/Senior.php';
require_once __DIR__ . '/../../../src/models/Instruktor.php';
require_once __DIR__ . '/../../../src/models/Koordynator.php';
require_once __DIR__ . '/../../../src/models/Administrator.php';

try {
    // Walidacja wymaganych pól
    $wymagane = ['login', 'email', 'haslo', 'rola', 'imie', 'nazwisko'];
    foreach ($wymagane as $pole) {
        if (!isset($_POST[$pole]) || empty($_POST[$pole])) {
            echo json_encode([
                'sukces' => false,
                'komunikat' => "Pole {$pole} jest wymagane"
            ]);
            exit;
        }
    }
    
    // Walidacja numeru telefonu
    if (isset($_POST['telefon']) && !empty($_POST['telefon'])) {
        if (!preg_match('/^[0-9]{9}$/', $_POST['telefon'])) {
            echo json_encode([
                'sukces' => false,
                'komunikat' => 'Numer telefonu musi zawierać dokładnie 9 cyfr'
            ]);
            exit;
        }
    }
    
    // Przygotowanie danych użytkownika
    $dane = [
        'login' => $_POST['login'],
        'email' => $_POST['email'],
        'haslo' => password_hash($_POST['haslo'], PASSWORD_DEFAULT),
        'rola' => $_POST['rola'],
        'imie' => $_POST['imie'],
        'nazwisko' => $_POST['nazwisko'],
        'telefon' => $_POST['telefon'] ?? null,
        'data_urodzenia' => $_POST['data_urodzenia'] ?? null,
        'czy_aktywny' => true
    ];
    
    // Utworzenie obiektu odpowiedniej klasy
    $klasy = [
        'senior' => 'Senior',
        'instruktor' => 'Instruktor',
        'koordynator' => 'Koordynator',
        'administrator' => 'Administrator'
    ];
    
    $klasa = $klasy[$dane['rola']] ?? null;
    if (!$klasa) {
        echo json_encode([
            'sukces' => false,
            'komunikat' => 'Nieprawidłowa rola użytkownika'
        ]);
        exit;
    }
    
    $user = new $klasa($dane);
    $wynik = $user->zapisz();
    
    echo json_encode($wynik);
    
} catch (Exception $e) {
    echo json_encode([
        'sukces' => false,
        'komunikat' => 'Błąd serwera: ' . $e->getMessage()
    ]);
}
?>
