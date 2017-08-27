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
     * @var array
     */
    protected $allowedFieldsToFilter = ['username', 'email', 'status'];

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        /** @var TaskRepository $taskRepository */
        $taskRepository = $this->container->get('task_repository');

        $sortData = [];

        if (in_array($request->get('sort-field'), $this->allowedFieldsToFilter)) {
            $sortData['field'] = $request->get('sort-field');
            $sortData['order'] = $request->get('sort-type');
        }

        $paginator = $taskRepository->getFilteredTasksPaginator($request->get('page', 1), $sortData);

        return $this->render('index/index.html.twig', [
            'paginator' => $paginator,
            'headers' => $this->buildHeaders($request),
            'paginatorPages' => $paginator->getPagesHtml('/', $request->query->all())
        ]);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function buildHeaders(Request $request)
    {
        $params = [];

        if ($request->get('page')) {
            $params['page'] = $request->get('page');
        }

        return [
            'username' => '/?' . http_build_query($this->enrichParamsWithField('username', $params, $request)),
            'email' => '/?' . http_build_query($this->enrichParamsWithField('email', $params, $request)),
            'status' => '/?' . http_build_query($this->enrichParamsWithField('status', $params, $request))
        ];
    }

    /**
     * @param $fieldName
     * @param $params
     * @param Request $request
     */
    private function enrichParamsWithField($fieldName, $params, Request $request)
    {
        $params['sort-field'] = $fieldName;

        if ($request->get('sort-field') == $fieldName) {
            $params['sort-type'] = $request->get('sort-type') == 'DESC' ? 'ASC' : 'DESC';
        }

        return $params;
    }
}