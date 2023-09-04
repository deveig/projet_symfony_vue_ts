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
        $ingredient = new Ingredient();
        $ingredient->setIngredient('oil');
        $ingredient->setQuantity(5);
        $ingredient->setUnit('cl');

        $manager->persist($ingredient);
        $manager->flush();

        return;
    }
}
