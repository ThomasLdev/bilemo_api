<?php

namespace App\Controller;

use App\Api\ApiProblem;
use App\Api\ApiProblemException;
use App\Entity\User;
use App\Pagination\PaginationFactory;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

/**
 * @OA\Response(
 *     response=401,
 *     description="No token found, or expired token"
 * )
 */
#[Route('/api/users')]
class UserController extends AbstractController
{
    private SerializerInterface $serializer;
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private PaginationFactory $paginationFactory;

    public function __construct(SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator, PaginationFactory $paginationFactory)
    {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->paginationFactory = $paginationFactory;
    }

    /**
     * List all customers added by your brand
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a list of the customers added by your brand, which is everyone who has already bought a Bilemo product",
     *     @OA\JsonContent(
     *      type="array",
     *      @OA\Items(ref=@Model(type=User::class, groups={"user:read"}))
     *      )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Bad url, please check your request",
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Enter a page number if paginated",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    #[Route('', name: 'user_index', methods: ['GET'])]
    public function listAction(UserRepository $userRepository, Request $request): Response
    {
        $qb = $userRepository->findUserByClient($this->getUser()->getId());

        $paginatedCollection = $this->paginationFactory
            ->createCollection($qb, $request, 'user_index');

        return $this->json($paginatedCollection, Response::HTTP_OK, [], ['groups' => 'user:read'])->setSharedMaxAge(3600);
    }

    /**
     * Create a new Bilemo customer linked to the current logged user
     *
     * @OA\RequestBody(
     *         description="The new User resource",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/Id+json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="firstName",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="lastName",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="phoneNumber",
     *                     type="string"
     *                 ),
     *                  @OA\Property(
     *                     property="emailAddress",
     *                     type="string"
     *                 ),
     *             )
     *         )
     * )
     * @OA\Response(
     *     response=201,
     *     description="Create a new customer with the data sent in the request. The customer is linked in a manyToOne relation to it's brand.",
     *     @OA\JsonContent(
     *      type="array",
     *      @OA\Items(ref=@Model(type=User::class, groups={"user:read"}))
     *      )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Wrong Json format or Missing Parameter (ex : email or firstName)"
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    #[Route('', name: 'user_new', methods: ['POST'])]
    public function createAction(Request $request): Response
    {
        try {
            $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        } catch (NotEncodableValueException $e) {
            $apiProblem = new ApiProblem(
                400,
                ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT,
            );
            $apiProblem->set('errors', $e);

            throw new ApiProblemException($apiProblem);
        }

        $user->setClient($this->getUser());

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $this->throwApiProblemValidationException($errors);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new Response('', Response::HTTP_CREATED);
    }

    /**
     * Return a single customer based on it's id
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a single customer retrieved by it's id",
     *     @OA\JsonContent(
     *      type="array",
     *      @OA\Items(ref=@Model(type=User::class, groups={"user:read"}))
     *      )
     * )
     * @OA\Response(
     *     response=404,
     *     description="No user found for this id"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Acces Denied, you don't have sufficient rights to see or edit this ressource"
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Set the id of the wanted customer",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function showAction(User $user): Response
    {
        $this->denyAccessUnlessGranted('view', $user);

        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user:read'])->setSharedMaxAge(3600);
    }

    /**
     * Modify a customer with required data
     *
     * @OA\Response(
     *     response=200,
     *     description="Modifies a customer by sending all the required data",
     *     @OA\JsonContent(
     *      type="array",
     *      @OA\Items(ref=@Model(type=User::class, groups={"user:read"}))
     *      )
     * )
     * @OA\Response(
     *     response=404,
     *     description="No user found for this id"
     * )
     * @OA\Response(
     *     response=400,
     *     description="Wrong Json format or Missing Parameter (ex : email or firstName)"
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Set the id of the wanted customer",
     *     @OA\Schema(type="integer")
     * )
     * @OA\RequestBody(
     *         description="The new User resource",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/Id+json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="firstName",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="lastName",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="phoneNumber",
     *                     type="string"
     *                 ),
     *                  @OA\Property(
     *                     property="emailAddress",
     *                     type="string"
     *                 ),
     *             )
     *         )
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    #[Route('/{id}', name: 'user_edit_put', methods: ['PUT'])]
    public function updateAction(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('edit', $user);

        try {
            $userRequest = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        } catch (NotEncodableValueException $e) {
            $apiProblem = new ApiProblem(
                Response::HTTP_BAD_REQUEST,
                ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT,
            );
            $apiProblem->set('errors', $e);

            throw new ApiProblemException($apiProblem);
        }
        $errors = $this->validator->validate($userRequest);

        if (count($errors) > 0) {
            return $this->throwApiProblemValidationException($errors);
        }

        $user->setFirstName($userRequest->getFirstName());
        $user->setLastName($userRequest->getLastName());
        $user->setPhoneNumber($userRequest->getPhoneNumber());
        $user->setEmailAddress($userRequest->getEmailAddress());
        $user->setUpdatedAt(new DateTime());

        $this->entityManager->flush();

        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user:read']);
    }

    /**
     * Modify a customer with only the data you need to change
     *
     * @OA\Response(
     *     response=200,
     *     description="Modifies a customer by sending only one or more of the fields (first name and email only, as an example)",
     *     @OA\JsonContent(
     *      type="array",
     *      @OA\Items(ref=@Model(type=User::class, groups={"user:read"}))
     *      )
     * )
     * @OA\Response(
     *     response=404,
     *     description="No user found for this id"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Acces Denied, you don't have sufficient rights to see or edit this ressource"
     * )
     * @OA\Response(
     *     response=400,
     *     description="Wrong Json format or Missing Parameter (ex : email or firstName)"
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Set the id of the wanted customer",
     *     @OA\Schema(type="integer")
     * )
     * @OA\RequestBody(
     *         description="The new User resource",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/Id+json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="firstName",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="lastName",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="phoneNumber",
     *                     type="string"
     *                 ),
     *                  @OA\Property(
     *                     property="emailAddress",
     *                     type="string"
     *                 ),
     *             )
     *         )
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    #[Route('/{id}', name: 'user_edit_patch', methods: ['PATCH'])]
    public function editPatchAction(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('edit', $user);

        try {
            $user = $this->serializer->deserialize($request->getContent(), User::class, 'json', [
                AbstractNormalizer::OBJECT_TO_POPULATE => $user,
                'groups' => 'user:write'
            ]);
        } catch (NotEncodableValueException $e) {
            $apiProblem = new ApiProblem(
                Response::HTTP_BAD_REQUEST,
                ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT,
            );
            $apiProblem->set('errors', $e);

            throw new ApiProblemException($apiProblem);
        }

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $this->throwApiProblemValidationException($errors);
        }

        $user->setUpdatedAt(new DateTime());

        $this->entityManager->flush();

        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user:read']);
    }

    /**
     * Remove permanently a customer
     *
     * @OA\Response(
     *     response=204,
     *     description="Removes permanently a customer from our database"
     * )
     * @OA\Response(
     *     response=404,
     *     description="No user found for this id"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Acces Denied, you don't have sufficient rights to see or edit this ressource"
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Set the id of the wanted customer",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    #[Route('/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function deleteAction(User $user): Response
    {
        $this->denyAccessUnlessGranted('edit', $user);

        $this->entityManager->remove($user);
        $this->entityManager->flush();
        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    private function throwApiProblemValidationException($errors): Response
    {
        $apiProblem = new ApiProblem(
            Response::HTTP_BAD_REQUEST,
            ApiProblem::TYPE_VALIDATION_ERROR
        );

        $errorsMessage = [];

        foreach ($errors as $error) {
            $errorsMessage[$error->getPropertyPath()] = $error->getMessage();
        }

        $apiProblem->set('errors', $errorsMessage);

        throw new ApiProblemException($apiProblem);
    }
}
