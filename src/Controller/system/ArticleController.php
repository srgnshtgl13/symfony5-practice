<?php

namespace App\Controller\system;

use App\Entity\Article;
use App\Form\ArticleFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ArticleController extends AbstractController
{
    /**
    
     * @Route("/articles", name="article_list")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Article::class);
        $datas = $repository->findAll();
        return $this->render('articles/index.html.twig', ['datas' => $datas]);
    }

    /**
     * @Route("/articles/create", name="article_create", methods={"GET","HEAD"})
     */
    public function create(){
        $article = new Article();
        $article->setPublished(new \DateTime());
        $form = $this->createForm(ArticleFormType::class, $article);
        return $this->render('articles/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/articles/create", name="article_store", methods={"POST"})
     */
    public function store(Request $request){
        $form = $request->request->all();
        $data = $form['article_form'];
        
        $entityManager = $this->getDoctrine()->getManager();

        $article = new Article();
        $article->setTitle($data['title']);
        $article->setContent($data['content']);
        
        // tell Doctrine you want to (eventually) save the ar$article (no queries yet)
        $entityManager->persist($article);
        $article->setSlug($article->getTitle().$article->getId());

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return $this->redirect('/articles/create');
        
    }
}
