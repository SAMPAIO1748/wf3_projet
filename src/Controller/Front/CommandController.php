<?php

namespace App\Controller\Front;

use App\Entity\Command;
use App\Repository\CarRepository;
use App\Repository\CommandRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CommandController extends AbstractController
{
    /*
  Forme du panier dans la session :
  $panier = [
    id_car => quantity
  ]

  ex : $panier = [
    1 => 1,
    2 => 1,
    3 => 2
  ]

  */

    /**
     * @Route("cart/add/{id}", name="add_cart")
     */
    public function addCart($id, SessionInterface $sessionInterface)
    {
        // on récupère le tableau cart qui est enregistré dans la session
        // mais il n'existe pas alors sessionInterface le créé en tant que tableau vide.
        $cart = $sessionInterface->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        // on enregistre le chaangement dans la session
        $sessionInterface->set('cart', $cart);

        return $this->redirectToRoute("front_show_car", ['id' => $id]);
    }

    /**
     * @Route("cart", name="show_cart")
     */
    public function showCart(SessionInterface $sessionInterface, CarRepository $carRepository)
    {
        $cart = $sessionInterface->get('cart', []);

        $cartWithCar = [];

        foreach ($cart as $id => $quantity) {
            $cartWithCar[] = [
                'car' => $carRepository->find($id),
                'quantity' => $quantity
            ];
        }

        return $this->render("front/cart_show.html.twig", ['cartWithCar' => $cartWithCar]);
    }

    /**
     * @Route("cart/delete/{id}", name="delete_cart")
     */
    public function deleteCart($id, SessionInterface $sessionInterface)
    {
        $cart = $sessionInterface->get('cart', []);

        if (!empty($cart[$id]) && $cart[$id] === 1) {
            // unset supprime l'élément du tableau
            unset($cart[$id]);
        } else {
            $cart[$id]--;
        }

        $sessionInterface->set('cart', $cart);

        return $this->redirectToRoute("show_cart");
    }

    /**
     * @Route("cart/infos/", name="cart_infos")
     */
    public function cartInfos(UserRepository $userRepository)
    {
        $user = $this->getUser();

        if ($user) {
            $user_mail = $user->getUserIdentifier();
            $user_true = $userRepository->findOneBy(['email' => $user_mail]);

            return $this->render("front/infocart.html.twig", ['user' => $user_true]);
        } else {
            return $this->render("front/infocart.html.twig");
        }
    }

    /**
     * @Route("create/command", name="create_command")
     */
    public function createCommand(
        SessionInterface $sessionInterface,
        UserRepository $userRepository,
        EntityManagerInterface $entityManagerInterface,
        Request $request,
        CommandRepository $commandRepository,
        CarRepository $carRepository
    ) {

        $command = new Command();

        $commands = $commandRepository->findAll();
        $number = count($commands);
        $command_number = $number + 1;

        $command->setNumber("Command-" . $command_number);

        $cart = $sessionInterface->get('cart', []);

        $price = 0;

        foreach ($cart as $id_car => $quantity) {
            $car = $carRepository->find($id_car);
            $command->addCar($car);
            $price = $price + ($car->getPrice() * $quantity);
            unset($cart[$id_car]);
            $sessionInterface->set('cart', $cart);
        }

        $command->setTotalPrice($price);

        $user = $this->getUser();

        if ($user) {

            $user_mail = $user->getUserIdentifier();
            $user_true = $userRepository->findOneBy(['email' => $user_mail]);

            // on récupère les infos du formulaire
            $name = $request->request->get('name');
            $firstname = $request->request->get('firstname');
            $email = $request->request->get('email');

            $user_true->setName($name);
            $user_true->setFirstname($firstname);
            $user_true->setEmail($email);

            $entityManagerInterface->persist($user_true);
            $entityManagerInterface->flush();

            $command->setUser($user_true);

            $entityManagerInterface->persist($command);
            $entityManagerInterface->flush();
        } else {
            $name = $request->request->get('name');
            $firstname = $request->request->get('firstname');

            $command->setName($name);
            $command->setFirstname($firstname);

            $entityManagerInterface->persist($command);
            $entityManagerInterface->flush();
        }

        return $this->redirectToRoute("front_list_car");
    }
}
