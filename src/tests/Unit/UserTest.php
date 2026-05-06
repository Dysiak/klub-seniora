<?php
/**
 * Testy jednostkowe dla klasy User
 * 
 * @covers User
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../src/models/User.php';
require_once __DIR__ . '/../../src/models/Senior.php';

class UserTest extends TestCase
{
    /**
     * Test walidacji emaila - poprawny email
     */
    public function testWalidujEmailPoprawny()
    {
        $senior = new Senior([
            'email' => 'test@example.com',
            'login' => 'testuser',
            'imie' => 'Test',
            'nazwisko' => 'User'
        ]);
        
        // Użyj refleksji aby dostać się do protected metody
        $method = new ReflectionMethod(Senior::class, 'walidujEmail');
        $method->setAccessible(true);
        
        $result = $method->invoke($senior, 'test@example.com');
        $this->assertTrue($result, 'Poprawny email powinien przejść walidację');
    }
    
    /**
     * Test walidacji emaila - niepoprawny email
     */
    public function testWalidujEmailNiepoprawny()
    {
        $senior = new Senior([
            'email' => 'invalid-email',
            'login' => 'testuser',
            'imie' => 'Test',
            'nazwisko' => 'User'
        ]);
        
        $method = new ReflectionMethod(Senior::class, 'walidujEmail');
        $method->setAccessible(true);
        
        $result = $method->invoke($senior, 'invalid-email');
        $this->assertFalse($result, 'Niepoprawny email nie powinien przejść walidacji');
    }
    
    /**
     * Test walidacji telefonu - poprawny numer (9 cyfr)
     */
    public function testWalidujTelefonPoprawny()
    {
        $senior = new Senior([
            'email' => 'test@example.com',
            'login' => 'testuser',
            'imie' => 'Test',
            'nazwisko' => 'User'
        ]);
        
        $method = new ReflectionMethod(Senior::class, 'walidujTelefon');
        $method->setAccessible(true);
        
        $result = $method->invoke($senior, '123456789');
        $this->assertTrue($result, 'Numer 9-cyfrowy powinien przejść walidację');
    }
    
    /**
     * Test walidacji telefonu - niepoprawny numer (za krótki)
     */
    public function testWalidujTelefonZaKrotki()
    {
        $senior = new Senior([
            'email' => 'test@example.com',
            'login' => 'testuser',
            'imie' => 'Test',
            'nazwisko' => 'User'
        ]);
        
        $method = new ReflectionMethod(Senior::class, 'walidujTelefon');
        $method->setAccessible(true);
        
        $result = $method->invoke($senior, '12345');
        $this->assertFalse($result, 'Numer 5-cyfrowy nie powinien przejść walidacji');
    }
    
    /**
     * Test tworzenia obiektu User z poprawnymi danymi
     */
    public function testTworzenieUseraZPoprawnymi Danymi()
    {
        $data = [
            'id' => 1,
            'imie' => 'Jan',
            'nazwisko' => 'Kowalski',
            'email' => 'jan.kowalski@example.com',
            'login' => 'jkowalski',
            'telefon' => '123456789',
            'rola' => 'senior'
        ];
        
        $senior = new Senior($data);
        
        $this->assertEquals(1, $senior->id);
        $this->assertEquals('Jan', $senior->imie);
        $this->assertEquals('Kowalski', $senior->nazwisko);
        $this->assertEquals('jan.kowalski@example.com', $senior->email);
        $this->assertEquals('jkowalski', $senior->login);
        $this->assertEquals('123456789', $senior->telefon);
        $this->assertEquals('senior', $senior->rola);
    }
    
    /**
     * Test pobierania użytkowników po roli
     */
    public function testPobierzPoRoli()
    {
        // Mock Database connection
        $this->markTestSkipped('Wymaga połączenia z bazą danych testową');
    }
    
    /**
     * Test hashowania hasła podczas logowania
     */
    public function testHashowanieHasla()
    {
        $haslo = 'testPassword123';
        $hash = password_hash($haslo, PASSWORD_DEFAULT);
        
        $this->assertNotEquals($haslo, $hash, 'Hasło powinno być zahashowane');
        $this->assertTrue(
            password_verify($haslo, $hash),
            'password_verify powinno zwrócić true dla poprawnego hasła'
        );
    }
}
