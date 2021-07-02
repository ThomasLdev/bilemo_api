<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/phones')]
class PhoneController extends AbstractController
{
    #[Route('', name: 'phones_index', methods: ['GET'])]
    public function indexAction(PhoneRepository $phoneRepository): Response
    {
        return $this->json($phoneRepository->findAll(), 200, []);
    }

    #[Route('/{id}', name: '/phones_show', methods: ['GET'])]
    public function showAction($id, PhoneRepository $phoneRepository): Response
    {
        return $this->json($phoneRepository->findOneBy(['id' => $id]), 200, []);
    }
}