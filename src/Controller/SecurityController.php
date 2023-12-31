<?php

namespace App\Controller;

use App\Entity\PasswordToken;
use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\PasswordTokenRepository;
use App\Repository\UserRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Controller managing user security-related actions (login, logout, password reset).
 */
class SecurityController extends AbstractController
{
    /**
     * Handles user login.
     *
     * @param AuthenticationUtils $authenticationUtils The authentication utility.
     *
     * @return Response
     */
    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Get the login error if there is one.
        $error = $authenticationUtils->getLastAuthenticationError();
        // Last username entered by the user.
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Handles user logout.
     *
     * @return void
     */
    #[Route(path: '/deconnexion', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Initiates the process for resetting a forgotten password.
     *
     * @param Request $request The HTTP request.
     * @param UserRepository $userRepository The user repository.
     * @param TokenGeneratorInterface $tokenGenerator The token generator.
     * @param EntityManagerInterface $emi The entity manager.
     * @param SendMailService $mail The mail service.
     * @param PasswordTokenRepository $passwordToken The password token repository.
     *
     * @return Response
     */
    #[Route(path:'/oubli_mot_de_passe', name:'forgotten_password')]
    public function forgottenPassword(
        Request $request,
        UserRepository $userRepository,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $emi,
        SendMailService $mail,
        PasswordTokenRepository $passwordToken
        ): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            // Fetch the user by his email.
            $user = $userRepository->findOneByEmail($form->get('email')->getData());
            
            if ($user)
            {
                // Generate token and save it to data base.
                $token = $tokenGenerator->generateToken();
                $passwordToken = new PasswordToken;
                $passwordToken->setToken($token);
                $passwordToken->setUser($user);
                // Generate expiry date.
                $currentDateTime = new \DateTime();
                $expiryDateTime = $currentDateTime->add(new \DateInterval('PT10M'));
                $passwordToken->setExpiry($expiryDateTime);
                
                $emi->persist($passwordToken);
                $emi->flush();

                // Generate url.
                $url = $this->generateUrl('reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                
                // Send mail to the user with the token in the url.
                $context = compact('url', 'user');
                $mail->send(
                    'no-reply@snowtricks.fr',
                    $user->getEmail(),
                    'Réinitialisation du mot de passe',
                    'password_reset',
                    $context
                );

                $this->addFlash('success', "Si votre adresse email existe, un mail vous sera envoyé prochainement.");
                return $this->redirectToRoute('app_login');
            }
            $this->addFlash('success', "Si votre adresse email existe, un mail vous sera envoyé prochainement.");
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('security/reset_password_request.html.twig',[
            'requestPassForm' => $form->createView()
        ]);
    }

    /**
     * Resets the user's password using a provided token.
     *
     * @param string $token The password reset token.
     * @param Request $request The HTTP request.
     * @param PasswordTokenRepository $passwordToken The password token repository.
     * @param EntityManagerInterface $emi The entity manager.
     * @param UserPasswordHasherInterface $userPasswordHasher The user password hasher.
     *
     * @return Response
     */
    #[Route(path:'/oubli_mot_de_passe/{token}', name:'reset_password')]
    public function resetPassword(
        string $token,
        Request $request,
        PasswordTokenRepository $passwordToken,
        EntityManagerInterface $emi,
        UserPasswordHasherInterface $userPasswordHasher
        ): Response
    {
        // Check if this token is in data base.
        $userToken = $passwordToken->findOneByToken($token);
        if ($userToken)
        {
            $isTokenValid = $userToken->isValid($userToken->getExpiry());

            if (!$isTokenValid)
            {
                $emi->remove($userToken);
                $emi->flush();
                $this->addflash('danger', "Le lien sur lequel vous avez cliqué n'est plus valide.");
                return $this->redirectToRoute('app_login');
            }else{
                $form = $this->createForm(ResetPasswordFormType::class);

                $form->handleRequest($request);
                if($form->isSubmitted() && $form->isValid())
                {
                    $user = $userToken->getUser();
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user, $form->get('password')->getData()
                        )
                    );
                    $emi->persist($user);
                    $emi->flush();
                        
                    // Delete the token.
                    $emi->remove($userToken);
                    $emi->flush();

                    $this->addFlash('success', "Mot de passe modifié avec succès.");
                    return $this->redirectToRoute('app_login');
                }
                return $this->render('security/reset_password.html.twig',[
                    'passForm' => $form->createView(),
                ]);
            }
            $emi->remove($userToken);
            $emi->flush();
            $this->addFlash('danger', 'Une erreur est survenue');
            return $this->redirectToRoute('app_login');
        }
        $emi->remove($userToken);
        $emi->flush();
        $this->addFlash('danger', 'Une erreur est survenue.');
        return $this->redirectToRoute('app_login');
    }
}
