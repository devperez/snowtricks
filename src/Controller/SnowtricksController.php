<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Trick;
use App\Form\MediaType;
use App\Form\TricksFormType;
use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManager;
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

        $emi->beginTransaction();
        $trickForm = $this->createForm(TricksFormType::class);

        try {

            $trick = new Trick();
            $user = $this->getUser();
    
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
                                $this->addFlash('danger', "Il y a eu un problème lors de l'enregistrement de votre fichier.");
                            }
                        }
                    }
                }

                $video = $trickForm['video']->getData();
                
                if ($video)
                {
                    $media = new Media();
                    $media->setTrick($trick);
                    $media->setType('video');
                    $media->setMedia($video);
                    $emi->persist($media);
                }

                $emi->flush();
                $emi->commit();
    
                $this->addFlash('success', 'Votre trick a bien été créé !');
    
                return $this->redirectToRoute('app_snowtricks');
            }
        } catch (\Exception $e) {
            $emi->rollback();
            $this->addFlash('danger', 'Il y a eu un problème lors de la création de votre trick.');
            return $this->render('snowtricks/new.html.twig', [
                'trickForm' => $trickForm->createView()
            ]);
        }
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

    /**
     * Deletes a trick
     * 
     * @param Trick $trick The trick object to be deleted
     * 
     * @return Response An instance of response with the homepage
     */
    #[Route('/snowtricks/delete/{id}', name:'delete')]
    public function delete(EntityManagerInterface $emi, Trick $trick): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Accès refusé. Vous n\'avez pas les autorisations nécessaires.');
        }

        $mediaCollection = $trick->getMedia();
        $mediaCollection->initialize();
        
        foreach ($mediaCollection as $medium)
        {
            $mediumType = $medium->getType();
            if($mediumType === 'photo')
            {
                $filePath = $this->getParameter('kernel.project_dir') . '/public' . $medium->getMedia();

                if (file_exists($filePath))
                {
                    unlink($filePath);
                }
            }
            $emi->remove($trick);
            $emi->flush();
            $this->addFlash('success', 'Votre trick a bien été supprimé !');
        }
        return $this->redirectToRoute('app_snowtricks');
    }

    /**
     * Displays the form to edit a trick
     * 
     * @param Trick $trick The trick object to be edited
     * @param MediaRepository $mediaRapository The media associated to the trick
     * 
     * @return Response An instance of response with the form page
     */
    #[Route('/snowtricks/edit/{id}', name:'edit')]
    public function edit(Trick $trick, MediaRepository $mediaRepository): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Accès refusé. Vous n\'avez pas les autorisations nécessaires.');
        }

        $trickForm = $this->createForm(TricksFormType::class, $trick);
        $media = $mediaRepository->findBy(['trick' => $trick]);
        
        return $this->render('snowtricks/edit.html.twig', [
            'trickForm' => $trickForm->createView(),
            'trick' => $trick,
            'media' => $media,
        ]);
    }

    /**
     * Edits the trick
     * 
     * @param Trick $trick The trick object to be edited
     * @param Request $request The data sent in the form
     * @param EntityManagerInterface $emi Needed to persist the changes in the database
     * 
     * @return Response An instance of response with the homepage
     */
    #[Route('/snowtricks/store/{id}', name:"store")]
    public function store(Trick $trick, Request $request, EntityManagerInterface $emi): Response
    {
        // Fetch data stored in database concerning this trick
        $trickName = $trick->getName();
        $trickDescription = $trick->getDescription();
        $trickCategory = $trick->getCategory();


        $trickForm = $this->createForm(TricksFormType::class);
        $trickForm->handleRequest($request);
        // Fetch the data-id attribute : how to ?
        
        if ($trickForm->isSubmitted() && $trickForm->isValid()) {
            // Fetch data in the form and compare it to the database
            $trickFormName = $trickForm->get('name')->getData();
            $trickFormDescription = $trickForm->get('description')->getData();
            $trickFormCategory = $trickForm->get('category')->getData();
            
            if ($trickFormName != $trickName)
            {
                $trick->setName($trickFormName);
                $emi->persist($trick);
            }
            if ($trickFormDescription != $trickDescription)
            {
                $trick->setDescription($trickFormDescription);
                $emi->persist($trick);
            }
            if ($trickFormCategory != $trickCategory)
            {
                $trick->setCategory($trickFormCategory);
                $emi->persist($trick);
            }
            $emi->flush();

            $this->addFlash('success', 'Votre trick a bien été modifié !');
            return $this->redirectToRoute('app_snowtricks');
        }
    }
}
