<?php
/**
 * Klasa Sala - reprezentuje salę zajęciową w klubie seniora
 * Klasa implementująca związek z Zajecia (jedna sala - wiele zajęć)
 * 
 * KLASYFIKACJA METOD:
 * [UC] - Metody obsługujące główne przypadki użycia
 * [REL] - Metody do implementacji powiązań między klasami
 * [PROJ] - Metody wynikające z decyzji projektowych
 * [HELP] - Metody pomocnicze
 */
require_once __DIR__ . '/Database.php';

class Sala {
    // Atrybuty klasy
    private $id;
    private $nazwa;
    private $pojemnosc; // Maksymalna liczba osób
    private $opis;
    private $wyposazenie; // Tablica z wyposażeniem (np. ['projektor', 'mata do jogi'])
    private $czyDostepna; // Status aktywności sali
    
    /**
     * Konstruktor klasy Sala
     * @param array $dane - dane sali
     */
    public function __construct($dane = []) {
        $this->id = $dane['id'] ?? null;
        $this->nazwa = $dane['nazwa'] ?? '';
        $this->pojemnosc = $dane['pojemnosc'] ?? 0;
        $this->opis = $dane['opis'] ?? '';
        $this->wyposazenie = $dane['wyposazenie'] ?? [];
        $this->czyDostepna = $dane['czy_dostepna'] ?? true;
    }
    
    // ===== GETTERY =====
    public function getId() { return $this->id; }
    public function getNazwa() { return $this->nazwa; }
    public function getPojemnosc() { return $this->pojemnosc; }
    public function getOpis() { return $this->opis; }
    public function getWyposazenie() { return $this->wyposazenie; }
    public function getCzyDostepna() { return $this->czyDostepna; }
    
    // ===== SETTERY =====
    public function setNazwa($nazwa) { $this->nazwa = $nazwa; }
    public function setPojemnosc($pojemnosc) { $this->pojemnosc = $pojemnosc; }
    public function setOpis($opis) { $this->opis = $opis; }
    public function setWyposazenie($wyposazenie) { $this->wyposazenie = $wyposazenie; }
    public function setCzyDostepna($status) { $this->czyDostepna = $status; }
    
    /**
     * [REL] Metoda implementująca relację z Zajecia (JEDEN DO WIELU)
     * Pobiera wszystkie zajęcia odbywające się w tej sali
     * @return array - tablica obiektów Zajecia
     */
    public function getZajecia() {
        // TODO: Implementacja - pobranie z bazy danych
        // require_once 'Zajecia.php';
        // return Zajecia::pobierzDlaSali($this->id);
        return [];
    }
    
    /**
     * [HELP] Metoda pomocnicza: Sprawdzanie dostępności sali w danym terminie
     * Zapobiega konfliktom - dwie grupy nie mogą używać sali w tym samym czasie
     * @param string $data - data w formacie YYYY-MM-DD
     * @param string $godzinaOd - godzina rozpoczęcia HH:MM
     * @param string $godzinaDo - godzina zakończenia HH:MM
     * @param int $idZajecDoPomin - opcjonalnie ID zajęć do pominięcia (przy edycji)
     * @return bool - true jeśli sala jest wolna
     */
    public function sprawdzDostepnosc($data, $godzinaOd, $godzinaDo, $idZajecDoPomin = null) {
        // TODO: Implementacja - sprawdzenie w bazie danych
        // SELECT COUNT(*) FROM zajecia 
        // WHERE id_sali = $this->id 
        // AND data = $data 
        // AND (
        //     (godzina_od < $godzinaDo AND godzina_do > $godzinaOd)
        // )
        // AND ($idZajecDoPomin IS NULL OR id != $idZajecDoPomin)
        
        return true; // Tymczasowo zwracamy true
    }
    
    /**
     * [HELP] Metoda pomocnicza: Pobieranie harmonogramu sali dla danej daty
     * @param string $data - data w formacie YYYY-MM-DD
     * @return array - tablica zajęć w danym dniu
     */
    public function getHarmonogramDnia($data) {
        // TODO: Implementacja - pobranie z bazy danych
        return [];
    }
    
    /**
     * [HELP] Metoda pomocnicza: Dodawanie wyposażenia
     * @param string $element - nazwa elementu wyposażenia
     */
    public function dodajWyposazenie($element) {
        if (!in_array($element, $this->wyposazenie)) {
            $this->wyposazenie[] = $element;
        }
    }
    
    /**
     * [HELP] Metoda pomocnicza: Usuwanie wyposażenia
     * @param string $element - nazwa elementu wyposażenia
     */
    public function usunWyposazenie($element) {
        $klucz = array_search($element, $this->wyposazenie);
        if ($klucz !== false) {
            unset($this->wyposazenie[$klucz]);
            $this->wyposazenie = array_values($this->wyposazenie); // Reindeksowanie
        }
    }
    
    /**
     * [HELP] Metoda pomocnicza: Walidacja danych sali
     * @return array - ['valid' => bool, 'errors' => array]
     */
    public function waliduj() {
        $errors = [];
        
        if (empty($this->nazwa)) {
            $errors[] = 'Nazwa sali jest wymagana';
        }
        
        if ($this->pojemnosc <= 0) {
            $errors[] = 'Pojemność sali musi być większa od 0';
        }
        
        return [
            'valid' => count($errors) === 0,
            'errors' => $errors
        ];
    }
    
    /**
     * Metoda do zapisu sali w bazie danych
     * @return bool - true jeśli sukces
     */
    public function zapisz() {
        // TODO: Implementacja - INSERT do bazy danych
        return false;
    }
    
    /**
     * Metoda do aktualizacji sali w bazie danych
     * @return bool - true jeśli sukces
     */
    public function aktualizuj() {
        // TODO: Implementacja - UPDATE w bazie danych
        return false;
    }
    
    /**
     * Metoda statyczna: Pobieranie sali po ID
     * @param int $id
     * @return Sala|null
     */
    public static function pobierzPoId($id) {
        // TODO: Implementacja - SELECT z bazy danych
        return null;
    }
    
    /**
     * Metoda statyczna: Pobieranie dostępnych sal w danym terminie
     * @param string $data
     * @param string $godzinaOd
     * @param string $godzinaDo
     * @return array - tablica obiektów Sala
     */
    public static function pobierzDostepne($data, $godzinaOd, $godzinaDo) {
        // TODO: Implementacja - SELECT z bazy danych z warunkiem dostępności
        return [];
    }
    
    /**
     * Konwersja obiektu do tablicy
     * @return array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'nazwa' => $this->nazwa,
            'pojemnosc' => $this->pojemnosc,
            'opis' => $this->opis,
            'wyposazenie' => $this->wyposazenie,
            'czy_dostepna' => $this->czyDostepna
        ];
    }
    
    /**
     * [HELP] Pobierz wszystkie sale
     * @return array - tablica sal
     */
    public static function pobierzWszystkie() {
        try {
            $db = Database::getInstance()->getConnection();
            $sql = "SELECT * FROM sale WHERE czy_dostepna = 1 ORDER BY nazwa";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}
?>
