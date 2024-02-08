<?php

namespace App\Controller\Admin;

use App\Entity\Experience;
use App\Entity\Picture;
use App\Form\ExperienceType;
use App\Service\UploadProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/experience')]
class ExperienceAdminController extends AbstractController
{
    public function __construct(
        private UploadProvider $uploadProvider,
        private EntityManagerInterface $entityManager
    ){
    }

    #[Route('/new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $experience = new Experience();

        foreach ($request->files->all() as $file) {
            $fileName = $this->uploadProvider->upload($file);
            $picture = new Picture();
            $picture->setFileName($fileName);
            $experience->addPicture($picture);
        }

        $form = $this->createForm(ExperienceType::class, $experience);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $this->entityManager->persist($experience);
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