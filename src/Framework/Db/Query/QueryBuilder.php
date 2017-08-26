<?php

namespace Framework\Db\Query;

use Framework\Db\Query\Expression\CompositeExpression;

/**
 * Class QueryBuilder
 */
class QueryBuilder
{
    /*
     * The query types.
     */
    const SELECT = 0;
    const DELETE = 1;
    const UPDATE = 2;
    const INSERT = 3;

    /*
     * The builder states.
     */
    const STATE_DIRTY = 0;
    const STATE_CLEAN = 1;

    /**
     * @var array The array of SQL parts collected.
     */
    private $sqlParts = array(
        'select' => array(),
        'from' => array(),
        'join' => array(),
        'set' => array(),
        'where' => null,
        'groupBy' => array(),
        'having' => null,
        'orderBy' => array(),
        'values' => array(),
    );

    /**
     * The complete SQL string for this query.
     *
     * @var string
     */
    private $sql;

    /**
     * The query parameters.
     *
     * @var array
     */
    private $params = array();

    /**
     * The parameter type map of this query.
     *
     * @var array
     */
    private $paramTypes = array();

    /**
     * The type of query this is. Can be select, update or delete.
     *
     * @var integer
     */
    private $type = self::SELECT;

    /**
     * The state of the query object. Can be dirty or clean.
     *
     * @var integer
     */
    private $state = self::STATE_CLEAN;

    /**
     * The index of the first result to retrieve.
     *
     * @var integer
     */
    private $offset = null;

    /**
     * The maximum number of results to retrieve.
     *
     * @var integer
     */
    private $limit = null;

    /**
     * The counter of bound parameters used with {@see bindValue).
     *
     * @var integer
     */
    private $boundCounter = 0;

    /**
     * Gets the state of this query builder instance.
     *
     * @return integer Either QueryBuilder::STATE_DIRTY or QueryBuilder::STATE_CLEAN.
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Gets the complete SQL string
     *
     * @return string The SQL query string.
     */
    public function getSQL()
    {
        if ($this->sql !== null && $this->state === self::STATE_CLEAN) {
            return $this->sql;
        }

        switch ($this->type) {
            case self::INSERT:
                $sql = $this->getInsertSQL();
                break;
            case self::DELETE:
                $sql = $this->getDeleteSQL();
                break;

            case self::UPDATE:
                $sql = $this->getUpdateSQL();
                break;

            case self::SELECT:
            default:
                $sql = $this->getSelectSQL();
                break;
        }

        $this->state = self::STATE_CLEAN;
        $this->sql = $sql;

        return $sql;
    }

    /**
     * Sets a query parameter for the query
     *
     * @param string|integer $key The parameter position or name.
     * @param mixed $value The parameter value.
     * @param string|null $type One of the PDO::PARAM_* constants.
     *
     * @return $this
     */
    public function setParameter($key, $value, $type = null)
    {
        if ($type !== null) {
            $this->paramTypes[$key] = $type;
        }

        $this->params[$key] = $value;

        return $this;
    }

    /**
     * Sets a collection of query parameters
     *
     * @param array $params
     * @param array $types
     *
     * @return $this
     */
    public function setParameters(array $params, array $types = array())
    {
        $this->paramTypes = $types;
        $this->params = $params;

        return $this;
    }

    /**
     * Gets all defined query parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->params;
    }

    /**
     * Gets a query parameter of the query.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function getParameter($key)
    {
        return isset($this->params[$key]) ? $this->params[$key] : null;
    }

    /**
     * Gets all defined query parameter types
     *
     * @return array
     */
    public function getParameterTypes()
    {
        return $this->paramTypes;
    }

    /**
     * Gets a (previously set) query parameter type of the query
     *
     * @param mixed $key The key (index or name) of the bound parameter type.
     *
     * @return mixed The value of the bound parameter type.
     */
    public function getParameterType($key)
    {
        return isset($this->paramTypes[$key]) ? $this->paramTypes[$key] : null;
    }

    /**
     * Sets offset to the query.
     *
     * @param integer $offset
     *
     * @return $this
     */
    public function setOffset($offset)
    {
        $this->state = self::STATE_DIRTY;
        $this->offset = $offset;

        return $this;
    }

