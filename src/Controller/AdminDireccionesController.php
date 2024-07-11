<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminDireccionesController extends AbstractController
{
    #[Route('/admin/direcciones', name: 'app_admin_direcciones')]
    public function index(): Response
    {
        return $this->render('admin_direcciones/admin_direcciones.html.twig', [
            'controller_name' => 'AdminDireccionesController',
        ]);
    }
}
