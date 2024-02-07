<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/admin/user')]
class UserAdminController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private EntityManagerInterface $entityManager
    ){
    }

    #[Route('/me', methods: ['GET'])]
    public function me(NormalizerInterface $normalizer): JsonResponse
    {
        $user = $this->getUser();
        $data = $normalizer->normalize($user, 'json', ['groups' => 'user']);

        return new JsonResponse($data);
    }

    #[Route('/details/{id}', methods: ['GET'])]
    public function show(User $user, NormalizerInterface $normalizer): JsonResponse
    {
        $data = $normalizer->normalize($user, 'json', ['groups' => 'user']);
        return new JsonResponse($data);
    }

    #[Route('/edit/{id}', methods: ['POST'])]
    public function edit(Request $request): JsonResponse
    {
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);
        $form->remove('password');
        $form->add('newPassword', PasswordType::class);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $newPassword = $form->get('newPassword')->getData();
            if ($newPassword) {
                $hash = $this->userPasswordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hash);
            }
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