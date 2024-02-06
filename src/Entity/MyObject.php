<?php

namespace App\Entity;

use App\Repository\MyObjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MyObjectRepository::class)]
class MyObject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 2083)]
    private ?string $image = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $condition = null;

    #[ORM\ManyToMany(targetEntity: MyCollection::class, mappedBy: 'myobjects')]
    private Collection $myCollections;

    #[ORM\OneToMany(mappedBy: 'myObject', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'objects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    public function __construct()
    {
        $this->myCollections = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $Name): static
    {
        $this->name = $Name;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->condition;
    }

    public function setEtat(string $etat): static
    {
        $this->condition = $etat;

        return $this;
    }

    /**
     * @return Collection<int, MyCollection>
     */
    public function getMyCollections(): Collection
    {
        return $this->myCollections;
    }

    public function addMyCollection(MyCollection $myCollection): static
    {
        if (!$this->myCollections->contains($myCollection)) {
            $this->myCollections->add($myCollection);
            $myCollection->addMyobject($this);
        }

        return $this;
    }

    public function removeMyCollection(MyCollection $myCollection): static
    {
        if ($this->myCollections->removeElement($myCollection)) {
            $myCollection->removeMyobject($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setMyObject($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getMyObject() === $this) {
                $comment->setMyObject(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}
