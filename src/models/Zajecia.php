<?php
/**
 * Klasa Zajecia - reprezentuje zajęcia w klubie seniora
 * Klasa implementująca powiązania z Instruktor, Sala i Rezerwacja
 * 
 * KLASYFIKACJA METOD:
 * [UC] - Metody obsługujące główne przypadki użycia
 * [REL] - Metody do implementacji powiązań między klasami
 * [PROJ] - Metody wynikające z decyzji projektowych
 * [HELP] - Metody pomocnicze
 */
class Zajecia {
    // Atrybuty klasy
    private $id;
    private $nazwa;
    private $opis;
    private $data;
    private $godzinaOd;
    private $godzinaDo;
    private $limitMiejsc;
    private $wolneMiejsca;
    private $idInstruktora; // ASOCJACJA z klasą Instruktor
    private $idSali; // ASOCJACJA z klasą Sala
    private $status; // 'planowane', 'odbyte', 'odwolane'
    private $dataUtworzenia;
    
    /**
     * Konstruktor klasy Zajecia
     * @param array $dane - dane zajęć
     */
    public function __construct($dane = []) {
        $this->id = $dane['id'] ?? null;
        $this->nazwa = $dane['nazwa'] ?? '';
        $this->opis = $dane['opis'] ?? '';
        $this->data = $dane['data'] ?? '';
        $this->godzinaOd = $dane['godzina_od'] ?? '';
        $this->godzinaDo = $dane['godzina_do'] ?? '';
        $this->limitMiejsc = $dane['limit_miejsc'] ?? 0;
        $this->wolneMiejsca = $dane['wolne_miejsca'] ?? $this->limitMiejsc;
        $this->idInstruktora = $dane['id_instruktora'] ?? null;
        $this->idSali = $dane['id_sali'] ?? null;
        $this->status = $dane['status'] ?? 'planowane';
        $this->dataUtworzenia = $dane['data_utworzenia'] ?? date('Y-m-d H:i:s');
    }
    
    // ===== GETTERY =====
    public function getId() { return $this->id; }
    public function getNazwa() { return $this->nazwa; }
    public function getOpis() { return $this->opis; }
    public function getData() { return $this->data; }
    public function getGodzinaOd() { return $this->godzinaOd; }
    public function getGodzinaDo() { return $this->godzinaDo; }
    public function getLimitMiejsc() { return $this->limitMiejsc; }
    public function getWolneMiejsca() { return $this->wolneMiejsca; }
    public function getIdInstruktora() { return $this->idInstruktora; }
    public function getIdSali() { return $this->idSali; }
    public function getStatus() { return $this->status; }
    
    // ===== SETTERY =====
    public function setNazwa($nazwa) { $this->nazwa = $nazwa; }
    public function setOpis($opis) { $this->opis = $opis; }
    public function setData($data) { $this->data = $data; }
    public function setGodzinaOd($godzinaOd) { $this->godzinaOd = $godzinaOd; }
    public function setGodzinaDo($godzinaDo) { $this->godzinaDo = $godzinaDo; }
    public function setLimitMiejsc($limit) { $this->limitMiejsc = $limit; }
    public function setIdInstruktora($id) { $this->idInstruktora = $id; }
    public function setIdSali($id) { $this->idSali = $id; }
    public function setStatus($status) { $this->status = $status; }
    
    /**
     * [REL] Metoda implementująca relację ASOCJACJI z Instruktor
     * Pobiera obiekt instruktora prowadzącego zajęcia
     * @return Instruktor|null
     */
    public function getInstruktor() {
        // TODO: Implementacja - pobranie z bazy danych
        // require_once 'Instruktor.php';
        // return Instruktor::pobierzPoId($this->idInstruktora);
        return null;
    }
    
    /**
     * [REL] Metoda implementująca relację ASOCJACJI z Sala
     * Pobiera obiekt sali, w której odbywają się zajęcia
     * @return Sala|null
     */
    public function getSala() {
        // TODO: Implementacja - pobranie z bazy danych
        // require_once 'Sala.php';
        // return Sala::pobierzPoId($this->idSali);
        return null;
    }
    
    /**
     * [REL] Metoda implementująca relację AGREGACJI z Rezerwacja
     * Pobiera wszystkie rezerwacje na te zajęcia
     * @return array - tablica obiektów Rezerwacja
     */
    public function getRezerwacje() {
        // TODO: Implementacja - pobranie z bazy danych
        // require_once 'Rezerwacja.php';
        // return Rezerwacja::pobierzDlaZajec($this->id);
        return [];
    }
    
