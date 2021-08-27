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
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    #[Route('', name: 'user_index', methods: ['GET'])]
    public function listAction(UserRepository $userRepository, Request $request): Response
    {
        $qb = $userRepository->findUserByClient($this->getUser()->getId());

        $paginatedCollection = $this->paginationFactory
            ->createCollection($qb, $request, 'user_index');

        return $this->json($paginatedCollection, Response::HTTP_OK, [], ['groups' => 'user:read']);
    }

    /**
     * Create a new Bilemo customer linked to the current logged user
     *
     * @OA\Response(
     *     response=201,
     *     description="Create a new customer with the data sent in the request. The customer is linked in a manyToOne relation to it's brand.",
     *     @OA\JsonContent(
     *      type="array",
     *      @OA\Items(ref=@Model(type=User::class, groups={"user:read"}))
     *      )
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
        if ($user->getClient()->getUsername() === $this->getUser()->getUsername()) {
            return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user:read']);
        } else {
            $apiProblem = new ApiProblem(
                400,
                ApiProblem::TYPE_FORBIDDEN_RESSOURCE,
            );
            throw new ApiProblemException($apiProblem);
        }
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
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Set the id of the wanted customer",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    #[Route('/{id}', name: 'user_edit_put', methods: ['PUT'])]
    public function updateAction(Request $request, User $userExist): Response
    {
        try {
            $userRequest = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        } catch (NotEncodableValueException $e) {
            $apiProblem = new ApiProblem(
                403,
                ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT,
            );
            $apiProblem->set('errors', $e);

            throw new ApiProblemException($apiProblem);
        }

        if ($userExist->getClient()->getUsername() === $this->getUser()->getUsername()) {
            $errors = $this->validator->validate($userRequest);

            if (count($errors) > 0) {
                return $this->throwApiProblemValidationException($errors);
            }

            $userExist->setFirstName($userRequest->getFirstName());
            $userExist->setLastName($userRequest->getLastName());
            $userExist->setPhoneNumber($userRequest->getPhoneNumber());
            $userExist->setEmailAddress($userRequest->getEmailAddress());
            $userExist->setUpdatedAt(new DateTime());

            $this->entityManager->flush();

            return $this->json($userExist, Response::HTTP_OK, [], ['groups' => 'user:read']);
        } else {
            $this->throwApiProblemForbiddenRessourceException();
        }
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
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Set the id of the wanted customer",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    #[Route('/{id}', name: 'user_edit_patch', methods: ['PATCH'])]
    public function editPatchAction(Request $request, User $user): Response
    {
        if ($user->getClient()->getUsername() === $this->getUser()->getUsername()) {
            try {
                $user = $this->serializer->deserialize($request->getContent(), User::class, 'json', [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $user,
                    'groups' => 'user:write'
                ]);
            } catch (NotEncodableValueException $e) {
                $apiProblem = new ApiProblem(
                    400,
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
        } else {
            $this->throwApiProblemForbiddenRessourceException();
        }
    }

    /**
     * Remove permanently a customer
     *
     * @OA\Response(
     *     response=204,
     *     description="Removes permanently a customer from our database"
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
        if ($user->getClient()->getUsername() === $this->getUser()->getUsername()) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();

            return new Response(null, Response::HTTP_NO_CONTENT);
        } else {
            $this->throwApiProblemForbiddenRessourceException();
        }
    }

    private function throwApiProblemValidationException($errors): Response
    {
        $apiProblem = new ApiProblem(
            '400',
            ApiProblem::TYPE_VALIDATION_ERROR
        );

        $errorsMessage = [];

        foreach ($errors as $error) {
            $errorsMessage[$error->getPropertyPath()] = $error->getMessage();
        }

        $apiProblem->set('errors', $errorsMessage);

        throw new ApiProblemException($apiProblem);
    }

    private function throwApiProblemForbiddenRessourceException()
    {
        $apiProblem = new ApiProblem(
            403,
            ApiProblem::TYPE_FORBIDDEN_RESSOURCE,
        );
        throw new ApiProblemException($apiProblem);
    }
}
