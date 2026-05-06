<?php
/**
 * Testy jednostkowe dla klasy Rezerwacja
 * 
 * @covers Rezerwacja
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../src/models/Rezerwacja.php';

class RezerwacjaTest extends TestCase
{
    private $rezerwacja;
    
    protected function setUp(): void
    {
        $this->rezerwacja = new Rezerwacja([
            'id' => 1,
            'id_uczestnika' => 5,
            'id_zajec' => 1,
            'data_rezerwacji' => '2025-12-10 14:30:00',
            'status' => 'potwierdzona'
        ]);
    }
    
    /**
     * Test tworzenia obiektu Rezerwacja
     */
    public function testTworzenieRezerwacji()
    {
        $this->assertInstanceOf(Rezerwacja::class, $this->rezerwacja);
        $this->assertEquals(1, $this->rezerwacja->id);
        $this->assertEquals('potwierdzona', $this->rezerwacja->status);
    }
    
    /**
     * Test walidacji wymaganych pól
     */
    public function testWalidacjaWymaganychPol()
    {
        $this->assertNotEmpty($this->rezerwacja->id_uczestnika, 'ID uczestnika jest wymagane');
        $this->assertNotEmpty($this->rezerwacja->id_zajec, 'ID zajęć jest wymagane');
        $this->assertNotEmpty($this->rezerwacja->data_rezerwacji, 'Data rezerwacji jest wymagana');
    }
    
    /**
     * Test walidacji ID (muszą być liczbami dodatnimi)
     */
    public function testWalidacjaID()
    {
        $this->assertIsInt($this->rezerwacja->id);
        $this->assertIsInt($this->rezerwacja->id_uczestnika);
        $this->assertIsInt($this->rezerwacja->id_zajec);
        
        $this->assertGreaterThan(0, $this->rezerwacja->id);
        $this->assertGreaterThan(0, $this->rezerwacja->id_uczestnika);
        $this->assertGreaterThan(0, $this->rezerwacja->id_zajec);
    }
    
    /**
     * Test poprawnych statusów rezerwacji
     */
    public function testPoprawnychStatusow()
    {
        $dozwoloneStatusy = ['potwierdzona', 'anulowana', 'zrealizowana'];
        
        $this->assertContains(
            $this->rezerwacja->status,
            $dozwoloneStatusy,
            'Status musi być jednym z: potwierdzona, anulowana, zrealizowana'
        );
    }
    
    /**
     * Test formatu daty rezerwacji (YYYY-MM-DD HH:MM:SS)
     */
    public function testFormatDatyRezerwacji()
    {
        $pattern = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/';
        $this->assertMatchesRegularExpression(
            $pattern,
            $this->rezerwacja->data_rezerwacji,
            'Data rezerwacji musi być w formacie YYYY-MM-DD HH:MM:SS'
        );
    }
    
    /**
     * Test metody pobierania wszystkich rezerwacji
     */
    public function testPobierzWszystkie()
    {
        $this->markTestSkipped('Test integracyjny - wymaga bazy testowej');
    }
    
    /**
     * Test zapisu rezerwacji do bazy
     */
    public function testZapisz()
    {
        $this->markTestSkipped('Test integracyjny - wymaga bazy testowej');
    }
    
    /**
     * Test usuwania rezerwacji
     */
    public function testUsun()
    {
        $this->markTestSkipped('Test integracyjny - wymaga bazy testowej');
    }
}
