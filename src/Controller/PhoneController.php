<?php

namespace App\Controller;

use App\Entity\Phone;
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
        return $this->json($phoneRepository->findAll(), Response::HTTP_OK, []);
    }

    #[Route('/{id}', name: '/phones_show', methods: ['GET'])]
    public function showAction(Phone $phone): Response
    {
        return $this->json($phone, Response::HTTP_OK, []);
    }
}