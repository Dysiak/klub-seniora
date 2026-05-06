<?php
/**
 * Testy jednostkowe dla klasy Senior
 * 
 * @covers Senior
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../src/models/Senior.php';

class SeniorTest extends TestCase
{
    private $senior;
    
    protected function setUp(): void
    {
        $this->senior = new Senior([
            'id' => 1,
            'imie' => 'Anna',
            'nazwisko' => 'Nowak',
            'email' => 'anna.nowak@example.com',
            'login' => 'anowak',
            'telefon' => '987654321',
            'rola' => 'senior'
        ]);
    }
    
    /**
     * Test tworzenia obiektu Senior
     */
    public function testTworzenieSeniora()
    {
        $this->assertInstanceOf(Senior::class, $this->senior);
        $this->assertEquals('senior', $this->senior->rola);
        $this->assertEquals('Anna', $this->senior->imie);
    }
    
    /**
     * Test walidacji danych seniora
     */
    public function testWalidacjaDanych()
    {
        $this->assertNotEmpty($this->senior->imie, 'Imię nie może być puste');
        $this->assertNotEmpty($this->senior->nazwisko, 'Nazwisko nie może być puste');
        $this->assertNotEmpty($this->senior->email, 'Email nie może być pusty');
    }
    
    /**
     * Test uprawnień seniora
     */
    public function testGetUprawnienia()
    {
        $uprawnienia = $this->senior->getUprawnienia();
        
        $this->assertIsArray($uprawnienia);
        $this->assertArrayHasKey('przeglad_zajec', $uprawnienia);
        $this->assertArrayHasKey('zapisywanie_na_zajecia', $uprawnienia);
        $this->assertArrayHasKey('anulowanie_rezerwacji', $uprawnienia);
        $this->assertArrayHasKey('przeglad_moich_rezerwacji', $uprawnienia);
        
        $this->assertTrue($uprawnienia['przeglad_zajec']);
        $this->assertTrue($uprawnienia['zapisywanie_na_zajecia']);
        $this->assertTrue($uprawnienia['anulowanie_rezerwacji']);
    }
    
    /**
     * Test czy senior nie ma uprawnień administracyjnych
     */
    public function testBrakUprawnienAdministracyjnych()
    {
        $uprawnienia = $this->senior->getUprawnienia();
        
        $this->assertArrayNotHasKey('zarzadzanie_uzytkownikami', $uprawnienia);
        $this->assertArrayNotHasKey('tworzenie_zajec', $uprawnienia);
        $this->assertArrayNotHasKey('edycja_zajec', $uprawnienia);
    }
    
    /**
     * Test struktury danych zwracanych przez getMojeRezerwacje
     */
    public function testGetMojeRezerwacjeStruktura()
    {
        // Mock test - wymaga bazy danych
        $this->markTestSkipped('Wymaga połączenia z bazą testową');
    }
    
    /**
     * Test zapisywania na zajęcia - wymaga ID zajęć
     */
    public function testZapiszNaZajeciaWymagaIdZajec()
    {
        $this->markTestSkipped('Test integracyjny - wymaga bazy testowej');
    }
    
    /**
     * Test anulowania rezerwacji - wymaga ID rezerwacji
     */
    public function testAnulujRezerwacjeWymagaIdRezerwacji()
    {
        $this->markTestSkipped('Test integracyjny - wymaga bazy testowej');
    }
}
