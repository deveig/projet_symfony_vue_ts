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
        try {
            $ingredients = $ingredientRepository->findAll();

            return $this->json(data: $ingredients, headers: ['Access-Control-Allow-Origin' => '*', 'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content, Accept, Content-Type, Authorization', 'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, PATCH, OPTIONS']);
        } catch (\Exception $error) {
            $message = ['message' => 'Internal Server Error.'];

            return $this->json(data: $message, status: 500, headers: ['Access-Control-Allow-Origin' => '*', 'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content, Accept, Content-Type, Authorization', 'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, PATCH, OPTIONS']);
        }
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
                    && 1 === preg_match('/[^0]/', $quantity) &&  0 === preg_match('/\d+/', $unit)) {
                    $ingredient = new Ingredient();
                    $ingredient->setIngredient($name);
                    $ingredient->setQuantity(intval($quantity));
                    $ingredient->setUnit($unit);

                    $ingredientRepository->save($ingredient, true);
                } else {
                    throw new \Exception("Invalid data.");
                }
            } else {
                throw new \Exception("Invalid data.");
            }
            $message = ['message' => 'Data is saved.'];

            return $this->json(data: $message, headers: ['Access-Control-Allow-Origin' => '*', 'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content, Accept, Content-Type, Authorization', 'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, PATCH, OPTIONS']);
        } catch (\Exception $error) {
            $status = 500;
            if ('Invalid data.' === $error->getMessage()) {
                $message = ['message' => 'Invalid data.'];
                $status = 400;
            } else {
                $message = ['message' => 'Internal Server Error.'];
            }

            return $this->json(data: $message, status: $status, headers: ['Access-Control-Allow-Origin' => '*', 'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content, Accept, Content-Type, Authorization', 'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, PATCH, OPTIONS']);
        }
    }

    #[Route('/recipe/delete', methods: ['GET'])]
    public function deleteIngredient(IngredientRepository $ingredientRepository): JsonResponse
    {
        try {
            $ingredients = $ingredientRepository->findAll();
            $status = 200;
            if (0 < count($ingredients)) {
                $lastIngredient = $ingredients[count($ingredients) - 1];
                $ingredientRepository->remove($lastIngredient, true);
                $message = ['message' => 'Data is deleted.'];
            } else {
                $message = ['message' => 'No ingredient to remove.'];
                $status = 400;
            }

            return $this->json(data: $message, status: $status, headers: ['Access-Control-Allow-Origin' => '*', 'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content, Accept, Content-Type, Authorization', 'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, PATCH, OPTIONS']);
        } catch (\Exception $error) {
            $message = ['message' => 'Internal Server Error.'];

            return $this->json(data: $message, status: 500, headers: ['Access-Control-Allow-Origin' => '*', 'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content, Accept, Content-Type, Authorization', 'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, PATCH, OPTIONS']);
        }
    }
}
