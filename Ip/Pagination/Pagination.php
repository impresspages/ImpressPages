<?php

namespace Ip\Pagination;


class Pagination
{
    protected $options;
    protected $currentPage = 1;
    protected $totalPages = 1;
    protected $pagerSize = 11;

    public function __construct($options)
    {
        if (isset($options['totalPages'])) {
            $this->totalPages = $options['totalPages'];
        }

        if (isset($options['currentPage'])) {
            $this->currentPage = $options['currentPage'];
        }

        if (isset($options['pagerSize'])) {
            $this->pagerSize = $options['pagerSize'];
        }

        $this->options = $options;
    }

    /**
     * @return array
     */
    public function pages()
    {
        if ($this->totalPages < 1) {
            return;
        }

        $pagesLeft = floor($this->pagerSize / 2) - 2;
        if ($pagesLeft < 0) {
            $pagesLeft = 0;
        }

        $firstPage = max(1, $this->currentPage - $pagesLeft);
        if ($firstPage <= 3) {
            $firstPage = 1;
        }

        $pages = array();

        if ($firstPage > 1) {
            $pages = array(1, '..');
        }

        $pages = array_merge($pages, range($firstPage, $this->currentPage));

        $pagesLeft = max($pagesLeft, $this->pagerSize - count($pages) - 2);
        $lastPage = min($this->totalPages, $this->currentPage + $pagesLeft);

        if ($lastPage + 2 >= $this->totalPages) {
            $lastPage = $this->totalPages;
        }

        if ($this->currentPage < $lastPage) {
            $pages = array_merge($pages, range($this->currentPage + 1, $lastPage));
        }

        if ($lastPage < $this->totalPages) {
            $pages[] = '..';
            $pages[] = $this->totalPages;
        }

        if (isset($pages[1]) && $pages[1] == '..') {
            $pages[1] = array(
                'text' => '..',
                'page' => floor(($pages[0] + $pages[2]) / 2),
            );
        }

        $beforeLast = count($pages) - 2;
        if (isset($pages[$beforeLast]) && $pages[$beforeLast] == '..') {
            $pages[$beforeLast] = array(
                'text' => '..',
                'page' => floor(($pages[$beforeLast - 1] + $pages[$beforeLast + 1]) / 2),
            );
        }

        return $pages;
    }

    public function render($view = null)
    {
        if ($this->totalPages < 1 && $this->currentPage == 1) {
            return null;
        }

        if (!$view) {
            $view = __DIR__ . '/view/pagination.php';
        }

        $data = $this->options;
        $data['currentPage'] = $this->currentPage;
        $data['totalPages'] = $this->totalPages;
        $data['pages'] = $this->pages();

        return ipView($view, $data)->render();
    }

    public function pagerSize()
    {
        return $this->pagerSize;
    }

    public function currentPage()
    {
        return $this->currentPage;
    }

    public function totalPages()
    {
        return $this->totalPages;
    }


}
