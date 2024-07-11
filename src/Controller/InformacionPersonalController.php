<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class InformacionPersonalController extends AbstractController
{

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/informacion_personal', name: 'app_informacion_personal')]
    public function index(): Response
    {
        // Obtén el usuario autenticado
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException('No user is logged in!');
        }

        return $this->render('informacion_personal/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/perfil', name: 'app_profile')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Obtén el usuario autenticado
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException('No user is logged in!');
        }

        if ($request->isMethod('POST')) {
            $nombre = $request->request->get('nombre');
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $photo = $request->files->get('photo');

            if ($nombre) {
                $user->setNombre($nombre);
            }

            if ($email) {
                $user->setEmail($email);
            }

            if ($password) {
                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);
            }

            if ($photo) {
                $newFilename = uniqid().'.'.$photo->guessExtension();
                $photo->move(
                    $this->getParameter('photos_directory'),
                    $newFilename
                );
                $user->setPhoto($newFilename);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Perfil actualizado con éxito.');

            return $this->redirectToRoute('app_info_personal');
        }

        return $this->render('informacion_personal/index.html.twig', [
            'user' => $user,
        ]);
    }
}
