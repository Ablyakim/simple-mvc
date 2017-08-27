<?php


namespace Framework\Db;

use Framework\Db\Driver\DriverInterface;
use Framework\Db\Driver\MysqlDriver as PDOConnection;
use Framework\Db\Query\QueryBuilder;

/**
 * Class ConnectionProxy
 */
class ConnectionProxy implements ConnectionInterface, DriverInterface
{
    /**
     * @var array
     */
    protected $params;

    /**
     * @var DriverInterface
     */
    protected $connection;

    /**
     * Default fetch mode using in query execution
     *
     * @var int
     */
    protected $defaultFetchMode = \PDO::FETCH_ASSOC;

    /**
     * ConnectionProxy constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * @inheritDoc
     */
    function prepare($prepareString)
    {
        return $this->getConnection()->prepare($prepareString);
    }

    /**
     * @inheritDoc
     */
    function query()
    {
        return call_user_func_array([$this->getConnection(), 'query'], func_get_args());
    }

    /**
     * @inheritDoc
     */
    function quote($input, $type = \PDO::PARAM_STR)
    {
        return $this->getConnection()->quote($input, $type);
    }

    /**
     * @inheritDoc
     */
    function exec($statement)
    {
        return $this->getConnection()->exec($statement);
    }

    /**
     * @return DriverInterface
     */
    public function getConnection()
    {
        $this->connect();

        return $this->connection;
    }

    /**
     * Connect to database
     */
    protected function connect()
    {
        if ($this->connection) {
            return;
        }

        $params = $this->params;
        $username = $params['username'];
        $password = $params['password'];

        try {
            //move to factory and detect needed connection by database type
            $this->connection = new PDOConnection(
                $this->buildPdoDsn($params),
                $username,
                $password
            );
        } catch (\PDOException $e) {
            throw new \PDOException($e);
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @return \PDOStatement
     */
    public function executeByQueryBuilder(QueryBuilder $queryBuilder)
    {
        $query = $queryBuilder->getSQL();
        $params = $queryBuilder->getParameters();
        $types = $queryBuilder->getParameterTypes();

        return $this->executeQuery($query, $params, $types);
    }

    /**
     * @param string $query
     * @param array $params
     * @param array $types
     *
     * @return \PDOStatement
     */
    public function executeQuery($query, array $params = [], $types = [])
    {
        $connection = $this->getConnection();

        try {
            if ($params) {
                $stmt = $connection->prepare($query);

                if ($types) {
                    foreach ($types as $name => $type) {
                        $stmt->bindValue($params[$name], $name, $type);
                    }

                    $stmt->execute();
                } else {
                    $stmt->execute($params);
                }
            } else {
                $stmt = $connection->query($query);
            }
        } catch (\Exception $ex) {
            throw new \LogicException($ex);
        }

        $stmt->setFetchMode($this->defaultFetchMode);

        return $stmt;
    }

    /**
     * Constructs the MySql PDO DSN.
     *
     * @param array $params
     *
     * @return string The DSN.
     */
    protected function buildPdoDsn(array $params)
    {
        $dsn = 'mysql:';
        if (isset($params['host']) && $params['host'] != '') {
            $dsn .= 'host=' . $params['host'] . ';';
        }
        if (isset($params['port'])) {
            $dsn .= 'port=' . $params['port'] . ';';
        }
        if (isset($params['dbname'])) {
            $dsn .= 'dbname=' . $params['dbname'] . ';';
        }
        if (isset($params['unix_socket'])) {
            $dsn .= 'unix_socket=' . $params['unix_socket'] . ';';
        }
        if (isset($params['charset'])) {
            $dsn .= 'charset=' . $params['charset'] . ';';
        }

        return $dsn;
    }
}