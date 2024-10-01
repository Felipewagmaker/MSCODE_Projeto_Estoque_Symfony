<?php
namespace App\Controller\Produto;

use App\Repository\ProdutoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExcluirProdutoEstoqueController extends AbstractController
{

    public function __construct(
        private ProdutoRepository $produtoRepository,
    ){}

    #[Route('/produto/excluir/{id}', name: 'excluir_produto')]
    public function index(int|string $id): Response
    {
        $produto = $this->produtoRepository->find($id);
        
        $this->produtoRepository->excluir($produto);

        return $this->redirectToRoute('listar_produtos');
    }
}