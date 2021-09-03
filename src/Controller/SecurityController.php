<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;


class SecurityController extends AbstractController
{
    /**
     * List all customers added by your brand
     *
     * @OA\Response (
     *     response=200,
     *     description="Returns a list of the customers added by your brand, which is everyone who has already bought a Bilemo product"
     * )
     * @OA\Parameter (
     *     name="page",
     *     in="query",
     *     description="Enter a page number if paginated",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Tag(name="Token")
     */
    #[Route('/api/login_check', name: 'login_check', methods: ['POST'])]
    public function loginCheck()
    {}
}
