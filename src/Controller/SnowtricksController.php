<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Trick;
use App\Form\MediaType;
use App\Form\TricksFormType;
use App\Repository\TrickRepository;
use Symfony\Component\Mime\MimeTypes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * SnowtricksController handles the pages concerning snowboard tricks.
 */

class SnowtricksController extends AbstractController
{
    /**
     * @var TrickRepository
     */
    private $trickRepository;
    private $security;
    /**
     * SnowtricksController class constructor
     * 
     * @param TrickRepository $trickRepository The trick repository injected through dependency injection.
     */
    public function __construct(TrickRepository $trickRepository, Security $security)
    {
        $this->trickRepository = $trickRepository;
        $this->security = $security;
    }

    /**
     * Displays the homepage with all the tricks
     * 
     * @return Response An instance of Response with the homepage and the tricks.
     */
    #[Route('/', name: 'app_snowtricks')]
    public function index(): Response
    {
        // Fetch all the tricks through the trick repository.
        $tricks = $this->trickRepository->findAll();
        return $this->render('snowtricks/index.html.twig', [
            'controller_name' => 'SnowtricksController',
            'tricks' => $tricks
        ]);
    }

    /**
     * Creates a new trick
     * 
     * @param Request $request the data sent in the form
     * @param EntityManagerInterface $emi This allows the saving of a new trick in the database
     * @param Security $security The security service to get the authentified user
     * 
     * @return Response HTTP response to redirect the user after the trick creation
     */
    #[Route('snowtrick/create', name:'createTrick', methods: ['POST'])]
    public function createNewTrick(Request $request, EntityManagerInterface $emi): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Accès refusé. Vous n\'avez pas les autorisations nécessaires.');
        }

        $trick = new Trick();
        $user = $this->getUser();
    
        $trickForm = $this->createForm(TricksFormType::class);
        $trickForm->handleRequest($request);
    
        if ($trickForm->isSubmitted() && $trickForm->isValid()) {
            $trick->setUser($user);
            $trick->setName($trickForm->get('name')->getData());
            $trick->setDescription($trickForm->get('description')->getData());
            $trick->setCategory($trickForm->get('category')->getData());
            $emi->persist($trick);
    
            $mediaFiles = $trickForm['media']->getData();
    
            foreach ($mediaFiles as $media) {
                if ($media) {
                    $mimeTypes = new MimeTypes();
                    $mime = $mimeTypes->guessMimeType($media->getPathname());
    
                    if (str_starts_with($mime, 'image/')) {
                        //dd($mime);
                        $destination = $this->getParameter('kernel.project_dir') . '/public/images';
                        //dd($destination);
                        $relativePath = '/images';
                        $filesystem = new Filesystem();
                        if(!$filesystem->exists($destination))
                        {
                            $filesystem->mkdir($destination, 0777);
                        }
                        $originalFilename = pathinfo($media->getClientOriginalName(), PATHINFO_FILENAME);
                        $newFilename = $originalFilename . '-' . uniqid() . '.' . $media->guessExtension();
                        //dd($originalFilename, $newFilename);
                        try {
                            $media->move(
                                $destination,
                                $newFilename
                            );
    
                            $media = new Media();
                            $media->setTrick($trick);
                            $media->setType('photo');
                            $media->setMedia($relativePath . '/' . $newFilename);
    
                            $emi->persist($media);
                        } catch (FileException $e) {
                            // Gérer les erreurs liées au déplacement du fichier
                            dd($e);
                        }
                    }
                }
            }

            $video = $trickForm['video']->getData();
            if ($video)
            {
                //dd($video);
                $media = new Media();
                $media->setTrick($trick);
                $media->setType('video');
                $media->setMedia($video);

                $emi->persist($media);
            }

            $emi->flush();
    
            $this->addFlash('success', 'Votre trick a bien été créé !');
    
            return $this->redirectToRoute('app_snowtricks');
        } else {
            $this->addFlash('danger', 'Il y a eu un problème lors de la création de votre trick.');
        }
    
        return $this->render('snowtricks/new.html.twig', [
            'trickForm' => $trickForm->createView()
        ]);
    }

    /**
     * Displays trick creation form.
     *
     * @return Response An instance of response with the form to create a new trick
     */
    #[Route('/snowtricks/new', name:'newTrick', methods: ['GET'])]
    public function newTrick(): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Accès refusé. Vous n\'avez pas les autorisations nécessaires.');
        }

        $trick = new Trick();

        $trickForm = $this->createForm(TricksFormType::class, $trick);

        return $this->render('snowtricks/new.html.twig', [
            'trickForm' => $trickForm->createView(),
        ]);
    }


    /**
     * Displays the page of a trick.
     * 
     * @param Trick $trick The Trick object corresponding to the id in the URL.
     * 
     * @return Response An instance of Response with the trick page.
     */
    #[Route('/snowtricks/{id}', name: 'show')]
    public function show(Trick $trick): Response
    {
        return $this->render('snowtricks/show.html.twig', [
            'trick' => $trick
        ]);
    }

}
