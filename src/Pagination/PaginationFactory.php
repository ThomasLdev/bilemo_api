<?php


namespace App\Pagination;


use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class PaginationFactory
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function createCollection(QueryBuilder $qb, Request $request, String $route, Array $routeParams = []): PaginatedCollection
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

        $paginatedCollection = new PaginatedCollection($users, $pagerFanta->getNbResults());

        // make sure query parameters are included in pagination links
        $routeParams = array_merge($routeParams, $request->query->all());

        $createLinkUrl = function($targetPage) use ($route, $routeParams) {
            return $this->router->generate($route, array_merge(
                $routeParams,
                ['page' => $targetPage]
            ));
        };

        $paginatedCollection->addLink('self', $createLinkUrl($page));
        $paginatedCollection->addLink('first', $createLinkUrl(1));
        $paginatedCollection->addLink('last', $createLinkUrl($pagerFanta->getNbPages()));
        if ($pagerFanta->hasNextPage()) {
            $paginatedCollection->addLink('next', $createLinkUrl($pagerFanta->getNextPage()));
        }
        if ($pagerFanta->hasPreviousPage()) {
            $paginatedCollection->addLink('prev', $createLinkUrl($pagerFanta->getPreviousPage()));
        }

        return $paginatedCollection;
    }
}
