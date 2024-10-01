<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditarCategoriaController extends AbstractController
{
    #[Route('/editar/categoria', name: 'app_editar_categoria')]
    public function index(): Response
    {
        return $this->render('editar_categoria/index.html.twig', [
            'controller_name' => 'EditarCategoriaController',
        ]);
    }
}
