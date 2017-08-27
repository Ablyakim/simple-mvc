<?php


namespace App\Paginator;


use Framework\Db\ConnectionProxy;
use Framework\Db\Query\QueryBuilder;

/**
 * Class Paginator
 */
class Paginator implements \IteratorAggregate
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var ConnectionProxy
     */
    protected $db;

    /**
     * @var int
     */
    protected $countOnPage;

    /**
     * @var int
     */
    protected $currentPage;

    /**
     * @var int
     */
    protected $totalPagesCount;

    /**
     * @var int
     */
    protected $totalResultCount;

    /**
     * Paginator constructor.
     * @param QueryBuilder $queryBuilder
     * @param ConnectionProxy $db
     */
    public function __construct(QueryBuilder $queryBuilder, ConnectionProxy $db)
    {
        $this->queryBuilder = $queryBuilder;
        $this->db = $db;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        $results = $this->getSlice($this->getCurrentPage() - 1, $this->getCountOnPage());

        if ($results instanceof \Iterator) {
            return $results;
        }

        if ($results instanceof \IteratorAggregate) {
            return $results->getIterator();
        }

        return new \ArrayIterator($results);
    }

    /**
     * @param $offset
     * @param $length
     *
     * @return array
     */
    public function getSlice($offset, $length)
    {
        if ($offset < 0) {
            $offset = 0;
        }

        $qb = clone $this->queryBuilder;

        $qb->setLimit($length)
            ->setOffset($offset);

        return $this->db->executeByQueryBuilder($qb)->fetchAll();
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     * @return Paginator
     */
    public function setCurrentPage($currentPage)
    {
        if ($currentPage < 1) {
            $currentPage = 1;
        }

        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * @return int
     */
    public function getCountOnPage()
    {
        return $this->countOnPage;
    }

    /**
     * @param int $countOnPage
     * @return Paginator
     */
    public function setCountOnPage($countOnPage)
    {
        $this->countOnPage = $countOnPage;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalPagesCount()
    {
        if (null === $this->totalPagesCount) {
            $totalResultCount = $this->getTotalResultCount();
            $this->totalPagesCount = ceil($totalResultCount / $this->getCountOnPage());
        }

        return $this->totalPagesCount;
    }

    /**
     * @param int $totalPagesCount
     *
     * @return $this
     */
    public function setTotalPagesCount($totalPagesCount)
    {
        $this->totalPagesCount = $totalPagesCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalResultCount()
    {
        if (null === $this->totalPagesCount) {
            $qb = clone $this->queryBuilder;
            $qb->resetQueryPart('select');
            $qb->addSelect('COUNT(id)');
            $this->totalResultCount = (int)$this->db->executeByQueryBuilder($qb)->fetchColumn();
        }

        return $this->totalResultCount;
    }

    /**
     * @param $url
     * @param $definedParams
     *
     * @return mixed
     */
    public function getPagesHtml($url, $definedParams)
    {
        unset($definedParams['page']);

        //simple url generator for each page
        $urlGenerator = function ($page) use ($url, $definedParams) {
            return $url . '?' . http_build_query(array_merge($definedParams, ['page' => $page]));
        };

        $template = new PagesView($this);

        return $template->render($urlGenerator);
    }
}