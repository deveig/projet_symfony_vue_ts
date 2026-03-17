<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\SessionService;
use Doctrine\DBAL\Exception\DriverException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RequestStack;

class RecipeController extends AbstractController
{
    // Checks user and saves it or not.
    #[Route('/recipe/user', methods: ['POST'])]
    public function addUser(Request $request, UserRepository $userRepository): JsonResponse
    {
        try {
            $user_name = $request->request->getString('username');
            if ('' !== $user_name) {
                if (0 === preg_match('/\d+/', $user_name)) {
                    $user = new User();
                    $user->setUserName($user_name);
                    $password = password_hash($user->getUserName(), PASSWORD_DEFAULT);
                    $user->setPassword($password);

                    $userRepository->save($user, true);
                    $user = $userRepository->findOneBy(['password' => $password]);
                    $session = $request->getSession();
                    $session->start();
                    $session->set('user', $user);

                } else {
                    throw new \Exception("Invalid data.");
                }
            } else {
                throw new \Exception("Invalid data.");
            }
            $message = ['message' => 'User is saved.'];
            $status = 201;
            return $this->json(data: $message, status: $status);
        } catch (\Exception $error) {
            $status = 500;
            if ('Invalid data.' === $error->getMessage()) {
                $message = ['message' => 'Invalid data.'];
                $status = 400;
            } else {
                $message = ['message' => 'Internal Server Error.'];
            }

            return $this->json(data: $message, status: $status);
        }
    }

    //Gets user.
    #[Route('/recipe/user', methods: ['GET'])]
    public function getDataOfUser(Request $request, UserRepository $userRepository): JsonResponse
    {
        try {
            $session = $request->getSession();
            $user = $session->get('user');
            if($user != null){
                $user = $userRepository->find($user->getId());
                // return $this->json(data: $user, headers: ['Access-Control-Allow-Origin' => '*', 'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content, Accept, Content-Type, Authorization', 'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, PATCH, OPTIONS']);
                return $this->json(data: $user);
            } else {
                $message = ['message' => 'No user'];
                $status = 400;
                return $this->json(data: $message, status: $status);
            }
        } catch (\Exception $error) {
            $message = ['message' => 'Internal Server Error.'];
            $status = 500;
            return $this->json(data: $message, status: $status);
        }
    }

    //Gets all ingredients.
    #[Route('/recipe', name: 'recipe', methods: ['GET'])]
    public function getIngredients(Request $request, IngredientRepository $ingredientRepository): JsonResponse
    {
        try {
            $user = $request->getSession()->get("user");
            if($user != null){
                $ingredients = $ingredientRepository->findBy(['user' => $user]);
                return $this->json(data: $ingredients);
            } else {
                $message = ['message' => 'No user'];
                $status = 400;
                return $this->json(data: $message, status: $status);
            }
        } catch (\Exception $error) {
            $message = ['message' => 'Internal Server Error.'];
            $status = 500;
            return $this->json(data: $message, status: $status);
        }
    }

    // Checks ingredient and saves it or not.
    #[Route('/recipe', methods: ['POST'])]
    public function addIngredient(Request $request, IngredientRepository $ingredientRepository, UserRepository $userRepository): JsonResponse
    {
        try {
            $user = $request->getSession()->get("user");
            if($user != null){
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
                        $knownUser = $userRepository->find($user->getId());
                        $ingredient->setUser($knownUser);
                        $ingredientRepository->save($ingredient, true);
                        $message = ['message' => 'Data is saved.'];
                        $status = 201;
                    } else {
                        throw new \Exception("Invalid data.");
                    }
                } else {
                    throw new \Exception("Invalid data.");
                }
            } else {
                $message = ['message' => 'No user'];
                $status = 400;
            }
            return $this->json(data: $message, status: $status);
           
        } catch (\Exception $error) {
            if ('Invalid data.' === $error->getMessage()) {
                $message = ['message' => 'Invalid data.'];
                $status = 400;
            } else {
                $message = ['message' => 'Internal Server Error.'];
                $status = 500;
            }

            return $this->json(data: $message, status: $status);
        }
    }

    // Removes the last ingredient if there is one.
    #[Route('/recipe/delete', methods: ['GET'])]
    public function deleteIngredient(Request $request, IngredientRepository $ingredientRepository): JsonResponse
    {
        try {
            $user = $request->getSession()->get("user");
            if($user != null){
                $ingredients = $ingredientRepository->findBy(['user' => $user]);
                $status = 200;
                if (0 < count($ingredients)) {
                    $lastIngredient = $ingredients[count($ingredients) - 1];
                    $ingredientRepository->remove($lastIngredient, true);
                    $message = ['message' => 'Data is deleted.'];
                } else {
                    $message = ['message' => 'No ingredient to remove.'];
                    $status = 400;
                }
            } else {
                $message = ['message' => 'No user'];
            }
            return $this->json(data: $message, status: $status);
        } catch (\Exception $error) {
            $message = ['message' => 'Internal Server Error.'];
            $status = 500;
            return $this->json(data: $message, status: $status);
        }
    }
}
