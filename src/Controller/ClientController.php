<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//#[Route('/client')]
class ClientController extends AbstractController
{
    #[Route('/clients/{id}', name: 'clients_show', methods: ['GET'])]
    public function show($id, ClientRepository $clientRepository): Response
    {
        $client = $clientRepository->findOneBy(['id' => $id]);

        if (!$client) {
            throw $this->createNotFoundException(sprintf('No client found for the ID : "%s" :/', $id));
        }

        $data = $this->serializeClient($client);

        $response = new Response(json_encode($data), 200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    #[Route('/clients/{id}', name: 'clients_edit', methods: ['POST'])]
    public function edit(Request $request, Client $client): Response
    {
        // SEE FORMS FOR NEW AND EDIT
        return new Response('WIP');
    }

    private function serializeClient(Client $client): array
    {
        return [
            'id' => $client->getId(),
            'brand' => $client->getBrand(),
            'country' => $client->getCountry(),
            'phoneNumber' => $client->getPhoneNumber(),
            'emailAddress' => $client->getEmailAddress(),
            'createdAt' => $client->getCreatedAt()
        ];
    }
}