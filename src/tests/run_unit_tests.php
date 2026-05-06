<?php
/**
 * Prosty runner testów jednostkowych bez PHPUnit
 * Uruchamia wszystkie testy i wyświetla wyniki
 */

class SimpleTestRunner
{
    private $passed = 0;
    private $failed = 0;
    private $skipped = 0;
    private $errors = [];
    
    public function assert($condition, $message)
    {
        if ($condition) {
            $this->passed++;
            echo "✓ PASS: $message\n";
        } else {
            $this->failed++;
            echo "✗ FAIL: $message\n";
            $this->errors[] = $message;
        }
    }
    
    public function skip($message)
    {
        $this->skipped++;
        echo "⊘ SKIP: $message\n";
    }
    
    public function runTests()
    {
        echo "=== TESTY JEDNOSTKOWE KLUB SENIORA ===\n\n";
        
        // Test 1: Walidacja emaila
        echo "--- Test 1: Walidacja emaila ---\n";
        $this->testWalidacjaEmaila();
        
        // Test 2: Walidacja telefonu
        echo "\n--- Test 2: Walidacja telefonu ---\n";
        $this->testWalidacjaTelefonu();
        
        // Test 3: Hashowanie hasła
        echo "\n--- Test 3: Hashowanie hasła ---\n";
        $this->testHashowanieHasla();
        
        // Test 4: Tworzenie obiektu Senior
        echo "\n--- Test 4: Tworzenie obiektu Senior ---\n";
        $this->testTworzenieSeniora();
        
        // Test 5: Uprawnienia Senior
        echo "\n--- Test 5: Uprawnienia Senior ---\n";
        $this->testUprawnieniaSeniora();
        
        // Test 6: Tworzenie obiektu Koordynator
        echo "\n--- Test 6: Tworzenie obiektu Koordynator ---\n";
        $this->testTworzenieKoordynatora();
        
        // Test 7: Uprawnienia Koordynator
        echo "\n--- Test 7: Uprawnienia Koordynator ---\n";
        $this->testUprawnieniaKoordynatora();
        
        // Test 8: Walidacja danych zajęć
        echo "\n--- Test 8: Walidacja danych zajęć ---\n";
        $this->testWalidacjaZajec();
        
        // Test 9: Format daty zajęć
        echo "\n--- Test 9: Format daty zajęć ---\n";
        $this->testFormatDatyZajec();
        
        // Test 10: Walidacja rezerwacji
        echo "\n--- Test 10: Walidacja rezerwacji ---\n";
        $this->testWalidacjaRezerwacji();
        
        $this->printSummary();
    }
    
    private function testWalidacjaEmaila()
    {
        require_once __DIR__ . '/../models/Senior.php';
        
        $senior = new Senior([
            'email' => 'test@example.com',
            'login' => 'testuser',
            'imie' => 'Test',
            'nazwisko' => 'User'
        ]);
        
        // Test poprawnego emaila
        $method = new ReflectionMethod(Senior::class, 'walidujEmail');
        $method->setAccessible(true);
        
        $result1 = $method->invoke($senior, 'test@example.com');
        $this->assert($result1 === true, 'Poprawny email test@example.com powinien przejść walidację');
        
        $result2 = $method->invoke($senior, 'invalid-email');
        $this->assert($result2 === false, 'Niepoprawny email invalid-email nie powinien przejść walidacji');
        
        $result3 = $method->invoke($senior, 'user@domain.co.uk');
        $this->assert($result3 === true, 'Poprawny email user@domain.co.uk powinien przejść walidację');
    }
    
    private function testWalidacjaTelefonu()
    {
        require_once __DIR__ . '/../models/Senior.php';
        
        $senior = new Senior([
            'email' => 'test@example.com',
            'login' => 'testuser',
            'imie' => 'Test',
            'nazwisko' => 'User'
        ]);
        
        $method = new ReflectionMethod(Senior::class, 'walidujTelefon');
        $method->setAccessible(true);
        
        $result1 = $method->invoke($senior, '123456789');
        $this->assert($result1 === true, 'Numer 9-cyfrowy 123456789 powinien przejść walidację');
        
        $result2 = $method->invoke($senior, '12345');
        $this->assert($result2 === false, 'Numer 5-cyfrowy 12345 nie powinien przejść walidacji');
        
        $result3 = $method->invoke($senior, 'abc123456');
        $this->assert($result3 === false, 'Numer z literami abc123456 nie powinien przejść walidacji');
    }
    
