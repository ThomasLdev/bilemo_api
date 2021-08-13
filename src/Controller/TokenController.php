<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

#[Route('/token')]
class TokenController extends AbstractController
{
    #[Route('', name: 'generate_token', methods: ['POST'])]
    public function newTokenAction(Request $request, ClientRepository $clientRepository)
    {
        $client = $clientRepository->findOneBy(['brand' => $request->getUser()]);

        if(!$client) {
            throw $this->createNotFoundException('No Client');
        }

        $isValid = $this->get('security.password_encoder')
            ->isPasswordValid($client, $request->getPassword());

        if(!$isValid) {
            throw new BadCredentialsException();
        }

        return new Response('HO', Response::HTTP_OK);
    }
}
