<?php

namespace App\Controller;

use App\Entity\Equipement;
use App\Form\EquipementType;
use App\Repository\EquipementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiEquipementController extends AbstractController
{
    private EquipementRepository $equipementRepository;

    public function __construct(EquipementRepository $equipementRepository)
    {
        $this->equipementRepository = $equipementRepository;
    }

    #[Route('/', name: 'equipement_index', methods: "GET")]
    public function index(EquipementRepository $equipementRepository): Response
    {
        return $this->render('equipement/index.html.twig', [
            'equipements' => $equipementRepository->findAll(),
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/new', name: 'equipement_new', methods: ["GET", "POST"])]
    public function new(Request $request): Response
    {
        $equipement = new Equipement();
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->equipementRepository->save($equipement, true);
            return $this->redirectToRoute('equipement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('equipement/new.html.twig', [
            'equipement' => $equipement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'equipement_edit', methods: ["GET", "POST", "PUT"])]
    public function modifier(Request $request, $id, SerializerInterface $serializer): Response
    {
        $equipement = $this->equipementRepository->find($id);
        $equipement->setUpdatedAt(new \DateTimeImmutable());
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('equipement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('equipement/edit.html.twig', [
            'equipement' => $equipement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'equipement_delete', methods: "POST")]
    public function supprimer(Request $request, $id): Response
    {
        if ($this->isCsrfTokenValid('delete'. $id, $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $equipement = $this->equipementRepository->findOneBy(['id' => $id]);
            if ($equipement) {
                $entityManager->remove($equipement);
                $entityManager->flush();
            }

        }

        return $this->redirectToRoute('equipement_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/api/equipement', name: 'app_api_equipement', methods: "GET")]
    public function list(EquipementRepository $equipementRepository, Request $request)  : JsonResponse
    {
        $data = $equipementRepository->findAll();
        return $this->json($data);
    }

    #[Route('/api/equipement', name: 'api_equipement_store', methods: "POST")]
    public function store(Request $request, SerializerInterface $serializer, ValidatorInterface $validator) : JsonResponse
    {
        try {
            $postData = $request->getContent();
            $data = $serializer->deserialize($postData, Equipement::class, 'json');

            $errors = $validator->validate($data);
            if (count($errors)>0) {
                return $this->json($errors, 400);
            }

            $this->equipementRepository->save($data, true);

            return $this->json($data, 201);
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/api/equipement/{id}', name: 'api_equipement_edit', methods: "PUT")]
    public function edit(Request $request, $id, SerializerInterface $serializer, ValidatorInterface $validator) : JsonResponse
    {
        try {
            $postData = $request->getContent();
            $dataDeserialized = $serializer->deserialize($postData, Equipement::class, 'json');

            $errors = $validator->validate($dataDeserialized);
            if (count($errors)>0) {
                return $this->json($errors, 400);
            }

            $equipement = $this->equipementRepository->findOneBy(['id' => $id]);
            $postData = json_decode($postData, true);
            $equipement->setUpdatedAt(new \DateTimeImmutable());
            if (!empty($postData['number'])) {
                $equipement->setNumber($postData['number']);
            }

            if (!empty($postData['name'])) {
                $equipement->setName($postData['name']);
            }

            if (!empty($postData['category'])) {
                $equipement->setCategory($postData['category']);
            }

            if (!empty($postData['description'])) {
                $equipement->setDescription($postData['description']);
            }

            $this->equipementRepository->save($equipement, true);

            return $this->json([
                'status' => 'Modification effectuée avec succés'
            ], 201);
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/api/equipement/{id}', name: 'api_equipement_delete', methods: "DELETE")]
    public function delete($id) : JsonResponse
    {
        $equipement = $this->equipementRepository->findOneBy(['id' => $id]);

        if ($equipement) {
            $this->equipementRepository->remove($equipement, true);
            return $this->json([
                'message' => 'Suppression effectuée avec succés'
            ], 201);
        } else {
            return $this->json([
                'message' => "Erreur lors de la suppression",
            ], 400);
        }

    }
}
