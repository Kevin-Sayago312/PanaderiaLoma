<?php

// src/Controller/ComprasController.php

namespace App\Controller;

use App\Entity\Pedido;
use App\Repository\PedidoRepository;
use App\Repository\DireccionRepository;
use App\Repository\ProductosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ComprasController extends AbstractController
{
    private $pedidoRepository;
    private $productosRepository;
    private $direccionRepository;
    private $security;

    public function __construct(PedidoRepository $pedidoRepository, ProductosRepository $productosRepository, DireccionRepository $direccionRepository, Security $security)
    {
        $this->pedidoRepository = $pedidoRepository;
        $this->productosRepository = $productosRepository;
        $this->direccionRepository = $direccionRepository;
        $this->security = $security;
    }

    #[Route('/compras', name: 'app_compras')]
    public function index(): Response
    {   
        $productos = $this->productosRepository->findAll();
        $direccion = $this->productosRepository->findAll();
        $user = $this->security->getUser();
        $pedidos = $this->pedidoRepository->findBy(['user' => $user]);

        return $this->render('compras/index.html.twig', [
            'pedidos' => $pedidos,
            'productos' => $productos,
            'direccion' => $direccion,
        ]);
    }
}
