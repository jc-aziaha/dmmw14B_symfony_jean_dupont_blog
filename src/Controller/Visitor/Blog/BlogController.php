<?php

namespace App\Controller\Visitor\Blog;

use App\Entity\Tag;
use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\Category;
use App\Form\CommentFormType;
use App\Repository\TagRepository;
use App\Repository\PostRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    #[Route('/blog/all-posts', name: 'visitor.blog.index')]
    public function index(
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository,
        PostRepository $postRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        $categories     = $categoryRepository->findAll();
        $tags           = $tagRepository->findAll();
        $postsPublished = $postRepository->findBy(['isPublished'=> true], ['publishedAt' => 'DESC']);

        $posts = $paginator->paginate(
            $postsPublished,
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('pages/visitor/blog/index.html.twig', [
            'categories' => $categories,
            'tags'       => $tags,
            'posts'      => $posts
        ]);
    }


    #[Route('/blog/post/{id}/{slug}', name: 'visitor.blog.post.show', methods: ['GET', 'POST'])]
    public function show(
        Post $post, 
        Request $request,
        EntityManagerInterface $em
    ) : Response
    {
        $comment = new Comment();

        $form = $this->createForm(CommentFormType::class, $comment);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) 
        {
            $comment->setPost($post);
            $comment->setUser($this->getUser());

            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('visitor.blog.post.show', [
                "id"   => $post->getId(),
                "slug" => $post->getSlug(),
            ]);
        }

        return $this->render("pages/visitor/blog/show.html.twig", [
            "post" => $post,
            "form" => $form->createView()
        ]);
    }


    #[Route('/blog/posts/filter-by-category/{slug}', name: 'visitor.blog.posts.filter_by_category', methods:['GET'])]
    public function filterByCategory(
        Category $category, 
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository,
        PostRepository $postRepository,
        PaginatorInterface $paginator,
        Request $request
        ) : Response
        {
            $categories     = $categoryRepository->findAll();
            $tags           = $tagRepository->findAll();
            $postsPublished = $postRepository->filterPostsByCategory($category->getId());

            $posts = $paginator->paginate(
                $postsPublished,
                $request->query->getInt('page', 1), /*page number*/
                10 /*limit per page*/
            );
            
            return $this->render("pages/visitor/blog/index.html.twig", [
                "categories" => $categories,
                "tags"       => $tags,
                "posts"      => $posts
            ]);
        }  
        
        
        #[Route('/blog/posts/filter-by-tag/{slug}', name: 'visitor.blog.posts.filter_by_tag', methods:['GET'])]
        public function filterByTag(
            Tag $tag,
            CategoryRepository $categoryRepository,
            TagRepository $tagRepository,
            PostRepository $postRepository,
            PaginatorInterface $paginator,
            Request $request
        ) : Response
        {
            $categories     = $categoryRepository->findAll();
            $tags           = $tagRepository->findAll();
            $postsPublished = $postRepository->filterPostsByTag($tag->getId());

            $posts = $paginator->paginate(
                $postsPublished,
                $request->query->getInt('page', 1), /*page number*/
                10 /*limit per page*/
            );

            return $this->render("pages/visitor/blog/index.html.twig", [
                'categories' => $categories,
                'tags'       => $tags,
                'posts'      => $posts,
            ]);
        }


        #[Route('/blog/post/{id<\d+>}/{slug}/like', name: 'visitor.blog.post.like', methods: ['GET'])]
        public function like(
            Post $post, 
            PostLikeRepository $postLikeRepository,
            EntityManagerInterface $em
        ) : Response
        {
            $user = $this->getUser();
    
            if (!$user) 
            {
                return $this->json(array('code' => 403, 'message' => 'Unautorized'), 403);
            }
    
            if ( $post->isLikedByUser($user) ) 
            {
                $post_liked = $postLikeRepository->findOneBy(array('post' => $post, 'user' => $user));
    
                $em->remove($post_liked);
                $em->flush();
                
                return $this->json(array(
                    'code' => 200, 
                    'message' => 'Like supprimé',
                    'postLikes' => $postLikeRepository->count(array('post' => $post))
                ), 200);
            }
    
            $postLike = new PostLike();
            $postLike->setPost($post);
            $postLike->setUser($user);
    
            $em->persist($postLike);
            $em->flush();
    
            return $this->json(array(
                'code' => 200, 
                'message' => 'Like bien ajouté',
                'postLikes' => $postLikeRepository->count(array('post' => $post))
            ), 200);
        }
    }