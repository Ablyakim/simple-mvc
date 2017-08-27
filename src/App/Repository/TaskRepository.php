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
            ->from('task');

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
}