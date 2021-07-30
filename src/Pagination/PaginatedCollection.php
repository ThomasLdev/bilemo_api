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


    public function getItems()
    {
        return $this->items;
    }


    public function getTotal()
    {
        return $this->total;
    }


    public function getCount()
    {
        return $this->count;
    }


    public function getLinks()
    {
        return $this->_links;
    }


    public function setLinks(array $links)
    {
        $this->_links = $links;
    }
}
