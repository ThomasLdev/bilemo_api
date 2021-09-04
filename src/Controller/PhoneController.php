<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Pagination\PaginationFactory;
use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

/**
 * @OA\Response(
 *     response=401,
 *     description="No token found, or expired token"
 * )
 */
#[Route('/api/phones')]
class PhoneController extends AbstractController
{
    /**
     * List all phones available for sale
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a list of the available phones for sale right now. Futur products are not included",
     *     @OA\JsonContent(
     *      type="array",
     *      @OA\Items(ref=@Model(type=Phone::class))
     *      )
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Enter a page number if paginated",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Tag(name="Phones")
     * @Security(name="Bearer")
     */
    #[Route('', name: 'phones_index', methods: ['GET'])]
    public function indexAction(PhoneRepository $phoneRepository, PaginationFactory $paginationFactory, Request $request): Response
    {
        $qb = $phoneRepository->createQueryBuilder('phone');

        $paginatedCollection = $paginationFactory
            ->createCollection($qb, $request, 'phones_index');

        return $this->json($paginatedCollection, Response::HTTP_OK, [])->setSharedMaxAge(3600);
    }

    /**
     * Returns the wanted phone selected by id
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a single phone based on the request id",
     *     @OA\JsonContent(
     *      type="array",
     *      @OA\Items(ref=@Model(type=Phone::class))
     *      )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Bad url or unknown id",
     * )
     * @OA\Tag(name="Phones")
     * @Security(name="Bearer")
     */
    #[Route('/{id}', name: 'phones_show', methods: ['GET'])]
    public function showAction(Phone $phone): Response
    {
        return $this->json($phone, Response::HTTP_OK, [])->setSharedMaxAge(3600);
    }
}
