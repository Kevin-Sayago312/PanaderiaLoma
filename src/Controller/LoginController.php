<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\UserRepository;

class LoginController extends AbstractController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // El controlador puede estar vacío, Symfony manejará la ruta de cierre de sesión automáticamente
    }

    #[Route('/registrar-usuario', name: 'registrar_usuario', methods: ['POST'])]
    public function registrarUsuario(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $email = $request->request->get('email');
        $nombre = $request->request->get('nombre');
        $password = $request->request->get('password');

        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            // Si el correo electrónico ya está en uso, mostrar un mensaje de error y redirigir de vuelta al formulario de registro
            $this->addFlash('error', 'El correo electrónico ya está en uso.');
            return $this->redirectToRoute('app_login');
        }

        // Define la ruta de la imagen por defecto
        $photoDefaultPath = 'assets/img/defecto.png';
        // Crear una nueva instancia de User y establecer los valores
        $usuario = new User();
        $usuario->setEmail($email);
        $usuario->setNombre($nombre);
        $usuario->setPhoto($photoDefaultPath);

        // Hash de la contraseña
        $hashedPassword = $passwordHasher->hashPassword($usuario, $password);
        $usuario->setPassword($hashedPassword);
        
        // Asignar el rol por defecto
        $usuario->setRoles(['ROLE_USER']);

        // Guardar el usuario en la base de datos
        $entityManager->persist($usuario);
        $entityManager->flush();

        // Redirigir al usuario a la página de inicio después de un registro exitoso
        return $this->redirectToRoute('app_login');
    }
}