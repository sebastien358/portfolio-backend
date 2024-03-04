<?php

namespace App\Controller\Admin;

use App\Entity\Formation;
use App\Entity\Picture;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use App\Service\UploadProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/admin/formation')]
class FormationAdminController extends AbstractController
{
    public function __construct(
        private UploadProvider $uploadProvider,
        private FormationRepository $formationRepository,
        private EntityManagerInterface $entityManager
    ){
    }

    #[Route('/list', methods: ['GET'])]
    public function index(Request $request, NormalizerInterface $normalizer): JsonResponse
    {
        $formations = $this->formationRepository->findAll();
        $data = $normalizer->normalize($formations, 'json', ['groups' => 'formations']);

        return new JsonResponse($data);
    }

    #[Route('/new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $formation = new Formation();

        $form = $this->createForm(FormationType::class, $formation);
        $form->submit($request->request->all());

        foreach ($request->files->all() as $file) {
            $fileName = $this->uploadProvider->upload($file);
            $picture = new Picture();
            $picture->setFileName($fileName);
            $formation->addPicture($picture);
        }

        if ($form->isValid()) {
            $this->entityManager->persist($formation);
            $this->entityManager->flush();
        } else {
            return new JsonResponse($this->getErrorMessages($form));
        }

        return new JsonResponse();
    }

    #[Route('/details/{id}', methods: ['GET'])]
    public function show(Request $request, Formation $formation, NormalizerInterface $normalizer): JsonResponse
    {
        $data = $normalizer->normalize($formation,'json', ['groups' => 'formation']);

        $pictures = [];
        foreach ($formation->getPictures() as $picture) {
            $pictures[] = ['id' => $formation->getId(), 'url' => $request->getUriForPath('/images/') . $picture->getFileName()];
        }
        $data['pictures'] = $pictures;

        return new JsonResponse($data);
    }

    #[Route('/update/{id}', methods: ['POST'])]
    public function edit(Request $request, Formation $formation, NormalizerInterface $normalizer): JsonResponse
    {
        $form = $this->createForm(FormationType::class, $formation);
        $form->submit($request->request->all());

        foreach ($request->files->all() as $file) {
            $fileName = $this->uploadProvider->upload($file);
            $picture = new Picture();
            $picture->setFileName($fileName);
            $formation->addPicture($picture);
        }

        if ($form->isValid()) {
            $this->entityManager->flush();
        } else {
            return new JsonResponse($this->getErrorMessages($form));
        }

        $data = $normalizer->normalize($formation,'json', ['groups' => 'formation']);

        $pictures = [];
        foreach ($formation->getPictures() as $picture) {
            $pictures[] = ['id' => $formation->getId(), 'url' => $request->getUriForPath('/images/') . $picture->getFileName()];
        }
        $data['pictures'] = $pictures;

        return new JsonResponse($data);
    }

    #[Route('/delete/{id}', methods: ['DELETE'])]
    public function delete(Formation $formation): JsonResponse
    {
        $fileSystem = new Filesystem();

        foreach ($formation->getPictures() as $picture) {
            $fileSystem->remove($this->getParameter('picture_path') . $picture->getFileName());
        }
        $this->entityManager->remove($formation);
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