<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Overblog\GraphQLBundle\Annotation as GQL;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
#[GQL\Type(name: "Author")]
class Author
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[GQL\Field(type: "ID")]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups('Author')]
    #[GQL\Field]
    private ?string $lastName = null;

    #[ORM\Column(length: 50)]
    #[Groups('Author')]
    #[GQL\Field]
    private ?string $firstName = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Book::class, cascade: ['persist'], orphanRemoval: true)]
    #[MaxDepth(2)]
    #[Groups('Author_detail')]
    #[GQL\Field(type: "[Book]")]
    private Collection $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->setAuthor($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getAuthor() === $this) {
                $book->setAuthor(null);
            }
        }

        return $this;
    }
}