    /**
     * Returns offset.
     *
     * @return integer
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Sets the limit to the query.
     *
     * @param integer $limit
     *
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->state = self::STATE_DIRTY;
        $this->limit = $limit;

        return $this;
    }

    /**
     * Returns limit.
     *
     * @return integer
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Either appends to or replaces a single, generic query part.
     *
     * The available parts are: 'select', 'from', 'set', 'where',
     * 'groupBy', 'having' and 'orderBy'.
     *
     * @param string $sqlPartName
     * @param string|array $sqlPart
     * @param boolean $append
     *
     * @return $this
     */
    public function add($sqlPartName, $sqlPart, $append = false)
    {
        $isArray = is_array($sqlPart);
        $isMultiple = is_array($this->sqlParts[$sqlPartName]);

        if ($isMultiple && !$isArray) {
            $sqlPart = array($sqlPart);
        }

        $this->state = self::STATE_DIRTY;

        if ($append) {
            if (
                $sqlPartName == "orderBy"
                || $sqlPartName == "groupBy"
                || $sqlPartName == "select"
                || $sqlPartName == "set"
            ) {
                foreach ($sqlPart as $part) {
                    $this->sqlParts[$sqlPartName][] = $part;
                }
            } elseif ($isArray && is_array($sqlPart[key($sqlPart)])) {
                $key = key($sqlPart);
                $this->sqlParts[$sqlPartName][$key][] = $sqlPart[$key];
            } elseif ($isMultiple) {
                $this->sqlParts[$sqlPartName][] = $sqlPart;
            } else {
                $this->sqlParts[$sqlPartName] = $sqlPart;
            }

            return $this;
        }

        $this->sqlParts[$sqlPartName] = $sqlPart;

        return $this;
    }

    /**
     * Specifies an item that is to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param mixed $select The selection expressions.
     *
     * @return $this
     */
    public function select($select = null)
    {
        $this->type = self::SELECT;

        if (empty($select)) {
            return $this;
        }

        $selects = is_array($select) ? $select : func_get_args();

        return $this->add('select', $selects, false);
    }

    /**
     * Adds an item that is to be returned in the query result.
     *
     * @param mixed $select
     *
     * @return $this
     */
    public function addSelect($select = null)
    {
        $this->type = self::SELECT;

        if (empty($select)) {
            return $this;
        }

        $selects = is_array($select) ? $select : func_get_args();

        return $this->add('select', $selects, true);
    }

    /**
     * Turns the query being built into a bulk delete query that ranges over
     * a certain table.
     *
     * @param string $delete
     * @param string $alias
     *
     * @return $this
     */
    public function delete($delete = null, $alias = null)
    {
        $this->type = self::DELETE;

        if (!$delete) {
            return $this;
        }

        return $this->add('from', array(
            'table' => $delete,
            'alias' => $alias
        ));
    }

    /**
     * Turns the query being built into a bulk update query that ranges over
     * a certain table
     *
     * @param string $update The table whose rows are subject to the update.
     * @param string $alias The table alias used in the constructed query.
     *
     * @return $this.
     */
    public function update($update = null, $alias = null)
    {
        $this->type = self::UPDATE;

        if (!$update) {
            return $this;
        }

        return $this->add('from', array(
            'table' => $update,
            'alias' => $alias
        ));
    }

    /**
     * Turns the query being built into an insert query that inserts into
     * a certain table
     *
     * @param string $insert
     *
     * @return QueryBuilder
     */
    public function insert($insert = null)
    {
        $this->type = self::INSERT;

        if (!$insert) {
            return $this;
        }

        return $this->add('from', array(
            'table' => $insert
        ));
    }

    /**
     * Add from statement to the query
     *
     * @param string $from
     * @param string|null $alias
     *
     * @return $this
     */
    public function from($from, $alias = null)
    {
        return $this->add('from', array(
            'table' => $from,
            'alias' => $alias
        ), true);
    }

    /**
     * Creates and adds a join to the query.
     *
     * @param string $fromAlias The alias that points to a from clause.
     * @param string $join The table name to join.
     * @param string $alias The alias of the join table.
     * @param string $condition The condition for the join.
     *
     * @return $this
     */
    public function innerJoin($fromAlias, $join, $alias, $condition = null)
    {
        return $this->add('join', array(
            $fromAlias => array(
                'joinType' => 'inner',
                'joinTable' => $join,
                'joinAlias' => $alias,
                'joinCondition' => $condition
            )
        ), true);
    }

    /**
     * Creates and adds a left join to the query.
     *
     * @param string $fromAlias
     * @param string $join
     * @param string $alias
     * @param string $condition
     *
     * @return $this This QueryBuilder instance.
     */
    public function leftJoin($fromAlias, $join, $alias, $condition = null)
    {
        return $this->add('join', array(
            $fromAlias => array(
                'joinType' => 'left',
                'joinTable' => $join,
                'joinAlias' => $alias,
                'joinCondition' => $condition
            )
        ), true);
    }

