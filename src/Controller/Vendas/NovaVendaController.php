<?php

namespace App\Controller\Vendas;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NovaVendaController extends AbstractController
{
    #[Route('/venda/nova', name: 'nova_venda')]
    public function novaVenda(EntityManagerInterface $em): Response
    {

        $categorias = $em->createQuery('SELECT c.id, c.nome FROM App\Entity\Categoria c')->getResult();
        $produtos = $em->createQuery('SELECT p.id, p.nome, p.descricao, p.quantidade_disponivel, p.valor FROM App\Entity\Produto p')->getResult();


        return $this->render('app/vendas/nova_venda.html.twig', [
            'categorias' => $categorias,
            'produtos' => $produtos,
            'headTitle' => '- App',
            'active' => 'inicio',
        ]);
    }

    #[Route('/venda/confirmar', name: 'nova_venda_confirmar', methods: ['POST'])]
    public function confirmarVenda(Request $request, EntityManagerInterface $em): Response
    {

        $produtoId = $request->request->get('produto');
        $quantidade = $request->request->get('quantidade');


        $produto = $em->createQuery('SELECT p FROM App\Entity\Produto p WHERE p.id = :id')
            ->setParameter('id', $produtoId)
            ->getOneOrNullResult();


        if ($produto && $produto['quantidade_disponivel'] >= $quantidade) {
            $valorTotal = $produto['valor'] * $quantidade;


            $em->getConnection()->executeStatement(
                'INSERT INTO venda (produto_id, quantidade, valor_total, data_venda) VALUES (:produto_id, :quantidade, :valor_total, :data_venda)',
                [
                    'produto_id' => $produtoId,
                    'quantidade' => $quantidade,
                    'valor_total' => $valorTotal,
                    'data_venda' => (new \DateTime())->format('Y-m-d H:i:s')
                ]
            );


            $em->getConnection()->executeStatement(
                'UPDATE produto SET quantidade_disponivel = :quantidade_disponivel WHERE id = :id',
                [
                    'quantidade_disponivel' => $produto['quantidade_disponivel'] - $quantidade,
                    'id' => $produtoId
                ]
            );


            return $this->redirectToRoute('vendas_realizadas');
        }


        return $this->redirectToRoute('nova_venda');
    }

    #[Route('/venda/{id}/editar', name: 'editar_venda')]
    public function editarVenda(int $id, Request $request, EntityManagerInterface $em): Response
    {

        $venda = $em->createQuery('SELECT v FROM App\Entity\Venda v WHERE v.id = :id')
            ->setParameter('id', $id)
            ->getOneOrNullResult();

        if (!$venda) {
            throw $this->createNotFoundException('Venda não encontrada.');
        }

        if ($request->isMethod('POST')) {

            $quantidade = $request->request->get('quantidade');
            $valorTotal = $venda['produto']['valor'] * $quantidade;


            $em->getConnection()->executeStatement(
                'UPDATE venda SET quantidade = :quantidade, valor_total = :valor_total WHERE id = :id',
                [
                    'quantidade' => $quantidade,
                    'valor_total' => $valorTotal,
                    'id' => $id
                ]
            );


            $em->getConnection()->executeStatement(
                'UPDATE produto SET quantidade_disponivel = :quantidade_disponivel WHERE id = :produto_id',
                [
                    'quantidade_disponivel' => $venda['produto']['quantidade_disponivel'] + $venda['quantidade'] - $quantidade,
                    'produto_id' => $venda['produto']['id']
                ]
            );


            return $this->redirectToRoute('vendas_realizadas');
        }


        return $this->render('venda/editar_venda.html.twig', [
            'venda' => $venda,
        ]);
    }

    #[Route('/venda', name: 'vendas_realizadas')]
    public function vendasRealizadas(EntityManagerInterface $em): Response
    {

        $vendas = $em->createQuery('SELECT v.id, v.quantidade, v.valor_total, v.data_venda, p.nome as produto_nome FROM App\Entity\Venda v JOIN v.produto p')->getResult();


        return $this->render('venda/vendas_realizadas.html.twig', [
            'vendas' => $vendas,
        ]);
    }
}
