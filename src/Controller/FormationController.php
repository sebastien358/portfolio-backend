<?php

namespace App\Controller;

use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/formation')]
class FormationController extends AbstractController
{
    public function __construct(
        private FormationRepository $formationRepository
    ){
    }

    #[Route('/list', methods: ['GET'])]
    public function index(Request $request, NormalizerInterface $normalizer): JsonResponse
    {
        $data = $this->formationRepository->findAll();
        $dataFormation = $normalizer->normalize($data, 'json', ['groups' => 'formations']);

        foreach ($dataFormation as $key => $data) {
            $formationId = $this->formationRepository->find($data['id']);
            $pictures = [];
            foreach ($formationId->getPictures() as $picture) {
                $pictures[] = ['url' => $request->getUriForPath('/images/') . $picture->getFileName()];
            }
            $techno = explode(PHP_EOL, $data['techno']);

            $dataFormation[$key]['techno'] = $techno;
            $dataFormation[$key]['pictures'] = $pictures;
        }

        return new JsonResponse($dataFormation);
    }
}