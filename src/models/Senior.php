<?php
require_once 'User.php';

/**
 * Klasa Senior - dziedziczy po User
 * Reprezentuje seniora korzystającego z oferty klubu
 * 
 * KLASYFIKACJA METOD:
 * [UC] - Metody obsługujące główne przypadki użycia
 * [REL] - Metody do implementacji powiązań między klasami
 * [PROJ] - Metody wynikające z decyzji projektowych
 * [HELP] - Metody pomocnicze
 */
class Senior extends User {
    // Dodatkowe atrybuty specyficzne dla seniora
    private $preferencje; // Preferencje dotyczące zajęć
    private $historia; // Historia uczestnictwa
    
    /**
     * Konstruktor klasy Senior
     */
    public function __construct($dane = []) {
        parent::__construct($dane);
        $this->rola = 'senior';
        $this->preferencje = $dane['preferencje'] ?? [];
        $this->historia = $dane['historia'] ?? [];
    }
    
    /**
     * [PROJ] Implementacja metody abstrakcyjnej z klasy User
     * Wynikająca z decyzji projektowej - różne role mają różne uprawnienia
     * @return array - uprawnienia seniora
     */
    public function getUprawnienia() {
        return [
            'przeglad_zajec' => true,
            'zapis_na_zajecia' => true,
            'anulowanie_rezerwacji' => true,
            'przeglad_moich_zapisow' => true,
            'edycja_profilu' => true,
            'zmiana_hasla' => true,
            'zarzadzanie_zajecia' => false,
            'zarzadzanie_uzytkownikami' => false,
            'przeglad_list_uczestnikow' => false
        ];
    }
    
