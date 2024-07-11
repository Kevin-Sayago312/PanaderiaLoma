<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminPerfilController extends AbstractController
{
    #[Route('/admin_perfil', name: 'app_admin_perfil')]
    public function index(): Response
    {
        return $this->render('admin_perfil/admin_perfil.html.twig', [
            'controller_name' => 'AdminPerfilController',
        ]);
    }
}
