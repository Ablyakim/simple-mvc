<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Framework\Core\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TaskController
 */
class TaskController extends AbstractController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadTasksAction(Request $request)
    {
        /** @var TaskRepository $taskRepository */
        $taskRepository = $this->container->get('task_repository');

        $responseData = [
            'current_page' => $request->get('page', 1),
            'data' => $taskRepository->getAll()
        ];

        return new JsonResponse($responseData);
    }
}