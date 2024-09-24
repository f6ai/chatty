<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    #[Route('/post', name: 'app_post')]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/post/new', name: 'app_post_new', priority: 1)]
    public function new(Request $request, EntityManagerInterface $entityManagerInterface): Response 
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setCreated(new \DateTime());
            $entityManagerInterface->persist($post);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Your post was saved.');

            return $this->redirectToRoute('app_post');
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/post/{id}/edit', name: 'app_post_edit', priority: 2)]
    public function edit(Post $post, Request $request, EntityManagerInterface $entityManagerInterface): Response 
    {
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $entityManagerInterface->persist($post);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Your post was updated.');

            return $this->redirectToRoute('app_post', [
                'id'=> $post->getId(),
            ]);
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/post/{id}/comment', name: 'app_post_comment', priority: 3)]
    public function addComment(Post $post, CommentRepository $commentRepository, Request $request, EntityManagerInterface $entityManagerInterface): Response 
    {
        $form = $this->createForm(CommentType::class, new Comment());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setPost($post);

            $entityManagerInterface->persist($comment);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Your comment was saved.');

            return $this->redirectToRoute('app_post_show', [
                'id'=> $post->getId(),
            ]);
        }

        return $this->render('comment/_form.html.twig', [
            'form' => $form,
            'post' => $post,
        ]);
    }

     #[Route('/post/{id}', name: 'app_post_show')]
    public function showOne(Post $post): Response 
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'comments' => $post->getComments(),
        ]);
    }
}
