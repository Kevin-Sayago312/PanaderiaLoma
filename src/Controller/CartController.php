<?php

namespace App\Controller;

use App\Entity\Carrito;
use App\Repository\CarritoRepository;
use App\Repository\CategoriasRepository;
use App\Repository\ProductosRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;


class CartController extends AbstractController
{
    
    private $userRepository;
    private $productosRepository;
    private $categoriasRepository;
    private $entityManager;
    private $carritoRepository;

    public function __construct(UserRepository $userRepository, ProductosRepository $productosRepository, CategoriasRepository $categoriasRepository, EntityManagerInterface $entityManager, CarritoRepository $carritoRepository)
    {
        $this->userRepository = $userRepository;
        $this->productosRepository = $productosRepository;
        $this->categoriasRepository = $categoriasRepository;
        $this->carritoRepository = $carritoRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/cart', name: 'app_cart')]
    public function index(SessionInterface $session): Response
    {
        $carritos = $session->get('carritos', []);
        $subtotal = 0;
        foreach ($carritos as $item) {
            $subtotal += $item['productos']->getPrecio() * $item['cantidad'];
        }

        
        $carritos = $this->carritoRepository->findAll();
        return $this->render('cart/cart.html.twig', [
            'carritos' => $carritos,
            'subtotal' => $subtotal,
            
        ]);
    }

    #[Route('/cart/add', name: 'app_cart_add', methods: ['POST'])]
    public function addToCart(Request $request, SessionInterface $session, ProductosRepository $productosRepository): Response
    {
        $productoId = $request->request->get('producto_id');
        $cantidad = $request->request->get('cantidad', 1);

        $producto = $productosRepository->find($productoId);
        if (!$producto) {
            throw $this->createNotFoundException('Producto no encontrado');
        }

        $carrito = $session->get('carrito', []);

        // Verificar si el producto ya está en el carrito
        if (isset($carrito[$productoId])) {
            // Si el producto ya está en el carrito, actualiza la cantidad
            $carrito[$productoId]['cantidad'] += $cantidad;
        } else {
            // Si el producto no está en el carrito, añádelo
            $carrito[$productoId] = [
                'productos' => $producto,
                'cantidad' => $cantidad
            ];
        }

        $session->set('carrito', $carrito);

        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedException('Debe iniciar sesión para añadir productos al carrito.');
        }

        // Calcula el subtotal
        $subtotal = $producto->getPrecio() * $cantidad;

        // Verificar si ya hay un registro en la base de datos para este producto y usuario
        $carritoEntity = $this->entityManager->getRepository(Carrito::class)->findOneBy([
            'user' => $user,
            'productos' => $producto
        ]);

        if ($carritoEntity) {
            // Si ya hay un registro en la base de datos para este producto y usuario, actualiza la cantidad y el subtotal
            $carritoEntity->setCantidad($carritoEntity->getCantidad() + $cantidad);
            $carritoEntity->setSubtotal($carritoEntity->getSubtotal() + $subtotal);
        } else {
            // Si no hay un registro en la base de datos, crea uno nuevo
            $carritoEntity = new Carrito();
            $carritoEntity->setUser($user);
            $carritoEntity->setProductos($producto); // Aquí cambió a setProductos
            $carritoEntity->setCantidad($cantidad);
            $carritoEntity->setSubtotal($subtotal);
        }

        $this->entityManager->persist($carritoEntity);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/{id}/eliminar', name: 'app_cart_eliminar', methods: ['POST'])]
    public function eliminarCarrito(int $id, EntityManagerInterface $entityManager, CarritoRepository $carritoRepository): Response
    {
        $carrito = $carritoRepository->find($id);
    
        if (!$carrito) {
            throw $this->createNotFoundException('Carrito no encontrado');
        }
    
        $entityManager->remove($carrito);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_cart', [], Response::HTTP_SEE_OTHER);
    }
    

    #[Route('/cart/update/{id}', name: 'app_cart_update', methods: ['POST'])]
    public function updateQuantity(int $id, Request $request, EntityManagerInterface $entityManager, CarritoRepository $carritoRepository): Response
    {
        $cantidad = $request->request->get('cantidad');
        $carrito = $carritoRepository->find($id);
    
        if (!$carrito) {
            throw $this->createNotFoundException('Carrito no encontrado');
        }
    
        $carrito->setCantidad($cantidad);
        
        // Recalcula el subtotal con descuento y lo actualiza en la entidad del carrito
        $precioProducto = $carrito->getProductos()->getPrecio();
        $subtotal = $precioProducto * $cantidad;
        
        // Aplica el descuento si corresponde
        if ($cantidad >= 3) {
            $descuento = 0.2 * $precioProducto * floor($cantidad / 3);
            $subtotal -= $descuento;
        }
    
        $carrito->setSubtotal($subtotal);
    
        $entityManager->flush();
    
        return $this->redirectToRoute('app_cart', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/cart/checkout', name: 'app_cart_checkout', methods: ['POST'])]
    public function checkout(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cartItems = json_decode($request->request->get('cart_items'), true);
        return $this->redirectToRoute('app_checkout', [], Response::HTTP_SEE_OTHER);
        // Aquí puedes realizar cualquier lógica necesaria para procesar el pago
        // Por ejemplo, puedes calcular el total y realizar el pago con un proveedor de servicios de pago externo
        
        // Después de procesar el pago, puedes guardar los detalles del pedido en la base de datos si es necesario

        // Finalmente, redirige a una página de confirmación de pedido o de agradecimiento
    }

}