    /**
     * Creates and adds a right join to the query.
     *
     * @param string $fromAlias
     * @param string $join
     * @param string $alias
     * @param string $condition
     *
     * @return $this
     */
    public function rightJoin($fromAlias, $join, $alias, $condition = null)
    {
        return $this->add('join', array(
            $fromAlias => array(
                'joinType' => 'right',
                'joinTable' => $join,
                'joinAlias' => $alias,
                'joinCondition' => $condition
            )
        ), true);
    }

    /**
     * Sets a new value for a column in a bulk update query.
     *
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        return $this->add('set', $key . ' = ' . $value, true);
    }

    /**
     * Specifies one or more restrictions to the query result
     *
     * @param mixed $predicates
     *
     * @return $this
     */
    public function where($predicates)
    {
        if (!(func_num_args() == 1 && $predicates instanceof CompositeExpression)) {
            $predicates = new CompositeExpression(CompositeExpression::TYPE_AND, func_get_args());
        }

        return $this->add('where', $predicates);
    }

    /**
     * Adds one or more restrictions to the query results, forming a logical
     * conjunction with any previously specified restrictions.
     *
     * @param mixed $where The query restrictions.
     *
     * @return $this
     *
     * @see where()
     */
    public function andWhere($where)
    {
        $args = func_get_args();
        $where = $this->getQueryPart('where');

        if ($where instanceof CompositeExpression && $where->getType() === CompositeExpression::TYPE_AND) {
            $where->addMultiple($args);
        } else {
            array_unshift($args, $where);
            $where = new CompositeExpression(CompositeExpression::TYPE_AND, $args);
        }

        return $this->add('where', $where, true);
    }

    /**
     * Adds one or more restrictions to the query results, forming a logical
     * disjunction with any previously specified restrictions.
     *
     * @param mixed $where The WHERE statement.
     *
     * @return $this
     *
     * @see where()
     */
    public function orWhere($where)
    {
        $args = func_get_args();
        $where = $this->getQueryPart('where');

        if ($where instanceof CompositeExpression && $where->getType() === CompositeExpression::TYPE_OR) {
            $where->addMultiple($args);
        } else {
            array_unshift($args, $where);
            $where = new CompositeExpression(CompositeExpression::TYPE_OR, $args);
        }

        return $this->add('where', $where, true);
    }

    /**
     * Add groupBy to the query
     *
     * @param mixed $groupBy
     *
     * @return $this
     */
    public function groupBy($groupBy)
    {
        if (empty($groupBy)) {
            return $this;
        }

        $groupBy = is_array($groupBy) ? $groupBy : func_get_args();

        return $this->add('groupBy', $groupBy, false);
    }


    /**
     * Adds a grouping expression to the query.
     *
     * @param mixed $groupBy The grouping expression.
     *
     * @return $this
     */
    public function addGroupBy($groupBy)
    {
        if (empty($groupBy)) {
            return $this;
        }

        $groupBy = is_array($groupBy) ? $groupBy : func_get_args();

        return $this->add('groupBy', $groupBy, true);
    }

    /**
     * Sets a value for a column in an insert query.
     *
     * @param string $column
     * @param string $value
     *
     * @return QueryBuilder
     */
    public function setValue($column, $value)
    {
        $this->sqlParts['values'][$column] = $value;

        return $this;
    }

    /**
     * Specifies values for an insert query indexed by column names.
     * Replaces any previous values, if any.
     *
     * @param array $values The values to specify for the insert query indexed by column names.
     *
     * @return QueryBuilder This QueryBuilder instance.
     */
    public function values(array $values)
    {
        return $this->add('values', $values);
    }

    /**
     * Specifies a restriction over the groups of the query.
     * Replaces any previous having restrictions, if any.
     *
     * @param mixed $having The restriction over the groups.
     *
     * @return $this
     */
    public function having($having)
    {
        if (!(func_num_args() == 1 && $having instanceof CompositeExpression)) {
            $having = new CompositeExpression(CompositeExpression::TYPE_AND, func_get_args());
        }

        return $this->add('having', $having);
    }

    /**
     * Adds a restriction over the groups of the query, forming a logical
     * conjunction with any existing having restrictions.
     *
     * @param mixed $having The restriction to append.
     *
     * @return $this
     */
    public function andHaving($having)
    {
        $args = func_get_args();
        $having = $this->getQueryPart('having');

        if ($having instanceof CompositeExpression && $having->getType() === CompositeExpression::TYPE_AND) {
            $having->addMultiple($args);
        } else {
            array_unshift($args, $having);
            $having = new CompositeExpression(CompositeExpression::TYPE_AND, $args);
        }

        return $this->add('having', $having);
    }

