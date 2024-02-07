<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RequestPasswordType;
use App\Form\UserType;
use App\Service\MailerProvider;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private MailerProvider $mailerProvider,
        private EntityManagerInterface $entityManager
    ){
    }

    #[Route('/request/reset-password', methods: ['POST'])]
    public function requestPassword(Request $request): JsonResponse
    {
        $form = $this->createForm(RequestPasswordType::class);
        $form->submit($request->request->all());

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $request->request->get('email')]);
        if (!$user) {
            throw new \Exception('Aucun compte ne correspond à cet identifiant');
        }

        $token = uniqid();
        $user->setToken($token);
        $user->setResetRequestAt(new DateTime('now'));

        if ($form->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } else {
            return new JsonResponse($this->getErrorMessages($form));
        }

        $url = $this->getParameter('frontend_url') . '/reset-password/' . $token;

        $body = $this->render('emails/reset-password.html.twig', [
            'url' => $url,
        ])->getContent();

        $this->mailerProvider->sendEmail($user->getEmail(), 'Vous avez fait une demande de réinitialisation de mot de passe', $body);

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