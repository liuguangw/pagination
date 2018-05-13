<?php
namespace liuguang\pagination;

class Paginator
{

    public $currentPage;

    public $totalPage;

    public $options;

    public $contextCount = 3;

    /**
     *
     * @param int $totalPage
     *            总页数
     * @param int $currentPage
     *            当前页
     * @param array $options
     *            附加数据
     */
    public function __construct(int $totalPage, int $currentPage = 1, array $options = [])
    {
        if ($totalPage < 1) {
            $totalPage = 1;
        }
        $this->totalPage = $totalPage;
        if ($currentPage < 1 || $currentPage > $totalPage) {
            $currentPage = 1;
        }
        $this->currentPage = $currentPage;
        $this->options = $options;
    }

    /**
     *
     * @param int $totaCount
     *            数据总条数
     * @param int $perPageCount
     *            每页最多显示条数
     * @param int $currentPage
     *            当前页
     * @param array $options
     *            附加数据
     * @return Paginator
     */
    public static function initByData(int $totaCount, int $perPageCount, int $currentPage = 1, array $options = []): Paginator
    {
        $totalPage = ceil($totaCount / $perPageCount);
        $options['totalCount'] = $totaCount;
        $options['perPageCount'] = $perPageCount;
        return new static($totalPage, $currentPage, $options);
    }

    /**
     * 子类重写此方法
     *
     * @param int $page            
     * @return string
     */
    public function getPageUrl(int $page): string
    {
        return '/page/' . $page;
    }

    /**
     * 获取分页数据结构
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = [
            'previous' => null,
            'first_page' => null,
            'left_blank' => false,
            'items' => [],
            'right_blank' => false,
            'last_page' => null,
            'next' => null
        ];
        if ($this->currentPage > 1) {
            $data['previous'] = $this->getPageUrl($this->currentPage - 1);
            $data['first_page'] = $this->getPageUrl(1);
        }
        if ($this->currentPage < $this->totalPage) {
            $data['next'] = $this->getPageUrl($this->currentPage + 1);
            $data['last_page'] = $this->getPageUrl($this->totalPage);
        }
        $itemPageBegin = $this->currentPage - $this->contextCount;
        $itemPageEnd = $this->currentPage + $this->contextCount;
        // fix
        if ($itemPageBegin <= 1) {
            $itemPageBegin = 2;
        }
        if ($itemPageEnd >= $this->totalPage) {
            $itemPageEnd = $this->totalPage - 1;
        }
        if (($itemPageBegin < $this->totalPage) && ($itemPageEnd > 1)) {
            for ($i = $itemPageBegin; $i <= $itemPageEnd; $i ++) {
                $data['items'][] = [
                    'page' => $i,
                    'url' => $this->getPageUrl($i),
                    'active' => ($this->currentPage == $i)
                ];
            }
            if ($itemPageBegin > 2) {
                $data['left_blank'] = true;
            }
            if ($itemPageEnd < ($this->totalPage - 1)) {
                $data['right_blank'] = true;
            }
        }
        return $data;
    }

    /**
     * 获取分页html
     *
     * @return string
     */
    public function render(): string
    {
        if ($this->totalPage < 2) {
            return '';
        }
        $data = $this->toArray();
        $html = '<ul class="pagination">';
        // Previous
        if ($data['previous'] === null) {
            $html .= ('
    <li class="page-item disabled">
        <a class="page-link" href="#" tabindex="-1">上一页</a>
    </li>');
        } else {
            $html .= ('
    <li class="page-item">
        <a class="page-link" href="' . $data['previous'] . '">上一页</a>
    </li>');
        }
        // first_page
        if ($data['first_page'] === null) {
            $html .= ('
    <li class="page-item active">
        <a class="page-link" href="#">1 <span class="sr-only">(current)</span></a>
    </li>');
        } else {
            $html .= ('
    <li class="page-item">
        <a class="page-link" href="' . $data['first_page'] . '">1</a>
    </li>');
        }
        // left_blank
        if ($data['left_blank']) {
            $html .= ('
    <li class="page-item disabled">
        <a class="page-link" href="#" tabindex="-1">…</a>
    </li>');
        }
        // center
        foreach ($data['items'] as $itemNode) {
            if ($itemNode['active']) {
                $html .= ('
    <li class="page-item active">
        <a class="page-link" href="#">' . $itemNode['page'] . ' <span class="sr-only">(current)</span></a>
    </li>');
            } else {
                $html .= ('
    <li class="page-item">
        <a class="page-link" href="' . $itemNode['url'] . '">' . $itemNode['page'] . '</a>
    </li>');
            }
        }
        // right_blank
        if ($data['right_blank']) {
            $html .= ('
    <li class="page-item disabled">
        <a class="page-link" href="#" tabindex="-1">…</a>
    </li>');
        }
        // last_page
        if ($data['last_page'] === null) {
            $html .= ('
    <li class="page-item active">
        <a class="page-link" href="#">' . $this->totalPage . ' <span class="sr-only">(current)</span></a>
    </li>');
        } else {
            $html .= ('
    <li class="page-item">
        <a class="page-link" href="' . $data['last_page'] . '">' . $this->totalPage . '</a>
    </li>');
        }
        // next
        if ($data['next'] === null) {
            $html .= ('
    <li class="page-item disabled">
        <a class="page-link" href="#" tabindex="-1">下一页</a>
    </li>');
        } else {
            $html .= ('
    <li class="page-item">
        <a class="page-link" href="' . $data['next'] . '">下一页</a>
    </li>');
        }
        $html .= '
</ul>';
        return $html;
    }
}

