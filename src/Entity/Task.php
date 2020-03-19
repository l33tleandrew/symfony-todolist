<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Task
 *
 * @ORM\Table(name="tasks")
 * @ORM\Entity
 */
class Task
{
    /**
     * @var int|null
     *
     * @SWG\Property(readOnly=true)
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @SWG\Property(description="The title of the task.", example="Fare la spesa")
     *
     * @Assert\NotBlank
     * @Assert\Length(min="1", max="20")
     * @Assert\Type("string")
     *
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var string
     *
     * @SWG\Property(description="The description of the task.", example="Acquistare la farina ed il latte")
     *
     * @Assert\NotBlank
     * @Assert\Length(min="1", max="255")
     * @Assert\Type("string")
     *
     * @ORM\Column(type="string")
     */
    private $description;

    /**
     * @var bool
     *
     * @SWG\Property(description="A flag to check if the task has been done.", example=false)
     *
     * @Assert\Type("bool")
     *
     * @ORM\Column(type="boolean")
     */
    private $done;

    /**
     * @return int|null
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Task
     */
    public function setTitle(string $title) : Task
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() : string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Task
     */
    public function setDescription(string $description) : Task
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDone() : bool
    {
        return $this->done;
    }

    /**
     * @param bool $done
     *
     * @return Task
     */
    public function setDone(bool $done) : Task
    {
        $this->done = $done;

        return $this;
    }
}
