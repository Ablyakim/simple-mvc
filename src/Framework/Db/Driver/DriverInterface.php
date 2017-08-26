<?php


namespace Framework\Db\Driver;

/**
 * Interface DriverInterface
 */
interface DriverInterface
{

    /**
     * Prepares a statement for execution and returns a Statement object.
     *
     * @param string $prepareString
     *
     * @return \PDOStatement
     */
    function prepare($prepareString);

    /**
     * Executes an SQL statement
     *
     * @return \PDOStatement
     */
    function query();

    /**
     * Quotes a string for use in a query.
     *
     * @param string $input
     * @param integer $type
     *
     * @return string
     */
    function quote($input, $type = \PDO::PARAM_STR);

    /**
     * Executes an SQL statement and return the number of affected rows.
     *
     * @param string $statement
     *
     * @return integer
     */
    function exec($statement);
}