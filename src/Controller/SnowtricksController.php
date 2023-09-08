<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Trick;
use App\Form\MediaType;
use App\Form\TricksFormType;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
     * Displays trick creation form.
     *
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