    /**
     * Adds a restriction over the groups of the query, forming a logical
     * disjunction with any existing having restrictions.
     *
     * @param mixed $having The restriction to add.
     *
     * @return $this
     */
    public function orHaving($having)
    {
        $args = func_get_args();
        $having = $this->getQueryPart('having');

        if ($having instanceof CompositeExpression && $having->getType() === CompositeExpression::TYPE_OR) {
            $having->addMultiple($args);
        } else {
            array_unshift($args, $having);
            $having = new CompositeExpression(CompositeExpression::TYPE_OR, $args);
        }

        return $this->add('having', $having);
    }

    /**
     * Specifies an ordering for the query results.
     * Replaces any previously specified orderings, if any.
     *
     * @param string $sort
     * @param string $order
     *
     * @return $this
     */
    public function orderBy($sort, $order = null)
    {
        return $this->add('orderBy', $sort . ' ' . (!$order ? 'ASC' : $order), false);
    }

    /**
     * Adds an ordering to the query results.
     *
     * @param string $sort
     * @param string $order
     *
     * @return $this
     */
    public function addOrderBy($sort, $order = null)
    {
        return $this->add('orderBy', $sort . ' ' . (!$order ? 'ASC' : $order), true);
    }

    /**
     * Gets a query part by its name.
     *
     * @param string $queryPartName
     *
     * @return mixed
     */
    public function getQueryPart($queryPartName)
    {
        return $this->sqlParts[$queryPartName];
    }

    /**
     * Gets all query parts.
     *
     * @return array
     */
    public function getQueryParts()
    {
        return $this->sqlParts;
    }

    /**
     * Resets SQL parts.
     *
     * @param array|null $queryPartNames
     *
     * @return $this
     */
    public function resetQueryParts($queryPartNames = null)
    {
        if (is_null($queryPartNames)) {
            $queryPartNames = array_keys($this->sqlParts);
        }

        foreach ($queryPartNames as $queryPartName) {
            $this->resetQueryPart($queryPartName);
        }

        return $this;
    }

    /**
     * Resets a single SQL part.
     *
     * @param string $queryPartName
     *
     * @return $this
     */
    public function resetQueryPart($queryPartName)
    {
        $this->sqlParts[$queryPartName] = is_array($this->sqlParts[$queryPartName])
            ? array() : null;

        $this->state = self::STATE_DIRTY;

        return $this;
    }

    /**
     * Gets a string representation of this QueryBuilder which corresponds to
     * the final SQL query being constructed.
     *
     * @return string The string representation of this QueryBuilder.
     */
    public function __toString()
    {
        return $this->getSQL();
    }

    /**
     * Creates a new named parameter and bind the value $value to it
     *
     * @param mixed $value
     * @param mixed $type
     * @param string $placeHolder
     *
     * @return string the placeholder name used.
     */
    public function createNamedParameter($value, $type = \PDO::PARAM_STR, $placeHolder = null)
    {
        if ($placeHolder === null) {
            $this->boundCounter++;
            $placeHolder = ":dcValue" . $this->boundCounter;
        }

        $this->setParameter(substr($placeHolder, 1), $value, $type);

        return $placeHolder;
    }

    /**
     * Creates a new positional parameter and bind the given value to it.
     *
     * @param mixed $value
     * @param integer $type
     *
     * @return string
     */
    public function createPositionalParameter($value, $type = \PDO::PARAM_STR)
    {
        $this->boundCounter++;
        $this->setParameter($this->boundCounter, $value, $type);

        return "?";
    }

    /**
     * @return string
     *
     * @throws $this
     */
    private function getSelectSQL()
    {
        $query = 'SELECT ' . implode(', ', $this->sqlParts['select']);

        $query .= ($this->sqlParts['from'] ? ' FROM ' . implode(', ', $this->getFromClauses()) : '')
            . ($this->sqlParts['where'] !== null ? ' WHERE ' . ((string)$this->sqlParts['where']) : '')
            . ($this->sqlParts['groupBy'] ? ' GROUP BY ' . implode(', ', $this->sqlParts['groupBy']) : '')
            . ($this->sqlParts['having'] !== null ? ' HAVING ' . ((string)$this->sqlParts['having']) : '')
            . ($this->sqlParts['orderBy'] ? ' ORDER BY ' . implode(', ', $this->sqlParts['orderBy']) : '');

        if ($this->isLimitQuery()) {
            return $this->modifyLimitQuery($query, $this->limit, $this->offset);
        }

        return $query;
    }

