<?php


namespace App\Api;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/token')]
class TokenController
{
    #[Route('', name: 'generate_token', methods: ['POST'])]
    public function newTokenAction()
    {
        return new Response('TOKEN', Response::HTTP_OK);
    }
}