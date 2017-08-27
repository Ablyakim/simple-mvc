<?php


namespace App\Repository;

use App\Paginator\Paginator;
use Framework\Db\ConnectionProxy;
use Framework\Db\Query\QueryBuilder;

/**
 * Class TaskRepository
 */
class TaskRepository
{
    /**
     * @var array
     */
    protected $allowedFieldsToSave = [
        'username',
        'email',
        'status',
        'content'
    ];

    /**
     * @var string
     */
    protected $taskTableName = 'task';

    /**
     * @var ConnectionProxy
     */
    protected $db;

    /**
     * TaskRepository constructor.
     * @param ConnectionProxy $db
     */
    public function __construct(ConnectionProxy $db)
    {
        $this->db = $db;
    }

    /**
     * @return QueryBuilder
     */
    public function createFindAllQueryBuilder()
    {
        $qb = new QueryBuilder();

        $qb->select('*')
            ->from($this->taskTableName);

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
     * @return bool
     */
    public function save($taskData)
    {
        $qb = new QueryBuilder();

        $taskData['status'] = (int)(!empty($taskData['status']));

        if (!empty($taskData['id'])) {
            foreach ($this->allowedFieldsToSave as $field) {
                if (isset($taskData[$field])) {
                    //set values in this way to avoid SQL injection
                    $qb->set($field, ':' . $field)
                        ->setParameter($field, $taskData[$field]);
                }
            }

            $qb->where('id = :id')
                ->setParameter('id', $taskData['id']);

            $qb->update($this->taskTableName);
        } else {
            foreach ($this->allowedFieldsToSave as $field) {
                if (isset($taskData[$field])) {
                    //set values in this way to avoid SQL injection
                    $qb->setValue($field, ':' . $field)
                        ->setParameter($field, $taskData[$field]);
                }
            }

            $qb->insert($this->taskTableName);
        }

        $stmt = $this->db->prepare($qb->getSQL());

        return $stmt->execute($qb->getParameters());
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
            ->from($this->taskTableName)
            ->where('id = :id')
            ->setParameter('id', $id);

        return $this->db->executeByQueryBuilder($qb)->fetch();
    }
}