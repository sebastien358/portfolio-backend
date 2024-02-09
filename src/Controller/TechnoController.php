<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\Techno;
use App\Form\TechnoType;
use App\Repository\TechnoRepository;
use App\Service\UploadProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/techno')]
class TechnoController extends AbstractController
{
    public function __construct(
        private TechnoRepository $technoRepository,
        private EntityManagerInterface $entityManager
    ){
    }

    #[Route('/list', methods: ['GET'])]
    public function index(Request $request, NormalizerInterface $normalizer): JsonResponse
    {
        $technos = $this->technoRepository->findAll();
        $dataTechno = $normalizer->normalize($technos, 'json', ['groups' => 'technos']);

        foreach ($dataTechno as $key => $data) {
            $technoId = $this->technoRepository->find($data['id']);
            $pictures = [];
            foreach ($technoId->getPictures() as $picture) {
                $pictures[] = ['url' => $request->getUriForPath('/images/') . $picture->getFileName()];
            }
            $dataTechno[$key]['pictures'] = $pictures;
        }

        return new JsonResponse($dataTechno);
    }

    #[Route('/new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $techno = new Techno();

        $form = $this->createForm(TechnoType::class, $techno);
        $form->submit($request->request->all());

        foreach ($request->files->all() as $files) {
            $fileName = $this->uploadProvider->upload($files);
            $picture = new Picture();
            $picture->setFileName($fileName);
            $techno->addPicture($picture);
        }

        if ($form->isValid()) {
            $this->entityManager->persist($techno);
            $this->entityManager->flush();
        } else {
            return new JsonResponse($this->getErrorMessages($form));
        }

        return new JsonResponse();
    }

    private function getErrorMessages($form): array
    {
        $errors = [];
        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $child) {
            if ($child->isSubmitted() && !$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }
}