<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->findAll();
        $products = $productRepository->findBy([
            'category' => $category
        ]);

        return $this->render('home/index.html.twig', [
            'products' => $products
        ]);
    }
}
