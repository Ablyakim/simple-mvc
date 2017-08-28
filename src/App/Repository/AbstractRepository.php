<?php

namespace App\Repository;

use Framework\Db\ConnectionProxy;
use Framework\Db\Query\QueryBuilder;

abstract class AbstractRepository
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
    abstract function getAllowedFieldsToSave();

    /**
     * @return string
     */
    abstract function getTableName();

    /**
     * @param QueryBuilder $qb
     * @param $data
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function insertData(QueryBuilder $qb, $data)
    {
        foreach ($this->getAllowedFieldsToSave() as $field) {
            if (isset($data[$field])) {
                //set values in this way to avoid SQL injection
                $qb->setValue($field, ':' . $field)
                    ->setParameter($field, $data[$field]);
            }
        }

        $qb->insert($this->getTableName());

        $stmt = $this->db->prepare($qb->getSQL());

        if (!$stmt->execute($qb->getParameters())) {
            throw new \Exception('Something went wrong');
        }

        return $this->db->getConnection()->lastInsertId();
    }

    /**
     * @param QueryBuilder $qb
     * @param $data
     *
     * @return bool|int
     *
     * @throws \Exception
     */
    protected function updateData(QueryBuilder $qb, $data)
    {
        foreach ($this->getAllowedFieldsToSave() as $field) {
            if (isset($data[$field])) {
                //set values in this way to avoid SQL injection
                $qb->set($field, ':' . $field)
                    ->setParameter($field, $data[$field]);
            }
        }

        $qb->update($this->getTableName());

        $stmt = $this->db->prepare($qb->getSQL());

        if (!$stmt->execute($qb->getParameters())) {
            throw new \Exception('Something went wrong');
        }

        return !empty($data['id']) ? $data['id'] : true;
    }
}