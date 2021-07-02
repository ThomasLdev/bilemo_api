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

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('', name: 'user_index', methods: ['GET'])]
    public function listAction(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        $json = $this->serializer->serialize($users, 'json', ['groups' => 'user:read']);

        return new Response($json, 200, [
            "Content-Type" => "application/json"
        ]);
    }

    #[Route('', name: 'user_new', methods: ['POST'])]
    public function createAction(Request $request, EntityManagerInterface $entityManager, ClientRepository $clientRepository): Response
    {
        $data = $request->getContent();
        $user = $this->serializer
            ->deserialize($data, User::class, 'json');

        // Replace with current client later
        $defaultClient = $clientRepository->findOneBy(['brand' => 'FSR']);
        $user->setClient($defaultClient);

        $entityManager->persist($user);
        $entityManager->flush();

        return new Response('', Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function showAction($id, UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(['id' => $id]);
        $json = $this->serializer->serialize($user, 'json', ['groups' => 'user:read']);

        return new Response($json, 200, [
        'Content-Type' => 'application/json'
        ]);
    }

    #[Route('/{id}/edit', name: 'user_edit', methods: ['PATCH'])]
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
    }
}