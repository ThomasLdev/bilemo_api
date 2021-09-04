<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;


class SecurityController extends AbstractController
{
    /**
     * Returns a JWT token
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a JWT token with 3600 expiration time. Required for any request"
     * )
     * @OA\Response(
     *     response=401,
     *     description="Credientials are incorrect"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Bad Json format triggering a bad request"
     * )
     * @OA\RequestBody(
     *         description="The new User resource",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/Id+json",
     *             @OA\Schema(
     *                 type="array",
     *                 format="json",
     *                 @OA\Items(
     *                     type="string",
     *                 ),
     *                  @OA\Items(
     *                  type="string",
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
