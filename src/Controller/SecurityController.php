<?php

namespace App\Controller;

use App\Entity\PasswordToken;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\PasswordTokenRepository;
use App\Repository\UserRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/deconnexion', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

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
            // Fetch the user by his email
            $user = $userRepository->findOneByEmail($form->get('email')->getData());
            
            if($user)
            {
                // Generate token and save it to data base
                $token = $tokenGenerator->generateToken();
                $passwordToken = new PasswordToken;
                $passwordToken->setToken($token);
                $passwordToken->setUser($user);
                // Generate expiry date
                $currentDateTime = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
                $expiryDateTime = $currentDateTime->add(new \DateInterval('PT10M'));
                $passwordToken->setExpiry($expiryDateTime);
                
                $emi->persist($passwordToken);
                $emi->flush();

                // Generate url
                $url = $this->generateUrl('reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                
                // send mail to the user with the token in the url
                $context = compact('url', 'user');
                $mail->send(
                    'no-reply@snowtricks.fr',
                    $user->getEmail(),
                    'Réinitialisation du mot de passe',
                    'password_reset',
                    $context
                );

                $this->addFlash('success', "Un mail vient de vous être envoyé.");
                return $this->redirectToRoute('app_login');
            }
            $this->addFlash('danger', "Cette adresse email est inconnue.");
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('security/reset_password_request.html.twig',[
            'requestPassForm' => $form->createView()
        ]);
    }

    #[Route(path:'/oubli_mot_de_passe/{token}', name:'reset_password')]
    public function resetPassword(
        string $token,
        Request $request,
        UserRepository $userRepository,
        PasswordTokenRepository $passwordToken,
        EntityManagerInterface $emi,
        UserPasswordHasherInterface $userPasswordHasher
        ): Response
    {
        // Check if this token is in data base
        $userToken = $passwordToken->findOneByToken($token);
        //dd($userToken);
        if($userToken)
        {
            $isTokenValid = $userToken->isValid($userToken->getExpiry());
            //dd($isTokenValid);
            if(!$isTokenValid)
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
                    //dd($user);
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user, $form->get('password')->getData()
                            )
                        );
                    $emi->persist($user);
                    $emi->flush();
                        
                    // Delete the token
                    $emi->remove($userToken);
                    $emi->flush();

                    $this->addFlash('success', "Mot de passe modifié avec succès.");
                    return $this->redirectToRoute('app_login');
                }
                return $this->render('security/reset_password.html.twig',[
                    'passForm' => $form->createView(),
                ]);
            }
            $this->addFlash('danger', 'Token invalide');
            return $this->redirectToRoute('app_login');
        }
        $this->addFlash('danger', 'Une erreur est survenue.');
        return $this->redirectToRoute('app_login');
    }
}
