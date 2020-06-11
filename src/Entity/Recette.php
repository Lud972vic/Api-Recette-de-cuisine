<?php

namespace App\Entity;

use App\Repository\RecetteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RecetteRepository::class)
 */
class Recette
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $soustitre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ingredients;

    /**
     * @ORM\OneToMany(targetEntity=Condiment::class, mappedBy="recette", orphanRemoval=true)
     */
    private $condiments;

    public function __construct()
    {
        $this->condiments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getSoustitre(): ?string
    {
        return $this->soustitre;
    }

    public function setSoustitre(?string $soustitre): self
    {
        $this->soustitre = $soustitre;

        return $this;
    }

    public function getIngredients(): ?string
    {
        return $this->ingredients;
    }

    public function setIngredients(string $ingredients): self
    {
        $this->ingredients = $ingredients;

        return $this;
    }

    /*La fonction toArray() convertit notre objet en tableau afin que nous puissions l'afficher dans notre JsonResponse*/
    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'titre' => $this->getTitre(),
            'soustitre' => $this->getSoustitre(),
            'ingredients' => $this->getIngredients()
        ];
    }

    /**
     * @return Collection|Condiment[]
     */
    public function getCondiments(): Collection
    {
        return $this->condiments;
    }

    public function addCondiment(Condiment $condiment): self
    {
        if (!$this->condiments->contains($condiment)) {
            $this->condiments[] = $condiment;
            $condiment->setRecette($this);
        }

        return $this;
    }

    public function removeCondiment(Condiment $condiment): self
    {
        if ($this->condiments->contains($condiment)) {
            $this->condiments->removeElement($condiment);
            // set the owning side to null (unless already changed)
            if ($condiment->getRecette() === $this) {
                $condiment->setRecette(null);
            }
        }

        return $this;
    }
}
