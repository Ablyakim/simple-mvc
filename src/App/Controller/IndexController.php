<?php


namespace App\Controller;

use App\Repository\TaskRepository;
use Framework\Core\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class IndexController
 */
class IndexController extends AbstractController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        /** @var TaskRepository $taskRepository */
        $taskRepository = $this->container->get('task_repository');
        echo '<pre>';
        print_r($taskRepository->getAll());
        exit;
        return $this->render('index/index.html.twig');
    }
}