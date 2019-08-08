<?php


namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductType;
use App\Repository\ProductRepository;
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
        /** @var User $user */
        $user = $this->getUser();

       if (!$user->isAdmin() && !$user->isEditor()) {
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
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->isAdmin() && !$user->isEditor()) {
            return $this->redirectToRoute('index');
        }

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setIsAvailable(1);

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/product/edit/{id}", name="product_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function edit(Request $request, Product $product)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->isAdmin() && !$user->isEditor()) {
            return $this->redirectToRoute('index');
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_index', [
                'id' => $product->getId()
            ]);
        }

        return $this->render('product/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }

    /**
     * @Route("/product/{id}", name="product_details")
     */
    public function details(ProductRepository $productRepository, Product $product)
    {
        $currentProduct = $productRepository->find($product);

        return $this->render('product/details.html.twig', [
            'product' => $currentProduct
        ]);
    }

    /**
     * @Route("/product/delete/{id}", methods={"POST"}, name="product_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Product $product
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function remove(Product $product)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->isAdmin()) {
            return $this->redirectToRoute('index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();

        return $this->redirectToRoute('product_index');
    }
}