<?php

namespace App\Controller\Admin;

use App\Entity\Car;
use App\Form\CarType;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminCarController extends AbstractController
{

    /**
     * @Route("admin/cars", name="admin_list_car")
     */
    public function adminListCar(CarRepository $carRepository)
    {
        $cars = $carRepository->findAll();

        return $this->render("admin/car_list.html.twig", ['cars' => $cars]);
    }

    /**
     * @Route("admin/car/{id}", name="admin_show_car")
     */
    public function adminShowCar($id, CarRepository $carRepository)
    {
        $car = $carRepository->find($id);

        return $this->render("admin/car_show.html.twig", ['car' => $car]);
    }

    /**
     * @Route("admin/create/car", name="admin_creat_car")
     */
    public function adminCreateCar(
        EntityManagerInterface $entityManagerInterface,
        Request $request,
        SluggerInterface $sluggerInterface
    ) {
        $car = new Car();

        $carForm = $this->createForm(CarType::class, $car);

        $carForm->handleRequest($request);

        if ($carForm->isSubmitted() && $carForm->isValid()) {

            $imageFile = $carForm->get('image')->getData();

            if ($imageFile) {

                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $sluggerInterface->slug($originalFilename);

                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $car->setImage($newFilename);
            }

            $entityManagerInterface->persist($car);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("admin_list_car");
        }

        return $this->render('admin/car_form.html.twig', ['carForm' => $carForm->createView()]);
    }

    /**
     * @Route("admin/update/car/{id}", name="admin_update_car")
     */
    public function adminUpdateCar(
        $id,
        CarRepository $carRepository,
        EntityManagerInterface $entityManagerInterface,
        Request $request,
        SluggerInterface $sluggerInterface
    ) {

        $car = $carRepository->find($id);

        $carForm = $this->createForm(CarType::class, $car);

        $carForm->handleRequest($request);

        if ($carForm->isSubmitted() && $carForm->isValid()) {

            $imageFile = $carForm->get('image')->getData();

            if ($imageFile) {

                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $sluggerInterface->slug($originalFilename);

                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $car->setImage($newFilename);
            }

            $entityManagerInterface->persist($car);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("admin_list_car");
        }

        return $this->render('admin/car_form.html.twig', ['carForm' => $carForm->createView()]);
    }

    /**
     * @Route("admin/delete/car/{id}", name="admin_delete_car")
     */
    public function adminDeleteCar(
        $id,
        CarRepository $carRepository,
        EntityManagerInterface $entityManagerInterface
    ) {
        $car = $carRepository->find($id);

        $entityManagerInterface->remove($car);

        $entityManagerInterface->flush();

        return $this->redirectToRoute("admin_list_car");
    }
}
