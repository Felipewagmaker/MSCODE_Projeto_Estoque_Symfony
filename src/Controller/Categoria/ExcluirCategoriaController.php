<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExcluirCategoriaController extends AbstractController
{
    #[Route('/excluir/categoria', name: 'app_excluir_categoria')]
    public function index(): Response
    {
        return $this->render('excluir_categoria/index.html.twig', [
            'controller_name' => 'ExcluirCategoriaController',
        ]);
    }
}
