<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Doctrine\DBAL\Exception\DriverException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    // Gets all ingredients.
    #[Route('/recipe', name: 'recipe', methods: ['GET'])]
    public function getIngredients(IngredientRepository $ingredientRepository): JsonResponse
    {
        $ingredients = $ingredientRepository->findAll();

        return $this->json(data: $ingredients, headers: ['Access-Control-Allow-Origin' => '*', 'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content, Accept, Content-Type, Authorization', 'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, PATCH, OPTIONS']);
    }

    // Checks ingredient and saves it or not.
    #[Route('/recipe', methods: ['POST'])]
    public function addIngredient(Request $request, IngredientRepository $ingredientRepository): JsonResponse
    {
        try {
            $name = $request->request->getString('ingredient');
            $quantity = $request->request->getString('quantity');
            $unit = $request->request->getString('unit');

            if ('' !== $name && '' !== $quantity && '' !== $unit) {
                if (0 === preg_match('/\d+/', $name) && 0 === preg_match('/\D+/', $quantity) && 0 === preg_match('/-\d+/', $quantity)
                    && 1 === preg_match('/[^0]/', $quantity) && 0 === preg_match('/\d+/', $unit)) {
                    $ingredient = new Ingredient();
                    $ingredient->setIngredient($name);
                    $ingredient->setQuantity(intval($quantity));
                    $ingredient->setUnit($unit);

                    $ingredientRepository->save($ingredient, true);
                } else {
                    throw new JsonException('Invalid data.');
                }
            } else {
                throw new JsonException('Invalid data.');
            }

            $message = ['message' => 'Data are valid.'];

            return $this->json(data: $message, headers: ['Access-Control-Allow-Origin' => '*', 'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content, Accept, Content-Type, Authorization', 'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, PATCH, OPTIONS']);
        } catch (JsonException|DriverException $error) {
            $message = ['message' => 'Invalid data.'];

            return $this->json(data: $message, headers: ['Access-Control-Allow-Origin' => '*', 'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content, Accept, Content-Type, Authorization', 'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, PATCH, OPTIONS']);
        }
    }
}