    /**
     * Limit query mysql specified!!!
     *
     * @param $query
     * @param $limit
     * @param null $offset
     * @return string
     */
    private function modifyLimitQuery($query, $limit, $offset = null)
    {
        if ($limit !== null) {
            $limit = (int)$limit;
        }

        if ($offset !== null) {
            $offset = (int)$offset;

            if ($offset < 0) {
                throw new \LogicException("LIMIT argument offset=$offset is not valid");
            }
        }

        if ($limit !== null) {
            $query .= ' LIMIT ' . $limit;
            if ($offset !== null) {
                $query .= ' OFFSET ' . $offset;
            }
        } elseif ($offset !== null) {
            $query .= ' LIMIT 18446744073709551615 OFFSET ' . $offset;
        }

        return $query;
    }

    /**
     * @return bool
     */
    private function isLimitQuery()
    {
        return $this->limit !== null || $this->offset !== null;
    }

    /**
     * Converts this instance into an INSERT string in SQL.
     *
     * @return string
     */
    private function getInsertSQL()
    {
        return 'INSERT INTO ' . $this->sqlParts['from']['table'] .
            ' (' . implode(', ', array_keys($this->sqlParts['values'])) . ')' .
            ' VALUES(' . implode(', ', $this->sqlParts['values']) . ')';
    }

    /**
     * Converts this instance into an UPDATE string in SQL.
     *
     * @return string
     */
    private function getUpdateSQL()
    {
        $table = $this->sqlParts['from']['table'] . ($this->sqlParts['from']['alias'] ? ' ' . $this->sqlParts['from']['alias'] : '');
        $query = 'UPDATE ' . $table
            . ' SET ' . implode(", ", $this->sqlParts['set'])
            . ($this->sqlParts['where'] !== null ? ' WHERE ' . ((string)$this->sqlParts['where']) : '');

        return $query;
    }

    /**
     * Converts this instance into a DELETE string in SQL.
     *
     * @return string
     */
    private function getDeleteSQL()
    {
        $table = $this->sqlParts['from']['table'] . ($this->sqlParts['from']['alias'] ? ' ' . $this->sqlParts['from']['alias'] : '');
        $query = 'DELETE FROM ' . $table . ($this->sqlParts['where'] !== null ? ' WHERE ' . ((string)$this->sqlParts['where']) : '');

        return $query;
    }

    /**
     * @return string[]
     */
    private function getFromClauses()
    {
        $fromClauses = array();
        $knownAliases = array();

        // Loop through all FROM clauses
        foreach ($this->sqlParts['from'] as $from) {
            if ($from['alias'] === null) {
                $tableSql = $from['table'];
                $tableReference = $from['table'];
            } else {
                $tableSql = $from['table'] . ' ' . $from['alias'];
                $tableReference = $from['alias'];
            }

            $knownAliases[$tableReference] = true;

            $fromClauses[$tableReference] = $tableSql . $this->getJoinsSQL($tableReference, $knownAliases);
        }

        $this->ensureAllAliasesAreKnown($knownAliases);

        return $fromClauses;
    }

    /**
     * @param array $knownAliases
     *
     * @throws \LogicException
     */
    private function ensureAllAliasesAreKnown(array $knownAliases)
    {
        foreach ($this->sqlParts['join'] as $fromAlias => $joins) {
            if (!isset($knownAliases[$fromAlias])) {

                throw new \LogicException("The given alias '" . $fromAlias . "' is not part of " .
                    "any FROM or JOIN clause table. The currently registered " .
                    "aliases are: " . implode(", ", $knownAliases));
            }
        }
    }

    /**
     * @param string $fromAlias
     * @param array $knownAliases
     *
     * @return string
     */
    private function getJoinsSQL($fromAlias, array &$knownAliases)
    {
        $sql = '';

        if (isset($this->sqlParts['join'][$fromAlias])) {
            foreach ($this->sqlParts['join'][$fromAlias] as $join) {
                if (array_key_exists($join['joinAlias'], $knownAliases)) {

                    throw new \LogicException("The given alias '" . $join['joinAlias'] . "' is not unique." .
                        "The currently registered aliases are: " . implode(", ", array_keys($knownAliases)) . ".");
                }
                $sql .= ' ' . strtoupper($join['joinType'])
                    . ' JOIN ' . $join['joinTable'] . ' ' . $join['joinAlias']
                    . ' ON ' . ((string)$join['joinCondition']);
                $knownAliases[$join['joinAlias']] = true;
            }

            foreach ($this->sqlParts['join'][$fromAlias] as $join) {
                $sql .= $this->getJoinsSQL($join['joinAlias'], $knownAliases);
            }
        }

        return $sql;
    }
}