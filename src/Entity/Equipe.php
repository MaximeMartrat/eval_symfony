<?php

namespace App\Entity;

use App\Repository\EquipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipeRepository::class)]
class Equipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\Column]
    private ?int $budget = null;

    #[ORM\Column]
    private ?int $renommee = null;

    #[ORM\OneToMany(mappedBy: 'joueur_equipe', targetEntity: Joueur::class)]
    private Collection $joueurs;

    #[ORM\OneToMany(mappedBy: 'manager_equipe', targetEntity: Manager::class)]
    private Collection $managers;

    public function __construct()
    {
        $this->joueurs = new ArrayCollection();
        $this->managers = new ArrayCollection();
    }

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

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getBudget(): ?int
    {
        return $this->budget;
    }

    public function setBudget(): self
    {
        $joueurs = $this->getJoueurs();
        $managers = $this->getManagers();
        $sumSalaire = 0;

        if ($joueurs->count() > 0) {
            foreach ($joueurs as $joueur) {
            $sumSalaire += $joueur->getSalaire();
            }
        }
        if ($managers->count() > 0) {
            foreach ($managers as $manager) {
                $sumSalaire += $manager->getSalaire();
                }
        }
        $this->budget = $sumSalaire;
    return $this;
    }

    public function getRenommee(): ?int
    {
        return $this->renommee;
    }

    public function setRenommee(): self
    {
        $renommeeMoyenne = 0;
        $joueurs = $this->getJoueurs();

        if ($joueurs->count() > 0) {
            $sumRenommee = 0;
            foreach ($joueurs as $joueur) {
            $sumRenommee += $joueur->getRenommee();
        }
        $renommeeMoyenne = $sumRenommee / $joueurs->count();
    }

    $this->renommee = $renommeeMoyenne;

    return $this;
    }

    /**
     * @return Collection<int, Joueur>
     */
    public function getJoueurs(): Collection
    {
        return $this->joueurs;
    }

    public function addJoueur(Joueur $joueur): self
    {
        if (!$this->joueurs->contains($joueur)) {
            $this->joueurs->add($joueur);
            $joueur->setJoueurEquipe($this);
        }

        return $this;
    }

    public function removeJoueur(Joueur $joueur): self
    {
        if ($this->joueurs->removeElement($joueur)) {
            // set the owning side to null (unless already changed)
            if ($joueur->getJoueurEquipe() === $this) {
                $joueur->setJoueurEquipe(null);
            }
        }

        return $this;
    }

    public function countJoueursByPosition($position)
    {
        $count = 0;
        foreach($this->joueurs as $joueur) {
            if($joueur->getPosition() === $position) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * @return Collection<int, Manager>
     */
    public function getManagers(): Collection
    {
        return $this->managers;
    }

    public function addManager(Manager $manager): self
    {
        if (!$this->managers->contains($manager)) {
            $this->managers->add($manager);
            $manager->setManagerEquipe($this);
        }

        return $this;
    }

    public function removeManager(Manager $manager): self
    {
        if ($this->managers->removeElement($manager)) {
            // set the owning side to null (unless already changed)
            if ($manager->getManagerEquipe() === $this) {
                $manager->setManagerEquipe(null);
            }
        }

        return $this;
    }

    public function countManagersByRole($role)
    {
        $count = 0;
        foreach($this->managers as $manager) {
            if($manager->getRole() === $role) {
                $count++;
            }
        }
        return $count;
    }
}
