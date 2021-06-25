<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//#[Route('/products')]
class PhoneController extends AbstractController
{
    #[Route('/phones', name: 'phones_index', methods: ['GET'])]
    public function index(PhoneRepository $phoneRepository): Response
    {
        $phones = $phoneRepository->findAll();
        $data = ['phones' => []];

        foreach ($phones as $phone) {
            $data['phones'][] = $this->serializePhone($phone);
        }

        $response = new Response(json_encode($data), 200);

        if (!$phones) {
            throw $this->createNotFoundException('No user found :/');
        }

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    #[Route('/phones/{id}', name: '/phones_show', methods: ['GET'])]
    public function show($id, PhoneRepository $phoneRepository): Response
    {
        $phone = $phoneRepository->findOneBy(['id' => $id]);

        if (!$phone) {
            throw $this->createNotFoundException(sprintf('No phone found for the ID : "%s" :/', $id));
        }

        $data = $this->serializePhone($phone);

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    private function serializePhone(Phone $phone): array
    {
        return [
            'id' => $phone->getId(),
            'sku' => $phone->getSku(),
            'price' => $phone->getPrice(),
            'weight' => $phone->getweight(),
            'height' => $phone->getHeight(),
            'width' => $phone->getWidth(),
            'description' => $phone->getDescription()
        ];
    }
}