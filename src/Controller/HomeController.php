<?php

namespace App\Controller;


use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", defaults={"page": "1"}, name="index")
     * @Route("/page/{page<[1-9]\d*>}", methods={"GET"}, name="index_paginated")
     * @Cache(smaxage="10")
     */
    public function listAll(int $page, ProductRepository $productRepository,
                          CategoryRepository $categoryRepository)
    {
        $latestProducts = $productRepository->findLatest($page);

        return $this->render('home/index.html.twig', [
            'paginator' => $latestProducts,
        ]);
    }
}
