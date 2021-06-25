<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Form\PhoneType;
use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//#[Route('/products')]
class PhoneController extends AbstractController
{
    #[Route('/products', name: 'products_index', methods: ['GET'])]
    public function index(PhoneRepository $phoneRepository): Response
    {
        return new Response(('HEY PUTAIN CA MARCHE !'));
    }

    /*#[Route('/new', name: 'phone_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $phone = new Phone();
        $form = $this->createForm(PhoneType::class, $phone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($phone);
            $entityManager->flush();

            return $this->redirectToRoute('phone_index');
        }

        return $this->render('phone/new.html.twig', [
            'phone' => $phone,
            'form' => $form->createView(),
        ]);
    }*/

    #[Route('/products/{id}', name: 'products_byId', methods: ['GET'])]
    public function getProduct(Phone $phone): Response
    {
        return $this->render('phone/show.html.twig', [
            'phone' => $phone,
        ]);
    }

    /*#[Route('/{id}/edit', name: 'phone_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Phone $phone): Response
    {
        $form = $this->createForm(PhoneType::class, $phone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('phone_index');
        }

        return $this->render('phone/edit.html.twig', [
            'phone' => $phone,
            'form' => $form->createView(),
        ]);
    }*/

    /*#[Route('/{id}', name: 'phone_delete', methods: ['POST'])]
    public function delete(Request $request, Phone $phone): Response
    {
        if ($this->isCsrfTokenValid('delete'.$phone->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($phone);
            $entityManager->flush();
        }

        return $this->redirectToRoute('phone_index');
    }*/
}
