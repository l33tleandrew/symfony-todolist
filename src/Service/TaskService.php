<?php

namespace App\Service;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function validate(Task $task): array
    {
        $errors = [];
        $violations = $this->validator->validate($task);

        if (count($violations) > 0) {
            /** @var ConstraintViolationInterface $violation */
            foreach ($violations as $violation) {
                $errors[] = [$violation->getPropertyPath() => $violation->getMessage()];
            }
        }

        return $errors;
    }

    public function saveTask(Task $task)
    {
        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }

    public function updateTask(Task $existingTask, Task $newTask)
    {
        $existingTask->setTitle($newTask->getTitle());
        $existingTask->setDescription($newTask->getDescription());
        $existingTask->setDone($newTask->isDone());

        $this->entityManager->flush();
    }

    public function fetch($id): ?Task
    {
        return $this->entityManager->getRepository(Task::class)->find($id);
    }

    public function fetchAll(): array
    {
        return $this->entityManager->getRepository(Task::class)->findAll();
    }

    public function removeTask(Task $task)
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }
}
