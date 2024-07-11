<?php

// src/Controller/CheckoutController.php

namespace App\Controller;

use App\Entity\Pedido;
use App\Repository\CarritoRepository;
use App\Repository\DireccionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;

class CheckoutController extends AbstractController
{
    private $carritoRepository;
    private $direccionRepository;
    private $security;
    private $entityManager;

    public function __construct(
        CarritoRepository $carritoRepository,
        DireccionRepository $direccionRepository,
        Security $security,
        EntityManagerInterface $entityManager
    ) {
        $this->carritoRepository = $carritoRepository;
        $this->direccionRepository = $direccionRepository;
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    #[Route('/checkout', name: 'app_checkout')]
    public function index(): Response
    {
        $user = $this->security->getUser();
        $carritos = $this->carritoRepository->findBy(['user' => $user]);
        $direcciones = $this->direccionRepository->findBy(['user' => $user]);

        return $this->render('checkout/checkout.html.twig', [
            'carritos' => $carritos,
            'direcciones' => $direcciones,
        ]);
    }

    #[Route('/handle_billing', name: 'handle_billing', methods: ['POST'])]
    public function handleBilling(Request $request): Response
    {
        return $this->redirectToRoute('app_checkout');
    }

    #[Route('/handle_shipping', name: 'handle_shipping', methods: ['POST'])]
    public function handleShipping(Request $request): Response
    {
        $direccionId = $request->request->get('direccion');
        $direccion = $this->direccionRepository->find($direccionId);

        if (!$direccion) {
            throw $this->createNotFoundException('La dirección seleccionada no existe.');
        }

        $session = $request->getSession();
        $session->set('direccion_id', $direccion->getId());

        return $this->redirectToRoute('app_checkout');
    }

    #[Route('/paypal/success', name: 'paypal_success')]
    public function paypalSuccess(Request $request): Response
    {
        $orderID = $request->query->get('orderID');
        $user = $this->security->getUser();
        $carritos = $this->carritoRepository->findBy(['user' => $user]);

        $session = $request->getSession();
        
        


        $paymentStatus = $this->checkPayPalPaymentStatus($orderID);

        $pedidos = [];
        foreach ($carritos as $carrito) {
            $pedido = new Pedido();
            $pedido->setFechaPedido(new \DateTime());
            $pedido->setTotal($carrito->getSubtotal());
            $pedido->setUser($user);
            $pedido->setPago('PayPal');
            $pedido->setEstadoEnvio('Pendiente');
            $pedido->setEstatusPago($paymentStatus ? 'Completado' : 'Pendiente');

            // Obtener el producto y la cantidad desde el carrito
            $producto = $carrito->getProductos();
            $cantidad = $carrito->getCantidad();

            // Guardar el producto y la cantidad en el pedido
            $pedido->setProductoId($producto->getId());
            $pedido->setCantidad($cantidad);
            $pedido->setNombreProducto($producto->getNombre());
            $pedido->setDescripcionProducto($producto->getDescripcion());

            $this->entityManager->persist($pedido);
            $pedidos[] = $pedido;
        }

        $this->entityManager->flush();

        foreach ($carritos as $carrito) {
            $this->entityManager->remove($carrito);
        }
        $this->entityManager->flush();

        return $this->render('checkout/success.html.twig', [
            'paymentStatus' => $paymentStatus,
            'pedidos' => $pedidos,
        ]);
    }

    private function checkPayPalPaymentStatus($orderID)
    {
        // Simulación de verificación de estado de pago de PayPal
        // Aquí deberías integrar la lógica para verificar con la API de PayPal
        // y devolver true si el pago fue exitoso, o false si falló.
        $paymentStatus = true; // Simulación de éxito de pago
        return $paymentStatus;
    }
}