    /**
     * [UC] Metoda obsługująca przypadek użycia: Zapisywanie się na zajęcia
     * @param int $idZajec - ID zajęć, na które senior się zapisuje
     * @return array - ['sukces' => bool, 'komunikat' => string, 'id_rezerwacji' => int|null]
     */
    public function zapiszNaZajecia($idZajec) {
        try {
            require_once __DIR__ . '/Database.php';
            $db = Database::getInstance()->getConnection();
            
            // 1. Sprawdzenie czy zajęcia istnieją i są dostępne
            $stmt = $db->prepare("SELECT * FROM zajecia WHERE id = ? AND status = 'planowane'");
            $stmt->execute([$idZajec]);
            $zajecia = $stmt->fetch();
            
            if (!$zajecia) {
                return ['sukces' => false, 'komunikat' => 'Zajęcia nie istnieją lub są niedostępne', 'id_rezerwacji' => null];
            }
            
            // 2. Sprawdzenie dostępności miejsc
            if ($zajecia['wolne_miejsca'] <= 0) {
                return ['sukces' => false, 'komunikat' => 'Brak wolnych miejsc', 'id_rezerwacji' => null];
            }
            
            // 3. Sprawdzenie czy senior nie jest już zapisany (aktywna rezerwacja)
            $stmt = $db->prepare("SELECT id FROM rezerwacje WHERE id_seniora = ? AND id_zajec = ? AND status = 'aktywna'");
            $stmt->execute([$this->id, $idZajec]);
            if ($stmt->fetch()) {
                return ['sukces' => false, 'komunikat' => 'Jesteś już zapisany na te zajęcia', 'id_rezerwacji' => null];
            }
            
            // 3a. Sprawdzenie czy istnieje anulowana rezerwacja - jeśli tak, reaktywuj ją
            $stmt = $db->prepare("SELECT id FROM rezerwacje WHERE id_seniora = ? AND id_zajec = ? AND status = 'anulowana'");
            $stmt->execute([$this->id, $idZajec]);
            $anulowanaRezerwacja = $stmt->fetch();
            
            // 4. Utworzenie lub reaktywacja rezerwacji (transakcja)
            $db->beginTransaction();
            
            try {
                if ($anulowanaRezerwacja) {
                    // Reaktywuj anulowaną rezerwację
                    $stmt = $db->prepare("UPDATE rezerwacje SET status = 'aktywna', data_rezerwacji = NOW(), data_anulowania = NULL WHERE id = ?");
                    $stmt->execute([$anulowanaRezerwacja['id']]);
                    $idRezerwacji = $anulowanaRezerwacja['id'];
                } else {
                    // Wstaw nową rezerwację
                    $stmt = $db->prepare("INSERT INTO rezerwacje (id_seniora, id_zajec, status, potwierdzenie) 
                                         VALUES (?, ?, 'aktywna', 1)");
                    $stmt->execute([$this->id, $idZajec]);
                    $idRezerwacji = $db->lastInsertId();
                }
                
                // 5. Zaktualizuj liczbę wolnych miejsc
                $stmt = $db->prepare("UPDATE zajecia SET wolne_miejsca = wolne_miejsca - 1 WHERE id = ?");
                $stmt->execute([$idZajec]);
                
                $db->commit();
                
                return [
                    'sukces' => true,
                    'komunikat' => 'Zapisano na zajęcia pomyślnie',
                    'id_rezerwacji' => $idRezerwacji
                ];
                
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            
        } catch (Exception $e) {
            return [
                'sukces' => false,
                'komunikat' => 'Błąd zapisu: ' . $e->getMessage(),
                'id_rezerwacji' => null
            ];
        }
    }
    
    /**
     * [UC] Metoda obsługująca przypadek użycia: Anulowanie rezerwacji
     * @param int $idRezerwacji - ID rezerwacji do anulowania
     * @return array - ['sukces' => bool, 'komunikat' => string]
     */
    public function anulujRezerwacje($idRezerwacji) {
        try {
            require_once __DIR__ . '/Database.php';
            $db = Database::getInstance()->getConnection();
            
            // 1. Sprawdzenie czy rezerwacja istnieje i należy do tego seniora
            $stmt = $db->prepare("
                SELECT r.*, z.data, z.godzina_od 
                FROM rezerwacje r 
                JOIN zajecia z ON r.id_zajec = z.id 
                WHERE r.id = ? AND r.id_seniora = ?
            ");
            $stmt->execute([$idRezerwacji, $this->id]);
            $rezerwacja = $stmt->fetch();
            
            if (!$rezerwacja) {
                return ['sukces' => false, 'komunikat' => 'Rezerwacja nie istnieje lub nie należy do Ciebie'];
            }
            
            if ($rezerwacja['status'] !== 'aktywna') {
                return ['sukces' => false, 'komunikat' => 'Rezerwacja nie jest aktywna (status: ' . $rezerwacja['status'] . ')'];
            }
            
            // 2. Sprawdzenie czy zajęcia się jeszcze nie odbyły
            $dataZajec = strtotime($rezerwacja['data'] . ' ' . $rezerwacja['godzina_od']);
            if ($dataZajec < time()) {
                return ['sukces' => false, 'komunikat' => 'Nie można anulować rezerwacji - zajęcia już się odbyły'];
            }
            
            // 3 i 4. Anulowanie rezerwacji i zwrot miejsca (transakcja)
            $db->beginTransaction();
            
            try {
                // Zaktualizuj status rezerwacji
                $stmt = $db->prepare("UPDATE rezerwacje SET status = 'anulowana', data_anulowania = NOW() WHERE id = ?");
                $stmt->execute([$idRezerwacji]);
                
                // Zwiększ liczbę wolnych miejsc
                $stmt = $db->prepare("UPDATE zajecia SET wolne_miejsca = wolne_miejsca + 1 WHERE id = ?");
                $stmt->execute([$rezerwacja['id_zajec']]);
                
                $db->commit();
                
                return [
                    'sukces' => true,
                    'komunikat' => 'Rezerwacja została anulowana'
                ];
                
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            
        } catch (Exception $e) {
            return [
                'sukces' => false,
                'komunikat' => 'Błąd anulowania: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * [REL] Metoda do implementacji powiązania Senior -> Rezerwacja
     * Pobieranie aktywnych rezerwacji seniora
     * @param string $status - opcjonalnie filtruj po statusie ('aktywna', 'anulowana', 'zakonczona')
     * @return array - tablica z danymi rezerwacji i zajęć
     */
    public function getMojeRezerwacje($status = null) {
        try {
            require_once __DIR__ . '/Database.php';
            $db = Database::getInstance()->getConnection();
            
            $sql = "SELECT r.*, z.nazwa, z.data, z.godzina_od, z.godzina_do, s.nazwa as sala_nazwa 
                    FROM rezerwacje r 
                    JOIN zajecia z ON r.id_zajec = z.id 
                    LEFT JOIN sale s ON z.id_sali = s.id 
                    WHERE r.id_seniora = ?";
            
            $params = [$this->id];
            
            if ($status !== null) {
                $sql .= " AND r.status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY z.data DESC, z.godzina_od DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log('Błąd getMojeRezerwacje: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * [HELP] Metoda pomocnicza: Pobieranie historii uczestnictwa
     * @return array - historia zajęć
     */
    public function getHistoria() {
        return $this->historia;
    }
    
    /**
     * [HELP] Metoda pomocnicza: Sprawdzanie czy senior jest zapisany na dane zajęcia
     * @param int $idZajec
     * @return bool
     */
    public function czyZapisanyNaZajecia($idZajec) {
        // TODO: Implementacja - sprawdzenie w bazie danych
        return false;
    }
    
    /**
     * Metoda pomocnicza: Ustawianie preferencji
     * @param array $preferencje
     */
    public function setPreferencje($preferencje) {
        $this->preferencje = $preferencje;
    }
    
    /**
     * Metoda pomocnicza: Pobieranie preferencji
     * @return array
     */
    public function getPreferencje() {
        return $this->preferencje;
    }
}
?>
