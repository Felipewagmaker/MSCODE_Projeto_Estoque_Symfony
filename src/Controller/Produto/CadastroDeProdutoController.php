<?php

namespace App\Controller\Produto;

use App\Entity\Produto;
use App\Repository\CategoriaRepository;
use App\Repository\ProdutoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CadastroDeProdutoController extends AbstractController
{

    public function __construct(
        private ProdutoRepository $produtoRepository,
        private CategoriaRepository $categoriaRepository
    ) {}

    #[Route('/produtos/cadastrar', name: 'cadastrar_produto_show', methods: 'GET')]
    public function index(): Response
    {
        return $this->render('app/estoque/cadastro_produto.html.twig', [
            'headTitle' => '- Produtos',
            'active' => 'produtos',
            'title' => 'Novo',
            'cadastrar' => true,
            'categorias' => $this->categoriaRepository->findAll(),
        ]);
    }

    #[Route('/produtos/cadastrar', name: 'cadastrar_produto_salvar', methods: 'POST')]
    public function salvar(Request $request): Response
    {
        $nomeProduto = $request->request->get('nome');
        if (strlen($nomeProduto) > 100) {
            $this->addFlash('danger', 'Nome deve ter no máximo 100 caracteres!');
            return $this->redirectToRoute('cadastrar_produto_show');
        }

        $produtoExistente = $this->produtoRepository->findBy(['nome' => $nomeProduto]);
        if ($produtoExistente) {
            $this->addFlash('danger', "Produto com nome \"{$nomeProduto}\" já existe!");
            return $this->redirectToRoute('cadastrar_produto_show');
        }

        $request = $request->request;

        $categoria = $this->categoriaRepository->findBy(['id' => $request->get('categoriaId')])[0];

        $produto = new Produto();
        $produto->setNome($nomeProduto);
        $produto->setDescricao($request->get('descricao'));
        $produto->setCategoriaId($categoria);
        $produto->setQuantidadeInicial($request->get('quantidade'));
        $produto->setQuantidadeDisponivel($request->get('quantidade'));
        $produto->setValor($request->get('valor'));

        $this->produtoRepository->salvar($produto);

        return $this->redirectToRoute('listar_produtos');
    }
}