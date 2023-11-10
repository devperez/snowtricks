<?php

namespace App\Controller;

use App\Service\JWTService;
use App\Security\EmailVerifier;
use App\Service\SendMailService;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

/**
 * Controller managing user registration and email verification.
 */
class RegistrationController extends AbstractController
{
    /**
     * @var EmailVerifier
     */
    private EmailVerifier $emailVerifier;

    /**
     * RegistrationController constructor.
     *
     * @param EmailVerifier $emailVerifier The email verifier service.
     */
    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * Handles user registration.
     *
     * @param Request $request The HTTP request.
     * @param UserPasswordHasherInterface $userPasswordHasher The password hasher service.
     * @param UserRepository $userRepository The user repository.
     * @param EntityManagerInterface $emi The entity manager.
     * @param JWTService $jwt The JWT service.
     * @param SendMailService $mail The mail service.
     *
     * @return Response
     */
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
            // Encode the plain password.
            $user = $form->getData();

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $emi->persist($user);
            $emi->flush();

            // Set the header for the jwt.
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];
            // Set the payload for the jwt.
            $payload = [
                'user_id' => $user->getId()
            ];
            // Create jwt.
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));
            
            $mail->send(
                'no-reply@snowtricks.fr',
                $user->getEmail(),
                'Activation de votre compte',
                'register',
                compact('user', 'token')
            );
            $this->addFlash('success', 'Un mail vous a été envoyé.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Verifies the user's email using a provided token.
     *
     * @param TokenStorageInterface $tokenStorage The token storage.
     * @param string $token The verification token.
     * @param JWTService $jwt The JWT service.
     * @param UserRepository $userRepository The user repository.
     * @param EntityManagerInterface $emi The entity manager.
     *
     * @return Response
     */
    #[Route('/verify/{token}', name: 'verify_user')]
    public function verifyUser(tokenStorageInterface $tokenStorage, string $token, JWTService $jwt, UserRepository $userRepository, EntityManagerInterface $emi): Response
    {
        // Check if token is valid, is not expired and has not been modified.
        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret')))
        {
            $payload = $jwt->getPayload($token);
            $user = $userRepository->find($payload['user_id']);
            if ($user && !$user->getIsVerified())
            {
                $user->setIsVerified(true);
                $emi->flush($user);
                // Check if user is logged in.
                $logged_in_user = $tokenStorage->getToken()->getUser();
                if ($logged_in_user)
                {
                    $tokenStorage->setToken(null);
                    $this->addFlash('success', 'Utilisateur activé.');
                    return $this->redirectToRoute('app_account');
                }
                $this->addFlash('success', 'Utilisateur activé.');
                return $this->redirectToRoute('app_account');
            }
        }

        $this->addFlash('danger', 'Une erreur est survenue.');
        return $this->redirectToRoute('app_login');
    }
}