    private function testHashowanieHasla()
    {
        $haslo = 'testPassword123';
        $hash = password_hash($haslo, PASSWORD_DEFAULT);
        
        $this->assert($haslo !== $hash, 'Hasło powinno być różne od hasha');
        $this->assert(password_verify($haslo, $hash), 'password_verify powinno zwrócić true dla poprawnego hasła');
        $this->assert(!password_verify('wrongPassword', $hash), 'password_verify powinno zwrócić false dla błędnego hasła');
    }
    
    private function testTworzenieSeniora()
    {
        require_once __DIR__ . '/../models/Senior.php';
        
        $senior = new Senior([
            'id' => 1,
            'imie' => 'Anna',
            'nazwisko' => 'Nowak',
            'email' => 'anna.nowak@example.com',
            'login' => 'anowak',
            'telefon' => '987654321',
            'rola' => 'senior'
        ]);
        
        $this->assert($senior instanceof Senior, 'Obiekt powinien być instancją klasy Senior');
        $this->assert($senior->getRola() === 'senior', 'Rola powinna być senior');
        $this->assert($senior->getImie() === 'Anna', 'Imię powinno być Anna');
        $this->assert($senior->getNazwisko() === 'Nowak', 'Nazwisko powinno być Nowak');
    }
    
    private function testUprawnieniaSeniora()
    {
        require_once __DIR__ . '/../models/Senior.php';
        
        $senior = new Senior([
            'id' => 1,
            'imie' => 'Test',
            'nazwisko' => 'User',
            'email' => 'test@test.com',
            'login' => 'testuser',
            'rola' => 'senior'
        ]);
        
        $uprawnienia = $senior->getUprawnienia();
        
        $this->assert(is_array($uprawnienia), 'Uprawnienia powinny być tablicą');
        $this->assert(isset($uprawnienia['przeglad_zajec']), 'Senior powinien mieć uprawnienie przeglad_zajec');
        $this->assert(isset($uprawnienia['zapisywanie_na_zajecia']), 'Senior powinien mieć uprawnienie zapisywanie_na_zajecia');
        $this->assert($uprawnienia['przeglad_zajec'] === true, 'Uprawnienie przeglad_zajec powinno być true');
    }
    
    private function testTworzenieKoordynatora()
    {
        require_once __DIR__ . '/../models/Koordynator.php';
        
        $koordynator = new Koordynator([
            'id' => 2,
            'imie' => 'Maria',
            'nazwisko' => 'Kowalska',
            'email' => 'maria@example.com',
            'login' => 'mkowalska',
            'rola' => 'koordynator'
        ]);
        
        $this->assert($koordynator instanceof Koordynator, 'Obiekt powinien być instancją klasy Koordynator');
        $this->assert($koordynator->getRola() === 'koordynator', 'Rola powinna być koordynator');
        $this->assert($koordynator->getImie() === 'Maria', 'Imię powinno być Maria');
    }
    
    private function testUprawnieniaKoordynatora()
    {
        require_once __DIR__ . '/../models/Koordynator.php';
        
        $koordynator = new Koordynator([
            'id' => 2,
            'imie' => 'Test',
            'nazwisko' => 'Koordynator',
            'email' => 'test@test.com',
            'login' => 'testkoordynator',
            'rola' => 'koordynator'
        ]);
        
        $uprawnienia = $koordynator->getUprawnienia();
        
        $this->assert(is_array($uprawnienia), 'Uprawnienia powinny być tablicą');
        $this->assert(isset($uprawnienia['tworzenie_zajec']), 'Koordynator powinien mieć uprawnienie tworzenie_zajec');
        $this->assert(isset($uprawnienia['edycja_zajec']), 'Koordynator powinien mieć uprawnienie edycja_zajec');
        $this->assert($uprawnienia['tworzenie_zajec'] === true, 'Uprawnienie tworzenie_zajec powinno być true');
    }
    
