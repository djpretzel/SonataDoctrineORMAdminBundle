<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DoctrineORMAdminBundle\Tests\App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity */
class Book
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private string $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @ORM\ManyToOne(targetEntity="Author", inversedBy="books")
     */
    private ?Author $author;

    /**
     * @ORM\ManyToMany(targetEntity=Reader::class, cascade={"persist"})
     *
     * @var Collection<array-key, Reader>
     */
    private Collection $readers;

    /**
     * @ORM\ManyToMany(targetEntity="Category")
     *
     * @var Collection<array-key, Category>
     */
    private Collection $categories;

    public function __construct(
        string $id = '',
        string $name = '',
        ?Author $author = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->author = $author;
        $this->categories = new ArrayCollection();
        $this->readers = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(Author $author): void
    {
        $this->author = $author;
    }

    public function addCategory(Category $category): void
    {
        $this->categories->add($category);
    }

    public function removeCategory(Category $category): void
    {
        $this->categories->removeElement($category);
    }

    /**
     * @return Collection<array-key, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addReader(Reader $reader): void
    {
        $this->readers->add($reader);
    }

    /**
     * @return Collection<array-key, Reader>
     */
    public function getReaders(): Collection
    {
        return $this->readers;
    }
}
