<?php

namespace App\Controller;

use App\Entity\Task;
use App\Service\TaskService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class TaskController
 */
class TaskController
{
    /**
     * @var TaskService
     */
    private $taskService;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(TaskService $taskService, SerializerInterface $serializer)
    {
        $this->taskService = $taskService;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/task", name="task_create", methods={"POST"})
     * @SWG\Post(
     *     summary="Creates a Task resource.",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="task",
     *         in="body",
     *         type="json",
     *         description="Task object",
     *         @SWG\Schema(type="object", ref=@Model(type=Task::class))
     *     ),
     *     @SWG\Response(response=201, description="Task created", @Model(type=Task::class)),
     *     @SWG\Response(response=401, description="Invalid input")
     * )
     */
    public function create(Request $request)
    {
        /** @var Task $task */
        $task = $this->serializer->deserialize($request->getContent(), Task::class, 'json');

        if ($errors = $this->taskService->validate($task)) {
            return $this->createResponse(['errors' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->taskService->saveTask($task);

        return $this->createResponse($task, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/task/{id}", name="task_update", methods={"PUT"})
     * @SWG\Put(
     *     summary="Replaces a Task resource.",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(name="id", in="path", type="integer", description="The Task ID"),
     *     @SWG\Parameter(
     *         name="task",
     *         in="body",
     *         type="json",
     *         description="Task object",
     *         @SWG\Schema(type="object", ref=@Model(type=Task::class))
     *     ),
     *     @SWG\Response(response=200, description="Task updated", @Model(type=Task::class)),
     *     @SWG\Response(response=401, description="Invalid input"),
     *     @SWG\Response(response=404, description="Task not found")
     * )
     */
    public function update(Request $request, int $id)
    {
        $task = $this->taskService->fetch($id);

        if (!$task) {
            return $this->createResponse(null, JsonResponse::HTTP_NOT_FOUND);
        }

        /** @var Task $newTask */
        $newTask = $this->serializer->deserialize($request->getContent(), Task::class, 'json');

        if ($errors = $this->taskService->validate($newTask)) {
            return $this->createResponse(['errors' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->taskService->updateTask($task, $newTask);

        return $this->createResponse($task, JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/task/{id}", name="task_read", methods={"GET"})
     * @SWG\Get(
     *     summary="Retrieves a Task resource.",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(name="id", in="path", type="integer", description="The Task ID"),
     *     @SWG\Response(
     *         response=200,
     *         description="Task resource",
     *         @SWG\Schema(ref=@Model(type=Task::class))
     *     ),
     *     @SWG\Response(response=404, description="Task not found")
     * )
     */
    public function read(int $id)
    {
        $task = $this->taskService->fetch($id);

        if (!$task) {
            return $this->createResponse(null, JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->createResponse($task, JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/tasks/{id}", name="task_delete", methods={"DELETE"})
     * @SWG\Delete(
     *     summary="Removes a Task resource.",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(name="id", in="path", type="integer", description="The Task ID")),
     *     @SWG\Response(response=204, description="Task deleted"),
     *     @SWG\Response(response=404, description="Task not found")
     * )
     */
    public function delete(int $id)
    {
        $task = $this->taskService->fetch($id);

        if (!$task) {
            return $this->createResponse(null, JsonResponse::HTTP_NOT_FOUND);
        }

        $this->taskService->removeTask($task);

        return $this->createResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/tasks", name="task_read_all", methods={"GET"})
     * @SWG\Get(
     *     summary="Retrieves the collection of Task resources.",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=200,
     *         description="Task collection response",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=Task::class))
     *         )
     *     )
     * )
     */
    public function readAll()
    {
        $tasks = $this->taskService->fetchAll();

        return $this->createResponse($tasks, JsonResponse::HTTP_OK);
    }

    private function createResponse($data, $statusCode): JsonResponse
    {
        $data = $this->serializer->normalize($data);

        return JsonResponse::create($data, $statusCode);
    }
}
