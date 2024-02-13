<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Service\UploadProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/project')]
class ProjectController extends AbstractController
{
    public function __construct(
        private ProjectRepository $projectRepository,
    ){
    }

    #[Route('/list', methods: ['GET'])]
    public function index(Request $request, NormalizerInterface $normalizer): JsonResponse
    {
        $projects = $this->projectRepository->findAll();
        $dataProjects = $normalizer->normalize($projects,'json', ['groups' => 'projects']);

        foreach ($dataProjects as $key => $data) {
            $projectId = $this->projectRepository->find($data['id']);
            $pictures = [];
            foreach ($projectId->getPictures() as $picture) {
                $pictures[] = ['url' => $request->getUriForPath('/images/') . $picture->getFileName()];
            }
            $dataProjects[$key]['pictures'] = $pictures;
        }

        return new JsonResponse($dataProjects);
    }

    #[Route('/details/{id}', methods: ['GET'])]
    public function show(Request $request, Project $project, NormalizerInterface $normalizer): JsonResponse
    {
        $dataProjects = $normalizer->normalize($project,'json', ['groups' => 'projects']);

        $pictures = [];
        foreach ($project->getPictures() as $picture) {
            $pictures[] = ['url' => $request->getUriForPath('/images/') . $picture->getFileName()];
        }
        $objectif = explode(PHP_EOL, $dataProjects['objectif']);
        $fonctionnality = explode(PHP_EOL, $dataProjects['fonctionnality']);
        $competence = explode(PHP_EOL, $dataProjects['competence']);

        $dataProjects['objectif'] = $objectif;
        $dataProjects['fonctionnality'] = $fonctionnality;
        $dataProjects['competence'] = $competence;
        $dataProjects['pictures'] = $pictures;

        return new JsonResponse($dataProjects);
    }
}