<?php


namespace App\Tests\Unit;


use App\Entity\Equipement;
use PHPUnit\Framework\TestCase;

class EquipementTest extends TestCase
{
    private Equipement $equipement;

    protected function setUp(): void
    {
        parent::setUp();

        $this->equipement = new Equipement();

    }

    public function testGetName() : void
    {
        $value = "Iphone X";
        $response = $this->equipement->setName($value);

        self::assertInstanceOf(Equipement::class, $response);
        self::assertEquals($value, $this->equipement->getName());

    }

    public function testGetNumber() : void
    {
        $value = "13";
        $response = $this->equipement->setNumber($value);

        self::assertInstanceOf(Equipement::class, $response);
        self::assertEquals($value, $this->equipement->getNumber());

    }

    public function testGetDescription() : void
    {
        $value = "Etat impecable";
        $response = $this->equipement->setDescription($value);

        self::assertInstanceOf(Equipement::class, $response);
        self::assertEquals($value, $this->equipement->getDescription());

    }

    public function testGetCategory() : void
    {
        $value = "Ordinateur";
        $response = $this->equipement->setCategory($value);

        self::assertInstanceOf(Equipement::class, $response);
        self::assertEquals($value, $this->equipement->getCategory());

    }
}