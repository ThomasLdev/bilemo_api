<?php


namespace App\Pagination;


use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class PaginationFactory
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function createCollection(QueryBuilder $qb, Request $request, String $route, Array $routeParams = [])
    {
        $page = (int)$request->query->get("page", 1);

        $adapter = new QueryAdapter($qb);
        $pagerFanta = new Pagerfanta($adapter);
        $pagerFanta->setMaxPerPage(5);
        $pagerFanta->setCurrentPage($page);

        $users = [];

        foreach ($pagerFanta->getCurrentPageResults() as $user) {
            $users[] = $user;
        }

        /*$createLinkUrl = function($targetPage) use ($route, $routeParams) {
            return $this->router->generate($route, array_merge(
                $routeParams,
                ['page' => $targetPage]
            ));
        };*/

        $paginatedCollection = new PaginatedCollection($users, $pagerFanta->getNbResults());

        /*$response = [
            'items' => $items,
            'count' => count($items),
            'total' => $pagerFanta->getNbResults(),
            'self' => $this->addItemLink('self', $createLinkUrl($page)),
            'first' => $this->addItemLink('first', $createLinkUrl(1)),
            'last' => $this->addItemLink('last', $createLinkUrl($pagerFanta->getNbPages()))
        ];

        if ($pagerFanta->hasNextPage()) {
            $response[] = ['next' => $this->addItemLink('next', $createLinkUrl($pagerFanta->getNextPage()))];
        } elseif ($pagerFanta->hasPreviousPage()) {
            $response[] = ['prev' => $this->addItemLink('prev', $createLinkUrl($pagerFanta->getPreviousPage()))];
        }*/

        return $paginatedCollection;
    }

    public function addItemLink($rel, $url)
    {
        return $_links[$rel] = $url;
    }
}
