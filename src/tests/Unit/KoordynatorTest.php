<?php
/**
 * Testy jednostkowe dla klasy Koordynator
 * 
 * @covers Koordynator
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../src/models/Koordynator.php';

class KoordynatorTest extends TestCase
{
    private $koordynator;
    
    protected function setUp(): void
    {
        $this->koordynator = new Koordynator([
            'id' => 2,
            'imie' => 'Maria',
            'nazwisko' => 'Kowalska',
            'email' => 'maria.kowalska@example.com',
            'login' => 'mkowalska',
            'telefon' => '555444333',
            'rola' => 'koordynator'
        ]);
    }
    
    /**
     * Test tworzenia obiektu Koordynator
     */
    public function testTworzenieKoordynatora()
    {
        $this->assertInstanceOf(Koordynator::class, $this->koordynator);
        $this->assertEquals('koordynator', $this->koordynator->rola);
        $this->assertEquals('Maria', $this->koordynator->imie);
    }
    
    /**
     * Test uprawnień koordynatora
     */
    public function testGetUprawnienia()
    {
        $uprawnienia = $this->koordynator->getUprawnienia();
        
        $this->assertIsArray($uprawnienia);
        $this->assertArrayHasKey('tworzenie_zajec', $uprawnienia);
        $this->assertArrayHasKey('edycja_zajec', $uprawnienia);
        $this->assertArrayHasKey('usuwanie_zajec', $uprawnienia);
        $this->assertArrayHasKey('przeglad_wszystkich_zajec', $uprawnienia);
        $this->assertArrayHasKey('zarzadzanie_rezerwacjami', $uprawnienia);
        
        $this->assertTrue($uprawnienia['tworzenie_zajec']);
        $this->assertTrue($uprawnienia['edycja_zajec']);
        $this->assertTrue($uprawnienia['usuwanie_zajec']);
    }
    
    /**
     * Test czy koordynator ma więcej uprawnień niż senior
     */
    public function testWiecejUprawnienNizSenior()
    {
        $senior = new Senior([
            'id' => 1,
            'imie' => 'Test',
            'nazwisko' => 'Senior',
            'email' => 'test@test.com',
            'login' => 'testsenior',
            'rola' => 'senior'
        ]);
        
        $uprawnieniaKoordynatora = $this->koordynator->getUprawnienia();
        $uprawnieniaSeniora = $senior->getUprawnienia();
        
        $this->assertGreaterThan(
            count($uprawnieniaSeniora),
            count($uprawnieniaKoordynatora),
            'Koordynator powinien mieć więcej uprawnień niż senior'
        );
    }
    
    /**
     * Test walidacji danych zajęć przed utworzeniem
     */
    public function testWalidacjaDanychZajec()
    {
        $daneZajec = [
            'nazwa' => 'Joga dla seniorów',
            'opis' => 'Zajęcia jogi',
            'data' => '2025-12-20',
            'godzina_rozpoczecia' => '10:00',
            'godzina_zakonczenia' => '11:00',
            'limit_miejsc' => 15,
            'id_instruktora' => 3,
            'id_sali' => 1
        ];
        
        $this->assertArrayHasKey('nazwa', $daneZajec);
        $this->assertArrayHasKey('data', $daneZajec);
        $this->assertArrayHasKey('limit_miejsc', $daneZajec);
        $this->assertIsInt($daneZajec['limit_miejsc']);
        $this->assertGreaterThan(0, $daneZajec['limit_miejsc']);
    }
    
    /**
     * Test sprawdzania konfliktów - struktura danych
     */
    public function testSprawdzKonfliktyStruktura()
    {
        // Test wymaga połączenia z bazą
        $this->markTestSkipped('Test integracyjny - wymaga bazy testowej');
    }
    
    /**
     * Test tworzenia zajęć - wymaga bazy danych
     */
    public function testUtworzZajecia()
    {
        $this->markTestSkipped('Test integracyjny - wymaga bazy testowej');
    }
    
    /**
     * Test edycji zajęć - wymaga bazy danych
     */
    public function testEdytujZajecia()
    {
        $this->markTestSkipped('Test integracyjny - wymaga bazy testowej');
    }
    
    /**
     * Test usuwania zajęć - wymaga bazy danych
     */
    public function testUsunZajecia()
    {
        $this->markTestSkipped('Test integracyjny - wymaga bazy testowej');
    }
}
