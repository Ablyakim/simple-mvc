<?php


namespace App\Paginator;

/**
 * Class PagesView
 */
class PagesView
{
    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * @var string
     */
    protected $pageTemplate = '<li class="%s"><a href="%s">%s</a></li>';

    /**
     * @var string
     */
    protected $containerTemplate = '<ul class="pagination">%s</ul>';

    /**
     * @var int
     */
    protected $startPage;

    /**
     * @var int
     */
    protected $endPage;

    /**
     * @var int
     */
    protected $proximity = 5;

    /**
     * PagesView constructor.
     * @param Paginator $paginator
     */
    public function __construct(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @param callable $urlGenerator
     *
     * @return string
     */
    public function render(callable $urlGenerator)
    {
        if ($this->paginator->getTotalPagesCount() == 1) {
            return '';
        }

        $this->detectStartAndEndPages();

        $pages = $this->renderFirst($urlGenerator);

        foreach (range($this->startPage, $this->endPage) as $page) {
            $pages .= $this->renderPage($page, $urlGenerator);
        }

        $pages .= $this->renderLast($urlGenerator);

        return sprintf($this->containerTemplate, $pages);
    }

    /**
     * @param callable $urlGenerator
     *
     * @return string
     */
    protected function renderFirst(callable $urlGenerator)
    {
        if ($this->paginator->getCurrentPage() > 1) {
            return sprintf($this->pageTemplate, '', $urlGenerator(1), '<<');
        } else {
            return sprintf($this->pageTemplate, 'disabled', '#', '<<');
        }
    }

    /**
     * @param callable $urlGenerator
     *
     * @return string
     */
    protected function renderLast(callable $urlGenerator)
    {
        $totalPagesCount = $this->paginator->getTotalPagesCount();
        if ($this->paginator->getCurrentPage() < $totalPagesCount) {
            return sprintf($this->pageTemplate, '', $urlGenerator($totalPagesCount), '>>');
        } else {
            return sprintf($this->pageTemplate, 'disabled', '#', '>>');
        }
    }

    /**
     * @param $page
     * @param callable $urlGenerator
     *
     * @return string
     */
    protected function renderPage($page, callable $urlGenerator)
    {
        if ($page == $this->paginator->getCurrentPage()) {
            return sprintf($this->pageTemplate, 'active', '#', $page);
        }

        return sprintf($this->pageTemplate, '', $urlGenerator($page), $page);
    }

    /**
     * @return void
     */
    protected function detectStartAndEndPages()
    {
        $totalPageCount = $this->paginator->getTotalPagesCount();
        $currentPage = $this->paginator->getCurrentPage();

        $startPage = $currentPage - $this->proximity;
        $endPage = $currentPage + $this->proximity;

        if ($startPage < 1) {
            $endPage = min($endPage + (1 - $startPage), $totalPageCount);
            $startPage = 1;
        }

        if ($endPage > $totalPageCount) {
            $startPage = max($startPage - ($endPage - $totalPageCount), 1);
            $endPage = $totalPageCount;
        }

        $this->startPage = $startPage;
        $this->endPage = $endPage;
    }
}