    /**
     * [HELP] Metoda pomocnicza: Sprawdzanie dostępności miejsc
     * @return bool - true jeśli są wolne miejsca
     */
    public function czyMaWolneMiejsca() {
        return $this->wolneMiejsca > 0;
    }
    
    /**
     * [HELP] Metoda pomocnicza: Zmniejszanie liczby wolnych miejsc (przy zapisie)
     * @return bool - true jeśli udało się zmniejszyć
     */
    public function zmniejszWolneMiejsca() {
        if ($this->wolneMiejsca > 0) {
            $this->wolneMiejsca--;
            return true;
        }
        return false;
    }
    
    /**
     * [HELP] Metoda pomocnicza: Zwiększanie liczby wolnych miejsc (przy anulacji)
     * @return bool - true jeśli udało się zwiększyć
     */
    public function zwiekszWolneMiejsca() {
        if ($this->wolneMiejsca < $this->limitMiejsc) {
            $this->wolneMiejsca++;
            return true;
        }
        return false;
    }
    
    /**
     * [HELP] Metoda pomocnicza: Sprawdzanie czy zajęcia się już odbyły
     * @return bool - true jeśli zajęcia w przeszłości
     */
    public function czyOdbyte() {
        $dataZajec = strtotime($this->data . ' ' . $this->godzinaDo);
        return $dataZajec < time();
    }
    
    /**
     * [HELP] Metoda pomocnicza: Walidacja danych zajęć
     * @return array - ['valid' => bool, 'errors' => array]
     */
    public function waliduj() {
        $errors = [];
        
        if (empty($this->nazwa)) {
            $errors[] = 'Nazwa zajęć jest wymagana';
        }
        
        if (empty($this->data)) {
            $errors[] = 'Data jest wymagana';
        }
        
        if (empty($this->godzinaOd) || empty($this->godzinaDo)) {
            $errors[] = 'Godziny rozpoczęcia i zakończenia są wymagane';
        }
        
        if ($this->godzinaOd >= $this->godzinaDo) {
            $errors[] = 'Godzina zakończenia musi być późniejsza niż rozpoczęcia';
        }
        
        if ($this->limitMiejsc <= 0) {
            $errors[] = 'Limit miejsc musi być większy od 0';
        }
        
        if (empty($this->idInstruktora)) {
            $errors[] = 'Instruktor jest wymagany';
        }
        
        if (empty($this->idSali)) {
            $errors[] = 'Sala jest wymagana';
        }
        
        return [
            'valid' => count($errors) === 0,
            'errors' => $errors
        ];
    }
    
    /**
     * Metoda do zapisu zajęć w bazie danych
     * @return array - ['sukces' => bool, 'id' => int|null, 'komunikat' => string]
     */
    public function zapisz() {
        try {
            $walidacja = $this->waliduj();
            if (!$walidacja['valid']) {
                return ['sukces' => false, 'id' => null, 'komunikat' => implode(', ', $walidacja['errors'])];
            }
            
            require_once __DIR__ . '/Database.php';
            $db = Database::getInstance()->getConnection();
            
            $sql = "INSERT INTO zajecia (nazwa, opis, data, godzina_od, godzina_do, limit_miejsc, 
                                        wolne_miejsca, id_instruktora, id_sali, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $this->nazwa,
                $this->opis,
                $this->data,
                $this->godzinaOd,
                $this->godzinaDo,
                $this->limitMiejsc,
                $this->wolneMiejsca,
                $this->idInstruktora,
                $this->idSali,
                $this->status
            ]);
            
            $this->id = $db->lastInsertId();
            
