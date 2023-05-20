<?php

namespace App\Controller;

use App\Entity\Question;
use App\Form\QuestionType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuestionController extends AbstractController
{
    #[Route('/question', name: 'app_question')]
    public function index(ManagerRegistry $manager, Request $request): Response
    {
        $question = new Question;
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $om = $manager->getManager();
            $om->persist($question);
            $om->flush();

        }
    
        return $this->renderForm('question/index.html.twig', [
            'questions' => $manager->getRepository(Question::class)->findAll(),
            'form' => $form
        ]);
        // ["value1", "value2", "value3"] => Tableau simple
        // ['cle1' => "valeur1", 'cle2' => "valeur2", 'cle3' =>"valeur3"] =>tableau associatif une valeur est associée à une clé
    }
    #[Route('/question/{id}/update', name:'update_question', requirements:['id' => '\d+'], methods: ['GET', 'POST'])]
    public function update(int $id, ManagerRegistry $manager, Request $request): Response
    {
        $question = $manager->getRepository(Question::class)->find($id);
        if($question){
            
            $form = $this->createForm(QuestionType::class, $question);
            $form->handleRequest($request);
            
            if($form->isSubmitted && $form->isValid()){
                $om = $manager->getManager();
                $om ->persist($question);
                $om->flush();
                return $this->redirectToRoute('app_question');
            }
            return $this->renderForm('question/update.html.twig', [
                'form' => $form,
                'question' => $question
            ]);
            
        } else{
            return $this->redirectToRoute('app_question');
        }
    }
}
