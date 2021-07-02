<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/phones')]
class PhoneController extends AbstractController
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('', name: 'phones_index', methods: ['GET'])]
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

    #[Route('/{id}', name: '/phones_show', methods: ['GET'])]
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
}