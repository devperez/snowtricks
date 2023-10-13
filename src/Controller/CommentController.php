<?php

namespace App\Controller;

use DateTime;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    /**
     * Saves a comment in the data base
     * 
     * @param Request $request the content of the comment form
     * @param EntityManagerInterface $emi The manager that enables to save the data in the data base
     * @param $id The id of the trick that is being commented
     * 
     * @return Response An instance of response with the trick page
     */
    #[Route('/comment/{id}', name: 'commentTrick')]
    public function commentTrick(Request $request, EntityManagerInterface $emi, int $id): Response
    {
        $comment = new Comment();
        $user = $this->getUser();
        $trick = $emi->getRepository(Trick::class)->find($id);
        $commentForm = $this->createForm(CommentType::class);

        $commentForm->handleRequest($request);
        //dd($commentForm);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setUser($user);
            $comment->setTrick($trick);
            $comment->setComment($commentForm->get('comment')->getData());
            $comment->setCreatedAt(new DateTime());
            $emi->persist($comment);
            $emi->flush();
        }
        return $this->redirectToRoute('show', ['id' => $id]);
    }
}