            return ['sukces' => true, 'id' => $this->id, 'komunikat' => 'Zajęcia zostały utworzone'];
            
        } catch (Exception $e) {
            return ['sukces' => false, 'id' => null, 'komunikat' => 'Błąd zapisu: ' . $e->getMessage()];
        }
    }
    
    /**
     * Metoda do aktualizacji zajęć w bazie danych
     * @return array - ['sukces' => bool, 'komunikat' => string]
     */
    public function aktualizuj() {
        try {
            if (empty($this->id)) {
                return ['sukces' => false, 'komunikat' => 'Brak ID zajęć'];
            }
            
            $walidacja = $this->waliduj();
            if (!$walidacja['valid']) {
                return ['sukces' => false, 'komunikat' => implode(', ', $walidacja['errors'])];
            }
            
            require_once __DIR__ . '/Database.php';
            $db = Database::getInstance()->getConnection();
            
            $sql = "UPDATE zajecia SET nazwa = ?, opis = ?, data = ?, godzina_od = ?, godzina_do = ?, 
                                       limit_miejsc = ?, id_instruktora = ?, id_sali = ?, status = ? 
                    WHERE id = ?";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $this->nazwa,
                $this->opis,
                $this->data,
                $this->godzinaOd,
                $this->godzinaDo,
                $this->limitMiejsc,
                $this->idInstruktora,
                $this->idSali,
                $this->status,
                $this->id
            ]);
            
            return ['sukces' => true, 'komunikat' => 'Zajęcia zostały zaktualizowane'];
            
        } catch (Exception $e) {
            return ['sukces' => false, 'komunikat' => 'Błąd aktualizacji: ' . $e->getMessage()];
        }
    }
    
    /**
     * Metoda statyczna: Pobieranie wszystkich zajęć
     * @param string $status - opcjonalnie filtruj po statusie
     * @return array - tablica z danymi zajęć
     */
    public static function pobierzWszystkie($status = null) {
        try {
            require_once __DIR__ . '/Database.php';
            $db = Database::getInstance()->getConnection();
            
            $sql = "SELECT z.*, u.imie as instruktor_imie, u.nazwisko as instruktor_nazwisko, 
                           s.nazwa as sala_nazwa 
                    FROM zajecia z 
                    LEFT JOIN users u ON z.id_instruktora = u.id 
                    LEFT JOIN sale s ON z.id_sali = s.id";
            
            if ($status !== null) {
                $sql .= " WHERE z.status = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$status]);
            } else {
                $sql .= " ORDER BY z.data DESC, z.godzina_od DESC";
                $stmt = $db->query($sql);
            }
            
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log('Błąd pobierzWszystkie: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Metoda statyczna: Pobieranie zajęć po ID
     * @param int $id
     * @return array|null - dane zajęć lub null
     */
    public static function pobierzPoId($id) {
        try {
            require_once __DIR__ . '/Database.php';
            $db = Database::getInstance()->getConnection();
            
            $sql = "SELECT z.*, u.imie as instruktor_imie, u.nazwisko as instruktor_nazwisko, 
                           s.nazwa as sala_nazwa, s.pojemnosc as sala_pojemnosc 
                    FROM zajecia z 
                    LEFT JOIN users u ON z.id_instruktora = u.id 
                    LEFT JOIN sale s ON z.id_sali = s.id 
                    WHERE z.id = ?";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$id]);
            
            return $stmt->fetch() ?: null;
            
        } catch (Exception $e) {
            error_log('Błąd pobierzPoId: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Metoda statyczna: Pobieranie dostępnych zajęć (z wolnymi miejscami)
     * @return array - tablica z danymi zajęć
     */
    public static function pobierzDostepne() {
        try {
            require_once __DIR__ . '/Database.php';
            $db = Database::getInstance()->getConnection();
            
            $sql = "SELECT z.*, u.imie as instruktor_imie, u.nazwisko as instruktor_nazwisko, 
                           s.nazwa as sala_nazwa 
                    FROM zajecia z 
                    LEFT JOIN users u ON z.id_instruktora = u.id 
                    LEFT JOIN sale s ON z.id_sali = s.id 
                    WHERE z.wolne_miejsca > 0 
                      AND z.status = 'planowane' 
                      AND z.data >= CURDATE() 
                    ORDER BY z.data ASC, z.godzina_od ASC";
            
            $stmt = $db->query($sql);
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log('Błąd pobierzDostepne: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Konwersja obiektu do tablicy
     * @return array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'nazwa' => $this->nazwa,
            'opis' => $this->opis,
            'data' => $this->data,
            'godzina_od' => $this->godzinaOd,
            'godzina_do' => $this->godzinaDo,
            'limit_miejsc' => $this->limitMiejsc,
            'wolne_miejsca' => $this->wolneMiejsca,
            'id_instruktora' => $this->idInstruktora,
            'id_sali' => $this->idSali,
            'status' => $this->status,
            'data_utworzenia' => $this->dataUtworzenia
        ];
    }
}
?>
