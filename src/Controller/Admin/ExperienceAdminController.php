<?php

namespace App\Controller\Admin;

use App\Entity\Experience;
use App\Entity\Picture;
use App\Form\ExperienceType;
use App\Repository\ExperienceRepository;
use App\Service\UploadProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/admin/experience')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ExperienceAdminController extends AbstractController
{
    public function __construct(
        private UploadProvider $uploadProvider,
        private ExperienceRepository $experienceRepository,
        private EntityManagerInterface $entityManager
    ){
    }

    #[Route('/list', methods: ['GET'])]
    public function index(Request $request, NormalizerInterface $normalizer): JsonResponse
    {
        $experiences = $this->experienceRepository->findAll();
        $data = $normalizer->normalize($experiences, 'json', ['groups' => 'experiences']);

        return new JsonResponse($data);
    }

    #[Route('/new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $experience = new Experience();

        $form = $this->createForm(ExperienceType::class, $experience);
        $form->submit($request->request->all());

        foreach ($request->files->all() as $file) {
            $fileName = $this->uploadProvider->upload($file);
            $picture = new Picture();
            $picture->setFileName($fileName);
            $experience->addPicture($picture);
        }

        if ($form->isValid()) {
            $this->entityManager->persist($experience);
            $this->entityManager->flush();
        } else {
            return new JsonResponse($this->getErrorMessages($form));
        }

        return new JsonResponse();
    }

    #[Route('/details/{id}', methods: ['GET'])]
    public function show(Request $request, Experience $experience, NormalizerInterface $normalizer): JsonResponse
    {
        $data = $normalizer->normalize($experience, 'json', ['groups' => 'experience']);

        $pictures = [];
        foreach ($experience->getPictures() as $picture) {
            $pictures[] = ['id' => $experience->getId(), 'url' => $request->getUriForPath('/images/') . $picture->getFileName()];
        }
        $data['pictures'] = $pictures;

        return new JsonResponse($data);
    }

    #[Route('/update/{id}', methods: ['POST'])]
    public function update(Request $request, Experience $experience, NormalizerInterface $normalizer): JsonResponse
    {
        $form = $this->createForm(ExperienceType::class, $experience);
        $form->submit($request->request->all());

        foreach ($request->files->all() as $file) {
            $fileName = $this->uploadProvider->upload($file);
            $picture = new Picture();
            $picture->setFileName($fileName);
            $experience->addPicture($picture);
        }

        if ($form->isValid()) {
            $this->entityManager->flush();
        } else {
            return new JsonResponse($this->getErrorMessages($form));
        }

        $data = $normalizer->normalize($experience, 'json', ['groups' => 'experience']);

        $pictures = [];
        foreach ($experience->getPictures() as $picture) {
            $pictures[] = ['id' => $experience->getId(), 'url' => $request->getUriForPath('/images/') . $picture->getFileName()];
        }
        $data['pictures'] = $pictures;

        return new JsonResponse($data);
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