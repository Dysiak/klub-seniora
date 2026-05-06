<?php
require_once 'User.php';

/**
 * Klasa Koordynator - dziedziczy po User
 * Reprezentuje koordynatora odpowiedzialnego za planowanie zajęć
 * 
 * KLASYFIKACJA METOD:
 * [UC] - Metody obsługujące główne przypadki użycia
 * [REL] - Metody do implementacji powiązań między klasami
 * [PROJ] - Metody wynikające z decyzji projektowych
 * [HELP] - Metody pomocnicze
 */
class Koordynator extends User {
    // Dodatkowe atrybuty specyficzne dla koordynatora
    private $obszarOdpowiedzialnosci; // Obszar, którym się zajmuje
    
    /**
     * Konstruktor klasy Koordynator
     */
    public function __construct($dane = []) {
        parent::__construct($dane);
        $this->rola = 'koordynator';
        $this->obszarOdpowiedzialnosci = $dane['obszar'] ?? '';
    }
    
    /**
     * [PROJ] Implementacja metody abstrakcyjnej z klasy User
     * Wynikająca z decyzji projektowej - różne role mają różne uprawnienia
     * @return array - uprawnienia koordynatora
     */
    public function getUprawnienia() {
        return [
            'przeglad_zajec' => true,
            'tworzenie_zajec' => true,
            'edycja_zajec' => true,
            'usuwanie_zajec' => true,
            'zarzadzanie_salami' => true,
            'przypisywanie_instruktorow' => true,
            'przeglad_list_uczestnikow' => true,
            'przeglad_wszystkich_rezerwacji' => true,
            'edycja_profilu' => true,
            'zmiana_hasla' => true,
            'zarzadzanie_uzytkownikami' => false
        ];
    }
    
