<?php

namespace App\Controller\Categoria;

use App\Repository\CategoriaRepository;
use App\Repository\ProdutoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExcluirCategoriaController extends AbstractController
{

    public function __construct(
        private CategoriaRepository $categoriaRepository,
        private ProdutoRepository $produtoRepository,
    ){}

    #[Route('/categoria/excluir/{id}', name: 'excluir_categoria')]
    public function index(int|string $id): Response
    {
        $categoria = $this->categoriaRepository->find($id);
        
        $produtoExiste = $this->produtoRepository->findOneBy(['categoria_id' => $categoria]);

        if ($produtoExiste){
            $this->addFlash('danger','Existem produtos vinculados a esta categoria');
        }else {
            $this->categoriaRepository->excluir($categoria);
        }

        return $this->redirectToRoute('listar_categorias');
    }
}