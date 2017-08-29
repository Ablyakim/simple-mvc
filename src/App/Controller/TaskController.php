<?php

namespace App\Controller;

use App\Model\Uploader;
use App\Repository\ImageRepository;
use App\Repository\TaskRepository;
use App\Validator\TaskValidator;
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

        /** @var TaskValidator $validator */
        $validator = $this->container->get('task_validator');

        $taskData = array_merge($request->request->all(), ['id' => $id]);

        $errors = $validator->validate($taskData);

        if (empty($errors)) {
            /** @var TaskRepository $taskRepository */
            $taskRepository = $this->container->get('task_repository');

            $image = $this->uploadFileIfExist($request);

            if ($image) {
                /** @var ImageRepository $imageRepository */
                $imageRepository = $this->container->get('image_repository');
                $taskData['image_id'] = $imageRepository->save($image);
            }

            if ($taskRepository->save($taskData)) {
                $this->getSession()->set('task/success-message', 'Task was successfully created/update');

                if ($id) {
                    return new RedirectResponse('/task/edit/' . $request->get('id'));
                } else {
                    return new RedirectResponse('/task/create');
                }
            }

            $errors[] = 'Something went wrong';
        }

        $template = $id ? 'task/edit.html.twig' : 'task/create.html.twig';

        return $this->render($template, [
            'task' => $taskData,
            'errorMessage' => join($errors, ', ')
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request)
    {
        /** @var TaskRepository $taskRepository */
        $taskRepository = $this->container->get('task_repository');
        $taskData = $taskRepository->loadById($request->get('id'));

        if (!empty($taskData['image_id'])) {
            /** @var ImageRepository $imageRepository */
            $imageRepository = $this->container->get('image_repository');
            /** @var Uploader $uploader */
            $uploader = $this->container->get('uploader');

            $taskData['image'] = $uploader->getWebUrlOfImage(
                $imageRepository->loadById($taskData['image_id'])
            );
        }

        return $this->render('task/edit.html.twig', [
            'successMessage' => $this->getSuccessMessage(),
            'task' => $taskData
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
        $image = $this->uploadFileIfExist($request, true);

        if (!empty($image)) {
            $taskData['image'] = $this->container->get('uploader')->getWebUrlOfImage($image);
        }

        return $this->render('task/view.html.twig', ['task' => $taskData]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function doneAction(Request $request)
    {
        $this->checkAccess();

        /** @var TaskRepository $taskRepository */
        $taskRepository = $this->container->get('task_repository');

        $data = [
            'id' => $request->get('id'),
            'status' => 1
        ];

        $taskRepository->save($data);

        return new Response('Ok');
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

    /**
     * @param Request $request
     * @param bool $isTmpFile
     *
     * @return array|false
     */
    protected function uploadFileIfExist(Request $request, $isTmpFile = false)
    {
        if (!$request->files->get('file')) {
            return false;
        }

        /** @var Uploader $uploader */
        $uploader = $this->container->get('uploader');

        return $uploader->upload($request->files->get('file'), $isTmpFile);
    }
}