<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/users')]
class UserController extends AbstractController
{
    private SerializerInterface $serializer;
    private EntityManagerInterface $entityManager;

    public function __construct(SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    #[Route('', name: 'user_index', methods: ['GET'])]
    public function listAction(UserRepository $userRepository): Response
    {
        return $this->json($userRepository->findAll(), Response::HTTP_OK, [], ['groups' => 'user:read']);
    }

    #[Route('', name: 'user_new', methods: ['POST'])]
    public function createAction(Request $request, ClientRepository $clientRepository): Response
    {
        $user = $this->processJsonRequestToObject($request);

        // Replace with current client later
        $defaultClient = $clientRepository->findOneBy(['brand' => 'FSR']);
        $user->setClient($defaultClient);

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
    public function editPutAction(Request $request): Response
    {
        $data = $request->getContent();
        $user = $this->serializer->deserialize($data, User::class, 'json');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json($user, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'user_edit_patch', methods: ['PATCH'])]
    public function editPatchAction(Request $request, Int $id, UserRepository $userRepository): Response
    {
        $existingUser = $userRepository->findOneBy(['id' => $id]);

        //Comment update le user avec la data envoyée sans form et sans tout hydrater à la main?

        $data = $request->getContent();
        $user = $this->serializer->deserialize($data, User::class, 'json');

        return new Response('', Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function deleteAction(User $user): Response
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    private function processJsonRequestToObject(Request $request): User
    {
        $data = $request->getContent();
        return $this->serializer->deserialize($data, User::class, 'json');
    }
}