<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        // Verificar si el usuario tiene el rol ROLE_ADMIN
        if (!$this->isGranted('ROLE_ADMIN')) {
            // Si el usuario no tiene el rol ROLE_ADMIN, redirigir al index
            return $this->redirectToRoute('app_index');
        }

        return $this->render('admin/admin.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
