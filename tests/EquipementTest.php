<?php


namespace App\Tests;


use App\Entity\Equipement;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EquipementTest extends KernelTestCase
{
    public function getEntity() : Equipement
    {
        return (new Equipement())
            ->setCategory('Ordinateur')
            ->setDescription('Très bon état')
            ->setNumber("123")
            ->setName("Samsung");
    }

    public function assertHasErrors(Equipement $equipement, int $number = 0) :void
    {
        self::bootKernel();

        $error = self::$container->get('validator')->validate($equipement);
        $this->assertCount($number, $error);
    }

    public function testValidEntity() :void
    {
        $equipement = $this->getEntity();
        $this->assertHasErrors($equipement, 0);
    }

    public function testInvalidEntity() :void
    {
        $this->assertHasErrors($this->getEntity()->setName(""), 1);
        $this->assertHasErrors($this->getEntity()->setDescription(""), 1);
        $this->assertHasErrors($this->getEntity()->setNumber(""), 1);

    }
}