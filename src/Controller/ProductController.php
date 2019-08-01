<?php


namespace App\Controller;


use App\Entity\Product;
use App\Form\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product_index")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function index()
    {
        $user = $this->getUser();

        if (!$user->isAdmin() || !$user->isEditor()) {
            return $this->redirectToRoute('index');
        }

        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/product/create", name="product_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {
        $user = $this->getUser();

        if (!$user->isAdmin() || !$user->isEditor()) {
            return $this->redirectToRoute('index');
        }

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}