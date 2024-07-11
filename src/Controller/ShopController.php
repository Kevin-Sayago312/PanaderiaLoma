<?php

namespace App\Controller;

use App\Repository\CategoriasRepository;
use App\Repository\ProductosRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    private $productosRepository;
    private $categoriasRepository;

    public function __construct(ProductosRepository $productosRepository, CategoriasRepository $categoriasRepository)
    {
        $this->productosRepository = $productosRepository;
        $this->categoriasRepository = $categoriasRepository;
    }

    #[Route('/shop', name: 'app_shop')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        // Obtener la consulta de todos los productos
        $productosQuery = $this->productosRepository->findAllProductosQuery();
        
        // Paginar los resultados
        $productos = $paginator->paginate(
            $productosQuery, // Query a paginar
            $request->query->getInt('page', 1), // Número de página
            3 // Cantidad de elementos por página
        );
        
        

        $categorias = $this->categoriasRepository->findAll();
        
        return $this->render('shop/shop.html.twig', [
            'productos' => $productos,
            'categorias' => $categorias,
        ]);
    }
}


