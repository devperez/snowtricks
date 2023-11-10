<?php

namespace App\Controller;

use App\Form\ProfileImageType;
use App\Form\ProfilePasswordType;
use App\Repository\UserRepository;
use Symfony\Component\Mime\MimeTypes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AccountController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    
    /**
     * @var Security
     */
    private $security;
    
    /**
     * AccountController class constructor
     * 
     * @param UserRepository $userRepository The user repository injected through dependency injection.
     * @param Security $security The security component.
     */
    public function __construct(UserRepository $userRepository, Security $security)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    /**
     * Displays the user's account information.
     *
     * @return Response
     *
     * @throws AccessDeniedException If the user does not have the required role.
     */
    #[Route('/account', name: 'app_account')]
    public function index(): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Accès refusé. Vous n\'avez pas les autorisations nécessaires.');
        }

        $user = $this->getUser();
        $passwordForm = $this->createForm(ProfilePasswordType::class);
        $form = $this->createForm(ProfileImageType::class, $user);
        return $this->render('account/index.html.twig', [
            'form' => $form->createView(),
            'passwordForm' => $passwordForm,
            'user' => $user
        ]);
    }

    /**
     * Handles the upload and update of the user's profile picture.
     *
     * @param Request $request The HTTP request.
     * @param EntityManagerInterface $emi The entity manager.
     *
     * @return Response
     *
     * @throws AccessDeniedException If the user does not have the required role.
     */
    #[Route('/account/profilePic', name:'profilePic')]
    public function profilePic(Request $request, EntityManagerInterface $emi): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Accès refusé. Vous n\'avez pas les autorisations nécessaires.');
        }
        $user = $this->getUser();
        $userId = $user->getId();
        
        $profilePictureForm = $this->createForm(ProfileImageType::class);
        $profilePictureForm->handleRequest($request);

        if ($profilePictureForm->isSubmitted() && $profilePictureForm->isValid()) {
            $file = $profilePictureForm['photo']->getData();
            if ($file)
            {
                $mimeTypes = new MimeTypes();
                $mime = $mimeTypes->guessMimeType($file->getPathname());

                if (str_starts_with($mime, 'image/')) {
                    // Check if user already has a profile picture and if yes, delete it.
                    $picture = $user->getPhoto();
                    if ($picture)
                    {
                        $filePath = $this->getParameter('kernel.project_dir') . '/public' . $picture;
                        unlink($filePath);
                    }
                    $destination = $this->getParameter('kernel.project_dir') . '/public/images';
                    $relativePath = '/images';
                    $filesystem = new Filesystem();

                    if (!$filesystem->exists($destination)) {
                        $filesystem->mkdir($destination, 0777);
                    }
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $cleanedFilename = preg_replace("/[^A-Za-z0-9-]/", "-", $originalFilename);

                    $newFilename = $cleanedFilename . '-' . uniqid() . '.' . $file->guessExtension();
                    try {
                        $file->move(
                            $destination,
                            $newFilename
                        );

                        $user = $this->userRepository->find($userId);
                        $user->setPhoto($relativePath . '/' . $newFilename);
                        

                        $emi->flush();
                    } catch (FileException $e) {
                        $this->addFlash('danger', "Il y a eu un problème lors de l'enregistrement de votre fichier.");
                    }
                }
            }
        }
        return $this->redirectToRoute('app_account');
    }

    /**
     * Handles the change of the user's password.
     *
     * @param Request $request The HTTP request.
     * @param UserPasswordHasherInterface $passwordEncoder The password encoder.
     * @param EntityManagerInterface $emi The entity manager.
     *
     * @return Response
     *
     * @throws AccessDeniedException If the user does not have the required role.
     */
    #[Route('/account/editPassword', name:'editPassword', methods:['POST'])]
    public function editPassword(Request $request, UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $emi): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Accès refusé. Vous n\'avez pas les autorisations nécessaires.');
        }

        $profilePasswordForm = $this->createForm(ProfilePasswordType::class);
        $profilePasswordForm->handleRequest($request);

        if ($profilePasswordForm->isSubmitted() && $profilePasswordForm->isValid()) {
            $user = $this->getUser();
            $password = $profilePasswordForm->get('password')['first']->getData();
            $hashedPassword = $passwordEncoder->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            $emi->persist($user);
            $emi->flush();

            $this->addFlash('success', "Votre mot de passe a été mis à jour avec succès.");
            return $this->redirectToRoute('app_account');
        }

        $this->addFlash('danger', "Vos mots de passe doivent correspondre.");
        return $this->redirectToRoute('app_account');
    }
}
