<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SnowtricksController extends AbstractController
{
    private $trickRepository;

    public function __construct(TrickRepository $trickRepository)
    {
        $this->trickRepository = $trickRepository;
    }

    #[Route('/snowtricks', name: 'app_snowtricks')]
    public function index(): Response
    {
        $tricks = $this->trickRepository->findAll();
        return $this->render('snowtricks/index.html.twig', [
            'controller_name' => 'SnowtricksController',
            'tricks' => $tricks
        ]);
    }

    #[Route('/snowtricks/{id}', name: 'show')]
    public function show(Trick $trick)
    {
        return $this->render('snowtricks/show.html.twig', [
            'trick' => $trick
        ]);
    }
}
