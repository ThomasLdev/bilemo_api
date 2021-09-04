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
     * @OA\Response(
     *     response=200,
     *     description="Returns a list of the customers added by your brand, which is everyone who has already bought a Bilemo product"
     * )
     * @OA\RequestBody(
     *         description="The new User resource",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/Id+json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="brand",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 )
     *             )
     *         )
     * )
     * @OA\Tag(name="Token")
     */
    #[Route('/api/login_check', name: 'login_check', methods: ['POST'])]
    public function loginCheck()
    {
    }
}
