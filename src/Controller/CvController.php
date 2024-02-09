<?php

namespace App\Controller;

use App\Entity\Cv;
use App\Entity\Picture;
use App\Form\CvType;

use App\Repository\CvRepository;
use App\Service\UploadProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/cv')]
class CvController extends AbstractController
{
    public function __construct(
        private CvRepository $cvRepository
    ){
    }

    #[Route('/list', methods: ['GET'])]
    public function index(Request $request, NormalizerInterface $normalizer): JsonResponse
    {
        $cv = $this->cvRepository->findAll();
        $dataCv = $normalizer->normalize($cv, 'json', ['groups' => 'cv']);

        foreach ($dataCv as $key => $data) {
            $cvId = $this->cvRepository->find($data['id']);
            $pictures = [];
            foreach ($cvId->getPictures() as $picture) {
                $pictures[] = ['url' => $request->getUriForPath('/images/') . $picture->getFileName()];
            }

            $dataCv[$key]['pictures'] = $pictures;
        }

        return new JsonResponse($dataCv);
    }
}