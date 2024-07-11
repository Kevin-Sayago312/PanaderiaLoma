<?php
namespace App\Controller;

namespace App\Controller;

use App\Entity\Productos;
use App\Entity\Categorias;
use App\Repository\ProductosRepository;
use App\Repository\CategoriasRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AddCategoriasController extends AbstractController
{
    private $productosRepository;
    private $categoriasRepository;

    public function __construct(ProductosRepository $productosRepository, CategoriasRepository $categoriasRepository)
    {
        $this->productosRepository = $productosRepository;
        $this->categoriasRepository = $categoriasRepository;
    }

    #[Route('/add_categorias', name: 'app_add_categorias')]
    public function index(): Response
    {
        $productos = $this->productosRepository->findAll();
        $categorias = $this->categoriasRepository->findAll();

        return $this->render('add_categorias/index.html.twig', [
            'productos' => $productos,
            'categorias' => $categorias,
        ]);
    }

    #[Route('/registrar_producto', name: 'app_registrar_producto', methods: ['POST'])]
    public function registrarProducto(Request $request, EntityManagerInterface $entityManager): Response
    {
        $nombre = $request->request->get('nombre');
        $descripcion = $request->request->get('descripcion');
        $categoriaId = $request->request->get('categoria');
        $precio = $request->request->get('precio');
        $existencia = $request->request->get('existencia');
        
        /** @var UploadedFile $photo */
        $photo = $request->files->get('photo');

        $existingProduct = $entityManager->getRepository(Productos::class)->findOneBy(['nombre' => $nombre]);
        if ($existingProduct) {
            $this->addFlash('error', 'El producto ya existe.');
            return $this->redirectToRoute('app_add_categorias');
        }

        $categoria = $entityManager->getRepository(Categorias::class)->find($categoriaId);
        if (!$categoria) {
            $this->addFlash('error', 'Categoría no válida.');
            return $this->redirectToRoute('app_add_categorias');
        }

        $productos = new Productos();
        $productos->setNombre($nombre);
        $productos->setDescripcion($descripcion);
        $productos->setCategorias($categoria);
        $productos->setPrecio($precio);
        $productos->setExistencia($existencia);

        if ($photo) {
            $newFilename = uniqid() . '.' . $photo->guessExtension();

            try {
                $photo->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('error', 'Error al subir la imagen.');
                return $this->redirectToRoute('app_add_categorias');
            }

            $productos->setPhoto($newFilename);
        }

        $entityManager->persist($productos);
        $entityManager->flush();

        return $this->redirectToRoute('app_add_categorias');
    }

#[Route('/delete_producto/{id}', name: 'app_producto_delete', methods: ['POST'])]
public function delete(Request $request, int $id, EntityManagerInterface $entityManager): Response
{
    $producto = $entityManager->getRepository(Productos::class)->find($id);

    if (!$producto) {
        throw $this->createNotFoundException('Producto no encontrado.');
    }

    if ($this->isCsrfTokenValid('delete' . $id, $request->request->get('_token'))) {
        $entityManager->remove($producto);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_add_categorias', [], Response::HTTP_SEE_OTHER);
}

#[Route('/editar_producto/{id}', name: 'app_producto_edit', methods: ['POST'])]
public function editarProducto(Request $request, EntityManagerInterface $entityManager, int $id): Response
{
    $nombre = $request->request->get('nombre');
    $descripcion = $request->request->get('descripcion');
    $categoriaId = $request->request->get('categoria');
    $precio = $request->request->get('precio');
    $existencia = $request->request->get('existencia');

    /** @var UploadedFile $photo */
    $photo = $request->files->get('photo');

    $producto = $entityManager->getRepository(Productos::class)->find($id);

    if (!$producto) {
        $this->addFlash('error', 'Producto no encontrado.');
        return $this->redirectToRoute('app_add_categorias');
    }

    $producto->setNombre($nombre);
    $producto->setDescripcion($descripcion);
    $producto->setPrecio($precio);
    $producto->setExistencia($existencia);

    if ($photo) {
        $newFilename = uniqid() . '.' . $photo->guessExtension();

        try {
            $photo->move(
                $this->getParameter('uploads_directory'),
                $newFilename
            );
        } catch (FileException $e) {
            $this->addFlash('error', 'Error al subir la imagen.');
            return $this->redirectToRoute('app_add_categorias');
        }

        $producto->setPhoto($newFilename);
    }

    $categoria = $entityManager->getRepository(Categorias::class)->find($categoriaId);
    if (!$categoria) {
        $this->addFlash('error', 'Categoría no válida.');
        return $this->redirectToRoute('app_add_categorias');
    }
    $producto->setCategorias($categoria);

    $entityManager->flush();

    return $this->redirectToRoute('app_add_categorias');
}

}
