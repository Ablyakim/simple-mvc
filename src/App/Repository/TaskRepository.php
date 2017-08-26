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
            ->andWhere('id = :id')
            ->setParameter('id', 1)
            ->setLimit(10);

        var_dump($this->db->executeByQueryBuilder($qb)->fetchAll());
        exit;
        return $this->db->query('SELECT * FROM `task` LIMIT 50')
            ->fetchAll();
    }
}