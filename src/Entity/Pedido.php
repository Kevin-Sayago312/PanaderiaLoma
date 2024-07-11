<?php

// src/Entity/Pedido.php

namespace App\Entity;

use App\Repository\PedidoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PedidoRepository::class)]
class Pedido
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $fecha_pedido = null;

    #[ORM\Column]
    private ?int $total = null;
    

    #[ORM\ManyToOne(inversedBy: 'pedidos')]
    private ?User $user = null;


    #[ORM\Column(length: 255)]
    private ?string $pago = null;

    #[ORM\Column(length: 255)]
    private ?string $estadoEnvio = null;

    #[ORM\Column]
    private ?int $producto_id = null;

    #[ORM\Column(length: 255)]
    private ?string $estatus_pago = null;

    #[ORM\Column]
    private ?int $cantidad = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre_producto = null;

    #[ORM\Column(length: 255)]
    private ?string $descripcion_producto = null;

    // Getters y Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaPedido(): ?\DateTimeInterface
    {
        return $this->fecha_pedido;
    }

    public function setFechaPedido(\DateTimeInterface $fecha_pedido): self
    {
        $this->fecha_pedido = $fecha_pedido;
        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }


    public function getPago(): ?string
    {
        return $this->pago;
    }

    public function setPago(string $pago): self
    {
        $this->pago = $pago;
        return $this;
    }

    public function getEstadoEnvio(): ?string
    {
        return $this->estadoEnvio;
    }

    public function setEstadoEnvio(string $estadoEnvio): self
    {
        $this->estadoEnvio = $estadoEnvio;
        return $this;
    }

    public function getProductoId(): ?int
    {
        return $this->producto_id;
    }

    public function setProductoId(int $producto_id): self
    {
        $this->producto_id = $producto_id;
        return $this;
    }

    public function getEstatusPago(): ?string
    {
        return $this->estatus_pago;
    }

    public function setEstatusPago(string $estatus_pago): self
    {
        $this->estatus_pago = $estatus_pago;
        return $this;
    }

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): static
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getNombreProducto(): ?string
    {
        return $this->nombre_producto;
    }

    public function setNombreProducto(string $nombre_producto): static
    {
        $this->nombre_producto = $nombre_producto;

        return $this;
    }

    public function getDescripcionProducto(): ?string
    {
        return $this->descripcion_producto;
    }

    public function setDescripcionProducto(string $descripcion_producto): static
    {
        $this->descripcion_producto = $descripcion_producto;

        return $this;
    }
}
