<?php

namespace App\Entity;

use App\Repository\JoueurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JoueurRepository::class)]
class Joueur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $position = null;

    #[ORM\Column]
    private ?int $vitesse = null;

    #[ORM\Column]
    private ?int $dribble = null;

    #[ORM\Column]
    private ?int $tir = null;

    #[ORM\Column]
    private ?int $renommee = null;

    #[ORM\Column]
    private ?int $salaire = null;

    #[ORM\Column(nullable: true)]
    private ?int $arret = null;

    #[ORM\ManyToOne(inversedBy: 'joueurs')]
    private ?Equipe $joueur_equipe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getVitesse(): ?int
    {
        return $this->vitesse;
    }

    public function setVitesse(int $vitesse): self
    {
        $this->vitesse = $vitesse;

        return $this;
    }

    public function getDribble(): ?int
    {
        return $this->dribble;
    }

    public function setDribble(int $dribble): self
    {
        $this->dribble = $dribble;

        return $this;
    }

    public function getTir(): ?int
    {
        return $this->tir;
    }

    public function setTir(int $tir): self
    {
        $this->tir = $tir;

        return $this;
    }

    public function getRenommee(): ?int
    {
        return $this->renommee;
    }

    public function setRenommee(int $renommee): self
    {
        $this->renommee = $renommee;

        return $this;
    }

    public function getSalaire(): ?int
    {
        return $this->salaire;
    }

    public function setSalaire(int $salaire): self
    {
        $this->salaire = $salaire;

        return $this;
    }

    public function getArret(): ?int
    {
        return $this->arret;
    }

    public function setArret(?int $arret): self
    {
        $this->arret = $arret;

        return $this;
    }

    public function getJoueurEquipe(): ?Equipe
    {
        return $this->joueur_equipe;
    }

    public function setJoueurEquipe(?Equipe $joueur_equipe): self
    {
        $this->joueur_equipe = $joueur_equipe;

        return $this;
    }
}
