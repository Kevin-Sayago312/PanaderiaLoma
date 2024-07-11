<?php
namespace App\Controller;

use App\Entity\Categorias;
use App\Repository\CategoriasRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class AddProductosController extends AbstractController
{
    private $categoriasRepository;

    public function __construct(CategoriasRepository $categoriasRepository)
    {
        $this->categoriasRepository = $categoriasRepository;
    }

    #[Route('/add_productos', name: 'app_add_productos')]
    public function index(): Response
    {
        $categorias = $this->categoriasRepository->findAll();

        return $this->render('add_productos/index.html.twig', [
            'categorias' => $categorias,
        ]);
    }

    #[Route('/registrar_categoria', name: 'app_registrar_categoria', methods: ['POST'])]
    public function registrarCategoria(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categoriaNombre = $request->request->get('categoria');

        $existingCategoria = $entityManager->getRepository(Categorias::class)->findOneBy(['categoria' => $categoriaNombre]);
        if ($existingCategoria) {
            $this->addFlash('error', 'La categoría ya existe.');
            return $this->redirectToRoute('app_add_productos');
        }

        $categoria = new Categorias();
        $categoria->setCategoria($categoriaNombre);

        $entityManager->persist($categoria);
        $entityManager->flush();

        return $this->redirectToRoute('app_add_productos');
    }

    #[Route('/eliminar_categoria/{id}', name: 'app_categoria_delete', methods: ['POST'])]
public function deleteCategoria(Request $request, Categorias $categoria, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete'.$categoria->getId(), $request->request->get('_token'))) {
        $entityManager->remove($categoria);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_add_productos', [], Response::HTTP_SEE_OTHER);
}
    #[Route('/editar_categoria/{id}', name: 'app_categoria_edit', methods: ['POST'])]
    public function editarCategoria(Request $request, EntityManagerInterface $entityManager, int $id): Response
{
    // Obtener el nuevo nombre de la categoría del formulario
    $nuevoNombre = $request->request->get('categoria');

    // Buscar la categoría existente por su ID
    $categoria = $entityManager->getRepository(Categorias::class)->find($id);

    // Si la categoría no existe, mostrar un mensaje de error y redireccionar
    if (!$categoria) {
        $this->addFlash('error', 'Categoría no encontrada.');
        return $this->redirectToRoute('app_add_productos');
    }

    // Establecer el nuevo nombre de la categoría
    $categoria->setCategoria($nuevoNombre);

    // Persistir los cambios en la base de datos
    $entityManager->flush();

    // Redireccionar a donde sea necesario después de editar la categoría
    return $this->redirectToRoute('app_add_productos');
}
}
