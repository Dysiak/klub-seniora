<?php
/**
 * Klasa bazowa User - reprezentuje użytkownika systemu
 * Abstrakcyjna klasa bazowa dla wszystkich typów użytkowników
 * 
 * KLASYFIKACJA METOD:
 * [UC] - Metody obsługujące główne przypadki użycia
 * [REL] - Metody do implementacji powiązań między klasami
 * [PROJ] - Metody wynikające z decyzji projektowych
 * [HELP] - Metody pomocnicze
 */
require_once __DIR__ . '/Database.php';

abstract class User {
    // Atrybuty chronione - dostępne dla klas pochodnych
    protected $id;
    protected $imie;
    protected $nazwisko;
    protected $email;
    protected $login;
    protected $haslo; // Zahashowane hasło
    protected $telefon;
    protected $rola; // 'senior', 'instruktor', 'koordynator', 'administrator'
    protected $dataRejestracji;
    protected $czyAktywny;
    
    /**
     * Konstruktor klasy User
     * @param array $dane - tablica z danymi użytkownika
     */
    public function __construct($dane = []) {
        $this->id = $dane['id'] ?? null;
        $this->imie = $dane['imie'] ?? '';
        $this->nazwisko = $dane['nazwisko'] ?? '';
        $this->email = $dane['email'] ?? '';
        $this->login = $dane['login'] ?? '';
        $this->haslo = $dane['haslo'] ?? '';
        $this->telefon = $dane['telefon'] ?? '';
        $this->rola = $dane['rola'] ?? '';
        $this->dataRejestracji = $dane['data_rejestracji'] ?? date('Y-m-d H:i:s');
        $this->czyAktywny = $dane['czy_aktywny'] ?? true;
    }
    
    // ===== GETTERY =====
    public function getId() { return $this->id; }
    public function getImie() { return $this->imie; }
    public function getNazwisko() { return $this->nazwisko; }
    public function getEmail() { return $this->email; }
    public function getLogin() { return $this->login; }
    public function getTelefon() { return $this->telefon; }
    public function getRola() { return $this->rola; }
    public function getDataRejestracji() { return $this->dataRejestracji; }
    public function getCzyAktywny() { return $this->czyAktywny; }
    
    // ===== SETTERY =====
    public function setImie($imie) { $this->imie = $imie; }
    public function setNazwisko($nazwisko) { $this->nazwisko = $nazwisko; }
    public function setEmail($email) { $this->email = $email; }
    public function setTelefon($telefon) { $this->telefon = $telefon; }
    public function setCzyAktywny($status) { $this->czyAktywny = $status; }
    
    /**
     * [PROJ] Metoda do ustawiania hasła (z hashowaniem)
     * Wynikająca z decyzji projektowej - hasła muszą być bezpiecznie przechowywane
     * @param string $haslo - hasło w postaci jawnej
     */
    public function setHaslo($haslo) {
        $this->haslo = password_hash($haslo, PASSWORD_DEFAULT);
    }
    
    /**
     * [UC] Metoda do weryfikacji hasła - obsługuje przypadek użycia "Logowanie"
     * @param string $haslo - hasło do sprawdzenia
     * @return bool - true jeśli hasło poprawne
     */
    public function sprawdzHaslo($haslo) {
        return password_verify($haslo, $this->haslo);
    }
    
    /**
     * [PROJ] Metoda abstrakcyjna - wynikająca z decyzji projektowej
     * Musi być zaimplementowana w klasach pochodnych
     * Zwraca specyficzne uprawnienia dla danej roli
     * @return array - tablica uprawnień
     */
    abstract public function getUprawnienia();
    
