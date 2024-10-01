<?php

namespace App\Controller\Produto;

use App\Repository\ProdutoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ListarController extends AbstractController
{
    public function __construct(
        private ProdutoRepository $produtoRepository
    ){}

    #[Route('/produtos', name: 'listar_produtos')]
    public function index(): Response
    {
        return $this->render('app/estoque/listar_produto.html.twig', [
            'headTitle' => '- Produtos',
            'active' => 'produtos',
            'produtos' => $this->produtoRepository->findAll(),
        ]);
    }
}