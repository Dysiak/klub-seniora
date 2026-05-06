<?php
require_once 'User.php';

/**
 * Klasa Administrator - dziedziczy po User
 * Reprezentuje administratora z pełnymi uprawnieniami systemowymi
 * 
 * KLASYFIKACJA METOD:
 * [UC] - Metody obsługujące główne przypadki użycia
 * [REL] - Metody do implementacji powiązań między klasami
 * [PROJ] - Metody wynikające z decyzji projektowych
 * [HELP] - Metody pomocnicze
 */
class Administrator extends User {
    
    /**
     * Konstruktor klasy Administrator
     */
    public function __construct($dane = []) {
        parent::__construct($dane);
        $this->rola = 'administrator';
    }
    
    /**
     * [PROJ] Implementacja metody abstrakcyjnej z klasy User
     * Wynikająca z decyzji projektowej - administrator ma pełne uprawnienia
     * @return array - pełne uprawnienia administratora
     */
    public function getUprawnienia() {
        return [
            'wszystkie' => true,
            'tworzenie_uzytkownikow' => true,
            'edycja_uzytkownikow' => true,
            'usuwanie_uzytkownikow' => true,
            'przypisywanie_rol' => true,
            'blokowanie_kont' => true,
            'resetowanie_hasel' => true,
            'przeglad_logow' => true,
            'zarzadzanie_systemem' => true,
            'dostep_do_wszystkich_funkcji' => true
        ];
    }
    
    /**
     * [UC] Metoda obsługująca przypadek użycia: Tworzenie konta użytkownika
     * @param array $daneUzytkownika - dane nowego użytkownika
     * @return array - ['sukces' => bool, 'id' => int, 'komunikat' => string]
     */
    public function utworzUzytkownika($daneUzytkownika) {
        // TODO: Implementacja z bazą danych
        // 1. Walidacja danych
        // 2. Sprawdzenie unikalności loginu i email
        // 3. Generowanie/hashowanie hasła
        // 4. Utworzenie użytkownika w bazie
        // 5. Przypisanie roli
        
        return [
            'sukces' => false,
            'id' => null,
            'komunikat' => 'Metoda do implementacji'
        ];
    }
    
    /**
     * [UC] Metoda obsługująca przypadek użycia: Edycja użytkownika
     * @param int $idUzytkownika - ID użytkownika do edycji
     * @param array $noweDane - nowe dane użytkownika
     * @return array - ['sukces' => bool, 'komunikat' => string]
     */
    public function edytujUzytkownika($idUzytkownika, $noweDane) {
        // TODO: Implementacja z bazą danych
        // 1. Sprawdzić czy użytkownik istnieje
        // 2. Walidacja nowych danych
        // 3. Sprawdzić unikalność (jeśli zmieniono login/email)
        // 4. Zaktualizować dane w bazie
        
        return [
            'sukces' => false,
            'komunikat' => 'Metoda do implementacji'
        ];
    }
    
    /**
     * [UC] Metoda obsługująca przypadek użycia: Usuwanie użytkownika
     * @param int $idUzytkownika - ID użytkownika do usunięcia
     * @return array - ['sukces' => bool, 'komunikat' => string]
     */
    public function usunUzytkownika($idUzytkownika) {
        // TODO: Implementacja z bazą danych
        // 1. Sprawdzić czy użytkownik istnieje
        // 2. Sprawdzić czy można usunąć (np. czy nie jest to jedyny admin)
        // 3. Usunąć z bazy (lub zaznaczyć jako nieaktywny)
        
        return [
            'sukces' => false,
            'komunikat' => 'Metoda do implementacji'
        ];
    }
    
    /**
     * [UC] Metoda obsługująca przypadek użycia: Blokowanie konta
     * @param int $idUzytkownika
     * @return array - ['sukces' => bool, 'komunikat' => string]
     */
    public function zablokujKonto($idUzytkownika) {
        // TODO: Implementacja
        // 1. Sprawdzić czy użytkownik istnieje
        // 2. Ustawić status czyAktywny na false
        // 3. Opcjonalnie: wylogować użytkownika ze wszystkich sesji
        
        return [
            'sukces' => false,
            'komunikat' => 'Metoda do implementacji'
        ];
    }
    
    /**
     * [UC] Metoda obsługująca przypadek użycia: Odblokowywanie konta
     * @param int $idUzytkownika
     * @return array - ['sukces' => bool, 'komunikat' => string]
     */
    public function odblokujKonto($idUzytkownika) {
        // TODO: Implementacja
        // 1. Sprawdzić czy użytkownik istnieje
        // 2. Ustawić status czyAktywny na true
        
        return [
            'sukces' => false,
            'komunikat' => 'Metoda do implementacji'
        ];
    }
    
    /**
     * [UC] Metoda obsługująca przypadek użycia: Resetowanie hasła
     * @param int $idUzytkownika
     * @return array - ['sukces' => bool, 'noweHaslo' => string, 'komunikat' => string]
     */
    public function resetujHaslo($idUzytkownika) {
        // TODO: Implementacja
        // 1. Sprawdzić czy użytkownik istnieje
        // 2. Wygenerować nowe tymczasowe hasło
        // 3. Zaktualizować w bazie (zahashowane)
        // 4. Opcjonalnie: wysłać email do użytkownika
        
        return [
            'sukces' => false,
            'noweHaslo' => '',
            'komunikat' => 'Metoda do implementacji'
        ];
    }
    
    /**
     * Metoda pomocnicza: Przypisywanie roli użytkownikowi
     * @param int $idUzytkownika
     * @param string $nowaRola
     * @return bool
     */
    public function przypiszRole($idUzytkownika, $nowaRola) {
        // TODO: Implementacja
        // 1. Sprawdzić czy rola jest prawidłowa
        // 2. Zaktualizować w bazie
        $dozwoloneRole = ['senior', 'instruktor', 'koordynator', 'administrator'];
        if (!in_array($nowaRola, $dozwoloneRole)) {
            return false;
        }
        return false;
    }
    
    /**
     * Metoda pomocnicza: Generowanie losowego hasła
     * @param int $dlugosc
     * @return string
     */
    private function generujHaslo($dlugosc = 10) {
        $znaki = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
        $haslo = '';
        for ($i = 0; $i < $dlugosc; $i++) {
            $haslo .= $znaki[random_int(0, strlen($znaki) - 1)];
        }
        return $haslo;
    }
    
    /**
     * Metoda pomocnicza: Pobieranie wszystkich użytkowników
     * @return array
     */
    public function getAllUzytkownicy() {
        // TODO: Implementacja - pobranie z bazy
        return [];
    }
    
    /**
     * Metoda pomocnicza: Pobieranie logów systemowych
     * @param string $dataOd
     * @param string $dataDo
     * @return array
     */
    public function getLogi($dataOd = null, $dataDo = null) {
        // TODO: Implementacja - pobranie z bazy
        return [];
    }
}
?>
