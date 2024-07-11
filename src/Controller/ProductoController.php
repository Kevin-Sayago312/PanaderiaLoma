<?php

namespace App\Controller;

use App\Repository\CategoriasRepository;
use App\Repository\ProductosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductoController extends AbstractController
{
    private $productosRepository;
    private $categoriasRepository;

    public function __construct(ProductosRepository $productosRepository, CategoriasRepository $categoriasRepository)
    {
        $this->productosRepository = $productosRepository;
        $this->categoriasRepository = $categoriasRepository;
    }

    #[Route('/producto', name: 'app_producto')]
    public function index(): Response
    {
        $productos = $this->productosRepository->findAll();
        $categorias = $this->categoriasRepository->findAll();

        return $this->render('producto/producto.html.twig', [
            'productos' => $productos,
            'categorias' => $categorias,
        ]);
    }
}
