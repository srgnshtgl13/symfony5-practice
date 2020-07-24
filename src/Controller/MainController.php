<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        // dd($this->getDoctrine()->getConnection()->prepare('select * from public.user where id=2'));
        // dd($this->getDoctrine()->getConnection()->getSchemaManager()->getDatabasePlatform()->getDefaultSchemaName());
        // $repository = $this->getDoctrine()->getRepository(User::class);
        
        return $this->render('home.html.twig');
    }

    public function generateUniqueSlug($slug, $repo, $slugCount){

    }
}
