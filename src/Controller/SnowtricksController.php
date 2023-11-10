<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\TricksFormType;
use App\Repository\CommentRepository;
use App\Repository\MediaRepository;
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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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

    /**
     * @var Security
     */
    private $security;

    /**
     * SnowtricksController class constructor.
     * 
     * @param TrickRepository $trickRepository The trick repository injected through dependency injection.
     * @param Security $security The security component.
     */
    public function __construct(TrickRepository $trickRepository, Security $security)
    {
        $this->trickRepository = $trickRepository;
        $this->security = $security;
    }

    /**
     * Displays the homepage with all the tricks.
     * 
     * @return Response An instance of Response with the homepage and the tricks.
     */
    #[Route('/', name: 'app_snowtricks')]
    public function index(): Response
    {
        // Fetch all the tricks through the trick repository.
        $tricks = $this->trickRepository->findAll();
        
        return $this->render('snowtricks/index.html.twig', [
            'tricks' => $tricks
        ]);
    }

    /**
     * Creates a new trick
     * 
     * @param Request $request the data sent in the form.
     * @param EntityManagerInterface $emi This allows the saving of a new trick in the database.
     * @param Security $security The security service to get the authentified user.
     * 
     * @return Response HTTP response to redirect the user after the trick creation.
     */
    #[Route('snowtrick/create', name: 'createTrick', methods: ['POST'])]
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
                        $destination = $this->getParameter('kernel.project_dir') . '/public/images';
                        $relativePath = '/images';
                        $filesystem = new Filesystem();
                        if (!$filesystem->exists($destination)) {
                            $filesystem->mkdir($destination, 0777);
                        }
                        $originalFilename = pathinfo($media->getClientOriginalName(), PATHINFO_FILENAME);
                        $newFilename = $originalFilename . '-' . uniqid() . '.' . $media->guessExtension();
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

            if ($video) {
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
        } catch (\Exception $e) {
            $emi->rollback();
            $this->addFlash('danger', 'Il y a eu un problème lors de la création de votre trick.');
            return $this->render('snowtricks/new.html.twig', [
                'trickForm' => $trickForm->createView()
            ]);
        }

        $this->addFlash('danger', 'Un autre trick porte déjà ce nom.');

        return $this->redirectToRoute(('app_snowtricks'));
    }

    /**
     * Displays trick creation form.
     *
     * @return Response An instance of response with the form to create a new trick.
     */
    #[Route('/snowtricks/new', name: 'newTrick', methods: ['GET'])]
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
     * Deletes a trick.
     * 
     * @param Trick $trick The trick object to be deleted.
     * 
     * @return Response An instance of response with the homepage.
     */
    #[Route('/snowtricks/delete/{id}', name: 'delete')]
    public function delete(EntityManagerInterface $emi, Trick $trick): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Accès refusé. Vous n\'avez pas les autorisations nécessaires.');
        }

        $mediaCollection = $trick->getMedia();
        $mediaCollection->initialize();

        foreach ($mediaCollection as $medium) {
            $mediumType = $medium->getType();
            if ($mediumType === 'photo') {
                $filePath = $this->getParameter('kernel.project_dir') . '/public' . $medium->getMedia();

                if (file_exists($filePath)) {
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
     * Displays the form to edit a trick.
     * 
     * @param Trick $trick The trick object to be edited.
     * @param MediaRepository $mediaRapository The media associated to the trick.
     * 
     * @return Response An instance of response with the form page.
     */
    #[Route('/snowtricks/edit/{id}', name: 'edit')]
    public function edit(Trick $trick, MediaRepository $mediaRepository): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Accès refusé. Vous n\'avez pas les autorisations nécessaires.');
        }

        $trickForm = $this->createForm(TricksFormType::class, $trick);

        $media = $mediaRepository->findBy(['trick' => $trick]);
        $is_editing = true;
        return $this->render('snowtricks/edit.html.twig', [
            'trickForm' => $trickForm->createView(),
            'trick' => $trick,
            'media' => $media,
            'is_editing' => $is_editing,
        ]);
    }

    /**
     * Edits the trick.
     * 
     * @param Trick $trick The trick object to be edited.
     * @param Request $request The data sent in the form.
     * @param EntityManagerInterface $emi Needed to persist the changes in the database.
     * 
     * @return Response An instance of response with the homepage.
     */
    #[Route('/snowtricks/store/{id}', name: "store")]
    public function store(SessionInterface $session, Trick $trick, Request $request, EntityManagerInterface $emi): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Accès refusé. Vous n\'avez pas les autorisations nécessaires.');
        }
        $trickForm = $this->createForm(TricksFormType::class);
        $trickForm->handleRequest($request);
        $emi->beginTransaction();

        try {
            $this->updateTrickDetails($trick, $request, $emi);
            $this->updateTrickMedia($trick, $request, $emi);
            $this->deleteTrickPicture($trick, $request, $emi);
            $this->deleteTrickVideo($trick,$request, $emi);

            $emi->flush();
            $emi->commit();

        }catch (\Exception $e) {
            $emi->rollback();
            $this->addFlash('danger', 'Il y a eu un problème lors de la modification de votre trick.');
            return $this->redirectToRoute('app_snowtricks');
        }
        if (!$session->getFlashBag()->has('danger')) {
            $this->addFlash('success', 'Votre trick a bien été modifié !');
        }
        return $this->redirectToRoute('app_snowtricks');
    }

    /**
     * Updates the details of a trick.
     *
     * @param Trick $trick The trick object to be edited.
     * @param Request $request The data sent in the form.
     * @param EntityManagerInterface $emi Needed to persist the changes in the database.
     *
     * @return void
     */
    private function updateTrickDetails(Trick $trick, Request $request, EntityManagerInterface $emi): void
    {
        // Fetch data stored in database concerning this trick.
        $trickName = $trick->getName();
        $trickDescription = $trick->getDescription();
        $trickCategory = $trick->getCategory();
        
        // Fetch data in the form and compare it to the database.
        $trickForm = $this->createForm(TricksFormType::class);
        $trickForm->handleRequest($request);

        $trickFormName = $trickForm->get('name')->getData();
        $trickFormDescription = $trickForm->get('description')->getData();
        $trickFormCategory = $trickForm->get('category')->getData();
    
        if ($trickFormName != $trickName) {
            $trick->setName($trickFormName);
            $emi->persist($trick);
        }
        if ($trickFormDescription != $trickDescription) {
            $trick->setDescription($trickFormDescription);
            $emi->persist($trick);
        }
        if ($trickFormCategory != $trickCategory) {
            $trick->setCategory($trickFormCategory);
            $emi->persist($trick);
        }
    }

    /**
     * Updates the media (images and videos) of a trick.
     *
     * @param Trick $trick The trick object to be edited.
     * @param Request $request The data sent in the form.
     * @param EntityManagerInterface $emi Needed to persist the changes in the database.
     *
     * @return void
     */
    private function updateTrickMedia(Trick $trick, Request $request, EntityManagerInterface $emi)
    {
        $trickForm = $this->createForm(TricksFormType::class);
        $trickForm->handleRequest($request);

        // Deal with images upload if there are any.
        $mediaFiles = $trickForm['media']->getData();
        foreach ($mediaFiles as $media) {
            if ($media) {
                $mimeTypes = new MimeTypes();
                $mime = $mimeTypes->guessMimeType($media->getPathname());

                if (str_starts_with($mime, 'image/')) {
                    $destination = $this->getParameter('kernel.project_dir') . '/public/images';
                    $relativePath = '/images';
                    $filesystem = new Filesystem();
                    if (!$filesystem->exists($destination)) {
                        $filesystem->mkdir($destination, 0777);
                    }
                    $originalFilename = pathinfo($media->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $originalFilename . '-' . uniqid() . '.' . $media->guessExtension();
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
                        $emi->flush();
                    } catch (FileException $e) {
                        $this->addFlash('danger', "Il y a eu un problème lors de l'enregistrement de votre fichier.");
                    }
                }
            }
        }

        // Deal with videos upload if there are any.
        $video = $trickForm['video']->getData();

        if ($video) {
            $media = new Media();
            $media->setTrick($trick);
            $media->setType('video');
            $media->setMedia($video);
            $emi->persist($media);
            $emi->flush();
        }
    }

    /**
     * Deletes a picture of a trick.
     *
     * @param Trick $trick The trick object.
     * @param Request $request The data sent in the form.
     * @param EntityManagerInterface $emi Needed to persist the changes in the database.
     *
     * @return Response|null A response instance or null if no redirection is needed.
     */
    private function deleteTrickPicture(Trick $trick, Request $request, EntityManagerInterface $emi)
    {
        // Fetch the media repository.
        $mediaRepository = $emi->getRepository(Media::class);

        // Fetch the ids of the images to delete.
        $imagesToDelete = [];
        $dataImage = $request->request->all();
        if (isset($dataImage['image']) && is_array($dataImage['image'])) {
            foreach ($dataImage['image'] as $imageId) {
                $imagesToDelete[] = $imageId;
            }
            foreach ($imagesToDelete as $imageId) {
                $image = $mediaRepository->find($imageId);
                $trick = $image->getTrick();
                $trickImages = $mediaRepository->findBy(['trick' => $trick]);
                $linkedImages = [];
                foreach ($trickImages as $trickImage)
                {
                    if ($trickImage->getType() == 'photo')
                    {
                        $linkedImages[] = $trickImage;
                    }
                }

                $hasOtherImages = count($linkedImages) > 1;

                $filePath = $this->getParameter('kernel.project_dir') . '/public' . $image->getMedia();
                if ($hasOtherImages && count($imagesToDelete) < count($linkedImages)) {
                    unlink($filePath);
                    $emi->remove($image);
                } else {
                    $this->addFlash('danger', 'Vous ne pouvez pas avoir un trick sans image.');
                    return $this->redirectToRoute('app_snowtricks');
                }
            }
        }
    }

    /**
     * Deletes a video of a trick.
     *
     * @param Trick $trick The trick object.
     * @param Request $request The data sent in the form.
     * @param EntityManagerInterface $emi Needed to persist the changes in the database.
     *
     * @return void
     */
    private function deleteTrickVideo(Trick $trick, Request $request, EntityManagerInterface $emi)
    {
        // Fetch the media repository.
        $mediaRepository = $emi->getRepository(Media::class);

        // Fetch the ids of the videos to delete.
        $videosToDelete = [];
        $dataVideo = $request->request->all();
        if (isset($dataVideo['video']) && is_array($dataVideo['video'])) {
            foreach ($dataVideo['video'] as $videoId) {
                $videosToDelete[] = $videoId;
            }
            foreach ($videosToDelete as $videoId) {
                $video = $mediaRepository->find($videoId);
                if ($video) {
                    $emi->remove($video);
                }
            }
        }
    }

    /**
     * Displays the page of a trick.
     * 
     * @param Trick $trick The Trick object corresponding to the id in the URL.
     * 
     * @return Response An instance of Response with the trick page.
     */
    #[Route('/snowtricks/{id}/{page}', name: 'show', methods:['GET'])]
    public function show(Trick $trick, CommentRepository $repo, int $page=1): Response
    {
        $user = $this->security->getUser();
        $comment = new Comment();

        if ($user)
        {
            $comment->setUser($user);
        }
        
        $thisPage = $page;
        
        $limit = 10;
        $offset = ($thisPage - 1) * $limit;
        $commentQuery = $repo->createQueryBuilder('c')
            ->where('c.trick = :trick')
            ->setParameter('trick', $trick)
            ->orderBy('c.created_at', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery();
        $comments = $commentQuery->getResult();
        $totalComments = $repo->count(['trick' => $trick]);
        $maxPages = ceil($totalComments / $limit);

        $commentForm = $this->createForm(CommentType::class, $comment);
        return $this->render('snowtricks/show.html.twig', [
            'trick' => $trick,
            'commentForm' => $commentForm->createView(),
            'comments' => $comments,
            'maxPages' => $maxPages,
            'thisPage' => $thisPage
        ]);
    }
}
