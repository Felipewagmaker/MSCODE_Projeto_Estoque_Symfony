<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/app', name: 'app', methods: 'GET')]
class AppController extends AbstractController
{
    public function __invoke(Request $request): Response
    {
        return $this->render('app/index.html.twig', [
            'headTitle' => '- App',
            'active' => 'inicio',
        ]);
    }
}