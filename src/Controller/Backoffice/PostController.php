<?php

namespace App\Controller\Backoffice;;

use DateTime;
use App\Entity\Post;
use App\Form\PostType;
use App\Service\PostSort;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/back/post")
 */
class PostController extends AbstractController
{

    /**
     * @Route("/", name="app_post_index", methods={"GET"})
     */
    public function index(PostSort $postSort ): Response
    {
        // call the service to get all posts if there is a sort given
        $posts = $postSort->sortAllPosts();

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/awaiting", name="app_post_awaiting", methods={"GET"})
     */
    public function awaitingList(PostSort $postSort): Response
    {
        $posts = $postSort->sortAwaitingPosts();

        return $this->render('post/index_awaiting.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/new", name="app_post_new", methods={"GET", "POST"})
     */
    public function new(Request $request, PostRepository $postRepository, SluggerInterface $SluggerInterface): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // when the form is submitted and valid, set of the slug and published date 
            
            $post->setSlug($SluggerInterface->slug($post->getTitle())->lower());

            if($post->getStatus()== 2)
            {
                $post->setPublishedAt(new DateTime());
            }

            $postRepository->add($post, true);
            $this->addFlash('success', "Un nouvel écrit a été ajouté avec succés");

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_post_show", methods={"GET"})
     */
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_post_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Post $post, PostRepository $postRepository, SluggerInterface $SluggerInterface): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setSlug($SluggerInterface->slug($post->getTitle())->lower());

            if($post->getStatus()== 2)
            {
                $post->setPublishedAt(new DateTime());
            }

            $postRepository->add($post, true);

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_post_delete", methods={"POST"})
     */
    public function delete(Request $request, Post $post, PostRepository $postRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $postRepository->remove($post, true);
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/publish", name="app_post_publish", methods={"PUT"})
     */
    public function publish(ManagerRegistry $doctrine, ?Post $post): Response
    {

        if(!$post) 
        {
            throw $this->createNotFoundException("L'écrit n'existe pas");
        }

        $post->setStatus(2);
        $post->setPublishedAt(new DateTime());

        // save the modification of the entity
        $entityManager = $doctrine->getManager();
        $entityManager->persist($post);
        $entityManager->flush();
        

        return $this->redirectToRoute('app_post_awaiting', [], Response::HTTP_SEE_OTHER);
    }


}
