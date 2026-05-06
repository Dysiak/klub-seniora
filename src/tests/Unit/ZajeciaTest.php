<?php
/**
 * Testy jednostkowe dla klasy Zajecia
 * 
 * @covers Zajecia
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../src/models/Zajecia.php';

class ZajeciaTest extends TestCase
{
    private $zajecia;
    
    protected function setUp(): void
    {
        $this->zajecia = new Zajecia([
            'id' => 1,
            'nazwa' => 'Joga dla seniorów',
            'opis' => 'Zajęcia relaksacyjne',
            'data' => '2025-12-20',
            'godzina_rozpoczecia' => '10:00:00',
            'godzina_zakonczenia' => '11:00:00',
            'limit_miejsc' => 15,
            'wolne_miejsca' => 10,
            'status' => 'planowane',
            'id_instruktora' => 3,
            'id_sali' => 1
        ]);
    }
    
    /**
     * Test tworzenia obiektu Zajecia
     */
    public function testTworzenieZajec()
    {
        $this->assertInstanceOf(Zajecia::class, $this->zajecia);
        $this->assertEquals('Joga dla seniorów', $this->zajecia->nazwa);
        $this->assertEquals('planowane', $this->zajecia->status);
    }
    
    /**
     * Test walidacji wymaganych pól
     */
    public function testWalidacjaWymaganychPol()
    {
        $this->assertNotEmpty($this->zajecia->nazwa, 'Nazwa jest wymagana');
        $this->assertNotEmpty($this->zajecia->data, 'Data jest wymagana');
        $this->assertNotEmpty($this->zajecia->godzina_rozpoczecia, 'Godzina rozpoczęcia jest wymagana');
        $this->assertNotEmpty($this->zajecia->limit_miejsc, 'Limit miejsc jest wymagany');
    }
    
    /**
     * Test walidacji limitu miejsc (musi być > 0)
     */
    public function testLimitMiejscWiekszyOdZera()
    {
        $this->assertGreaterThan(0, $this->zajecia->limit_miejsc);
    }
    
    /**
     * Test walidacji wolnych miejsc (nie może być ujemne)
     */
    public function testWolneMiejscaNieUjemne()
    {
        $this->assertGreaterThanOrEqual(0, $this->zajecia->wolne_miejsca);
    }
    
    /**
     * Test walidacji wolnych miejsc (nie więcej niż limit)
     */
    public function testWolneMiejscaNiePrzekraczaLimitu()
    {
        $this->assertLessThanOrEqual(
            $this->zajecia->limit_miejsc,
            $this->zajecia->wolne_miejsca,
            'Wolne miejsca nie mogą przekraczać limitu'
        );
    }
    
    /**
     * Test poprawnych statusów zajęć
     */
    public function testPoprawnychStatusow()
    {
        $dozwoloneStatusy = ['planowane', 'odbyte', 'odwolane'];
        
        $this->assertContains(
            $this->zajecia->status,
            $dozwoloneStatusy,
            'Status musi być jednym z: planowane, odbyte, odwolane'
        );
    }
    
    /**
     * Test formatu daty (YYYY-MM-DD)
     */
    public function testFormatDaty()
    {
        $pattern = '/^\d{4}-\d{2}-\d{2}$/';
        $this->assertMatchesRegularExpression(
            $pattern,
            $this->zajecia->data,
            'Data musi być w formacie YYYY-MM-DD'
        );
    }
    
    /**
     * Test formatu godziny (HH:MM:SS)
     */
    public function testFormatGodziny()
    {
        $pattern = '/^\d{2}:\d{2}:\d{2}$/';
        $this->assertMatchesRegularExpression(
            $pattern,
            $this->zajecia->godzina_rozpoczecia,
            'Godzina musi być w formacie HH:MM:SS'
        );
    }
    
    /**
     * Test logiki biznesowej - godzina zakończenia po rozpoczęciu
     */
    public function testGodzinaZakonczeniaPoRozpoczęciu()
    {
        $rozpoczecie = strtotime($this->zajecia->godzina_rozpoczecia);
        $zakonczenie = strtotime($this->zajecia->godzina_zakonczenia);
        
        $this->assertGreaterThan(
            $rozpoczecie,
            $zakonczenie,
            'Godzina zakończenia musi być późniejsza niż rozpoczęcie'
        );
    }
    
    /**
     * Test pobierania wszystkich zajęć
     */
    public function testPobierzWszystkie()
    {
        $this->markTestSkipped('Test integracyjny - wymaga bazy testowej');
    }
    
    /**
     * Test pobierania dostępnych zajęć
     */
    public function testPobierzDostepne()
    {
        $this->markTestSkipped('Test integracyjny - wymaga bazy testowej');
    }
}
