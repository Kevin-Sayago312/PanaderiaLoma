<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(): Response
    {
        return $this->render('register/register.html.twig', [
            'controller_name' => 'RegisterController',
        ]);
    }
}
