<?php

namespace App\Controller;

use DateTime;
use App\Entity\Vehicule;
use App\Form\VehiculeType;
use App\Repository\VehiculeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class VehiculeController extends AbstractController
{

    /**
     * @Route("/admin/ajout-voiture", name="ajout_voiture")
     */
    public function ajout(ManagerRegistry $doctrine, Request $request): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
             $this->addFlash('error', "Veuillez vous connecter pour accéder à la page");
             return $this->redirectToRoute('app_login');
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
             $this->addFlash('error', "Vous n'avez pas les droits pour accéder à cette page");
             return $this->redirectToRoute('app_home');
        }


        $car = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $car);
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
            $car->setdateEnregistrement ( new DateTime("now"));

            $photo = $form->get('photo')->getData();

            if($car->getPhoto()!== null){

            $file = $form->get('photo')->getData();

            $fileName= uniqid(). '-' . $file->guessExtension();
            try{
            $file->move(
                $this->getParameter('images_directory'),
                $fileName
            );
            }catch(FileException $e){
                return new Response($e->getMessage());
            }
            $car->setPhoto($fileName);
        }

            $manager=$doctrine->getManager();
            $manager->persist($car);
            $manager->flush();

             $this->addFlash('success', "Le véhicule a bien été ajouté");

            return $this->redirectToRoute('app_voiture');
        }
        return $this->render('voiture/formVehicule.html.twig',[
            'formVehicule'=>$form->createView()
        ]);
    }

    /**
     * @Route("/voitures", name="app_voiture")
     */
    public function AllVoitures(ManagerRegistry $doctrine, Request $request): Response
    {
        $AllVoitures=$doctrine->getRepository(Vehicule::class)->findAll();
        return $this->render('voiture/AllVoitures.html.twig', [
            'AllVoitures'=>$AllVoitures
        ]);
    }

    /**
     * @Route("/admin/adminvoiture", name="admin_app_voiture")
     */
    public function adminVoitures(ManagerRegistry $doctrine, Request $request): Response
    {
        $adminVoitures=$doctrine->getRepository(Vehicule::class)->findAll();
        return $this->render('voiture/admin/adminVoitures.html.twig', [
            'adminVoitures'=>$adminVoitures
        ]);
    }

       /**
     * @Route("/one_car/{id<\d+>}", name="one_car")
     */
    public function oneCar($id, ManagerRegistry $doctrine): Response
    {
        $car=$doctrine->getRepository(Vehicule::class)->find($id);
        return $this->render('voiture/oneCar.html.twig', [
            'car'=>$car
        ]);
    }

    /**
     * @Route("/admin/update_car/{id<\d+>}", name="update_car")
     */
    public function update(ManagerRegistry $doctrine, $id, Request $request) : Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
             $this->addFlash('error', "Veuillez vous connecter pour accéder à la page");
             return $this->redirectToRoute('app_login');
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
             $this->addFlash('error', "Vous n'avez pas les droits pour accéder à cette page");
             return $this->redirectToRoute('app_home');
        }

        $car = $doctrine->getRepository(Vehicule::class)->find($id);
        $form =$this->createForm(VehiculeType::class, $car);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
        $car->setdateEnregistrement ( new DateTime("now"));
            $photo = $form->get('photo')->getData();
            if($car->getPhoto()!== null){
            $file = $form->get('photo')->getData();
            $fileName= uniqid(). '-' .$file->guessExtension();
            try{
            $file->move(
                $this->getParameter('images_directory'),
                $fileName
            );
            }catch(FileException $e){
                return new Response($e->getMessage());
            }
            $car->setPhoto($fileName);
        }
        $manager=$doctrine->getManager();
        $manager->persist($car);
        $manager->flush();

        $this->addFlash('success', "Le véhicule a bien été mis à jour");

        return $this->redirectToRoute("admin_app_voiture");
        }

        return $this->render('voiture/formVehicule.html.twig', [
            'formVehicule'=>$form->createView(),
        ]);
    }


          /**
     * @Route("/admin/delete_car_{id<\d+>}", name="delete_car")
     */
        public function delete($id, VehiculeRepository $repo)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
             $this->addFlash('error', "Veuillez vous connecter pour accéder à la page");
             return $this->redirectToRoute('app_login');
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
             $this->addFlash('error', "Vous n'avez pas les droits pour accéder à cette page");
             return $this->redirectToRoute('app_home');
        }
                $car = $repo->find($id);
                $repo->remove($car, 1); 

                $this->addFlash('success', "La fiche a bien été supprimée");

                return $this->redirectToRoute("admin_app_voiture");
    } 


}
