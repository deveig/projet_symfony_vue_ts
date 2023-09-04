<?php 

namespace App\Tests\Controller;

use App\Controller\RecipeController;
use App\Repository\IngredientRepository;
use App\Entity\Ingredient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecipeControllerTest extends KernelTestCase
{
    public function testGetAllIngredients()
    {
        self::bootKernel();

        $container = static::getContainer();

        $ingredientRepository = $container->get(IngredientRepository::class);
        $ingredients = $ingredientRepository->findAll();

        $this->assertCount(1, $ingredients);
        $this->assertInstanceOf(Ingredient::class, $ingredients[0]);
        $this->assertEquals(1, $ingredients[0]->getId());
        $this->assertEquals('oil', $ingredients[0]->getIngredient());
        $this->assertEquals(5, $ingredients[0]->getQuantity());
        $this->assertEquals('cl', $ingredients[0]->getUnit());
    }

    public function testAddIngredient()
    {
        // Boots the Symfony kernel.
        self::bootKernel();
        // Accesses the service container.
        $container = static::getContainer();

        $ingredientRepository = $container->get(IngredientRepository::class);
        $ingredients = $ingredientRepository->findAll();

        $this->assertCount(1, $ingredients);

        $ingredient = new Ingredient();
        $ingredient->setIngredient('onion');
        $ingredient->setQuantity(1);
        $ingredient->setUnit('piece');

        $ingredientRepository->save($ingredient, true);
        $ingredients = $ingredientRepository->findAll();

        $this->assertCount(2, $ingredients);
        $this->assertEquals('onion', $ingredients[1]->getIngredient());
        $this->assertEquals(1, $ingredients[1]->getQuantity());
        $this->assertEquals('piece', $ingredients[1]->getUnit());
    }
}