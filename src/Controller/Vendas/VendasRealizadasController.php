<?php

namespace App\Controller\Vendas;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class VendasRealizadasController extends AbstractController
{
    #[Route('/vendas/realizadas', name: 'app_vendas_realizadas')]
    public function listarVendas(Connection $connection): Response
    {

        $sql = "SELECT v.id, v.quantidade, v.valor_total, v.data_venda, p.nome AS produto_nome 
                FROM vendas v 
                JOIN produtos p ON v.produto_id = p.id";


        $vendas = $connection->fetchAllAssociative($sql);


        return $this->render('app/vendas/vendas_realizadas.html.twig', [
            'headTitle' => '- App',
            'active' => 'inicio',
            'vendas' => $vendas,
        ]);
    }

    /**
     * @Route("/vendas/{id}/editar", name="editar_venda", methods={"GET", "POST"})
     */
    public function editarVenda(int $id, Request $request, Connection $connection): Response
    {

        $venda = $connection->fetchAssociative('SELECT * FROM vendas WHERE id = ?', [$id]);

        if (!$venda) {
            throw $this->createNotFoundException('Venda não encontrada.');
        }


        if ($request->isMethod('POST')) {
            $quantidade = $request->request->get('quantidade');
            $valorTotal = $quantidade * $venda['valor_total'] / $venda['quantidade'];


            $connection->executeStatement('UPDATE vendas SET quantidade = ?, valor_total = ? WHERE id = ?', [
                $quantidade,
                $valorTotal,
                $id,
            ]);

            $produtoId = $venda['produto_id'];
            $produto = $connection->fetchAssociative('SELECT quantidade_disponivel FROM produtos WHERE id = ?', [$produtoId]);
            $novaQuantidadeDisponivel = $produto['quantidade_disponivel'] + ($venda['quantidade'] - $quantidade);

            $connection->executeStatement('UPDATE produtos SET quantidade_disponivel = ? WHERE id = ?', [
                $novaQuantidadeDisponivel,
                $produtoId,
            ]);

            return $this->redirectToRoute('vendas_realizadas');
        }

        return $this->render('venda/editar_venda.html.twig', [
            'venda' => $venda,
        ]);
    }
}
