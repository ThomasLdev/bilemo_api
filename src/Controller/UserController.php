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
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/users')]
class UserController extends AbstractController
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('', name: 'user_index', methods: ['GET'])]
    public function listAction(UserRepository $userRepository): Response
    {
        return $this->json($userRepository->findAll(), 200, [], ['groups' => 'user:read']);
    }

    #[Route('', name: 'user_new', methods: ['POST'])]
    public function createAction(Request $request, EntityManagerInterface $entityManager, ClientRepository $clientRepository): Response
    {
        $data = $request->getContent();

        try {
            $user = $this->serializer
                ->deserialize($data, User::class, 'json');

            // Replace with current client later
            $defaultClient = $clientRepository->findOneBy(['brand' => 'FSR']);
            $user->setClient($defaultClient);

            $entityManager->persist($user);
            $entityManager->flush();

            return new Response('', Response::HTTP_CREATED);

        } catch(NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ]);
        }
    }

    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function showAction($id, UserRepository $userRepository): Response
    {
        return $this->json($userRepository->findOneBy(['id' => $id]), 200, [], ['groups' => 'user:read']);
    }

    /*#[Route('/{id}/edit', name: 'user_edit', methods: ['PATCH'])]
    public function editAction(Request $request, User $user): Response
    {
        $data = $request->getContent();
        $user = $this->get('serializer')
            ->deserialize($data, 'src/Entity/User', 'json');
        dd($user);
    }

    #[Route('/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function deleteAction(Request $request, User $user): Response
    {
        return new Response('WIP');
    }*/
}