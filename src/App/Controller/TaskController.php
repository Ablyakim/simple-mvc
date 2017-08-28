<?php

namespace App\Controller;

use App\Model\Uploader;
use App\Repository\TaskRepository;
use Framework\Core\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TaskController
 */
class TaskController extends AbstractController
{
    /**
     * @return Response
     */
    public function createAction()
    {
        return $this->render('task/create.html.twig', [
            'successMessage' => $this->getSuccessMessage()
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function saveAction(Request $request)
    {
        $id = $request->get('id');

        if ($id) {
            $this->checkAccess();
        }

        $taskData = array_merge($request->request->all(), ['id' => $id]);

        /** @var TaskRepository $taskRepository */
        $taskRepository = $this->container->get('task_repository');

        if ($taskRepository->save($taskData)) {
            $this->getSession()->set('task/success-message', 'Task was successfully created');

            if ($id) {
                return new RedirectResponse('/task/edit/' . $request->get('id'));
            } else {
                return new RedirectResponse('/task/create');
            }
        }
        if ($id) {
            return $this->render('task/edit.html.twig', [
                'task' => $taskData,
                'errorMessage' => 'Something went wrong'
            ]);
        } else {
            return $this->render('task/create.html.twig', [
                'task' => $taskData,
                'errorMessage' => 'Something went wrong'
            ]);
        }
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request)
    {
        $this->checkAccess();

        /** @var TaskRepository $taskRepository */
        $taskRepository = $this->container->get('task_repository');

        return $this->render('task/edit.html.twig', [
            'successMessage' => $this->getSuccessMessage(),
            'task' => $taskRepository->loadById($request->get('id'))
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function previewAction(Request $request)
    {
        $taskData = $request->request->all();

        if ($request->files->all()) {
            /** @var Uploader $uploader */
            $uploader = $this->container->get('uploader');
            $image = $uploader->upload($request->files->get('file'), true);

            if (!empty($image)) {
                $taskData['image'] = $uploader->getWebUrlOfImage($image);
            }
        }

        return $this->render('task/view.html.twig', ['task' => $taskData]);
    }

    /**
     * @return mixed
     */
    protected function getSuccessMessage()
    {
        //Can use flash messages
        $message = $this->getSession()->get('task/success-message');
        $this->getSession()->remove('task/success-message');

        return $message;
    }
}