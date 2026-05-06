<?php
/**
 * Klasa Database - zarządzanie połączeniem z bazą danych
 * Implementacja wzorca Singleton
 * 
 * KLASYFIKACJA METOD:
 * [UC] - Metody obsługujące główne przypadki użycia
 * [REL] - Metody do implementacji powiązań między klasami
 * [PROJ] - Metody wynikające z decyzji projektowych (np. wzorce projektowe)
 * [HELP] - Metody pomocnicze
 */
class Database {
    private static $instance = null;
    private $conn;
    
    // Parametry połączenia - DO ZMIANY według konfiguracji XAMPP
    private $host = 'localhost';
    private $dbname = 'klub_seniora';
    private $username = 'root';
    private $password = '';
    
    /**
     * [PROJ] Prywatny konstruktor (wzorzec Singleton)
     * Decyzja projektowa: jeden punkt dostępu do bazy danych w całej aplikacji
     */
    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die("Błąd połączenia z bazą danych: " . $e->getMessage());
        }
    }
    
    /**
     * [PROJ] Metoda zwracająca instancję połączenia (wzorzec Singleton)
     * Decyzja projektowa: zarządzanie jednym połączeniem z bazą danych
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * [HELP] Metoda zwracająca obiekt PDO
     * @return PDO
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * [PROJ] Zapobieganie klonowaniu (wzorzec Singleton)
     */
    private function __clone() {}
    
    /**
     * [PROJ] Zapobieganie deserializacji (wzorzec Singleton)
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
?>
