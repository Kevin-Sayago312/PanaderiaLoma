<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CategoriasRepository;
use App\Repository\ProductosRepository;
use App\Repository\SorteoRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class IndexController extends AbstractController
{

    private $userRepository;
    private $productosRepository;
    private $categoriasRepository;
  

    public function __construct(UserRepository $userRepository, ProductosRepository $productosRepository, CategoriasRepository $categoriasRepository)
    {
        $this->userRepository = $userRepository;
        $this->productosRepository = $productosRepository;
        $this->categoriasRepository = $categoriasRepository;

    }

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        $users = $this->userRepository->findAll();
        $productos = $this->productosRepository->findAll();
        $categorias = $this->categoriasRepository->findAll();
        return $this->render('index/index.html.twig', [
            'users' => $users,
            'productos' => $productos,
            'categorias' => $categorias,
                
        ]);        
    }
    
}
