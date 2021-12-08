<?php

namespace App\Mygento\Infrastructure\Controller\Http\News;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsController extends AbstractController
{
    /**
     * @Route("/news", name="news_index")
     */
    public function index(): Response
    {
        return new Response();
    }
}