<?php

namespace App\Api;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tokens')]
class TokenController
{
    #[Route('', name: 'token', methods: ['POST'])]
    public function newTokenAction(): Response
    {
        return new Response('TOKEN');
    }
}
