<?php

namespace App\Controller\Front;

use App\Repository\CarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
