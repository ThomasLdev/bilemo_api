<?php

namespace App\Controller;

use App\Api\ApiProblem;
use App\Api\ApiProblemException;
use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/users')]
class UserController extends AbstractController
{
    private SerializerInterface $serializer;
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    #[Route('', name: 'user_index', methods: ['GET'])]
    public function listAction(UserRepository $userRepository): Response
    {
        return $this->json($userRepository->findAll(), Response::HTTP_OK, [], ['groups' => 'user:read']);
    }

    /**
     * @throws ApiProblemException
     */
    #[Route('', name: 'user_new', methods: ['POST'])]
    public function createAction(Request $request, ClientRepository $clientRepository): Response
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
        // Replace with current client later
        $defaultClient = $clientRepository->findOneBy(['brand' => 'FSR']);
        $user->setClient($defaultClient);

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $this->throwApiProblemValidationException($errors);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new Response('', Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function showAction(User $user): Response
    {
        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user:read']);
    }

    #[Route('/{id}', name: 'user_edit_put', methods: ['PUT'])]
    public function updateAction(Request $request, User $userExist): Response
    {
        $userRequest = $this->serializer->deserialize($request->getContent(), User::class, 'json');

        $errors = $this->validator->validate($userRequest);

        if (count($errors) > 0) {
            return $this->throwApiProblemValidationException($errors);
        }

        $userExist->setEmailAddress($userRequest->getEmailAddress());

        $this->entityManager->flush();

        return $this->json($userRequest, Response::HTTP_OK, [], ['groups' => 'user:read']);
    }

    #[Route('/{id}', name: 'user_edit_patch', methods: ['PATCH'])]
    public function editPatchAction(Request $request, User $user): Response
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $user,
            'groups' => 'user:write'
        ]);

        $this->entityManager->flush();

        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user:read']);
    }

    #[Route('/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function deleteAction(User $user): Response
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    private function throwApiProblemValidationException($errors): Response
    {
        $apiProblem = new ApiProblem(
            '400',
            ApiProblem::TYPE_VALIDATION_ERROR
        );

        $apiProblem->set('errors', $errors);

        throw new ApiProblemException($apiProblem);
    }
}