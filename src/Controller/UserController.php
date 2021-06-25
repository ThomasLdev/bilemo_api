<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//#[Route('/users')]
class UserController extends AbstractController
{
    #[Route('/users', name: 'user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        $data = ['users' => []];

        foreach ($users as $user) {
            $data['users'][] = $this->serializeUser($user);
        }

        $response = new Response(json_encode($data), 200);

        if (!$users) {
            throw $this->createNotFoundException('No user found :/');
        }

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    #[Route('/users', name: 'user_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->submit($data);

        $entityManager->flush();

        $response = new Response('User added !', 200);
        $response->headers->set('Location', 'users/' . $user->getId());
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    #[Route('/users/{id}', name: 'user_show', methods: ['GET'])]
    public function show($id, UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(['id' => $id]);

        if (!$user) {
            throw $this->createNotFoundException(sprintf('No user found for the ID : "%s" :/', $id));
        }

        $data = $this->serializeUser($user);

        $response = new Response(json_encode($data), 200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        // SEE FORMS
        return new Response('WIP');
    }

    #[Route('/users/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function delete(Request $request, User $user): Response
    {
        return new Response('WIP');
    }

    private function serializeUser(User $user): array
    {
        return [
            'id' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'phoneNumber' => $user->getPhoneNumber(),
            'emailAddress' => $user->getEmailAddress(),
            'createdAt' => $user->getCreatedAt(),
            'Client' => $user->getClient()->getBrand(),
            'Products' => $user->getPhones()
        ];
    }
}