<?php

namespace App\Controller\Admin;

use App\Entity\Picture;
use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Service\UploadProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/admin/project')]
class ProjectAdminController extends AbstractController
{
    public function __construct(
        private UploadProvider $uploadProvider,
        private ProjectRepository $projectRepository,
        private EntityManagerInterface $entityManager
    ){
    }

    #[Route('/list', methods: ['GET'])]
    public function index(NormalizerInterface $normalizer): JsonResponse
    {
        $projects = $this->projectRepository->findAll();
        $dataProjects = $normalizer->normalize($projects,'json', ['groups' => 'projects']);

        return new JsonResponse($dataProjects);
    }

    #[Route('/new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $project = new Project();

        $form = $this->createForm(ProjectType::class, $project);
        $form->submit($request->request->all());

        foreach ($request->files->all() as $file) {
            $fileName = $this->uploadProvider->upload($file);
            $picture = new Picture();
            $picture->setFileName($fileName);
            $project->addPicture($picture);
        }

        if ($form->isValid()) {
            $this->entityManager->persist($project);
            $this->entityManager->flush();
        } else {
            return new JsonResponse($this->getErrorMessages($form));
        }

        return new JsonResponse();
    }

    #[Route('/details/{id}', methods: ['GET'])]
    public function show(Request $request, Project $project, NormalizerInterface $normalizer): JsonResponse
    {
        $dataProjects = $normalizer->normalize($project,'json', ['groups' => 'projects']);

        $pictures = [];
        foreach ($project->getPictures() as $picture) {
            $pictures[] = ['id' => $picture->getId(), 'url' => $request->getUriForPath('/images/') . $picture->getFileName()];
        }
        $dataProjects['pictures'] = $pictures;

        return new JsonResponse($dataProjects);
    }

    #[Route('/update/{id}', methods: ['POST'])]
    public function update(Request $request, Project $project): JsonResponse
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->submit($request->request->all());

        foreach ($request->files->all() as $file) {
            $fileName = $this->uploadProvider->upload($file);
            $picture = new Picture();
            $picture->setFileName($fileName);
            $project->addPicture($picture);
        }

        if ($form->isValid()) {
            $this->entityManager->flush();
        } else {
            return new JsonResponse($this->getErrorMessages($form));
        }

        $pictures = [];
        foreach ($project->getPictures() as $picture) {
            $pictures[] = $request->getUriForPath('/images/') . $picture->getFileName();
        }
        $dataProjects['pictures'] = $pictures;

        return new JsonResponse($dataProjects);
    }

    #[Route('/delete/{id}', methods: ['DELETE'])]
    public function delete(Project $project): JsonResponse
    {
        $fileSystem = new Filesystem();

        foreach ($project->getPictures() as $picture) {
            $fileSystem->remove($this->getParameter('picture_path') . $picture->getFileName());
        }
        $this->entityManager->remove($project);
        $this->entityManager->flush();

        return new JsonResponse();
    }

    #[Route('/delete/picture/{id}', methods: ['DELETE'])]
    public function picture(Picture $picture): JsonResponse
    {
        $fileSystem = new Filesystem();

        $fileSystem->remove($this->getParameter('picture_path') . $picture->getFileName());
        $this->entityManager->remove($picture);
        $this->entityManager->flush();

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