    /**
     * [UC] Metoda obsługująca przypadek użycia: Tworzenie zajęć
     * @param array $daneZajec - dane nowych zajęć
     * @return array - ['sukces' => bool, 'id' => int, 'komunikat' => string]
     */
    public function utworzZajecia($daneZajec) {
        try {
            require_once __DIR__ . '/Zajecia.php';
            
            // Ustawienie domyślnych wartości
            $daneZajec['wolne_miejsca'] = $daneZajec['limit_miejsc'] ?? 0;
            $daneZajec['status'] = 'planowane';
            
            // Utworzenie obiektu Zajecia
            $zajecia = new Zajecia($daneZajec);
            
            // Sprawdzenie konfliktów (jeśli podano salę i instruktora)
            if (!empty($daneZajec['id_sali']) && !empty($daneZajec['id_instruktora'])) {
                $konflikty = $this->sprawdzKonflikty(
                    $daneZajec['data'],
                    $daneZajec['godzina_od'],
                    $daneZajec['godzina_do'],
                    $daneZajec['id_sali'],
                    $daneZajec['id_instruktora']
                );
                
                if (!$konflikty['dostepne']) {
                    return [
                        'sukces' => false,
                        'id' => null,
                        'komunikat' => 'Konflikt: ' . $konflikty['komunikat']
                    ];
                }
            }
            
            // Zapis do bazy
            $wynik = $zajecia->zapisz();
            
            return $wynik;
            
        } catch (Exception $e) {
            return [
                'sukces' => false,
                'id' => null,
                'komunikat' => 'Błąd tworzenia zajęć: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * [UC] Metoda obsługująca przypadek użycia: Edycja zajęć
     * @param int $idZajec - ID zajęć do edycji
     * @param array $noweDane - nowe dane zajęć
     * @return array - ['sukces' => bool, 'komunikat' => string]
     */
    public function edytujZajecia($idZajec, $noweDane) {
        try {
            require_once __DIR__ . '/Zajecia.php';
            require_once __DIR__ . '/Database.php';
            $db = Database::getInstance()->getConnection();
            
            // Pobranie bieżących danych zajęć
            $stmt = $db->prepare("SELECT * FROM zajecia WHERE id = ?");
            $stmt->execute([$idZajec]);
            $obecneDane = $stmt->fetch();
            
            if (!$obecneDane) {
                return ['sukces' => false, 'komunikat' => 'Zajęcia nie istnieją'];
            }
            
            // Połączenie obecnych i nowych danych
            $dane = array_merge($obecneDane, $noweDane);
            $dane['id'] = $idZajec;
            
            // Sprawdzenie konfliktów jeśli zmieniono termin, salę lub instruktora
            if (isset($noweDane['data']) || isset($noweDane['godzina_od']) || 
                isset($noweDane['id_sali']) || isset($noweDane['id_instruktora'])) {
                
                $konflikty = $this->sprawdzKonflikty(
                    $dane['data'],
                    $dane['godzina_od'],
                    $dane['godzina_do'],
                    $dane['id_sali'],
                    $dane['id_instruktora'],
                    $idZajec
                );
                
                if (!$konflikty['dostepne']) {
                    return ['sukces' => false, 'komunikat' => 'Konflikt: ' . $konflikty['komunikat']];
                }
            }
            
            // Utworzenie obiektu i aktualizacja
            $zajecia = new Zajecia($dane);
            $wynik = $zajecia->aktualizuj();
            
            return $wynik;
            
        } catch (Exception $e) {
            return ['sukces' => false, 'komunikat' => 'Błąd edycji: ' . $e->getMessage()];
        }
    }
    
    /**
     * [UC] Metoda obsługująca przypadek użycia: Usuwanie zajęć
     * @param int $idZajec - ID zajęć do usunięcia
     * @return array - ['sukces' => bool, 'komunikat' => string]
     */
    public function usunZajecia($idZajec) {
        try {
            require_once __DIR__ . '/Database.php';
            $db = Database::getInstance()->getConnection();
            
            // Sprawdzenie czy zajęcia istnieją
            $stmt = $db->prepare("SELECT * FROM zajecia WHERE id = ?");
            $stmt->execute([$idZajec]);
            $zajecia = $stmt->fetch();
            
            if (!$zajecia) {
                return ['sukces' => false, 'komunikat' => 'Zajęcia nie istnieją'];
            }
            
            // Sprawdzenie czy są aktywne rezerwacje
            $stmt = $db->prepare("SELECT COUNT(*) as liczba FROM rezerwacje WHERE id_zajec = ? AND status = 'aktywna'");
            $stmt->execute([$idZajec]);
            $result = $stmt->fetch();
            
            if ($result['liczba'] > 0) {
                return [
                    'sukces' => false,
                    'komunikat' => 'Nie można usunąć zajęć - istnieją aktywne rezerwacje (' . $result['liczba'] . ')'
                ];
            }
            
            // Usunięcie zajęć
            $stmt = $db->prepare("DELETE FROM zajecia WHERE id = ?");
            $stmt->execute([$idZajec]);
            
            return ['sukces' => true, 'komunikat' => 'Zajęcia zostały usunięte'];
            
        } catch (Exception $e) {
            return [
                'sukces' => false,
                'komunikat' => 'Błąd usuwania: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * [HELP] Metoda pomocnicza: Przypisywanie instruktora do zajęć
     * @param int $idZajec
     * @param int $idInstruktora
     * @return bool
     */
    public function przypiszInstruktora($idZajec, $idInstruktora) {
        // TODO: Implementacja
        // 1. Sprawdzić dostępność instruktora
        // 2. Przypisać w bazie
        return false;
    }
    
    /**
     * [HELP] Metoda pomocnicza: Przypisywanie sali do zajęć
     * @param int $idZajec
     * @param int $idSali
     * @return bool
     */
    public function przypiszSale($idZajec, $idSali) {
        // TODO: Implementacja
        // 1. Sprawdzić dostępność sali
        // 2. Przypisać w bazie
        return false;
    }
    
    /**
     * [HELP] Metoda pomocnicza: Sprawdzanie konfliktów w harmonogramie
     * @param string $data
     * @param string $godzinaOd
     * @param string $godzinaDo
     * @param int $idSali
     * @param int $idInstruktora
     * @param int $idZajecDoPomin - opcjonalnie przy edycji
     * @return array - ['dostepne' => bool, 'komunikat' => string]
     */
    public function sprawdzKonflikty($data, $godzinaOd, $godzinaDo, $idSali, $idInstruktora, $idZajecDoPomin = null) {
        try {
            require_once __DIR__ . '/Database.php';
            $db = Database::getInstance()->getConnection();
            
            $sql = "SELECT COUNT(*) as liczba FROM zajecia 
                    WHERE data = ? 
                    AND status = 'planowane'
                    AND ((godzina_od < ? AND godzina_do > ?) OR 
                         (godzina_od < ? AND godzina_do > ?) OR
                         (godzina_od >= ? AND godzina_do <= ?))
                    AND (id_sali = ? OR id_instruktora = ?)";
            
            $params = [$data, $godzinaDo, $godzinaOd, $godzinaDo, $godzinaOd, $godzinaOd, $godzinaDo, $idSali, $idInstruktora];
            
            if ($idZajecDoPomin !== null) {
                $sql .= " AND id != ?";
                $params[] = $idZajecDoPomin;
            }
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            if ($result['liczba'] > 0) {
                return [
                    'dostepne' => false,
                    'komunikat' => 'Sala lub instruktor zajęci w podanym terminie'
                ];
            }
            
            return ['dostepne' => true, 'komunikat' => ''];
            
        } catch (Exception $e) {
            return ['dostepne' => false, 'komunikat' => 'Błąd sprawdzania: ' . $e->getMessage()];
        }
    }
    
    /**
     * [REL] Metoda do implementacji dostępu do wszystkich rezerwacji
     * @return array
     */
    public function getAllRezerwacje() {
        // TODO: Implementacja - pobranie z bazy
        return [];
    }
}
?>
