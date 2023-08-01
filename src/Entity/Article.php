<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $commentaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'article')]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'article')]
    private ?Langue $langue = null;

    #[ORM\ManyToOne(inversedBy: 'article')]
    private ?User $user = null;

    #[ORM\Column]
    private ?bool $is_published = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $ids_unread_notification = [];

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $event_at = null;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Galerie::class, cascade: ["persist", "remove"])]
    private Collection $galerie;


    public function __construct()
    {
        $date_now = new DateTimeImmutable();
        $this->is_published = false;
        $this->created_at = $date_now;
        $this->updated_at = $date_now;
        $this->galerie = new ArrayCollection();
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

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getLangue(): ?Langue
    {
        return $this->langue;
    }

    public function setLangue(?Langue $langue): self
    {
        $this->langue = $langue;

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

    public function isIsPublished(): ?bool
    {
        return $this->is_published;
    }

    public function setIsPublished(bool $is_published): self
    {
        $this->is_published = $is_published;

        return $this;
    }

    public function getIdsUnreadNotification(): array
    {
        return $this->ids_unread_notification;
    }

    public function setIdsUnreadNotification(array $ids_unread_notification): self
    {
        $this->ids_unread_notification = $ids_unread_notification;

        return $this;
    }

    public function getEventAt(): ?\DateTimeImmutable
    {
        return $this->event_at;
    }

    public function setEventAt(?\DateTimeImmutable $event_at): self
    {
        $this->event_at = $event_at;

        return $this;
    }

    /**
     * @return Collection<int, Galerie>
     */
    public function getGalerie(): Collection
    {
        return $this->galerie;
    }

    public function addGalerie(Galerie $galerie): self
    {
        if (!$this->galerie->contains($galerie)) {
            $this->galerie->add($galerie);
            $galerie->setArticle($this);
        }

        return $this;
    }

    public function removeGalerie(Galerie $galerie): self
    {
        if ($this->galerie->removeElement($galerie)) {
            // set the owning side to null (unless already changed)
            if ($galerie->getArticle() === $this) {
                $galerie->setArticle(null);
            }
        }
        return $this;
    }
}
