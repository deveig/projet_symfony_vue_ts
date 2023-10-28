<?php 

namespace App\Tests\Controller;

use App\Controller\RecipeController;
use App\Repository\IngredientRepository;
use App\Entity\Ingredient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecipeControllerTest extends KernelTestCase
{
    public function testGetAllIngredients()
    {
        // Arranges
        self::bootKernel();

        $container = static::getContainer();

        $ingredientRepository = $container->get(IngredientRepository::class);

        $recipeController = $container->get(RecipeController::class);

        // Acts
        $response = $recipeController->getIngredients($ingredientRepository);

        // Asserts
        $this->assertCount(2, json_decode($response->getContent()));
        $this->assertEquals(1, json_decode($response->getContent())[0]->id);
        $this->assertEquals('oil', json_decode($response->getContent())[0]->ingredient);
        $this->assertEquals(5, json_decode($response->getContent())[0]->quantity);
        $this->assertEquals('cl', json_decode($response->getContent())[0]->unit);
        $this->assertEquals(2, json_decode($response->getContent())[1]->id);
        $this->assertEquals('salad', json_decode($response->getContent())[1]->ingredient);
        $this->assertEquals(1, json_decode($response->getContent())[1]->quantity);
        $this->assertEquals('piece', json_decode($response->getContent())[1]->unit);
    }   

    public function testAddAValidIngredient()
    {
        // Arranges
        self::bootKernel();

        $container = static::getContainer();

        $ingredientRepository = $container->get(IngredientRepository::class);

        $recipeController = $container->get(RecipeController::class);

        $ingredient = new Ingredient();
        $ingredient->setIngredient('onion');
        $ingredient->setQuantity(1);
        $ingredient->setUnit('piece');

        $request = new Request(request: ['ingredient' => $ingredient->getIngredient(), 'quantity' => strval($ingredient->getQuantity()), 'unit' => $ingredient->getUnit()]);

        $message = json_encode(['message' => 'Data is saved.']);
        
        // Acts
        $response = $recipeController->addIngredient($request, $ingredientRepository);

        // Asserts
        $this->assertEquals($message, $response->getContent());
    }

    public function testAddAnInvalidIngredient()
    {
        // Arranges
        self::bootKernel();

        $container = static::getContainer();

        $ingredientRepository = $container->get(IngredientRepository::class);

        $recipeController = $container->get(RecipeController::class);

        $ingredient = new Ingredient();
        $ingredient->setIngredient('onion1');
        $ingredient->setQuantity(1);
        $ingredient->setUnit('piece');

        $request = new Request(request: ['ingredient' => $ingredient->getIngredient(), 'quantity' => strval($ingredient->getQuantity()), 'unit' => $ingredient->getUnit()]);

        $message = json_encode(['message' => 'Invalid data.']);

        // Acts
        $response = $recipeController->addIngredient($request, $ingredientRepository);

        // Asserts
        $this->assertEquals($message, $response->getContent());
    }

    public function testDeleteTheLastIngredient()
    {
        // Arranges
        self::bootKernel();

        $container = static::getContainer();

        $ingredientRepository = $container->get(IngredientRepository::class);

        $recipeController = $container->get(RecipeController::class);

        $message = json_encode(['message' => 'Data is deleted.']);

        // Acts
        $response = $recipeController->deleteIngredient($ingredientRepository);

        // Asserts
        $this->assertEquals($message, $response->getContent());
    }
    public function testDeleteWhenNoIngredient()
    {
        // Arranges
        self::bootKernel();

        $container = static::getContainer();

        $ingredientRepository = $container->get(IngredientRepository::class);

        $recipeController = $container->get(RecipeController::class);

        $message = json_encode(['message' => 'No ingredient to remove.']);

        // Acts
        $recipeController->deleteIngredient($ingredientRepository);
        $recipeController->deleteIngredient($ingredientRepository);
        $response = $recipeController->deleteIngredient($ingredientRepository);

        // Asserts
        $this->assertEquals($message, $response->getContent());
    }
}