<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PerfilUsuarioController extends AbstractController
{
    #[Route('/perfil_usuario', name: 'app_perfil_usuario')]
    public function index(): Response
    {
        return $this->render('perfil_usuario/index.html.twig', [
            'controller_name' => 'PerfilUsuarioController',
        ]);
    }
}
