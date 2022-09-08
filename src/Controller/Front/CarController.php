<?php

namespace App\Controller\Front;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CarRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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
    public function ShowCar(
        $id,
        CarRepository $carRepository,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManagerInterface
    ) {
        $car = $carRepository->find($id);

        $user = $this->getUser();

        if ($user) {
            $user_email = $user->getUserIdentifier();
            $user_final = $userRepository->findOneBy(['email' => $user_email]);
        }



        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setDate(new \DateTime("NOW"));
            $comment->setCar($car);
            $comment->setUser($user_final);

            $entityManagerInterface->persist($comment);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("front_show_car", ['id' => $id]);
        }

        return $this->render("front/car_show.html.twig", ['car' => $car, 'commentForm' => $commentForm->createView()]);
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
