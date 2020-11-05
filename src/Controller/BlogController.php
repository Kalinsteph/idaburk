<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
    {


        //$repo = $this->getDoctrine()->getRepository(Article::class);

        /**Pour trouver l'ensemble des articles dans le repository */
        $articles = $repo->findAll();
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }


    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig', [
            'titr' => "Bienvenue les amis au projet filets sociaux",
            'age' => 18
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */    
    public function create(Article $article=null, Request $request, EntityManagerInterface $manager){
    dump($request);
       
            if(!$article){
            $article = new Article();

            }


           /* $form = $this->createformBuilder($article)
                         ->add('titre')
                         ->add('contenu')
                         ->add('image')
                         ->getForm();*/

            $form = $this->createForm(ArticleType::class, $article);       
            //Analyser la requête passé/ ici contient les données du formulaire
            $form->handleRequest($request);


            // Si le formulaire a été soumis contient des données et si les données contenu sont correctes
            if($form->isSubmitted() && $form->isValid()){

                if(!$article->getId()){
                    $article->setdateCreation(new \Datetime());

                }

                //Prepartion de l'article pour l'enregistrement dans le manager
                $manager->persist($article);

                // Enregistrement effective
                $manager->flush();

                // Faire une redirection vers la route blog_show

                return $this->redirectToRoute('blog_show', ['id'=>$article->getId()]);
            }

    return $this->render('blog/create.html.twig', [
        'formArticle' => $form->createView(),
        'editMode' => $article->getId() !==null
    ]);
    
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $contenu, Request $request, EntityManagerInterface $manager){
        //lexcut ci-dessous declare la variable $repoar qui se trouve dans le repository de la doctrine
        //$repoar = $this->getDoctrine()->getRepository(Article::class);

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);    

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $comment->setDateCreation(new \DateTime())
                    ->setArticle($contenu);
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $contenu->getId()]);
        }
        return $this->render('blog/show.html.twig' , 
        ['contenu' => $contenu,
        'commentForm' => $form->createView()
        ]);
        }

}
