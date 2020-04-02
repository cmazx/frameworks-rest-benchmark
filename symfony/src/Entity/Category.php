<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 *
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"api"})
     */
    private $id;
    /**
     * @ORM\Column(type="string",length=255)
     * @Groups({"api"})
     */
    private $title;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Todo", inversedBy="category")
     * @Groups({"internal"})
     */
    private $todos;

    /**
     * @return mixed
     */
    public function getTodos()
    {
        return $this->todos;
    }

    /**
     * @param mixed $todos
     */
    public function setTodos($todos): void
    {
        $this->todos = $todos;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }
}
