<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class IngredientFixture extends Fixture
{
    // Loads test data.
    public function load(ObjectManager $manager): void
    {
        $firstIngredient = new Ingredient();
        $firstIngredient->setIngredient('oil');
        $firstIngredient->setQuantity(5);
        $firstIngredient->setUnit('cl');

        $manager->persist($firstIngredient);
        $manager->flush();

        $secondIngredient = new Ingredient();
        $secondIngredient->setIngredient('salad');
        $secondIngredient->setQuantity(1);
        $secondIngredient->setUnit('piece');

        $manager->persist($secondIngredient);
        $manager->flush();

        return;
    }
}
