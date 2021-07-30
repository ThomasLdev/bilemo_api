<?php

namespace App\Pagination;

use Symfony\Component\Serializer\Annotation\Groups;

class PaginatedCollection
{
    /**
     * @Groups("user:read")
     */
    private $items;
    /**
     * @Groups("user:read")
     */
    private $total;
    /**
     * @Groups("user:read")
     */
    private $count;
    /**
     * @Groups("user:read")
     */
    private $_links = [];


    public function __construct($items, $total)
    {
        $this->items = $items;
        $this->total = $total;
        $this->count = count($items);
    }

    public function addLink($rel, $url)
    {
        $this->_links[$rel] = $url;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @return array
     */
    public function getLinks(): array
    {
        return $this->_links;
    }

    /**
     * @param array $links
     */
    public function setLinks(array $links): void
    {
        $this->_links = $links;
    }
}
