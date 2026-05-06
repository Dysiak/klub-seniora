<?php
require_once 'User.php';

/**
 * Klasa Instruktor - dziedziczy po User
 * Reprezentuje instruktora prowadzącego zajęcia
 * 
 * KLASYFIKACJA METOD:
 * [UC] - Metody obsługujące główne przypadki użycia
 * [REL] - Metody do implementacji powiązań między klasami
 * [PROJ] - Metody wynikające z decyzji projektowych
 * [HELP] - Metody pomocnicze
 */
class Instruktor extends User {
    // Dodatkowe atrybuty specyficzne dla instruktora
    private $specjalizacje; // Lista specjalizacji (np. joga, taniec)
    private $dostepnosc; // Harmonogram dostępności
    
    /**
     * Konstruktor klasy Instruktor
     */
    public function __construct($dane = []) {
        parent::__construct($dane);
        $this->rola = 'instruktor';
        $this->specjalizacje = $dane['specjalizacje'] ?? [];
        $this->dostepnosc = $dane['dostepnosc'] ?? [];
    }
    
    /**
     * [PROJ] Implementacja metody abstrakcyjnej z klasy User
     * Wynikająca z decyzji projektowej - różne role mają różne uprawnienia
     * @return array - uprawnienia instruktora
     */
    public function getUprawnienia() {
        return [
            'przeglad_zajec' => true,
            'przeglad_moich_zajec' => true,
            'przeglad_list_uczestnikow' => true,
            'edycja_profilu' => true,
            'zmiana_hasla' => true,
            'zapis_na_zajecia' => false,
            'zarzadzanie_zajecia' => false,
            'zarzadzanie_uzytkownikami' => false
        ];
    }
    
    /**
     * [UC] Metoda obsługująca przypadek użycia: Przeglądanie listy uczestników
     * @param int $idZajec - ID zajęć
     * @return array - ['sukces' => bool, 'uczestnicy' => array, 'komunikat' => string]
     */
    public function pobierzListeUczestnikow($idZajec) {
        try {
            require_once __DIR__ . '/Database.php';
            $db = Database::getInstance()->getConnection();
            
            // 1. Sprawdzenie czy zajęcia są prowadzone przez tego instruktora
            $stmt = $db->prepare("SELECT * FROM zajecia WHERE id = ? AND id_instruktora = ?");
            $stmt->execute([$idZajec, $this->id]);
            $zajecia = $stmt->fetch();
            
            if (!$zajecia) {
                return [
                    'sukces' => false,
                    'uczestnicy' => [],
                    'komunikat' => 'Nie prowadzisz tych zajęć'
                ];
            }
            
            // 2. Pobranie listy uczestników
            $sql = "SELECT u.id, u.imie, u.nazwisko, u.email, u.telefon, 
                           r.data_rezerwacji, r.status, r.potwierdzenie
                    FROM rezerwacje r
                    JOIN users u ON r.id_seniora = u.id
                    WHERE r.id_zajec = ? AND r.status = 'aktywna'
                    ORDER BY u.nazwisko, u.imie";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$idZajec]);
            $uczestnicy = $stmt->fetchAll();
            
            return [
                'sukces' => true,
                'uczestnicy' => $uczestnicy,
                'komunikat' => 'Znaleziono ' . count($uczestnicy) . ' uczestników',
                'zajecia' => $zajecia
            ];
            
        } catch (Exception $e) {
            return [
                'sukces' => false,
                'uczestnicy' => [],
                'komunikat' => 'Błąd pobierania listy: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * [REL] Metoda do implementacji powiązania Instruktor → Zajęcia
     * Pobieranie zajęć prowadzonych przez instruktora
     * @param string $status - opcjonalnie filtruj po statusie
     * @return array - tablica z danymi zajęć
     */
    public function getMojeZajecia($status = null) {
        try {
            require_once __DIR__ . '/Database.php';
            $db = Database::getInstance()->getConnection();
            
            $sql = "SELECT z.*, s.nazwa as sala_nazwa, 
                           (SELECT COUNT(*) FROM rezerwacje WHERE id_zajec = z.id AND status = 'aktywna') as liczba_uczestnikow
                    FROM zajecia z
                    LEFT JOIN sale s ON z.id_sali = s.id
                    WHERE z.id_instruktora = ?";
            
            $params = [$this->id];
            
            if ($status !== null) {
                $sql .= " AND z.status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY z.data DESC, z.godzina_od DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log('Błąd getMojeZajecia: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * [HELP] Metoda pomocnicza: Sprawdzanie dostępności w danym terminie
     * @param string $data - data w formacie YYYY-MM-DD
     * @param string $godzinaOd - godzina rozpoczęcia HH:MM
     * @param string $godzinaDo - godzina zakończenia HH:MM
     * @return bool - true jeśli instruktor jest dostępny
     */
    public function sprawdzDostepnosc($data, $godzinaOd, $godzinaDo) {
        // TODO: Implementacja - sprawdzenie w bazie czy nie ma konfliktu
        return true;
    }
    
    /**
     * [HELP] Metoda pomocnicza: Dodawanie specjalizacji
     * @param string $specjalizacja
     */
    public function dodajSpecjalizacje($specjalizacja) {
        if (!in_array($specjalizacja, $this->specjalizacje)) {
            $this->specjalizacje[] = $specjalizacja;
        }
    }
    
    /**
     * [HELP] Metoda pomocnicza: Pobieranie specjalizacji
     * @return array
     */
    public function getSpecjalizacje() {
        return $this->specjalizacje;
    }
    
    /**
     * [HELP] Metoda pomocnicza: Ustawianie dostępności
     * @param array $dostepnosc
     */
    public function setDostepnosc($dostepnosc) {
        $this->dostepnosc = $dostepnosc;
    }
    
    /**
     * [HELP] Metoda pomocnicza: Pobieranie dostępności
     * @return array
     */
    public function getDostepnosc() {
        return $this->dostepnosc;
    }
}
?>
