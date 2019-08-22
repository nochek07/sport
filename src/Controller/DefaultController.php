<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * Главная страница
     *
     * @Route("/test", name="homepage")
     */
    public function index()
    {
        return $this->render('base.html.twig');
//        return new Response();
//        return $this->redirect(
//            $this->getParameter('redirect_url')
//        );
    }
}