    private function testWalidacjaZajec()
    {
        require_once __DIR__ . '/../models/Zajecia.php';
        
        $zajecia = new Zajecia([
            'id' => 1,
            'nazwa' => 'Joga',
            'opis' => 'Zajęcia jogi',
            'data' => '2025-12-20',
            'godzina_od' => '10:00:00',
            'godzina_do' => '11:00:00',
            'limit_miejsc' => 15,
            'wolne_miejsca' => 10,
            'status' => 'planowane',
            'id_instruktora' => 3,
            'id_sali' => 1
        ]);
        
        $this->assert($zajecia->getNazwa() !== '', 'Nazwa nie może być pusta');
        $this->assert($zajecia->getLimitMiejsc() > 0, 'Limit miejsc musi być większy od 0');
        $this->assert($zajecia->getWolneMiejsca() >= 0, 'Wolne miejsca nie mogą być ujemne');
        $this->assert($zajecia->getWolneMiejsca() <= $zajecia->getLimitMiejsc(), 'Wolne miejsca nie mogą przekraczać limitu');
    }
    
    private function testFormatDatyZajec()
    {
        require_once __DIR__ . '/../models/Zajecia.php';
        
        $zajecia = new Zajecia([
            'id' => 1,
            'nazwa' => 'Joga',
            'data' => '2025-12-20',
            'godzina_od' => '10:00:00',
            'godzina_do' => '11:00:00',
            'limit_miejsc' => 15,
            'status' => 'planowane',
            'id_instruktora' => 3,
            'id_sali' => 1
        ]);
        
        $this->assert(
            preg_match('/^\d{4}-\d{2}-\d{2}$/', $zajecia->getData()),
            'Data powinna być w formacie YYYY-MM-DD'
        );
        
        $this->assert(
            preg_match('/^\d{2}:\d{2}:\d{2}$/', $zajecia->getGodzinaOd()),
            'Godzina rozpoczęcia powinna być w formacie HH:MM:SS'
        );
        
        $rozpoczecie = strtotime($zajecia->getGodzinaOd());
        $zakonczenie = strtotime($zajecia->getGodzinaDo());
        $this->assert(
            $zakonczenie > $rozpoczecie,
            'Godzina zakończenia musi być późniejsza niż rozpoczęcie'
        );
    }
    
    private function testWalidacjaRezerwacji()
    {
        require_once __DIR__ . '/../models/Rezerwacja.php';
        
        $rezerwacja = new Rezerwacja([
            'id' => 1,
            'id_seniora' => 5,
            'id_zajec' => 1,
            'data_rezerwacji' => '2025-12-10 14:30:00',
            'status' => 'aktywna'
        ]);
        
        $this->assert($rezerwacja->getId() > 0, 'ID rezerwacji musi być większe od 0');
        $this->assert($rezerwacja->getIdSeniora() > 0, 'ID uczestnika musi być większe od 0');
        $this->assert($rezerwacja->getIdZajec() > 0, 'ID zajęć musi być większe od 0');
        $this->assert(
            in_array($rezerwacja->getStatus(), ['aktywna', 'anulowana', 'zakonczona']),
            'Status musi być jednym z: aktywna, anulowana, zakonczona'
        );
    }
    
    private function printSummary()
    {
        echo "\n=== PODSUMOWANIE TESTÓW ===\n";
        echo "✓ Passed: {$this->passed}\n";
        echo "✗ Failed: {$this->failed}\n";
        echo "⊘ Skipped: {$this->skipped}\n";
        echo "Total: " . ($this->passed + $this->failed + $this->skipped) . "\n";
        
        if ($this->failed > 0) {
            echo "\n=== BŁĘDY ===\n";
            foreach ($this->errors as $error) {
                echo "  • $error\n";
            }
        }
        
        $percentage = $this->passed / ($this->passed + $this->failed) * 100;
        echo "\nSukces: " . number_format($percentage, 2) . "%\n";
    }
}

// Uruchomienie testów
$runner = new SimpleTestRunner();
$runner->runTests();
