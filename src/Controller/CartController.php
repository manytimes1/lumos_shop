<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use App\Repository\CartRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart_index")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function index(CartRepository $cartRepository)
    {
        $user = $this->getUser();
        $cartProducts = $cartRepository->findBy(['user' => $user]);

        $totalPrice = 0;
        $products = [];

        for ($i = 0; $i < count($cartProducts); $i++) {
            $product = $cartProducts[$i]->getProduct();
            $products[] = $product;
            $totalPrice += $product->totalPrice();
        }

        return $this->render('cart/index.twig', [
            'products' => $products,
            'total' => $totalPrice
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="add_to_cart")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param CartRepository $cartRepository
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addProduct(Product $product, CartRepository $cartRepository)
    {
        $user = $this->getUser();
        $cart = $cartRepository->findOneBy([
            'product' => $product,
            'user' => $user
        ]);

        if (null == $cart) {
            $cart = new Cart();
            $cart->setProduct($product);
            $cart->setUser($user);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($cart);
        $em->flush();

        return $this->redirectToRoute('index');
    }
}