<?php

namespace App\Controller\Front;

use App\Repository\CarRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @Route("search", name="search")
     */
    public function search(
        Request $request,
        CarRepository $carRepository
    ) {

        $term = $request->query->get("search");

        $cars = $carRepository->searchByTerm($term);

        return $this->render("front/search.html.twig", ['cars' => $cars, 'term' => $term]);
    }
}
