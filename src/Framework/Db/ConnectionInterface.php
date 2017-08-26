<?php

namespace Framework\Db;

use Framework\Db\Query\QueryBuilder;

/**
 * Interface ConnectionInterface
 */
interface ConnectionInterface
{
    /**
     * Executes an query from Query builder
     *
     * @param QueryBuilder $queryBuilder
     *
     * @return ConnectionInterface
     *
     * @throws \LogicException
     */
    function executeByQueryBuilder(QueryBuilder $queryBuilder);

    /**
     * Executes an, optionally parametrized, SQL query.
     *
     * @param string $query
     * @param array $params
     * @param array $types
     *
     * @return ConnectionInterface
     *
     * @throws \LogicException
     */
    function executeQuery($query, array $params = array(), $types = array());
}