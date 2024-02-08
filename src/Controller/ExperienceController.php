<?php

namespace App\Controller;

use App\Repository\ExperienceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/experience')]
class ExperienceController extends AbstractController
{
    public function __construct(
        private ExperienceRepository $experienceRepository,
        //private EntityManagerInterface $entityManager
    ){
    }

    #[Route('/list', methods: ['GET'])]
    public function index(Request $request, NormalizerInterface $normalizer): JsonResponse
    {
        $experiences = $this->experienceRepository->findAll();
        $dataExperiences = $normalizer->normalize($experiences, 'json', ['groups' => 'experiences']);

        foreach ($dataExperiences as $key => $data) {
            $experienceId = $this->experienceRepository->find($data['id']);
            $pictures = [];
            foreach ($experienceId->getPictures() as $picture) {
                $pictures[] = ['url' => $request->getUriForPath('/images/') . $picture->getFileName()];
            }

            $content = explode(PHP_EOL, $data['content']);

            $dataExperiences[$key]['content'] = $content;
            $dataExperiences[$key]['pictures'] = $pictures;
        }

        return new JsonResponse($dataExperiences);
    }
}