<?php

namespace App\Repository;

use App\Paginator\Paginator;
use Framework\Db\Query\QueryBuilder;

/**
 * Class TaskRepository
 */
class TaskRepository extends AbstractRepository
{
    /**
     * @var array
     */
    protected $allowedFieldsToSave = [
        'username',
        'email',
        'status',
        'content',
        'image_id'
    ];

    /**
     * @return QueryBuilder
     */
    public function createFindAllQueryBuilder()
    {
        $qb = new QueryBuilder();

        $qb->select('*')
            ->from($this->getTableName());

        return $qb;
    }

    /**
     * @param $page
     * @param array $sortData
     *
     * @return Paginator
     */
    public function getFilteredTasksPaginator($page, $sortData = [])
    {
        $qb = $this->createFindAllQueryBuilder();

        if (!empty($sortData)) {
            $qb->addOrderBy($sortData['field'], $sortData['order'] == 'DESC' ? 'DESC' : 'ASC');
        }

        $paginator = new Paginator($qb, $this->db);

        $paginator->setCountOnPage(3)
            ->setCurrentPage($page);

        return $paginator;
    }

    /**
     * @param $taskData
     *
     * @return bool|int
     */
    public function save($taskData)
    {
        $taskData['status'] = (int)(!empty($taskData['status']));

        $qb = new QueryBuilder();

        if (!empty($taskData['id'])) {

            $qb->where('id = :id')
                ->setParameter('id', $taskData['id']);

            return $this->updateData($qb, $taskData);
        } else {
            return $this->insertData($qb, $taskData);
        }
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function loadById($id)
    {
        $qb = new QueryBuilder();

        $qb->select('*')
            ->from($this->getTableName())
            ->where('id = :id')
            ->setParameter('id', $id);

        return $this->db->executeByQueryBuilder($qb)->fetch();
    }

    /**
     * @inheritDoc
     */
    public function getAllowedFieldsToSave()
    {
        return $this->allowedFieldsToSave;
    }

    /**
     * @inheritDoc
     */
    public function getTableName()
    {
        return 'task';
    }
}