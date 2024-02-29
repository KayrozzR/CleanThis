<?php

namespace App\Entity;

use App\Repository\OperationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OperationRepository::class)]
class Operation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $facture = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(nullable: true)]
    private ?float $note = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_debut = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $date_fin = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $image_resultat = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $reclamation = null;

    #[ORM\Column]
    private ?bool $status_paiement = null;

    #[ORM\Column]
    private ?bool $status_operation = null;

    #[ORM\ManyToMany(targetEntity: Devis::class, mappedBy: 'Operation')]
    private Collection $devis;

    #[ORM\Column(length: 255)]
    private ?string $address_intervention = null;

    public function __construct()
    {
        $this->devis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFacture(): ?string
    {
        return $this->facture;
    }

    public function setFacture(?string $facture): static
    {
        $this->facture = $facture;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(?float $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeImmutable
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeImmutable $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeImmutable
    {
        return $this->date_fin;
    }

    public function setDateFin(?\DateTimeImmutable $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getImageResultat(): ?string
    {
        return $this->image_resultat;
    }

    public function setImageResultat(?string $image_resultat): static
    {
        $this->image_resultat = $image_resultat;

        return $this;
    }

    public function getReclamation(): ?string
    {
        return $this->reclamation;
    }

    public function setReclamation(?string $reclamation): static
    {
        $this->reclamation = $reclamation;

        return $this;
    }

    public function isStatusPaiement(): ?bool
    {
        return $this->status_paiement;
    }

    public function setStatusPaiement(bool $status_paiement): static
    {
        $this->status_paiement = $status_paiement;

        return $this;
    }

    public function isStatusOperation(): ?bool
    {
        return $this->status_operation;
    }

    public function setStatusOperation(bool $status_operation): static
    {
        $this->status_operation = $status_operation;

        return $this;
    }

    /**
     * @return Collection<int, Devis>
     */
    public function getDevis(): Collection
    {
        return $this->devis;
    }

    public function addDevi(Devis $devi): static
    {
        if (!$this->devis->contains($devi)) {
            $this->devis->add($devi);
            $devi->addOperation($this);
        }

        return $this;
    }

    public function removeDevi(Devis $devi): static
    {
        if ($this->devis->removeElement($devi)) {
            $devi->removeOperation($this);
        }

        return $this;
    }

    public function getAddressIntervention(): ?string
    {
        return $this->address_intervention;
    }

    public function setAddressIntervention(string $address_intervention): static
    {
        $this->address_intervention = $address_intervention;

        return $this;
    }
}
