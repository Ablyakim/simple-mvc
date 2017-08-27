<?php


namespace App\Repository;

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
     * @return array
     */
    public function getAll()
    {
        $qb = new QueryBuilder();

        $qb->select('*')
            ->from('task')
            ->setLimit(10);

        return $this->db->executeByQueryBuilder($qb)->fetchAll();
    }

    /**
     * @return QueryBuilder
     */
    public function getAllQueryBuilder()
    {
        $qb = new QueryBuilder();

        $qb->select('*')
            ->from('task');

        return $qb;
    }
}