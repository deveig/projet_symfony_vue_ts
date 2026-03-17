<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class IngredientFixture extends Fixture
{
    // Loads test data.
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUserName("Sam");
        $user->setPassword(hash('sha256', $user->getUserName()));

        $firstIngredient = new Ingredient();
        $firstIngredient->setIngredient('oil');
        $firstIngredient->setQuantity(5);
        $firstIngredient->setUnit('cl');
        $firstIngredient->setUser($user);

        $manager->persist($firstIngredient);
        $manager->flush();

        $secondIngredient = new Ingredient();
        $secondIngredient->setIngredient('salad');
        $secondIngredient->setQuantity(1);
        $secondIngredient->setUnit('piece');
        $secondIngredient->setUser($user);

        $manager->persist($secondIngredient);
        $manager->flush();

        return;
    }
}
