<?php 

namespace App\Tests\Controller;

use App\Controller\RecipeController;
use App\Repository\IngredientRepository;
use App\Entity\Ingredient;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\SessionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

class RecipeControllerTest extends KernelTestCase
{
    public function testAddAnUser()
    {
        // Arranges
        self::bootKernel();

        $container = static::getContainer();

        $userRepository = $container->get(UserRepository::class);

        $storage = new MockFileSessionStorage();
        $session = new Session($storage);
        $session->start();

        $recipeController = $container->get(RecipeController::class);

        $user = new User();
        $user->setUserName('Sandra');

        $request = Request::create(uri: '/recipe/user', method: 'POST', parameters: ['username' => $user->getUserName()]);
        $request->setSession($session);

        $message = json_encode(['message' => 'User is saved.']);
        
        // Acts
        
        $response = $recipeController->addUser($request, $userRepository);

        // Asserts
        $this->assertEquals($message, $response->getContent());
        $this->assertTrue($session->has('user'));
    }

    public function testGetAllIngredients()
    {
        // Arranges
        self::bootKernel();

        $container = static::getContainer();

        $ingredientRepository = $container->get(IngredientRepository::class);

        $storage = new MockFileSessionStorage();
        $session = new Session($storage);
        $session->start();
        $userRepository = $container->get(UserRepository::class);
        $session->set('user', $userRepository->find(1));

        $recipeController = $container->get(RecipeController::class);

        $request = Request::create(uri: "/recipe", method: "GET");
        $request->setSession($session);

        // Acts
        $response = $recipeController->getIngredients($request, $ingredientRepository);
        
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

        $storage = new MockFileSessionStorage();
        $session = new Session($storage);
        $session->start();
        $userRepository = $container->get(UserRepository::class);
        $session->set('user', $userRepository->find(1));

        $recipeController = $container->get(RecipeController::class);

        $ingredient = new Ingredient();
        $ingredient->setIngredient('onion');
        $ingredient->setQuantity(1);
        $ingredient->setUnit('piece');

        $request = new Request(request: ['ingredient' => $ingredient->getIngredient(), 'quantity' => strval($ingredient->getQuantity()), 'unit' => $ingredient->getUnit()]);
        $request->setSession($session);

        $message = json_encode(['message' => 'Data is saved.']);
        
        // Acts
        $response = $recipeController->addIngredient($request, $ingredientRepository, $userRepository);

        // Asserts
        $this->assertEquals($message, $response->getContent());
    }

    public function testAddAnInvalidIngredient()
    {
        // Arranges
        self::bootKernel();

        $container = static::getContainer();

        $ingredientRepository = $container->get(IngredientRepository::class);

        $storage = new MockFileSessionStorage();
        $session = new Session($storage);
        $session->start();
        $userRepository = $container->get(UserRepository::class);
        $session->set('user', $userRepository->find(1));

        $recipeController = $container->get(RecipeController::class);

        $ingredient = new Ingredient();
        $ingredient->setIngredient('onion1');
        $ingredient->setQuantity(1);
        $ingredient->setUnit('piece');

        $request = new Request(request: ['ingredient' => $ingredient->getIngredient(), 'quantity' => strval($ingredient->getQuantity()), 'unit' => $ingredient->getUnit()]);
        $request->setSession($session);

        $message = json_encode(['message' => 'Invalid data.']);

        // Acts
        $response = $recipeController->addIngredient($request, $ingredientRepository, $userRepository);

        // Asserts
        $this->assertEquals($message, $response->getContent());
    }

    public function testDeleteTheLastIngredient()
    {
        // Arranges
        self::bootKernel();

        $container = static::getContainer();

        $ingredientRepository = $container->get(IngredientRepository::class);

        $storage = new MockFileSessionStorage();
        $session = new Session($storage);
        $session->start();
        $userRepository = $container->get(UserRepository::class);
        $session->set('user', $userRepository->find(1));

        $recipeController = $container->get(RecipeController::class);

        $request = Request::create(uri: "/recipe/delete", method: "GET");
        $request->setSession($session);

        $message = json_encode(['message' => 'Data is deleted.']);

        // Acts
        $response = $recipeController->deleteIngredient($request, $ingredientRepository);

        // Asserts
        $this->assertEquals($message, $response->getContent());
    }
    public function testDeleteWhenNoIngredient()
    {
        // Arranges
        self::bootKernel();

        $container = static::getContainer();

        $ingredientRepository = $container->get(IngredientRepository::class);

        $storage = new MockFileSessionStorage();
        $session = new Session($storage);
        $session->start();
        $userRepository = $container->get(UserRepository::class);
        $session->set('user', $userRepository->find(1));

        $recipeController = $container->get(RecipeController::class);

        $request = Request::create(uri: "/recipe/delete", method: "GET");
        $request->setSession($session);

        $message = json_encode(['message' => 'No ingredient to remove.']);

        // Acts
        $recipeController->deleteIngredient($request, $ingredientRepository);
        $recipeController->deleteIngredient($request, $ingredientRepository);
        $response = $recipeController->deleteIngredient($request, $ingredientRepository);

        // Asserts
        $this->assertEquals($message, $response->getContent());
    }
}