<?php

namespace App\Entity;

use App\Repository\MenuItemRepository;
use Doctrine\ORM\Mapping as ORM;

use App\EntityTraits\OrderableTrait;

/**
 * @ORM\Entity(repositoryClass=MenuItemRepository::class)
 */
class MenuItem
{
    use OrderableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\OneToOne(targetEntity=Page::class)
     */
    private $page;

    /**
     * @ORM\OneToOne(targetEntity=DownloadableFile::class)
     */
    private $downloadable_file;

    /**
     * @ORM\Column(type="integer")
     */
    private $order_val;

    /**
     * @ORM\ManyToOne(targetEntity=MenuItem::class)
     */
    private $parent;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getDownloadableFile(): ?DownloadableFile
    {
        return $this->downloadable_file;
    }

    public function setDownloadableFile(?DownloadableFile $downloadable_file): self
    {
        $this->downloadable_file = $downloadable_file;

        return $this;
    }

    public function getOrderVal(): ?int
    {
        return $this->order_val;
    }

    public function setOrderVal(int $order_val): self
    {
        $this->order_val = $order_val;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }
}
