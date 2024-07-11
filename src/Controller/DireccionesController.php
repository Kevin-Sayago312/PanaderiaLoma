<?php

namespace App\Controller;

use App\Entity\Direccion;
use App\Repository\DireccionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class DireccionesController extends AbstractController
{
    #[Route('/direcciones', name: 'app_direcciones', methods: ['GET'])]
    public function index(DireccionRepository $direccionRepository): Response
    {
        $user = $this->getUser();
        $direcciones = $direccionRepository->findBy(['user' => $user]);

        return $this->render('direcciones/index.html.twig', [
            'direcciones' => $direcciones,
        ]);
    }

    #[Route('/direcciones/crear', name: 'app_direcciones_crear', methods: ['POST'])]
    public function crear(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $security->getUser();
        $data = json_decode($request->getContent(), true);

        $direccion = new Direccion();
        $direccion->setNombre($data['nombre']);
        $direccion->setCalle($data['calle']);
        $direccion->setCiudad($data['ciudad']);
        $direccion->setEstado($data['estado']);
        $direccion->setCp($data['cp']);
        $direccion->setPais($data['pais']);
        $direccion->setUser($user);

        $entityManager->persist($direccion);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Dirección creada correctamente', 'direccion' => $direccion], Response::HTTP_CREATED);
    }

    #[Route('/direcciones/{id}/editar', name: 'app_direcciones_editar', methods: ['POST'])]
    public function editar(int $id, Request $request, EntityManagerInterface $entityManager, DireccionRepository $direccionRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $direccion = $direccionRepository->find($id);

        if (!$direccion || $direccion->getUser() !== $this->getUser()) {
            return new JsonResponse(['message' => 'Dirección no encontrada o no autorizada'], Response::HTTP_NOT_FOUND);
        }

        $direccion->setNombre($data['nombre']);
        $direccion->setCalle($data['calle']);
        $direccion->setCiudad($data['ciudad']);
        $direccion->setEstado($data['estado']);
        $direccion->setCp($data['cp']);
        $direccion->setPais($data['pais']);

        $entityManager->flush();

        return new JsonResponse(['message' => 'Dirección editada correctamente']);
    }

    #[Route('/direcciones/{id}/eliminar', name: 'app_direcciones_eliminar', methods: ['POST'])]
    public function eliminar(int $id, EntityManagerInterface $entityManager, DireccionRepository $direccionRepository): Response
    {
        $direccion = $direccionRepository->find($id);

        if (!$direccion || $direccion->getUser() !== $this->getUser()) {
            return new JsonResponse(['message' => 'Dirección no encontrada o no autorizada'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($direccion);
        $entityManager->flush();

        return $this->redirectToRoute('app_direcciones', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/direcciones/{id}', name: 'app_direcciones_show', methods: ['GET'])]
    public function show(int $id, DireccionRepository $direccionRepository): Response
    {
        $direccion = $direccionRepository->find($id);

        if (!$direccion || $direccion->getUser() !== $this->getUser()) {
            return new JsonResponse(['message' => 'Dirección no encontrada o no autorizada'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($direccion);
    }
}
