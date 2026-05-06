<?php
/**
 * Klasa Rezerwacja - reprezentuje zapis seniora na zajęcia
 * Klasa implementująca relację między Senior i Zajecia
 * 
 * KLASYFIKACJA METOD:
 * [UC] - Metody obsługujące główne przypadki użycia
 * [REL] - Metody do implementacji powiązań między klasami
 * [PROJ] - Metody wynikające z decyzji projektowych
 * [HELP] - Metody pomocnicze
 */
class Rezerwacja {
    // Atrybuty klasy
    private $id;
    private $idSeniora; // ASOCJACJA z klasą Senior
    private $idZajec; // ASOCJACJA z klasą Zajecia
    private $dataRezerwacji;
    private $status; // 'aktywna', 'anulowana', 'zakonczona'
    private $dataAnulowania;
    private $potwierdzenie; // true/false
    
    /**
     * Konstruktor klasy Rezerwacja
     * @param array $dane - dane rezerwacji
     */
    public function __construct($dane = []) {
        $this->id = $dane['id'] ?? null;
        $this->idSeniora = $dane['id_seniora'] ?? null;
        $this->idZajec = $dane['id_zajec'] ?? null;
        $this->dataRezerwacji = $dane['data_rezerwacji'] ?? date('Y-m-d H:i:s');
        $this->status = $dane['status'] ?? 'aktywna';
        $this->dataAnulowania = $dane['data_anulowania'] ?? null;
        $this->potwierdzenie = $dane['potwierdzenie'] ?? false;
    }
    
    // ===== GETTERY =====
    public function getId() { return $this->id; }
    public function getIdSeniora() { return $this->idSeniora; }
    public function getIdZajec() { return $this->idZajec; }
    public function getDataRezerwacji() { return $this->dataRezerwacji; }
    public function getStatus() { return $this->status; }
    public function getDataAnulowania() { return $this->dataAnulowania; }
    public function getPotwierdzenie() { return $this->potwierdzenie; }
    
    // ===== SETTERY =====
    public function setStatus($status) { $this->status = $status; }
    public function setPotwierdzenie($potwierdzenie) { $this->potwierdzenie = $potwierdzenie; }
    
    /**
     * [REL] Metoda implementująca relację ASOCJACJI z Senior
     * Pobiera obiekt seniora, który dokonał rezerwacji
     * @return Senior|null
     */
    public function getSenior() {
        // TODO: Implementacja - pobranie z bazy danych
        // require_once 'Senior.php';
        // return Senior::pobierzPoId($this->idSeniora);
        return null;
    }
    
    /**
     * [REL] Metoda implementująca relację ASOCJACJI z Zajecia
     * Pobiera obiekt zajęć, na które została dokonana rezerwacja
     * @return Zajecia|null
     */
    public function getZajecia() {
        // TODO: Implementacja - pobranie z bazy danych
        // require_once 'Zajecia.php';
        // return Zajecia::pobierzPoId($this->idZajec);
        return null;
    }
    
    /**
     * [UC] Metoda obsługująca przypadek użycia: Anulowanie rezerwacji
     * @return array - ['sukces' => bool, 'komunikat' => string]
     */
    public function anuluj() {
        // Sprawdzenie czy rezerwacja jest aktywna
        if ($this->status !== 'aktywna') {
            return [
                'sukces' => false,
                'komunikat' => 'Nie można anulować rezerwacji o statusie: ' . $this->status
            ];
        }
        
        // Sprawdzenie czy zajęcia się jeszcze nie odbyły
        // TODO: Pobrać zajęcia i sprawdzić datę
        
        // Zmiana statusu
        $this->status = 'anulowana';
        $this->dataAnulowania = date('Y-m-d H:i:s');
        
        // TODO: Aktualizacja w bazie danych
        // TODO: Zwiększenie liczby wolnych miejsc w zajęciach
        
        return [
            'sukces' => true,
            'komunikat' => 'Rezerwacja została anulowana'
        ];
    }
    
    /**
     * [HELP] Metoda pomocnicza: Potwierdzanie rezerwacji
     * @return bool
     */
    public function potwierdz() {
        $this->potwierdzenie = true;
        $this->status = 'aktywna';
        // TODO: Aktualizacja w bazie danych
        return true;
    }
    
    /**
     * [HELP] Metoda pomocnicza: Sprawdzanie czy rezerwacja jest aktywna
     * @return bool
     */
    public function czyAktywna() {
        return $this->status === 'aktywna';
    }
    
    /**
     * [HELP] Metoda pomocnicza: Walidacja rezerwacji
     * @return array - ['valid' => bool, 'errors' => array]
     */
    public function waliduj() {
        $errors = [];
        
        if (empty($this->idSeniora)) {
            $errors[] = 'ID seniora jest wymagane';
        }
        
        if (empty($this->idZajec)) {
            $errors[] = 'ID zajęć jest wymagane';
        }
        
        return [
            'valid' => count($errors) === 0,
            'errors' => $errors
        ];
    }
    
    /**
     * Metoda do zapisu rezerwacji w bazie danych
     * @return bool - true jeśli sukces
     */
    public function zapisz() {
        // TODO: Implementacja - INSERT do bazy danych
        return false;
    }
    
    /**
     * Metoda do aktualizacji rezerwacji w bazie danych
     * @return bool - true jeśli sukces
     */
    public function aktualizuj() {
        // TODO: Implementacja - UPDATE w bazie danych
        return false;
    }
    
    /**
     * Metoda statyczna: Sprawdzanie czy senior jest już zapisany na dane zajęcia
     * @param int $idSeniora
     * @param int $idZajec
     * @return bool
     */
    public static function sprawdzCzyIstnieje($idSeniora, $idZajec) {
        // TODO: Implementacja - SELECT z bazy danych
        return false;
    }
    
    /**
     * Metoda statyczna: Pobieranie rezerwacji po ID
     * @param int $id
     * @return Rezerwacja|null
     */
    public static function pobierzPoId($id) {
        // TODO: Implementacja - SELECT z bazy danych
        return null;
    }
    
    /**
     * Metoda statyczna: Pobieranie rezerwacji dla seniora
     * @param int $idSeniora
     * @param string $status - opcjonalnie filtrowanie po statusie
     * @return array - tablica obiektów Rezerwacja
     */
    public static function pobierzDlaSeniora($idSeniora, $status = null) {
        // TODO: Implementacja - SELECT z bazy danych
        return [];
    }
    
    /**
     * Metoda statyczna: Pobieranie rezerwacji dla zajęć
     * @param int $idZajec
     * @param string $status - opcjonalnie filtrowanie po statusie
     * @return array - tablica obiektów Rezerwacja
     */
    public static function pobierzDlaZajec($idZajec, $status = null) {
        // TODO: Implementacja - SELECT z bazy danych
        return [];
    }
    
    /**
     * Konwersja obiektu do tablicy
     * @return array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'id_seniora' => $this->idSeniora,
            'id_zajec' => $this->idZajec,
            'data_rezerwacji' => $this->dataRezerwacji,
            'status' => $this->status,
            'data_anulowania' => $this->dataAnulowania,
            'potwierdzenie' => $this->potwierdzenie
        ];
    }
}
?>
