<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\VehiculeType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VehiculeController extends AbstractController
{
    /**
     * @Route("ajout-voiture", name="ajout_voiture")
     */
    public function ajout(ManagerRegistry $doctrine, Request $request): Response
    {
        $car = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $car);
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
            $manager=$doctrine->getManager();
            $manager->persist($car);
            $manager->flush();

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
     * @Route("/one_car/{id}", name="one_car")
     */
    public function oneCar($id, ManagerRegistry $doctrine): Response
    {
        $car=$doctrine->getRepository(Vehicule::class)->find($id);
        return $this->render('voiture/oneCar.html.twig', [
            'car'=>$car
        ]);
    }

    // /**
    //  * @Route("/one_car/{id}", name="one_car")
    //  */
    // public function oneCar($id, ManagerRegistry $doctrine): Response
    // {
    //     $car=$doctrine->getRepository(Vehicule::class)->find($id);
    //     return $this->render('voiture/oneCar.html.twig', [
    //         'car'=>$car
    //     ]);
    // }

    /**
     * @Route("/update_car/{id<\d+>}", name="update_car")
     */
    public function update(ManagerRegistry $doctrine, $id, Request $request) : Response
    {
        $car = $doctrine->getRepository(Vehicule::class)->find($id);
        $form =$this->createForm(VehiculeType::class, $car);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
        $manager=$doctrine->getManager();
        $manager->persist($car);
        $manager->flush();

                return $this->redirectToRoute("app_voiture");
        }
        return $this->render('voiture/formVehicule.html.twig', [
            'formVehicule'=>$form->createView(),
        ]);
    }

          /**
     * @Route("/delete_car_{id}", name="delete_car")
     */
        public function delete($id, ManagerRegistry $doctrine)
    {
                $car = $doctrine->getRepository(Vehicule::class)->find($id);
                $manager=$doctrine->getManager();
                $manager->remove($car);
                $manager->flush();
                return $this->redirectToRoute("app_voiture");
    } 




    
}