    /**
     * [UC] Metoda do logowania użytkownika
     * Obsługuje przypadek użycia "Logowanie do systemu"
     * @param string $login
     * @param string $haslo
     * @return array - ['sukces' => bool, 'user' => User|null, 'komunikat' => string]
     */
    public static function loguj($login, $haslo) {
        try {
            require_once __DIR__ . '/Database.php';
            $db = Database::getInstance()->getConnection();
            
            // Pobranie użytkownika z bazy
            $stmt = $db->prepare("SELECT * FROM users WHERE login = ? AND czy_aktywny = 1");
            $stmt->execute([$login]);
            $dane = $stmt->fetch();
            
            if (!$dane) {
                return [
                    'sukces' => false,
                    'user' => null,
                    'komunikat' => 'Nieprawidłowy login lub hasło'
                ];
            }
            
            // Sprawdzenie hasła
            if (!password_verify($haslo, $dane['haslo'])) {
                return [
                    'sukces' => false,
                    'user' => null,
                    'komunikat' => 'Nieprawidłowy login lub hasło'
                ];
            }
            
            // Utworzenie odpowiedniego obiektu na podstawie roli
            $user = self::utworzObiektPoRoli($dane);
            
            // Utworzenie sesji
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_id'] = $dane['id'];
            $_SESSION['user_rola'] = $dane['rola'];
            $_SESSION['user_login'] = $dane['login'];
            
            return [
                'sukces' => true,
                'user' => $user,
                'komunikat' => 'Zalogowano pomyślnie'
            ];
            
        } catch (Exception $e) {
            return [
                'sukces' => false,
                'user' => null,
                'komunikat' => 'Błąd logowania: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * [HELP] Metoda pomocnicza - tworzy odpowiedni obiekt na podstawie roli
     * @param array $dane - dane użytkownika z bazy
     * @return User - obiekt odpowiedniej klasy pochodnej
     */
    private static function utworzObiektPoRoli($dane) {
        switch ($dane['rola']) {
            case 'senior':
                require_once __DIR__ . '/Senior.php';
                return new Senior($dane);
            case 'instruktor':
                require_once __DIR__ . '/Instruktor.php';
                return new Instruktor($dane);
            case 'koordynator':
                require_once __DIR__ . '/Koordynator.php';
                return new Koordynator($dane);
            case 'administrator':
                require_once __DIR__ . '/Administrator.php';
                return new Administrator($dane);
            default:
                throw new Exception('Nieznana rola użytkownika');
        }
    }
    
    /**
     * [UC] Metoda do wylogowania użytkownika
     * Obsługuje przypadek użycia "Wylogowanie"
     * @return bool - true jeśli sukces
     */
    public static function wyloguj() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Wyczyść wszystkie zmienne sesji
        $_SESSION = [];
        
        // Zniszcz cookie sesji
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Zniszcz sesję
        session_destroy();
        
        return true;
    }
    
    /**
     * [HELP] Metoda pomocnicza - walidacja email
     * @param string $email
     * @return bool
     */
    protected function walidujEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * [HELP] Metoda pomocnicza - walidacja telefonu
     * @param string $telefon
     * @return bool
     */
    protected function walidujTelefon($telefon) {
        return preg_match('/^[0-9]{9,15}$/', $telefon);
    }
    
    /**
     * [UC] Zapis użytkownika do bazy danych
     * Obsługuje przypadek użycia "Rejestracja użytkownika"
     * @return array - ['sukces' => bool, 'id' => int|null, 'komunikat' => string]
     */
    public function zapisz() {
        try {
            // Walidacja danych
            if (empty($this->imie) || empty($this->nazwisko)) {
                return ['sukces' => false, 'id' => null, 'komunikat' => 'Imię i nazwisko są wymagane'];
            }
            
            if (!$this->walidujEmail($this->email)) {
                return ['sukces' => false, 'id' => null, 'komunikat' => 'Nieprawidłowy adres email'];
            }
            
            if (empty($this->login) || strlen($this->login) < 3) {
                return ['sukces' => false, 'id' => null, 'komunikat' => 'Login musi mieć min. 3 znaki'];
            }
            
            require_once __DIR__ . '/Database.php';
            $db = Database::getInstance()->getConnection();
            
            // Sprawdź czy login/email nie istnieją
            $stmt = $db->prepare("SELECT id FROM users WHERE login = ? OR email = ?");
            $stmt->execute([$this->login, $this->email]);
            if ($stmt->fetch()) {
                return ['sukces' => false, 'id' => null, 'komunikat' => 'Login lub email już istnieje'];
            }
            
            // Wstaw do bazy
            $sql = "INSERT INTO users (imie, nazwisko, email, login, haslo, telefon, rola, czy_aktywny) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $this->imie,
                $this->nazwisko,
                $this->email,
                $this->login,
                $this->haslo,
                $this->telefon,
                $this->rola,
                $this->czyAktywny ? 1 : 0
            ]);
            
            $this->id = $db->lastInsertId();
            
            return [
                'sukces' => true,
                'id' => $this->id,
                'komunikat' => 'Użytkownik został utworzony'
            ];
            
        } catch (Exception $e) {
            return [
                'sukces' => false,
                'id' => null,
                'komunikat' => 'Błąd zapisu: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * [UC] Aktualizacja danych użytkownika w bazie
     * Obsługuje przypadek użycia "Edycja danych użytkownika"
     * @return array - ['sukces' => bool, 'komunikat' => string]
     */
    public function aktualizuj() {
        try {
            if (empty($this->id)) {
                return ['sukces' => false, 'komunikat' => 'Brak ID użytkownika'];
            }
            
            // Walidacja
            if (!$this->walidujEmail($this->email)) {
                return ['sukces' => false, 'komunikat' => 'Nieprawidłowy adres email'];
            }
            
            require_once __DIR__ . '/Database.php';
            $db = Database::getInstance()->getConnection();
            
            // Sprawdź czy email nie jest zajęty przez innego użytkownika
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$this->email, $this->id]);
            if ($stmt->fetch()) {
                return ['sukces' => false, 'komunikat' => 'Ten email jest już używany'];
            }
            
            // Aktualizacja
            $sql = "UPDATE users SET imie = ?, nazwisko = ?, email = ?, telefon = ?, czy_aktywny = ? 
                    WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $this->imie,
                $this->nazwisko,
                $this->email,
                $this->telefon,
                $this->czyAktywny ? 1 : 0,
                $this->id
            ]);
            
            return [
                'sukces' => true,
                'komunikat' => 'Dane użytkownika zostały zaktualizowane'
            ];
            
        } catch (Exception $e) {
            return [
                'sukces' => false,
                'komunikat' => 'Błąd aktualizacji: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Konwersja obiektu do tablicy
     * @return array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'imie' => $this->imie,
            'nazwisko' => $this->nazwisko,
            'email' => $this->email,
            'login' => $this->login,
            'telefon' => $this->telefon,
            'rola' => $this->rola,
            'data_rejestracji' => $this->dataRejestracji,
            'czy_aktywny' => $this->czyAktywny
        ];
    }
    
    /**
     * Pobierz użytkowników według roli
     * [HELP] - Metoda pomocnicza
     * @param string $rola - rola użytkowników do pobrania
     * @return array - tablica użytkowników
     */
    public static function pobierzPoRoli($rola) {
        try {
            $db = Database::getInstance()->getConnection();
            $sql = "SELECT * FROM users WHERE rola = ? ORDER BY nazwisko, imie";
            $stmt = $db->prepare($sql);
            $stmt->execute([$rola]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Pobierz wszystkich użytkowników
     * [HELP] - Metoda pomocnicza
     * @return array - tablica wszystkich użytkowników
     */
    public static function pobierzWszystkich() {
        try {
            $db = Database::getInstance()->getConnection();
            $sql = "SELECT id, login, email, imie, nazwisko, telefon, rola, 
                           data_rejestracji, czy_aktywny as aktywny 
                    FROM users ORDER BY rola, nazwisko, imie";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}
?>
