<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Form\ReponseType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReponseController extends AbstractController
{
    #[Route('/reponse', name: 'app_reponse')]
    public function index(ManagerRegistry $manager, Request $request): Response
    {
        $reponse = new Reponse;
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $reponse->setScore(0);
            $om = $manager->getManager();
            $om->persist($reponse);
            $om->flush();
        }

        return $this->renderForm('reponse/index.html.twig', [
            'reponses' => $manager->getRepository(Reponse::class)->findAll(),
            'form' => $form
        ]);
    }
    

    #[Route('/reponse/{id}/update', name:'update_reponse')]
    public function update(int $id, ManagerRegistry $manager, Request $request): Response
    {
       $reponse = $manager->getRepository(Reponse::class)->find($id);
       if($reponse){
           $form = $this->createForm(ReponseType::class, $reponse);
           $form->handleRequest($request);
           if($form->isSubmitted() && $form->isValid()){
               $om = $manager->getManager();
               $om->persist($reponse);
               $om->flush();
               return $this->redirectToRoute('app_reponse');
           }
           return $this->renderForm('reponse/update.html.twig', [
               'form' => $form,
               'reponse' => $reponse
           ]);

       }
       return $this->redirectToRoute('app_reponse');
    }

    #[Route('/reponse/{id}/increment', name: 'increment_score')]
    public function incrementScore(int $id, ManagerRegistry $manager): Response
    {
        $reponse = $manager->getRepository(Reponse::class)->find($id);
        if($reponse){
            $reponse->setScore($reponse->getScore() +1);
            $om = $manager->getManager();
            $om->persist($reponse);
            $om->flush();
        }
        return $this->redirectToRoute('home');
    } 

}
