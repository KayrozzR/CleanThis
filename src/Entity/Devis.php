<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\DevisRepository;
use App\Entity\Trait\CreatedAtTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: DevisRepository::class)]
class Devis
{
    use CreatedAtTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $url_devis = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $image_object = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $status = null;

    #[ORM\ManyToOne(inversedBy: 'devis')]
    private ?User $User = null;

    #[ORM\ManyToMany(targetEntity: TypeOperation::class, inversedBy: 'devis')]
    private Collection $Type_Operation;

    #[ORM\ManyToMany(targetEntity: Operation::class, inversedBy: 'devis')]
    private Collection $Operation;

    #[ORM\Column(length: 255)]
    private ?string $adresse_intervention = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $mail = null;

    #[ORM\Column(length: 12)]
    private ?string $tel = null;

    public function __construct()
    {
        $this->Type_Operation = new ArrayCollection();
        $this->Operation = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrlDevis(): ?string
    {
        return $this->url_devis;
    }

    public function setUrlDevis(?string $url_devis): static
    {
        $this->url_devis = $url_devis;

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

    public function getImageObject(): ?string
    {
        return $this->image_object;
    }

    public function setImageObject(?string $image_object): static
    {
        $this->image_object = $image_object;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

        return $this;
    }

    /**
     * @return Collection<int, TypeOperation>
     */
    public function getTypeOperation(): Collection
    {
        return $this->Type_Operation;
    }

    public function addTypeOperation(TypeOperation $typeOperation): static
    {
        if (!$this->Type_Operation->contains($typeOperation)) {
            $this->Type_Operation->add($typeOperation);
        }

        return $this;
    }

    public function removeTypeOperation(TypeOperation $typeOperation): static
    {
        $this->Type_Operation->removeElement($typeOperation);

        return $this;
    }

    /**
     * @return Collection<int, Operation>
     */
    public function getOperation(): Collection
    {
        return $this->Operation;
    }

    public function addOperation(Operation $operation): static
    {
        if (!$this->Operation->contains($operation)) {
            $this->Operation->add($operation);
        }

        return $this;
    }

    public function removeOperation(Operation $operation): static
    {
        $this->Operation->removeElement($operation);

        return $this;
    }

    public function getAdresseIntervention(): ?string
    {
        return $this->adresse_intervention;
    }

    public function setAdresseIntervention(string $adresse_intervention): static
    {
        $this->adresse_intervention = $adresse_intervention;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): static
    {
        $this->tel = $tel;

        return $this;
    }
}
