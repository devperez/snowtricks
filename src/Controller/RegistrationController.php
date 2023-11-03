<?php

namespace App\Controller;

use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserRepository $userRepository,
        EntityManagerInterface $emi,
        JWTService $jwt,
        SendMailService $mail): Response
    {
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user = $form->getData();

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $emi->persist($user);
            $emi->flush();

            // Set the header for the jwt
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];
            // Set the payload for the jwt
            $payload = [
                'user_id' => $user->getId()
            ];
            // Create jwt
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));
            
            $mail->send(
                'no-reply@snowtricks.fr',
                $user->getEmail(),
                'Activation de votre compte',
                'register',
                compact('user', 'token')
            );
            return $this->redirectToRoute('app_login');
            
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/{token}', name: 'verify_user')]
    public function verifyUser(string $token, JWTService $jwt, UserRepository $userRepository, EntityManagerInterface $emi): Response
    {
        // Check if token is valid, is not expired and has not been modified
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret')))
        {
            $payload = $jwt->getPayload($token);
            $user = $userRepository->find($payload['user_id']);
            if ($user && !$user->getIsVerified())
            {
                $user->setIsVerified(true);
                $emi->flush($user);

                $this->addFlash('success', 'Utilisateur activé');
                return $this->redirectToRoute('app_account');
            }
        }

        $this->addFlash('danger', 'Une erreur est survenue.');
        return $this->redirectToRoute('app_login');
    }
}
