<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em
            ->getRepository(Product::class)
            ->createQueryBuilder('e')
            ->addOrderBy('e.addedOn', 'DESC')
            ->getQuery()
            ->execute();

        return $this->render('home/index.html.twig', [
            'products' => $products
        ]);
    }
}
