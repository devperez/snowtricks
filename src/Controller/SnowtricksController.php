<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Trick;
use App\Form\MediaType;
use App\Form\TricksFormType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * SnowtricksController handles the pages concerning snowboard tricks.
 */

class SnowtricksController extends AbstractController
{
    /**
     * @var TrickRepository
     */
    private $trickRepository;

    /**
     * SnowtricksController class constructor
     * 
     * @param TrickRepository $trickRepository The trick repository injected through dependency injection.
     */
    public function __construct(TrickRepository $trickRepository)
    {
        $this->trickRepository = $trickRepository;
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
    // public function createNewTrick(Request $request, EntityManagerInterface $emi): Response
    // {
    //     $trick = new Trick();

    //     $trickForm = $this->createForm(TricksFormType::class);
    //     $user = $this->getUser();
    //     $trickForm->handleRequest($request);
        
    //     if($trickForm->isSubmitted() && $trickForm->isValid())
    //     {
    //         $trick->setUser($user);
    //         $emi->persist($trick);
    //         //$emi->flush();

    //         $mediaForm = $this->createForm(MediaType::class);
    //         $mediaForm->handleRequest($request);

    //         dd($mediaForm);
    //         $this->addFlash('success', 'Votre trick a bien été créé !');

    //         return $this->redirectToRoute('app_snowtricks');
    //     }
    //     $this->addflash('danger', 'Il y a eu un problème lors de la création de votre trick.');
        
    //     return $this->render('snowtricks/new.html.twig',[
    //         'trickForm' => $trickForm->createView(),
    //     ]);
    // }

    public function createNewTrick(Request $request, EntityManagerInterface $emi): Response
{
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

        $media = $trickForm['media']->getData();
        //faire condition pour vérifier si c'est une image
        if (strstr($media['mimeType'], 'image/') !== false) {
            dd($media);
            $media = $mediaForm->getData();
            $mediaFile = $mediaForm->get('media')->getData();

            $destination = '/public/images';
            $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename.'-'.uniqid().'.'.$mediaFile->guessExtension();

            try {
                $mediaFile->move(
                    $destination,
                    $newFilename
                );
            } catch (FileException $e) {
                // Gérer les erreurs liées au déplacement du fichier
            }

            
            $media->setFileName($newFilename);

            $emi->persist($media);
        }

        $emi->flush();

        $this->addFlash('success', 'Votre trick a bien été créé !');

        return $this->redirectToRoute('app_snowtricks');
    }

    $this->addFlash('danger', 'Il y a eu un problème lors de la création de votre trick.');

    return $this->render('snowtricks/new.html.twig', [
        'trickForm' => $trickForm->createView(),
        'mediaForm' => $mediaForm->createView(),
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
