<?php

// src/Entity/Productos.php

namespace App\Entity;

use App\Repository\ProductosRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductosRepository::class)]
class Productos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(nullable: true)]
    private ?float $precio = null;

    #[ORM\Column(nullable: true)]
    private ?int $existencia = null;

    #[ORM\ManyToOne(inversedBy: 'productos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorias $categorias = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\OneToMany(targetEntity: Carrito::class, mappedBy: 'productos')]
    private Collection $carritoz;

    public function __construct()
    {
        $this->carritoz = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): static
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): static
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getPrecio(): ?float
    {
        return $this->precio;
    }

    public function setPrecio(?float $precio): static
    {
        $this->precio = $precio;
        return $this;
    }

    public function getExistencia(): ?int
    {
        return $this->existencia;
    }

    public function setExistencia(?int $existencia): static
    {
        $this->existencia = $existencia;
        return $this;
    }

    public function getCategorias(): ?Categorias
    {
        return $this->categorias;
    }

    public function setCategorias(?Categorias $categorias): static
    {
        $this->categorias = $categorias;
        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;
        return $this;
    }

    /**
     * @return Collection<int, Carrito>
     */
    public function getCarritoz(): Collection
    {
        return $this->carritoz;
    }

    public function addCarritoz(Carrito $carritoz): static
    {
        if (!$this->carritoz->contains($carritoz)) {
            $this->carritoz->add($carritoz);
            $carritoz->setProductos($this);
        }
        return $this;
    }

    public function removeCarritoz(Carrito $carritoz): static
    {
        if ($this->carritoz->removeElement($carritoz)) {
            // set the owning side to null (unless already changed)
            if ($carritoz->getProductos() === $this) {
                $carritoz->setProductos(null);
            }
        }
        return $this;
    }
}
