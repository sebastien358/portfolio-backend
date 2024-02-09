<?php

namespace App\Controller\Admin;

use App\Entity\Cv;
use App\Entity\Picture;
use App\Form\CvType;

use App\Service\UploadProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/cv')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class CvAdminController extends AbstractController
{
    public function __construct(
        private UploadProvider $uploadProvider,
        private EntityManagerInterface $entityManager
    ){
    }

    #[Route('/new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $cv = new Cv();

        $form = $this->createForm(CvType::class, $cv);
        $form->submit($request->request->all());

        foreach ($request->files->all() as $file) {
            $fileName = $this->uploadProvider->upload($file);
            $picture = new Picture();
            $picture->setFileName($fileName);
            $cv->addPicture($picture);
        }

        if ($form->isValid()) {
            $this->entityManager->persist($cv);
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