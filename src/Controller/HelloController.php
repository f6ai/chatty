<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HelloController extends AbstractController
{
    private array $messages = ['Hello', 'Hi', 'World'];

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('base.html.twig',[
            
        ]);
    }

    #[Route('/messages/{id<\d+>}', name: 'app_show_one')]
    public function showOne($id): Response 
    {
        return new Response($this->messages[$id]);

    }
}
