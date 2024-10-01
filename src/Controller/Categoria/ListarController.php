<?php

namespace App\Controller\Categoria;

use App\Repository\CategoriaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListarController extends AbstractController
{
    public function __construct(
        private CategoriaRepository $categoriaRepository,
    ) {}

    #[Route('categorias', name: 'listar_categorias')]
    public function show(): Response
    {
        return $this->render('app/categoria/listar.html.twig', [
            'categorias' => $this->categoriaRepository->findAll(),
            'headTitle' => '- Categorias',
            'active' => 'produtos',
        ]);
    }
}