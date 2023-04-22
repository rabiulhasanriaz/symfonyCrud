<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ArticleRepository;

class ArticleController extends AbstractController
 {
    /**
     * @Route("/", name="hello")
     */
    public function index(EntityManagerInterface $entityManager){
        // return new Response("<html><body>Hello</body></html>");
        $articles = $entityManager->getRepository(Article::class)->findAll();

        return $this->render('articles/index.html.twig', array("articles" => $articles));
    
    }

     /**
      * @Route("/article/new", name="new_article")
      * Method("GET","POST")
      */

    public function new(EntityManagerInterface $entityManager, Request $request){
//        print('a');
        $article = new Article();

        $form = $this->createFormBuilder($article)
        ->add("title", TextType::class, array('attr' => 
        array('class' => 'form-control')
        ))->add('body', TextAreaType::class, array(
            'required' => false,
            'attr' => array('class' => 'form-control')
        ))->add('save', SubmitType::class, array(
            'label' => 'Create',
            'attr' => array('class' => 'btn btn-primary mt-3')
        ))->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $article = $form->getData();

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('hello');
        }
        return $this->render('articles/new.html.twig', array(
            'form' => $form->createView()
        ));
    }

     /**
     * @Route("/article/{id}", name="show")
     */

     public function show(int $id, ArticleRepository $articleRepository){
        $article = $articleRepository
            ->find($id);
        
        // $articleRepository = $entityManager->getRepository(Article::class);
        // $article = $articleRepository->find($id);

        return $this->render('articles/show.html.twig', array('article' => $article));
     }

     /**
      * @Route("/article/delete/{id}", name="delete")
      */
    public function delete(int $id, EntityManagerInterface $entityManager, ArticleRepository $articleRepository){
        print('a');
        $article = $articleRepository
            ->find($id);
        $entityManager->remove($article);
        $entityManager->flush();
        return $this->redirectToRoute('hello');
    }


    /**
     * @Route("/article/edit/{id}", name="edit_article")
     * Method("GET","POST")
     */

    public function edit(ArticleRepository $articleRepository,EntityManagerInterface $entityManager, Request $request, int $id){
//        print('a');
        $article = new Article();
        $article = $articleRepository
            ->find($id);

        $form = $this->createFormBuilder($article)
            ->add("title", TextType::class, array('attr' =>
                array('class' => 'form-control')
            ))->add('body', TextAreaType::class, array(
                'required' => false,
                'attr' => array('class' => 'form-control')
            ))->add('save', SubmitType::class, array(
                'label' => 'Update',
                'attr' => array('class' => 'btn btn-primary mt-3')
            ))->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();

            return $this->redirectToRoute('hello');
        }
        return $this->render('articles/edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

    // /**
    //  * @Route("/article/save", name="article-save")
    //  */

    // public function save(EntityManagerInterface $entityManager): Response{

    //     $article = new Article();
    //     $article->setTitle('Article One');
    //     $article->setBody('This is the body for article 1');

    //     $entityManager->persist($article);

    //     $entityManager->flush();

    //     return new Response("Saved an Article with a id of" . $article->getId());
    // }
}