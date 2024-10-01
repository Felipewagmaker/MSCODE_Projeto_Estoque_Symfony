<?php

namespace App\Controller\Produto;

use App\Entity\Produto;
use App\Repository\CategoriaRepository;
use App\Repository\ProdutoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
class EditarProdutoEstoqueController extends AbstractController
{

    public function __construct(
        private ProdutoRepository $produtoRepository,
        private CategoriaRepository $categoriaRepository,
    ){}

    #[Route('/produto/editar/{id}', name: 'editar_produto_show')]
    public function index(int $id): Response
    {
        $produto = $this->produtoRepository->findBy(['id' => $id])[0];
        $categorias = $this->categoriaRepository->findAll();

        return $this->render('app/estoque/cadastro_produto.html.twig', [
            'headTitle' => '- Produtos',
            'active' => 'produtos',
            'title' => 'Editar',
            'cadastrar' => false,
            'produto' => $produto,
            'categorias' => $categorias,
        ]);
    }

    #[Route('/produto/editar/salvar/{id}', name: 'editar_produto_salvar')]
    public function editar(Request $request, int $id): Response
    {
        $nomeProduto = $request->request->get('nome');
        if (strlen($nomeProduto) > 100) {
            $this->addFlash('danger', 'Nome deve ter no máximo 100 caracteres!');
            return $this->redirectToRoute('cadastrar_produto_show');
        }

        $request = $request->request;

        $categoria = $this->categoriaRepository->findBy(['id' => $request->get('categoriaId')])[0];
        
        $produto = $this->produtoRepository->find($id);

        $produto->setNome($nomeProduto);
        $produto->setDescricao($request->get('descricao'));
        $produto->setCategoriaId($categoria);
        $produto->setValor($request->get('valor'));

        $this->produtoRepository->getEntityManager()->flush();

        return $this->redirectToRoute('listar_produtos');
    }
}