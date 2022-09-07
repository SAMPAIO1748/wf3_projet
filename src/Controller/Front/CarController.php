<?php

namespace App\Controller\Front;

use App\Repository\CarRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CarController extends AbstractController
{
    /**
     * @Route("/cars", name="front_list_car")
     */
    public function ListCar(CarRepository $carRepository)
    {
        $cars = $carRepository->findAll();

        return $this->render("front/car_list.html.twig", ['cars' => $cars]);
    }

    /**
     * @Route("/car/{id}", name="front_show_car")
     */
    public function ShowCar($id, CarRepository $carRepository)
    {
        $car = $carRepository->find($id);

        return $this->render("front/car_show.html.twig", ['car' => $car]);
    }
}
