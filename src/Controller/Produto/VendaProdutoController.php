<?php

namespace App\Controller\Produto;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VendaProdutoController extends AbstractController
{
    #[Route('/produtos', name: 'produtos_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $query = $em->createQuery('SELECT p FROM App\Entity\Produto p');
        $produtos = $query->getResult();
        return $this->render('produto/list.html.twig', [
            'produtos' => $produtos,
            'headTitle' => '- App',
            'active' => 'inicio',
        ]);
    }

    #[Route('/produtos/{id}/editar', name: 'editar_produto')]
    public function editarProduto(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $query = $em->createQuery('SELECT p FROM App\Entity\Produto p WHERE p.id = :id')
            ->setParameter('id', $id);
        $produto = $query->getOneOrNullResult();
        if (!$produto) {
            throw $this->createNotFoundException('Produto não encontrado.');
        }

        if ($request->isMethod('POST')) {
            $nome = $request->request->get('nome');
            $descricao = $request->request->get('descricao');
            $quantidadeDisponivel = $request->request->get('quantidade_disponivel');
            $valor = $request->request->get('valor');
            $produto['nome'] = $nome;
            $produto['descricao'] = $descricao;
            $produto['quantidade_disponivel'] = $quantidadeDisponivel;
            $produto['valor'] = $valor;
            $em->createQuery('UPDATE App\Entity\Produto p SET p.nome = :nome, p.descricao = :descricao, p.quantidade_disponivel = :quantidade_disponivel, p.valor = :valor WHERE p.id = :id')
                ->setParameters([
                    'nome' => $nome,
                    'descricao' => $descricao,
                    'quantidade_disponivel' => $quantidadeDisponivel,
                    'valor' => $valor,
                    'id' => $id
                ])
                ->execute();
            return $this->redirectToRoute('produtos_list');
        }
        return $this->render('produto/editar_produto.html.twig', [
            'produto' => $produto,
        ]);
    }

    #[Route('/venda/nova', name: 'nova_venda')]
    public function novaVenda(EntityManagerInterface $em): Response
    {
        $categorias = $em->createQuery('SELECT c FROM App\Entity\Categoria c')->getResult();
        $produtos = $em->createQuery('SELECT p FROM App\Entity\Produto p')->getResult();
        return $this->render('venda/nova_venda.html.twig', [
            'categorias' => $categorias,
            'produtos' => $produtos,
        ]);
    }

    #[Route('/venda/confirmar', name: 'nova_venda_confirmar', methods: ['POST'])]
    public function confirmarVenda(Request $request, EntityManagerInterface $em): Response
    {
        $produtoId = $request->request->get('produto');
        $quantidade = $request->request->get('quantidade');

        $query = $em->createQuery('SELECT p FROM App\Entity\Produto p WHERE p.id = :id')
            ->setParameter('id', $produtoId);
        $produto = $query->getOneOrNullResult();

        if ($produto && $produto['quantidade_disponivel'] >= $quantidade) {
            $valorTotal = $produto['valor'] * $quantidade;
            $em->createQuery('INSERT INTO App\Entity\Venda (produto_id, quantidade, valor_total, data_venda) VALUES (:produto_id, :quantidade, :valor_total, :data_venda)')
                ->setParameters([
                    'produto_id' => $produtoId,
                    'quantidade' => $quantidade,
                    'valor_total' => $valorTotal,
                    'data_venda' => new \DateTime()
                ])
                ->execute();
            $em->createQuery('UPDATE App\Entity\Produto p SET p.quantidade_disponivel = :quantidade_disponivel WHERE p.id = :id')
                ->setParameters([
                    'quantidade_disponivel' => $produto['quantidade_disponivel'] - $quantidade,
                    'id' => $produtoId
                ])
                ->execute();
            return $this->redirectToRoute('vendas_realizadas');
        }
        return $this->redirectToRoute('nova_venda');
    }

    #[Route('/venda/{id}/editar', name: 'editar_venda')]
    public function editarVenda(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $query = $em->createQuery('SELECT v FROM App\Entity\Venda v WHERE v.id = :id')
            ->setParameter('id', $id);
        $venda = $query->getOneOrNullResult();
        if (!$venda) {
            throw $this->createNotFoundException('Venda não encontrada.');
        }

        if ($request->isMethod('POST')) {
            $quantidade = $request->request->get('quantidade');

            $valorTotal = $venda['produto']['valor'] * $quantidade;
            $em->createQuery('UPDATE App\Entity\Venda v SET v.quantidade = :quantidade, v.valor_total = :valor_total WHERE v.id = :id')
                ->setParameters([
                    'quantidade' => $quantidade,
                    'valor_total' => $valorTotal,
                    'id' => $id
                ])
                ->execute();

            $em->createQuery('UPDATE App\Entity\Produto p SET p.quantidade_disponivel = :quantidade_disponivel WHERE p.id = :id')
                ->setParameters([
                    'quantidade_disponivel' => $venda['produto']['quantidade_disponivel'] + $venda['quantidade'] - $quantidade,
                    'id' => $venda['produto']['id']
                ])
                ->execute();


            return $this->redirectToRoute('vendas_realizadas');
        }


        return $this->render('venda/editar_venda.html.twig', [
            'venda' => $venda,
        ]);
    }

    #[Route('/venda', name: 'vendas_realizadas')]
    public function vendasRealizadas(EntityManagerInterface $em): Response
    {

        $vendas = $em->createQuery('SELECT v FROM App\Entity\Venda v')->getResult();


        return $this->render('venda/vendas_realizadas.html.twig', [
            'vendas' => $vendas,
        ]);
    }
}
